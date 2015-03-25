<?php

/**
 * Title: WordPress pay Event Espresso 4.6+ payment method
 * Description:
 * Copyright: Copyright (c) 2005 - 2015
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.1.0
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
	 * Creates the billing form for this payment method type
	 * @param \EE_Transaction $transaction
	 * @return NULL
	 */
	public function generate_new_billing_form( EE_Transaction $transaction = null ) {
		$config_id = $this->_gateway->get_config_id();

		$gateway = Pronamic_WP_Pay_Plugin::get_gateway( $config_id );

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

	//////////////////////////////////////////////////

	/**
	 * Gets the form for all the settings related to this payment method type
	 *
	 * @return EE_Payment_Method_Form
	 */
	public function generate_new_settings_form() {
		EE_Registry::instance()->load_helper( 'Template' );

		$form = new EE_Payment_Method_Form( array(
			'extra_meta_inputs' => array(
				'config_id' => new EE_Select_Input(
					Pronamic_WP_Pay_Plugin::get_config_select_options(),
					array(
						'html_label_text' => __( 'Configuration', 'pronamic_ideal' ),
					)
				)
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