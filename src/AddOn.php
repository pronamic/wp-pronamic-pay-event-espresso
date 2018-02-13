<?php

namespace Pronamic\WordPress\Pay\Extensions\EventEspresso;

use EE_Addon;
use EE_Register_Addon;
use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Title: WordPress pay Event Espresso addon
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 1.1.3
 * @since   1.1.0
 */
class AddOn extends EE_Addon {
	/**
	 * Register addon
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.16.p/tests/mocks/addons/new-payment-method/espresso-new-payment-method.php#L45
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.16.p/tests/mocks/addons/new-payment-method/EE_New_Payment_Method.class.php#L26-L46
	 */
	public static function register_addon() {
		class_alias( 'Pronamic\WordPress\Pay\Extensions\EventEspresso\AddOn', 'EE_Pronamic_WP_Pay_AddOn' );

		$payment_methods_paths = array(
			dirname( __FILE__ ) . '/ee/payment-methods/Pronamic',
		);


		if ( PaymentMethods::is_active( PaymentMethods::IDEAL ) ) {
			$payment_methods_paths[] = dirname( __FILE__ ) . '/ee/payment-methods/Pronamic_IDeal';
		}
		EE_Register_Addon::register(
			'pronamic_pay',
			array(
				'version'              => '1.0.0',
				'min_core_version'     => '4.6.0',
				'main_file_path'       => dirname( __FILE__ ) . '/ee/EE_Pronamic_WP_Pay_AddOn.php',
				'class_name'           => 'EE_Pronamic_WP_Pay_AddOn',
				'payment_method_paths' => $payment_methods_paths,
			)
		);
	}
}
