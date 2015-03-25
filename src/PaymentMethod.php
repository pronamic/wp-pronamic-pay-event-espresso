<?php

/**
 * Title: WordPress pay Event Espresso payment method
 * Description:
 * Copyright: Copyright (c) 2005 - 2015
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.1.0
 * @since 1.1.0
 */
class Pronamic_WP_Pay_Extensions_EventEspresso_PaymentMethod extends EE_PMT_Base {
	const help_tab_link = 'ee_mock_onsite_help';

	//////////////////////////////////////////////////

	/**
	 * Constructs and initializes an Event Espresso payment method
	 *
	 * @param EE_Payment_Method $pm_instance
	 */
	public function __construct( $pm_instance = null ) {
		$this->_gateway     = new Pronamic_WP_Pay_Extensions_EventEspresso_Gateway();
		$this->_pretty_name = __( 'Pronamic', 'pronamic_ideal' );

		parent::__construct( $pm_instance );
	}

	//////////////////////////////////////////////////

	/**
	 * Creates the billing form for this payment method type
	 * @param \EE_Transaction $transaction
	 * @return NULL
	 */
	public function generate_new_billing_form( EE_Transaction $transaction = NULL ) {
		return NULL;
	}

	/**
	 * Gets the form for all the settings related to this payment method type
	 * @return EE_Payment_Method_Form
	 */
	public function generate_new_settings_form() {
		EE_Registry::instance()->load_helper('Template');
		$form = new EE_Payment_Method_Form(array(
			'extra_meta_inputs'=>array(
				'login_id'=>new EE_Text_Input(array(
					'html_label_text'=>  sprintf(__("Login ID %s", "event_espresso"),  EEH_Template::get_help_tab_link(self::help_tab_link))
				)))));
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
