<?php

/**
 * Title: WordPress pay Event Espresso addon
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.1.3
 * @since 1.1.0
 */
class Pronamic_WP_Pay_Extensions_EventEspresso_AddOn extends EE_Addon {
	/**
	 * Register addon
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.16.p/tests/mocks/addons/new-payment-method/espresso-new-payment-method.php#L45
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.16.p/tests/mocks/addons/new-payment-method/EE_New_Payment_Method.class.php#L26-L46
	 */
	public static function register_addon() {
		EE_Register_Addon::register(
			'pronamic_pay',
			array(
				'version'              => '1.0.0',
				'min_core_version'     => '4.6.0',
				'main_file_path'       => dirname( __FILE__ ) . '/ee/EE_Pronamic_WP_Pay_AddOn.php',
				'class_name'           => 'EE_Pronamic_WP_Pay_AddOn',
				'payment_method_paths' => array(
					dirname( __FILE__ ) . '/ee/payment-methods/Pronamic',
					dirname( __FILE__ ) . '/ee/payment-methods/Pronamic_IDeal',
				),
			)
		);
	}

	//////////////////////////////////////////////////

	/**
	 * Constructs and initialize Pronamic Pay addon
	 */
	public function __construct() {

	}
}
