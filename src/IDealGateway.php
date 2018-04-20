<?php

namespace Pronamic\WordPress\Pay\Extensions\EventEspresso;

use EE_Line_Item;
use EE_Offsite_Gateway;
use EE_Payment;
use EE_Transaction;
use EEM_Gateways;
use EEM_Payment;
use Pronamic\WordPress\Pay\Admin\AdminModule;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Title: WordPress pay Event Espresso iDEAL gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 1.1.3
 * @since   1.0.1
 */
class IDealGateway extends EE_Offsite_Gateway {
	/**
	 * Instance
	 *
	 * @var _instance Gateway instance.
	 */
	private static $_instance = null;

	/**
	 * Instance.
	 *
	 * @param EEM_Gateways $model Gateway model.
	 *
	 * @return IDealGateway
	 */
	public static function instance( EEM_Gateways $model ) {
		// Check if class object is instantiated.
		if ( null === self::$_instance || ! is_object( self::$_instance ) || ! ( self::$_instance instanceof self ) ) {
			self::$_instance = new self( $model );
		}

		return self::$_instance;
	}

	/**
	 * Constructs and initialize iDEAL gateway.
	 *
	 * @param EEM_Gateways $model Gateway model.
	 */
	public function __construct( EEM_Gateways $model ) {
		$this->_gateway_name = 'pronamic_pay_ideal';
		$this->_path         = str_replace( '\\', '/', __FILE__ );
		$this->_btn_img      = plugins_url( 'images/ideal/ee-4-icon.png', Plugin::$file );

		// @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/core/db_classes/EE_Offsite_Gateway.class.php#L4
		// @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/core/db_classes/EE_Gateway.class.php#L26
		parent::__construct( $model );
	}

	/**
	 * Default settings
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/core/db_classes/EE_Gateway.class.php#L83
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/modules/gateways/Paypal_Standard/EE_Paypal_Standard.class.php#L55
	 */
	protected function _default_settings() {
		$this->_payment_settings['display_name'] = __( 'iDEAL', 'pronamic_ideal' );
		$this->_payment_settings['button_url']   = plugins_url( 'images/ideal/ee-4-icon.png', Plugin::$file );
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
				<label><?php esc_html_e( 'Configuration', 'pronamic_ideal' ); ?></label>
			</th>
			<td>
				<?php

				echo AdminModule::dropdown_configs( array( // WPCS: xss ok.
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
	 * @param string $selected_gateway Selected gateway.
	 */
	public function espresso_display_payment_gateways( $selected_gateway = '' ) {
		$this->_css_class = ( $selected_gateway === $this->_gateway_name ) ? '' : ' hidden';

		echo $this->_generate_payment_gateway_selection_button(); // WPCS: xss ok.

		$config_id = $this->_payment_settings['config_id'];

		$gateway = Plugin::get_gateway( $config_id );

		$gateway->set_payment_method( PaymentMethods::IDEAL );

		?>
		<div id="reg-page-billing-info-<?php echo esc_attr( $this->_gateway_name ); ?>-dv" class="reg-page-billing-info-dv <?php echo esc_attr( $this->_css_class ); ?>">
			<h3><?php esc_html_e( 'You have selected "iDEAL" as your method of payment', 'pronamic_ideal' ); ?></h3>
			<p><?php esc_html_e( 'After finalizing your registration, you will be transferred to iDEAL where your payment will be securely processed.', 'pronamic_ideal' ); ?></p>

			<?php

			echo $gateway->get_input_html(); // WPCS: xss ok.

			?>
		</div>
		<?php
	}

	/**
	 * Display settings help
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/modules/gateways/Paypal_Standard/EE_Paypal_Standard.class.php#L302-L345
	 */
	protected function _display_settings_help() {
	}

	/**
	 * Process payment start.
	 *
	 * @param EE_Line_Item   $total_line_item Total line item.
	 * @param EE_Transaction $transaction     Transaction.
	 */
	public function process_payment_start( EE_Line_Item $total_line_item, $transaction = null ) {
		if ( ! $transaction ) {
			$transaction = $total_line_item->transaction();
		}

		$config_id = $this->_payment_settings['config_id'];

		$gateway = Plugin::get_gateway( $config_id );

		if ( ! $gateway ) {
			return;
		}

		$data = new PaymentData( $this, $total_line_item, $transaction );

		$payment = Plugin::start( $config_id, $gateway, $data );

		$error = $gateway->get_error();

		if ( ! is_wp_error( $error ) ) {
			$offsite_form = $this->submitPayment();

			$offsite_form['form'] = $gateway->get_form_html( $payment, true );

			$this->_EEM_Gateways->set_off_site_form( $offsite_form );

			$this->redirect_after_reg_step_3();
		}
	}

	/**
	 * Handle IPN for transaction
	 *
	 * @param EE_Transaction $transaction
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
				'PAY_amount'          => $pronamic_payment->get_amount()->get_amount(),
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

		// Update
		$this->update_transaction_with_payment( $transaction, $payment );
	}
}
