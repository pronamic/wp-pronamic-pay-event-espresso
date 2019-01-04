<?php
/**
 * Addon
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\EventEspresso
 */

namespace Pronamic\WordPress\Pay\Extensions\EventEspresso;

use EE_Addon;
use EE_Register_Addon;
use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Title: WordPress pay Event Espresso addon
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   1.1.0
 */
class AddOn extends EE_Addon {
	/**
	 * Register addon.
	 *
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.16.p/tests/mocks/addons/new-payment-method/espresso-new-payment-method.php#L45
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.16.p/tests/mocks/addons/new-payment-method/EE_New_Payment_Method.class.php#L26-L46
	 */
	public static function register_addon() {
		class_alias( 'Pronamic\WordPress\Pay\Extensions\EventEspresso\AddOn', 'EE_Pronamic_WP_Pay_AddOn' );

		$payment_methods_paths = array(
			dirname( __FILE__ ) . '/ee/payment-methods/Pronamic',
		);

		if ( PaymentMethods::is_active( PaymentMethods::ALIPAY ) ) {
			$payment_methods_paths[] = dirname( __FILE__ ) . '/ee/payment-methods/Pronamic_Alipay';
		}

		if ( PaymentMethods::is_active( PaymentMethods::BANCONTACT ) ) {
			$payment_methods_paths[] = dirname( __FILE__ ) . '/ee/payment-methods/Pronamic_Bancontact';
		}

		if ( PaymentMethods::is_active( PaymentMethods::BANK_TRANSFER ) ) {
			$payment_methods_paths[] = dirname( __FILE__ ) . '/ee/payment-methods/Pronamic_BankTransfer';
		}

		if ( PaymentMethods::is_active( PaymentMethods::BELFIUS ) ) {
			$payment_methods_paths[] = dirname( __FILE__ ) . '/ee/payment-methods/Pronamic_Belfius';
		}

		if ( PaymentMethods::is_active( PaymentMethods::BITCOIN ) ) {
			$payment_methods_paths[] = dirname( __FILE__ ) . '/ee/payment-methods/Pronamic_Bitcoin';
		}

		if ( PaymentMethods::is_active( PaymentMethods::CREDIT_CARD ) ) {
			$payment_methods_paths[] = dirname( __FILE__ ) . '/ee/payment-methods/Pronamic_CreditCard';
		}

		if ( PaymentMethods::is_active( PaymentMethods::DIRECT_DEBIT ) ) {
			$payment_methods_paths[] = dirname( __FILE__ ) . '/ee/payment-methods/Pronamic_DirectDebit';
		}

		if ( PaymentMethods::is_active( PaymentMethods::GIROPAY ) ) {
			$payment_methods_paths[] = dirname( __FILE__ ) . '/ee/payment-methods/Pronamic_Giropay';
		}

		if ( PaymentMethods::is_active( PaymentMethods::IDEAL ) ) {
			$payment_methods_paths[] = dirname( __FILE__ ) . '/ee/payment-methods/Pronamic_IDeal';
		}

		if ( PaymentMethods::is_active( PaymentMethods::IDEALQR ) ) {
			$payment_methods_paths[] = dirname( __FILE__ ) . '/ee/payment-methods/Pronamic_IDealQR';
		}

		if ( PaymentMethods::is_active( PaymentMethods::KBC ) ) {
			$payment_methods_paths[] = dirname( __FILE__ ) . '/ee/payment-methods/Pronamic_KBC';
		}

		if ( PaymentMethods::is_active( PaymentMethods::PAYCONIQ ) ) {
			$payment_methods_paths[] = dirname( __FILE__ ) . '/ee/payment-methods/Pronamic_Payconiq';
		}

		if ( PaymentMethods::is_active( PaymentMethods::PAYPAL ) ) {
			$payment_methods_paths[] = dirname( __FILE__ ) . '/ee/payment-methods/Pronamic_PayPal';
		}

		if ( PaymentMethods::is_active( PaymentMethods::SOFORT ) ) {
			$payment_methods_paths[] = dirname( __FILE__ ) . '/ee/payment-methods/Pronamic_Sofort';
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
