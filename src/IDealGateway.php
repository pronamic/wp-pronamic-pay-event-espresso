<?php

/**
 * Title: WordPress pay Event Espresso iDEAL gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.1.3
 * @since 1.0.1
 */
class Pronamic_WP_Pay_Extensions_EventEspresso_IDealGateway extends EE_Offsite_Gateway {
	/**
	 * Instance
	 */
	private static $_instance = null;

	//////////////////////////////////////////////////

	/**
	 * Instance
	 */
	public static function instance( EEM_Gateways $model ) {
		// check if class object is instantiated
		if ( null === self::$_instance || ! is_object( self::$_instance ) || ! ( self::$_instance instanceof self ) ) {
			self::$_instance = new self( $model );
		}

		return self::$_instance;
	}

	//////////////////////////////////////////////////

	/**
	 * Constructs and initialize iDEAL gateway
	 *
	 * @param EEM_Gateways $model
	 */
	public function __construct( EEM_Gateways $model ) {
		$this->_gateway_name = 'pronamic_pay_ideal';
		$this->_path         = str_replace( '\\', '/', __FILE__ );
		$this->_btn_img      = plugins_url( 'images/ideal/ee-4-icon.png', Pronamic_WP_Pay_Plugin::$file );

		// @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/core/db_classes/EE_Offsite_Gateway.class.php#L4
		// @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/core/db_classes/EE_Gateway.class.php#L26
		parent::__construct( $model );
	}

	//////////////////////////////////////////////////
	// Abstract functions
	//////////////////////////////////////////////////

	/**
	 * Default settings
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/core/db_classes/EE_Gateway.class.php#L83
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/modules/gateways/Paypal_Standard/EE_Paypal_Standard.class.php#L55
	 */
	protected function _default_settings() {
		$this->_payment_settings['display_name'] = __( 'iDEAL', 'pronamic_ideal' );
		$this->_payment_settings['button_url']   = plugins_url( 'images/ideal/ee-4-icon.png', Pronamic_WP_Pay_Plugin::$file );
		$this->_payment_settings['current_path'] = '';
	}

	/**
	 * Update settings
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/core/db_classes/EE_Gateway.class.php#L84
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/modules/gateways/Paypal_Standard/EE_Paypal_Standard.class.php#L67-L74
	 */
	protected function _update_settings() {
		$this->_payment_settings['config_id']  = filter_input( INPUT_POST, 'config_id', FILTER_SANITIZE_STRING );
		$this->_payment_settings['button_url'] = filter_input( INPUT_POST, 'button_url', FILTER_SANITIZE_STRING );
	}

	/**
	 * Display settings
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/core/db_classes/EE_Gateway.class.php#L85
	 */
	protected function _display_settings() {
		?>
		<tr>
			<th>
				<label><?php _e( 'Configuration', 'pronamic_ideal' ); ?></label>
			</th>
			<td>
				<?php

				echo Pronamic_WP_Pay_Admin::dropdown_configs( array(
					'name'     => 'config_id',
					'selected' => $this->_payment_settings['config_id'],
				) );

				?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Display payment gateways
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/core/db_classes/EE_Gateway.class.php#L86
	 *
	 * @param string $selected_gateway
	 */
	public function espresso_display_payment_gateways( $selected_gateway = '' ) {
		$this->_css_class = ( $selected_gateway === $this->_gateway_name ) ? '' : ' hidden';

		echo $this->_generate_payment_gateway_selection_button();

		$config_id = $this->_payment_settings['config_id'];

		$gateway = Pronamic_WP_Pay_Plugin::get_gateway( $config_id );

		$gateway->set_payment_method( Pronamic_WP_Pay_PaymentMethods::IDEAL );

		?>
		<div id="reg-page-billing-info-<?php echo $this->_gateway_name; ?>-dv" class="reg-page-billing-info-dv <?php echo $this->_css_class; ?>">
			<h3><?php _e( 'You have selected "iDEAL" as your method of payment', 'pronamic_ideal' ); ?></h3>
			<p><?php _e( 'After finalizing your registration, you will be transferred to iDEAL where your payment will be securely processed.', 'pronamic_ideal' ); ?></p>

			<?php
			echo $gateway->get_input_html();
			?>
		</div>
		<?php
	}

	//////////////////////////////////////////////////

	/**
	 * Display settings help
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/modules/gateways/Paypal_Standard/EE_Paypal_Standard.class.php#L302-L345
	 */
	protected function _display_settings_help() {

	}

	//////////////////////////////////////////////////

	/**
	 * Process payment start
	 *
	 * @param EE_Line_Item $total_line_item
	 * @param $transaction
	 */
	public function process_payment_start( EE_Line_Item $total_line_item, $transaction = null ) {
		if ( ! $transaction ) {
			$transaction = $total_line_item->transaction();
		}

		$config_id = $this->_payment_settings['config_id'];

		$gateway = Pronamic_WP_Pay_Plugin::get_gateway( $config_id );

		if ( $gateway ) {
			$data = new Pronamic_WP_Pay_Extensions_EventEspresso_PaymentData( $this, $total_line_item, $transaction );

			$payment = Pronamic_WP_Pay_Plugin::start( $config_id, $gateway, $data );

			$error = $gateway->get_error();

			if ( ! is_wp_error( $error ) ) {
				$offsite_form = $this->submitPayment();

				$offsite_form['form'] = $gateway->get_form_html( $payment, true );

				$this->_EEM_Gateways->set_off_site_form( $offsite_form );

				$this->redirect_after_reg_step_3();
			}
		}
	}

	/**
	 * Handle IPN for transaction
	 */
	public function handle_ipn_for_transaction( EE_Transaction $transaction ) {
		global $pronamic_payment, $pronamic_url;

		// Transaction ID
	    $transaction_id = $transaction->ID();

	    // Payment
	    $payment = $this->_PAY->get_payment_by_txn_id_chq_nmbr( $transaction_id );

	    if ( empty( $payment ) ) {
			$payment = EE_Payment::new_instance( array(
				'TXN_ID'              => $transaction_id,
				'STS_ID'              => EEM_Payment::status_id_approved,
				'PAY_timestamp'       => $transaction->datetime(),
				'PAY_amount'          => $pronamic_payment->amount,
				'PAY_gateway'         => __( 'iDEAL', 'pronamic_ideal' ),
				'PAY_txn_id_chq_nmbr' => $transaction_id,
			) );
	    } else {
			$payment->set_status( EEM_Payment::status_id_approved );
	    }

	    // Save
		$payment->save();

		// URL
		$registration = $transaction->primary_registration();

		$pronamic_url = $this->_get_return_url( $registration );

		// Return update
		return $this->update_transaction_with_payment( $transaction, $payment );
	}
}
