<?php
/**
 * Amount transformer.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Mollie
 */

namespace Pronamic\WordPress\Mollie;

use Pronamic\WordPress\Money\Money;

/**
 * Amount transformer class
 */
class AmountTransformer {
	/**
	 * Transform Pronamic money to Mollie amount.
	 *
	 * @param Money $money Pronamic money to convert.
	 * @return Amount
	 */
	public function transform_wp_to_mollie( Money $money ) {
		$amount = new Amount(
			$money->get_currency()->get_alphabetic_code(),
			/**
			 * Make sure to send the right amount of decimals and omit the
			 * thousands separator. Non-string values are not accepted.
			 *
			 * @link https://docs.mollie.com/reference/v2/payments-api/create-payment
			 */
			$money->number_format( null, '.', '' )
		);

		return $amount;
	}

	/**
	 * Transform Mollie amount to Pronamic money.
	 *
	 * @param Amount $amount Mollie amount to convert.
	 * @return Money
	 */
	public function transform_mollie_to_wp( Amount $amount ) {
		return new Money( $amount->get_value(), $amount->get_currency() );
	}
}
