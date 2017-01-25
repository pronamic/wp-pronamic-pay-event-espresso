<?php

/**
 * Title: WordPress pay Event Espresso 4.6+ payment method
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.1.5
 * @since 1.1.0
 */
class Pronamic_WP_Pay_Extensions_EventEspresso_PaymentMethod extends EE_PMT_Base {
	/**
	 * Constructs and initializes an Event Espresso payment method
	 *
	 * @param EE_Payment_Method $pm_instance
	 */
	public function __construct( $pm_instance = null ) {
		$this->_gateway            = new Pronamic_WP_Pay_Extensions_EventEspresso_Gateway();
		$this->_pretty_name        = __( 'Pronamic', 'pronamic_ideal' );
		$this->_default_button_url = plugins_url( 'images/ideal/ee-4-icon.png', Pronamic_WP_Pay_Plugin::$file );

		parent::__construct( $pm_instance );
	}

	//////////////////////////////////////////////////

	/**
	 * Creates the billing form for this payment method type.
	 *
	 * @param \EE_Transaction $transaction
	 * @return NULL
	 */
	public function generate_new_billing_form( EE_Transaction $transaction = null ) {
		$config_id = $this->_gateway->get_config_id();

		$gateway = Pronamic_WP_Pay_Plugin::get_gateway( $config_id );

		if ( $gateway ) {
			if ( $gateway->payment_method_is_required() ) {
				$gateway->set_payment_method( Pronamic_WP_Pay_PaymentMethods::IDEAL );
			}

			$form = new EE_Billing_Info_Form(
				$this->_pm_instance,
				array(
					'name'        => 'Pronamic_WP_Pay_Billing_Form',
					'subsections' => array(
						'html'    => new EE_Form_Section_HTML( $gateway->get_input_html() ),
					),
				)
			);

			return $form;
		}

		return null;
	}

	//////////////////////////////////////////////////

	/**
	 * Gets the form for all the settings related to this payment method type
	 *
	 * @return EE_Payment_Method_Form
	 */
	public function generate_new_settings_form() {
		EE_Registry::instance()->load_helper( 'Template' );

		$config_options = Pronamic_WP_Pay_Plugin::get_config_select_options();

		// Fix for incorrect normalization strategy
		// @see https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/libraries/form_sections/inputs/EE_Form_Input_With_Options_Base.input.php#L89-L113
		$select_option = $config_options[0];

		unset( $config_options[0] );

		$config_options = array( 'select' => $select_option ) + $config_options;

		$form = new EE_Payment_Method_Form( array(
			'extra_meta_inputs' => array(
				'config_id' => new EE_Select_Input(
					$config_options,
					array(
						'html_label_text' => __( 'Configuration', 'pronamic_ideal' ),
						'default'         => get_option( 'pronamic_pay_config_id' ),
					)
				),
				'transaction_description' => new EE_Text_Input(
					array(
						'html_label_text' => __( 'Transaction description', 'pronamic_ideal' ),
						'html_help_text'  => sprintf( __( 'Available tags: %s', 'pronamic_ideal' ), sprintf( '<code>%s</code>', '{transaction_id}' ) ),
						'default'         => __( 'Event Espresso transaction {transaction_id}', 'pronamic_ideal' ),
					)
				),
			),
		) );

		return $form;
	}

	//////////////////////////////////////////////////

	/**
	 * System name
	 *
	 * We have to override the `system_name` function since we don't follow the
	 * Event Espresso class name syntax.
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/libraries/payment_methods/EE_PMT_Base.lib.php#L575-L583
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/admin_pages/payments/Payments_Admin_Page.core.php#L305
	 */
	public function system_name() {
		return 'Pronamic';
	}
}
