<?php

namespace Pronamic\WordPress\Pay\Extensions\EventEspresso;

use EE_Config;
use EE_Registry;
use EEM_Gateways;
use EEM_Transaction;
use Pronamic\WordPress\Pay\Core\Statuses;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Title: WordPress pay Event Espresso extension
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.2
 */
class Extension {
	/**
	 * Slug
	 *
	 * @var string
	 */
	const SLUG = 'eventespresso';

	/**
	 * Bootstrap
	 */
	public static function bootstrap() {
		new self();
	}

	/**
	 * Constructs and initalize an Event Espresso extension
	 */
	public function __construct() {
		// Actions
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 0 );
	}

	/**
	 * Is active
	 *
	 * @return boolean
	 */
	public static function is_active() {
		// @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/espresso.php#L53
		return defined( 'EVENT_ESPRESSO_VERSION' ) && version_compare( EVENT_ESPRESSO_VERSION, '4', '>=' );
	}

	/**
	 * Plugins loaded
	 */
	public function plugins_loaded() {
		if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
			return;
		}

		if ( version_compare( EVENT_ESPRESSO_VERSION, '4.6', '>=' ) ) {
			$this->init_ee4dot6plus();
		} elseif ( version_compare( EVENT_ESPRESSO_VERSION, '4', '>=' ) && version_compare( EVENT_ESPRESSO_VERSION, '4.6', '<' ) ) {
			$this->init_ee4_to_ee4dot6();
		}

		// Actions
		add_filter( 'pronamic_payment_redirect_url_' . self::SLUG, array( __CLASS__, 'redirect_url' ), 10, 2 );
		add_filter( 'pronamic_payment_source_text_' . self::SLUG, array( $this, 'source_text' ), 10, 2 );
		add_filter( 'pronamic_payment_source_description_' . self::SLUG, array( $this, 'source_description' ), 10, 2 );
		add_filter( 'pronamic_payment_source_url_' . self::SLUG, array( $this, 'source_url' ), 10, 2 );

		add_action( 'pronamic_payment_status_update_' . self::SLUG, array( $this, 'status_update' ), 10, 2 );
		add_action( 'pronamic_payment_status_update__' . self::SLUG . '_unknown_to_success', array( $this, 'update_status_unknown_to_success' ), 10, 2 );

		add_action( 'AHEE__EE_Transaction_Processor__update_transaction_and_registrations_after_checkout_or_payment', array( $this, 'remove_registration_update_notifications' ), 15, 2 );
	}

	/**
	 * Remove registration update notifications trigger action.
	 *
	 * @param EEM_Transaction $transaction   Transaction.
	 * @param array           $update_params Update parameters.
	 */
	public function remove_registration_update_notifications( $transaction, $update_params ) {
		$payment = get_pronamic_payment_by_meta( '_pronamic_payment_source_id', $transaction->ID() );

		if ( null !== $payment ) {
			remove_action( 'AHEE__EE_Registration_Processor__trigger_registration_update_notifications', array( 'EED_Messages', 'maybe_registration' ), 10, 2 );
		}
	}

	/**
	 * Initialize Event Espresso 4.6+
	 */
	private function init_ee4dot6plus() {
		// Actions
		add_action( 'AHEE__EE_System__load_espresso_addons', array( $this, 'load_espresso_addons' ) );
	}

	/**
	 * Load Espresso addons
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.16.p/core/EE_System.core.php#L162-L163
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.16.p/core/EE_System.core.php#L383-L398
	 */
	public function load_espresso_addons() {
		if ( ! class_exists( 'EE_Addon' ) ) {
			return;
		}

		/*
		 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.16.p/tests/mocks/addons/new-payment-method/espresso-new-payment-method.php#L45
		 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.16.p/tests/mocks/addons/new-payment-method/EE_New_Payment_Method.class.php#L26-L46
		 */
		AddOn::register_addon();
	}

	/**
	 * Initialize Event Espresso > 4.0 < 4.6
	 */
	private function init_ee4_to_ee4dot6() {
		if ( ! class_exists( 'EE_Offsite_Gateway' ) ) {
			return;
		}

		$gateways = array(
			'pronamic_pay_ideal' => 'Pronamic\WordPress\Pay\Extensions\EventEspresso\IDealGateway',
		);

		foreach ( $gateways as $gateway => $alias ) {
			// @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/core/db_models/EEM_Gateways.model.php#L217
			$class_name = 'EE_' . $gateway;

			class_alias( $alias, $class_name );

			// @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/core/db_models/EEM_Gateways.model.php#L198-L201
			if ( defined( 'EVENT_ESPRESSO_GATEWAY_DIR' ) ) {
				$gateway_dir   = EVENT_ESPRESSO_GATEWAY_DIR . $gateway;
				$gateway_class = $gateway_dir . '/' . $class_name . '.class.php';

				if ( ! is_readable( $gateway_class ) ) {
					$created = wp_mkdir_p( $gateway_dir );

					if ( $created ) {
						touch( $gateway_class );
					}
				}
			}
		}
	}

	/**
	 * Update lead status of the specified payment
	 *
	 * @param Payment $payment
	 * @param bool    $can_redirect
	 */
	public static function status_update( Payment $payment, $can_redirect = true ) {
		// Get return URLs
		$url_return  = get_post_meta( $payment->get_id(), '_pronamic_payment_url_return', true );
		$url_success = get_post_meta( $payment->get_id(), '_pronamic_payment_url_success', true );
		$url_cancel  = get_post_meta( $payment->get_id(), '_pronamic_payment_url_cancel', true );
		$url_error   = get_post_meta( $payment->get_id(), '_pronamic_payment_url_error', true );

		$url    = $url_return;
		$status = PaymentStatuses::PENDING;

		// Status
		switch ( $payment->get_status() ) {
			case Statuses::CANCELLED:
				$url    = $url_cancel;
				$status = PaymentStatuses::CANCELLED;

				break;
			case Statuses::EXPIRED:
				$url    = $url_error;
				$status = PaymentStatuses::FAILED;

				break;
			case Statuses::FAILURE:
				$url    = $url_error;
				$status = PaymentStatuses::FAILED;

				break;
			case Statuses::SUCCESS:
				$url    = $url_success;
				$status = PaymentStatuses::APPROVED;

				break;
		}

		if ( version_compare( EVENT_ESPRESSO_VERSION, '4.6', '>=' ) ) {
			// Transaction
			$transaction_processor = EE_Registry::instance()->load_class( 'Transaction_Processor' );
			$ee_transaction        = EEM_Transaction::instance()->get_one_by_ID( $payment->get_source_id() );
			$ee_payment            = $ee_transaction->last_payment();

			if ( $transaction_processor->reg_step_completed( $ee_transaction, 'finalize_registration' ) ) {
				// Set redirect URL to thank you page.
				$url = EE_Config::instance()->core->thank_you_page_url( array(
					'e_reg_url_link'    => $ee_transaction->primary_registration()->reg_url_link(),
					'ee_payment_method' => 'pronamic',
				) );
			}

			// Payment status has changed, save.
			if ( $ee_payment && $ee_payment->status() !== $status ) {
				$ee_payment->set_txn_id_chq_nmbr( $payment->get_transaction_id() );
				$ee_payment->set_status( $status );
				$ee_payment->save();

				$payment_processor = EE_Registry::instance()->load_core( 'Payment_Processor' );
				$payment_processor->update_txn_based_on_payment( $ee_transaction, $ee_payment, true, true );

				// Remove scheduled event to prevent updating transaction and sending notifications twice.
				wp_clear_scheduled_hook(
					'AHEE__EE_Cron_Tasks__update_transaction_with_payment_2',
					array(
						$ee_transaction->ID(),
						$ee_payment->ID(),
					)
				);
			}
		}

		// Redirect.
		if ( $can_redirect ) {
			wp_redirect( $url );

			exit;
		}
	}

	/**
	 * Update lead status of the specified payment
	 *
	 * @param Payment $payment
	 * @param bool    $can_redirect
	 */
	public function update_status_unknown_to_success( Payment $payment, $can_redirect = false ) {
		// Check for Eevent Espresso version 4.0 to 4.6.
		if ( ! ( version_compare( EVENT_ESPRESSO_VERSION, '4', '>=' ) && version_compare( EVENT_ESPRESSO_VERSION, '4.6', '<' ) ) ) {
			return;
		}

		$gateway = EEM_Gateways::instance()->get_gateway( 'pronamic_pay_ideal' );

		if ( ! $gateway ) {
			return;
		}

		$transaction_id = $payment->get_source_id();

		// @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/admin_pages/transactions/Transactions_Admin_Page.core.php#L332-L337
		$transaction_model = EEM_Transaction::instance();

		$transaction = $transaction_model->get_one_by_ID( $transaction_id );

		global $pronamic_payment, $pronamic_url;

		$pronamic_payment = $payment;

		$gateway->handle_ipn_for_transaction( $transaction );

		unset( $pronamic_payment );

		// Redirect.
		if ( $can_redirect ) {
			wp_redirect( $pronamic_url, 303 );

			exit;
		}
	}

	/**
	 * Payment redirect URL filter.
	 *
	 * @param string  $url
	 * @param Payment $payment
	 *
	 * @return string
	 */
	public static function redirect_url( $url, Payment $payment ) {
		$redirect_url = get_post_meta( $payment->get_id(), '_pronamic_payment_url_return', true );

		switch ( $payment->get_status() ) {
			case Statuses::CANCELLED:
				$redirect_url = get_post_meta( $payment->get_id(), '_pronamic_payment_url_cancel', true );

				break;
			case Statuses::FAILURE:
				$redirect_url = get_post_meta( $payment->get_id(), '_pronamic_payment_url_error', true );

				break;
			case Statuses::SUCCESS:
				$redirect_url = get_post_meta( $payment->get_id(), '_pronamic_payment_url_success', true );

				break;
		}

		if ( ! empty( $redirect_url ) ) {
			return $redirect_url;
		}

		return $url;
	}

	/**
	 * Source column
	 *
	 * @param string  $text
	 * @param Payment $payment
	 *
	 * @return string
	 */
	public static function source_text( $text, Payment $payment ) {
		$url = add_query_arg( array(
			'page'   => 'espresso_transactions',
			'action' => 'view_transaction',
			'TXN_ID' => $payment->get_source_id(),
		), admin_url( 'admin.php' ) );

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
	 * @param string  $description
	 * @param Payment $payment
	 *
	 * @return string
	 */
	public function source_description( $description, Payment $payment ) {
		return __( 'Event Espresso Transaction', 'pronamic_ideal' );
	}

	/**
	 * Source URL.
	 *
	 * @param string  $url
	 * @param Payment $payment
	 *
	 * @return string
	 */
	public function source_url( $url, Payment $payment ) {
		return add_query_arg( array(
			'page'   => 'espresso_transactions',
			'action' => 'view_transaction',
			'TXN_ID' => $payment->get_source_id(),
		), admin_url( 'admin.php' ) );
	}
}
