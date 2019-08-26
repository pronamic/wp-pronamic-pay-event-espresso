<?php
/**
 * Gateway
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\EventEspresso
 */

namespace Pronamic\WordPress\Pay\Extensions\EventEspresso;

use EE_Error;
use EE_Offsite_Gateway;
use EEI_Transaction;
use EEI_Payment;
use Pronamic\WordPress\Pay\Plugin;
use Pronamic\WordPress\Pay\Core\Statuses;

/**
 * Title: WordPress pay Event Espresso 4.6+ gateway
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.1.0
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
	 * Get the gateay configuration ID.
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

		$data = new PaymentData( $this, $total_line_item, $transaction );

		$payment = Plugin::start( $this->_config_id, $gateway, $data, $this->payment_method );

		$error = $gateway->get_error();

		if ( is_wp_error( $error ) ) {
			// @link https://github.com/eventespresso/event-espresso-core/blob/4.6.18.p/caffeinated/payment_methods/Mijireh/EEG_Mijireh.gateway.php#L147
			$error_message = sprintf(
				/* translators: %s: error message */
				__( 'Errors communicating with gateway: %s', 'pronamic_ideal' ),
				implode( ',', $error->get_error_messages() )
			);

			EE_Error::add_error( $error_message, __FILE__, __FUNCTION__, __LINE__ );

			throw new EE_Error( $error_message );
		} else {
			update_post_meta( $payment->get_id(), '_pronamic_payment_url_return', $return_url );
			update_post_meta( $payment->get_id(), '_pronamic_payment_url_success', $return_url );
			update_post_meta( $payment->get_id(), '_pronamic_payment_url_cancel', $cancel_url );
			update_post_meta( $payment->get_id(), '_pronamic_payment_url_error', $cancel_url );

			$redirect_url  = $payment->get_pay_redirect_url();
			$redirect_args = $gateway->get_output_fields();

			/*
			 * Since Event Espresso uses an HTML form to redirect users to the payment gateway
			 * we have to make sure an POST method is used when the redirect URL has query arguments.
			 * Otherwise the URL query arguments will be stripped by the users browser.
			 * Therefor we have to make sure the redirect arguments array is not empty.
			 *
			 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.18.p/core/db_classes/EE_Payment.class.php#L547
			 * @link http://stackoverflow.com/q/1116019
			 */
			if ( false !== strpos( $redirect_url, '?' ) && empty( $redirect_args ) ) {
				$redirect_args[] = '';
			}

			$ee_payment->set_redirect_url( $redirect_url );
			$ee_payment->set_redirect_args( $redirect_args );
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
			case Statuses::CANCELLED:
				$payment->set_status( $this->_pay_model->failed_status() );

				break;
			case Statuses::EXPIRED:
				$payment->set_status( $this->_pay_model->failed_status() );

				break;
			case Statuses::FAILURE:
				$payment->set_status( $this->_pay_model->failed_status() );

				break;
			case Statuses::SUCCESS:
				$payment->set_status( $this->_pay_model->approved_status() );

				break;
		}

		return $payment;
	}
}
