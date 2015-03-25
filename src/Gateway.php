<?php

/**
 * Title: WordPress pay Event Espresso 4.6+ gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2015
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.1.0
 * @since 1.1.0
 */
class Pronamic_WP_Pay_Extensions_EventEspresso_Gateway extends EE_Offsite_Gateway {
	/**
	 * Configuration ID
	 *
	 * Extra meta inputs on payment method settings forms are magically loaded
	 * into class variables like this one ($_config_id).
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/libraries/payment_methods/EE_PMT_Base.lib.php#L181-L183
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/libraries/payment_methods/EE_Gateway.lib.php#L158-L168
	 *
	 * @var string
	 */
	protected $_config_id;

	//////////////////////////////////////////////////

	/**
	 * Get the gateay configuration ID
	 *
	 * @return string
	 */
	public function get_config_id() {
		return $this->_config_id;
	}

	//////////////////////////////////////////////////

	private function get_fields_from_html( $html ) {
		$fields = array();

		$extracted_fields = explode( '<input ', $html );

		foreach ( $extracted_fields as $field ) {
			preg_match_all( '#([a-z]+=".*?")#i', $field, $inputs );

			foreach ( $inputs[0] as $match ) {
				list( $key, $value ) = explode( '="', $match );

				// Remove start and end quotes
				$value = substr( $value, 0, -1 );

				switch ( strtolower( $key ) ) {
					case 'name':
						$name = $value;
						break;

					case 'value':
						$input_value = $value;
						break;
				}
			}

			if ( isset( $name ) && isset( $input_value ) ) {
				$fields[ $name ] = $input_value;

				unset( $name );
				unset( $input_value );
			}
		}

		return $fields;
	}

	//////////////////////////////////////////////////

	/**
	 * Set redirection info
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/libraries/payment_methods/EE_Offsite_Gateway.lib.php#L51-L59
	 *
	 * @param EEI_Payment $payment
	 * @param type $billing_info
	 * @param type $return_url
	 * @param type $cancel_url
	 */
	public function set_redirection_info( $ee_payment, $billing_info = array(), $return_url = null, $notify_url = null, $cancel_url = null ) {
		$pronamic_gateway = Pronamic_WP_Pay_Plugin::get_gateway( $this->_config_id );

		if ( $pronamic_gateway ) {
			$transaction = $ee_payment->transaction();

			$total_line_item = $transaction->total_line_item();

			$data = new Pronamic_WP_Pay_Extensions_EventEspresso_PaymentData( $this, $total_line_item, $transaction );

			$pronamic_payment = Pronamic_WP_Pay_Plugin::start( $this->_config_id, $pronamic_gateway, $data );

			$error = $pronamic_gateway->get_error();

			if ( is_wp_error( $error ) ) {
				foreach ( $error->get_error_messages() as $message ) {
					// @todo Add message as notice to Event Espresso?
					die( $message );
				}
			} else {
				update_post_meta( $pronamic_payment->get_id(), '_pronamic_payment_url_return', $return_url );
				update_post_meta( $pronamic_payment->get_id(), '_pronamic_payment_url_success', $return_url );
				update_post_meta( $pronamic_payment->get_id(), '_pronamic_payment_url_cancel', $cancel_url );
				update_post_meta( $pronamic_payment->get_id(), '_pronamic_payment_url_error', $cancel_url );

				$args = $this->get_fields_from_html( $pronamic_gateway->get_output_html() );

				$ee_payment->set_redirect_url( $pronamic_payment->get_action_url() );
				$ee_payment->set_redirect_args( $args );
			}
		}

		return $ee_payment;
	}

	//////////////////////////////////////////////////

	/**
	 * Handle payment update
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/libraries/payment_methods/EE_Offsite_Gateway.lib.php#L63-L71
	 */
	public function handle_payment_update( $update_info, $transaction ) {
		// Nothing to do here, this is handeld from the Extension class.
	}
}
