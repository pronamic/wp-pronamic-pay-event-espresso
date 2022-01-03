<?php
/**
 * Gateway
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\EventEspresso
 */

namespace Pronamic\WordPress\Pay\Extensions\EventEspresso;

use EE_Error;
use EE_Offsite_Gateway;
use EEI_Transaction;
use EEI_Payment;
use Pronamic\WordPress\Money\Currency;
use Pronamic\WordPress\Money\Money;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Plugin;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Payments\PaymentStatus;

/**
 * Title: WordPress pay Event Espresso 4.6+ gateway
 * Description:
 * Copyright: 2005-2022 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.3.2
 * @since   1.1.0
 */
class Gateway extends EE_Offsite_Gateway {
	/**
	 * Payment method.
	 *
	 * @since 2.0.0
	 *
	 * @var string $payment_method
	 */
	protected $payment_method;

	/**
	 * Configuration ID
	 *
	 * Extra meta inputs on payment method settings forms are magically loaded
	 * into class variables like this one ($_config_id).
	 *
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/libraries/payment_methods/EE_PMT_Base.lib.php#L181-L183
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/libraries/payment_methods/EE_Gateway.lib.php#L158-L168
	 *
	 * @var string
	 */
	protected $_config_id;

	/**
	 * Currencies supported.
	 *
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.9.66.p/docs/L--Payment-Methods-and-Gateways/gateway-classes.md
	 * @var array
	 */
	protected $_currencies_supported = array(
		'EUR',
	);

	/**
	 * Transaction description.
	 *
	 * @since 1.1.5
	 * @var string
	 */
	protected $_transaction_description;

	/**
	 * Construct.
	 */
	public function __construct() {
		$this->set_uses_separate_IPN_request( true );

		parent::__construct();
	}

	/**
	 * Get the gateway configuration ID.
	 *
	 * @return string
	 */
	public function get_config_id() {
		return $this->_config_id;
	}

	/**
	 * Get the gateway transaction description.
	 *
	 * @since 1.1.5
	 * @return string
	 */
	public function get_transaction_description() {
		return $this->_transaction_description;
	}

	/**
	 * Set redirection info.
	 *
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/libraries/payment_methods/EE_Offsite_Gateway.lib.php#L51-L59
	 *
	 * @param EEI_Payment $ee_payment   Event Espresso payment.
	 * @param array       $billing_info Billing info.
	 * @param string      $return_url   Return URL.
	 * @param null        $notify_url   Notify URL.
	 * @param string      $cancel_url   Cancel URL.
	 * @return EEI_Payment
	 * @throws EE_Error Throws Event Espresso error if gateway or payment can't be initiated.
	 */
	public function set_redirection_info( $ee_payment, $billing_info = array(), $return_url = null, $notify_url = null, $cancel_url = null ) {
		$gateway = Plugin::get_gateway( $this->_config_id );

		if ( ! $gateway ) {
			$error = Plugin::get_default_error_message();

			// @link https://github.com/eventespresso/event-espresso-core/blob/4.6.18.p/caffeinated/payment_methods/Mijireh/EEG_Mijireh.gateway.php#L147
			throw new EE_Error( $error );
		}

		$transaction = $ee_payment->transaction();

		$total_line_item = $transaction->total_line_item();

		$transaction_id = $transaction->ID();

		$primary_attendee = $transaction->primary_registration()->attendee();

		/**
		 * Build payment.
		 */
		$payment = new Payment();

		$payment->source    = 'eventespresso';
		$payment->source_id = $transaction_id;
		$payment->order_id  = $transaction_id;

		// Description.
		$payment->set_description( EventEspressoHelper::get_description( $transaction_id, $this ) );

		$payment->title = EventEspressoHelper::get_title( $transaction_id );

		// Customer.
		$payment->set_customer( EventEspressoHelper::get_customer_from_attendee( $primary_attendee ) );

		// Currency.
		$currency = Currency::get_instance( 'EUR' );

		/**
		 * Amount.
		 *
		 * In version 2.3.1 or earlier we used `$transaction->total()`,
		 * but changed this to `$transaction->remaining()` so that
		 * incomplete or manual payments are also included.
		 *
		 * @link https://plugins.trac.wordpress.org/browser/event-espresso-decaf/tags/4.10.11.decaf/core/db_classes/EE_Transaction.class.php#L336 `EE_Transaction->total()`
		 * @link https://plugins.trac.wordpress.org/browser/event-espresso-decaf/tags/4.10.11.decaf/core/db_classes/EE_Transaction.class.php#L320 `EE_Transaction->remaining()`
		 * @link https://secure.helpscout.net/conversation/1507624590/22038?folderId=1425710
		 */
		$payment->set_total_amount( new Money( $transaction->remaining(), $currency ) );

		// Configuration.
		$payment->config_id = $this->_config_id;

		// Payment method.
		$payment_method = $this->payment_method;

		if ( null === $payment_method && $gateway->payment_method_is_required() ) {
			$payment_method = PaymentMethods::IDEAL;
		}

		$payment->set_payment_method( $payment_method );

		// Start.
		try {
			$payment = Plugin::start_payment( $payment );

			update_post_meta( $payment->get_id(), '_pronamic_payment_url_return', $return_url );
			update_post_meta( $payment->get_id(), '_pronamic_payment_url_success', $return_url );
			update_post_meta( $payment->get_id(), '_pronamic_payment_url_cancel', $cancel_url );
			update_post_meta( $payment->get_id(), '_pronamic_payment_url_error', $cancel_url );

			$redirect_url  = $payment->get_pay_redirect_url();
			$redirect_args = $gateway->get_output_fields( $payment );

			/*
			 * Since Event Espresso uses an HTML form to redirect users to the payment gateway
			 * we have to make sure an POST method is used when the redirect URL has query arguments.
			 * Otherwise the URL query arguments will be stripped by the users browser.
			 * Therefore we have to make sure the redirect arguments array is not empty.
			 *
			 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.18.p/core/db_classes/EE_Payment.class.php#L547
			 * @link http://stackoverflow.com/q/1116019
			 */
			if ( false !== strpos( $redirect_url, '?' ) && empty( $redirect_args ) ) {
				$redirect_args[] = '';
			}

			$ee_payment->set_redirect_url( $redirect_url );
			$ee_payment->set_redirect_args( $redirect_args );
		} catch ( \Exception $e ) {
			// @link https://github.com/eventespresso/event-espresso-core/blob/4.6.18.p/caffeinated/payment_methods/Mijireh/EEG_Mijireh.gateway.php#L147
			$error_message = sprintf(
				/* translators: %s: error message */
				__( 'Errors communicating with gateway: %s', 'pronamic_ideal' ),
				implode( ',', $e->getMessage() )
			);

			EE_Error::add_error( $error_message, __FILE__, __FUNCTION__, __LINE__ );

			throw new EE_Error( $error_message );
		}

		return $ee_payment;
	}

	/**
	 * Handle payment update
	 *
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/libraries/payment_methods/EE_Offsite_Gateway.lib.php#L63-L71
	 *
	 * @param array           $update_info Update info, pften the contents of $_REQUEST, but not necessarily.
	 * @param EEI_Transaction $transaction Event Espresso transaction.
	 * @return EEI_Payment
	 */
	public function handle_payment_update( $update_info, $transaction ) {
		if ( ! array_key_exists( 'pronamic_payment_status', $update_info ) ) {
			return;
		}

		$payment = $transaction->last_payment();

		if ( empty( $payment ) ) {
			return;
		}

		$status = $update_info['pronamic_payment_status'];

		switch ( $status ) {
			case PaymentStatus::CANCELLED:
				$payment->set_status( $this->_pay_model->failed_status() );

				break;
			case PaymentStatus::EXPIRED:
				$payment->set_status( $this->_pay_model->failed_status() );

				break;
			case PaymentStatus::FAILURE:
				$payment->set_status( $this->_pay_model->failed_status() );

				break;
			case PaymentStatus::SUCCESS:
				$payment->set_status( $this->_pay_model->approved_status() );

				break;
		}

		return $payment;
	}
}
