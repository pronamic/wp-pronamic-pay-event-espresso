<?php
/**
 * Payment Details
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Mollie
 */

namespace Pronamic\WordPress\Mollie;

/**
 * Payment details class
 */
class PaymentDetails extends ObjectAccess {
	/**
	 * Create payment details from JSON.
	 *
	 * @link https://docs.mollie.com/reference/v2/payments-api/get-payment
	 * @param object|null $json   JSON object.
	 * @return PaymentDetails|null
	 */
	public static function from_json( $json ) {
		if ( null === $json ) {
			return null;
		}

		$details = new self( $json );

		return $details;
	}
}
