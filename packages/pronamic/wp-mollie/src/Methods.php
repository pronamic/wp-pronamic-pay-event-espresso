<?php
/**
 * Mollie methods.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Mollie
 */

namespace Pronamic\WordPress\Mollie;

/**
 * Methods class
 */
class Methods {
	/**
	 * Constant for the Apple Pay method.
	 *
	 * @var string
	 */
	const APPLE_PAY = 'applepay';

	/**
	 * Constant for the Bancontact method.
	 *
	 * @var string
	 */
	const BANCONTACT = 'bancontact';

	/**
	 * Constant for the Billie method.
	 *
	 * @var string
	 */
	const BILLIE = 'billie';

	/**
	 * Constant for the BLIK method, Poland's most popular payment method.
	 *
	 * @link https://github.com/mollie/mollie-api-php/blob/ed5b2ba1dc8f30a4674f10ca78ad547c2df91008/src/Types/PaymentMethod.php#L47-L50
	 * @link https://www.mollie.com/en/payments/blik
	 * @var string
	 */
	const BLIK = 'blik';

	/**
	 * Constant for the iDEAL method.
	 *
	 * @var string
	 */
	const IDEAL = 'ideal';

	/**
	 * Constant for the Credit Card method.
	 *
	 * @var string
	 */
	const CREDITCARD = 'creditcard';

	/**
	 * Constant for the Direct Debit method.
	 *
	 * @var string
	 */
	const DIRECT_DEBIT = 'directdebit';

	/**
	 * Constant for the Sofort method.
	 *
	 * @var string
	 */
	const SOFORT = 'sofort';

	/**
	 * Constant for the Bank transfer method.
	 *
	 * @var string
	 */
	const BANKTRANSFER = 'banktransfer';

	/**
	 * Constant for the EPS method.
	 *
	 * @var string
	 */
	const EPS = 'eps';

	/**
	 * Constant for the Giropay method.
	 *
	 * @var string
	 */
	const GIROPAY = 'giropay';

	/**
	 * Constant for the Klarna - Pay Later method.
	 *
	 * @var string
	 */
	const KLARNA_PAY_LATER = 'klarnapaylater';

	/**
	 * Constant for the Klarna - Pay Now method.
	 *
	 * @var string
	 */
	const KLARNA_PAY_NOW = 'klarnapaynow';

	/**
	 * Constant for the Klarna - Slice It method.
	 *
	 * @var string
	 */
	const KLARNA_SLICE_IT = 'klarnasliceit';

	/**
	 * MyBank.
	 * 
	 * @link https://github.com/mollie/mollie-api-php/blob/ed5b2ba1dc8f30a4674f10ca78ad547c2df91008/src/Types/PaymentMethod.php#L114-L117
	 * @link https://github.com/mollie/WooCommerce/blob/bda9155ac19e1c576f19f436d74fe3f7fe845298/src/PaymentMethods/Mybank.php#L7
	 * @link https://mybank.eu/
	 * @var string
	 */
	const MYBANK = 'mybank';

	/**
	 * Constant for the PayPal method.
	 *
	 * @var string
	 */
	const PAYPAL = 'paypal';

	/**
	 * Constant for the Paysafecard method.
	 *
	 * @var string
	 */
	const PAYSAFECARD = 'paysafecard';

	/**
	 * Constant for the Gift cards method.
	 *
	 * @link https://www.mollie.com/en/giftcards
	 * @since 1.1.10
	 * @var string
	 */
	const PODIUMCADEAUKAART = 'podiumcadeaukaart';

	/**
	 * Constant for the Przelewy24 method.
	 *
	 * @var string
	 */
	const PRZELEWY24 = 'przelewy24';

	/**
	 * Constant for the KBC/CBC Payment Button method.
	 *
	 * @link https://www.mollie.com/en/kbccbc
	 * @since 1.1.10
	 * @var string
	 */
	const KBC = 'kbc';

	/**
	 * Constant for the Belfius Direct Net method.
	 *
	 * @link https://www.mollie.com/en/belfiusdirectnet
	 * @since 1.1.10
	 * @var string
	 */
	const BELFIUS = 'belfius';

	/**
	 * Constant for the in3 method.
	 *
	 * @link https://www.mollie.com/payments/in3
	 * @var string
	 */
	const IN3 = 'in3';

	/**
	 * Constant for the TWINT method.
	 *
	 * @link https://www.mollie.com/payments/twint
	 * @var string
	 */
	const TWINT = 'twint';
}
