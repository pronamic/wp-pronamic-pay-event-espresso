<?php
/**
 * Shipment
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Mollie
 */

namespace Pronamic\WordPress\Mollie;

/**
 * Shipment class
 */
class Shipment extends BaseResource {
	/**
	 * Create shipment from JSON.
	 *
	 * @link https://docs.mollie.com/reference/v2/orders-api/get-shipment
	 * @param object $json JSON object.
	 * @return Shipment
	 */
	public static function from_json( $json ) {
		$object_access = new ObjectAccess( $json );

		$shipment = new Shipment( $object_access->get_property( 'id' ) );

		return $shipment;
	}
}
