<?php
/**
 * Mandate
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Mollie
 */

namespace Pronamic\WordPress\Mollie;

/**
 * Mandate class
 */
class Mandate extends BaseResource {
	/**
	 * Create mandate from JSON.
	 *
	 * @param object $json JSON object.
	 * @return self
	 */
	public static function from_json( $json ) {
		$object_access = new ObjectAccess( $json );

		$mandate = new Mandate(
			$object_access->get_property( 'id' )
		);

		return $mandate;
	}
}
