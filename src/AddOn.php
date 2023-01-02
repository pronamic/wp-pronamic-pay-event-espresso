<?php
/**
 * Addon
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
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
 * Copyright: 2005-2023 Pronamic
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

		$payment_methods_paths = [
			__DIR__ . '/ee/payment-methods/Pronamic',
		];

		$payment_methods = [
			PaymentMethods::ALIPAY        => 'Pronamic_Alipay',
			PaymentMethods::BANCONTACT    => 'Pronamic_Bancontact',
			PaymentMethods::BANK_TRANSFER => 'Pronamic_BankTransfer',
			PaymentMethods::BELFIUS       => 'Pronamic_Belfius',
			PaymentMethods::BITCOIN       => 'Pronamic_Bitcoin',
			PaymentMethods::CREDIT_CARD   => 'Pronamic_CreditCard',
			PaymentMethods::DIRECT_DEBIT  => 'Pronamic_DirectDebit',
			PaymentMethods::GIROPAY       => 'Pronamic_Giropay',
			PaymentMethods::IDEAL         => 'Pronamic_IDeal',
			PaymentMethods::IDEALQR       => 'Pronamic_IDealQR',
			PaymentMethods::KBC           => 'Pronamic_KBC',
			PaymentMethods::PAYCONIQ      => 'Pronamic_Payconiq',
			PaymentMethods::PAYPAL        => 'Pronamic_PayPal',
			PaymentMethods::SOFORT        => 'Pronamic_Sofort',
		];

		foreach ( $payment_methods as $payment_method => $ee_payment_method ) {
			if ( ! PaymentMethods::is_active( $payment_method ) ) {
				continue;
			}

			$payment_methods_paths[] = __DIR__ . '/ee/payment-methods/' . $ee_payment_method;
		}

		EE_Register_Addon::register(
			'pronamic_pay',
			[
				'version'              => '1.0.0',
				'min_core_version'     => '4.6.0',
				'main_file_path'       => __DIR__ . '/ee/EE_Pronamic_WP_Pay_AddOn.php',
				'class_name'           => 'EE_Pronamic_WP_Pay_AddOn',
				'payment_method_paths' => $payment_methods_paths,
			]
		);
	}
}
