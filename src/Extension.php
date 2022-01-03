<?php
/**
 * Extension
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\EventEspresso
 */

namespace Pronamic\WordPress\Pay\Extensions\EventEspresso;

use EE_Config;
use EE_Payment_Processor;
use EE_Registry;
use EEM_Gateways;
use EEM_Transaction;
use Pronamic\WordPress\Pay\AbstractPluginIntegration;
use Pronamic\WordPress\Pay\Payments\PaymentStatus;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Title: WordPress pay Event Espresso extension
 * Description:
 * Copyright: 2005-2022 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.1.3
 * @since   1.0.2
 */
class Extension extends AbstractPluginIntegration {
	/**
	 * Slug.
	 *
	 * @var string
	 */
	const SLUG = 'eventespresso';

	/**
	 * Construct and initialize Event Espresso extension.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name' => __( 'Event Espresso', 'pronamic_ideal' ),
			)
		);

		// Dependencies.
		$dependencies = $this->get_dependencies();

		$dependencies->add( new EventEspressoDependency() );
	}

	/**
	 * Setup plugin integration.
	 *
	 * @return void
	 */
	public function setup() {
		add_filter( 'pronamic_payment_source_text_' . self::SLUG, array( $this, 'source_text' ), 10, 2 );
		add_filter( 'pronamic_payment_source_description_' . self::SLUG, array( $this, 'source_description' ), 10, 2 );

		// Check if dependencies are met and integration is active.
		if ( ! $this->is_active() ) {
			return;
		}

		// Actions.
		add_action( 'AHEE__EE_System__load_espresso_addons', array( $this, 'register_addon' ) );

		add_filter( 'pronamic_payment_source_url_' . self::SLUG, array( $this, 'source_url' ), 10, 2 );
		add_filter( 'pronamic_payment_redirect_url_' . self::SLUG, array( __CLASS__, 'redirect_url' ), 10, 2 );
		add_action( 'pronamic_payment_status_update_' . self::SLUG, array( $this, 'status_update' ), 10 );
	}

	/**
	 * Load Espresso addons.
	 *
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.16.p/core/EE_System.core.php#L162-L163
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.16.p/core/EE_System.core.php#L383-L398
	 *
	 * @hooked AHEE__EE_System__load_espresso_addons - 10 - https://github.com/eventespresso/event-espresso-core/blob/4.9.66.p/core/EE_System.core.php#L378
	 */
	public function register_addon() {
		/*
		 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.16.p/tests/mocks/addons/new-payment-method/espresso-new-payment-method.php#L45
		 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.16.p/tests/mocks/addons/new-payment-method/EE_New_Payment_Method.class.php#L26-L46
		 */
		AddOn::register_addon();
	}

	/**
	 * Update lead status of the specified payment.
	 *
	 * @param Payment $payment Pronamic payment.
	 */
	public static function status_update( Payment $payment ) {
		$ee_transaction = EEM_Transaction::instance()->get_one_by_ID( $payment->get_source_id() );
		$ee_payment     = $ee_transaction->last_payment();

		EE_Payment_Processor::instance()->process_ipn(
			array(
				'pronamic_payment_id'     => $payment->get_id(),
				'pronamic_payment_status' => $payment->get_status(),
			),
			$ee_transaction,
			$ee_payment->payment_method()
		);
	}

	/**
	 * Payment redirect URL filter.
	 *
	 * @param string  $url     Redirect URL.
	 * @param Payment $payment Pronamic payment.
	 *
	 * @return string
	 */
	public static function redirect_url( $url, Payment $payment ) {
		$redirect_url = get_post_meta( $payment->get_id(), '_pronamic_payment_url_return', true );

		switch ( $payment->get_status() ) {
			case PaymentStatus::CANCELLED:
				$redirect_url = get_post_meta( $payment->get_id(), '_pronamic_payment_url_cancel', true );

				break;
			case PaymentStatus::FAILURE:
				$redirect_url = get_post_meta( $payment->get_id(), '_pronamic_payment_url_error', true );

				break;
			case PaymentStatus::SUCCESS:
				$redirect_url = get_post_meta( $payment->get_id(), '_pronamic_payment_url_success', true );

				break;
		}

		if ( ! empty( $redirect_url ) ) {
			return $redirect_url;
		}

		return $url;
	}

	/**
	 * Source column.
	 *
	 * @param string  $text    Source text.
	 * @param Payment $payment Pronamic payment.
	 *
	 * @return string
	 */
	public static function source_text( $text, Payment $payment ) {
		$url = add_query_arg(
			array(
				'page'   => 'espresso_transactions',
				'action' => 'view_transaction',
				'TXN_ID' => $payment->get_source_id(),
			),
			admin_url( 'admin.php' )
		);

		$text = __( 'Event Espresso', 'pronamic_ideal' ) . '<br />';

		$text .= sprintf(
			'<a href="%s">%s</a>',
			esc_attr( $url ),
			/* translators: %s: payment source id */
			sprintf( __( 'Transaction %s', 'pronamic_ideal' ), $payment->get_source_id() )
		);

		return $text;
	}

	/**
	 * Source description.
	 *
	 * @param string  $description Source description.
	 * @param Payment $payment     Pronamic payment.
	 *
	 * @return string
	 */
	public function source_description( $description, Payment $payment ) {
		return __( 'Event Espresso Transaction', 'pronamic_ideal' );
	}

	/**
	 * Source URL.
	 *
	 * @param string  $url     Source URL.
	 * @param Payment $payment Pronamic payment.
	 *
	 * @return string
	 */
	public function source_url( $url, Payment $payment ) {
		return add_query_arg(
			array(
				'page'   => 'espresso_transactions',
				'action' => 'view_transaction',
				'TXN_ID' => $payment->get_source_id(),
			),
			admin_url( 'admin.php' )
		);
	}
}
