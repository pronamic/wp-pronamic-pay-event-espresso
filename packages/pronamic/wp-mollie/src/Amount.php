<?php
/**
 * Amount
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Mollie
 */

namespace Pronamic\WordPress\Mollie;

use InvalidArgumentException;
use JsonSerializable;
use stdClass;
use Pronamic\WordPress\Money\Money;

/**
 * Amount class
 */
class Amount implements JsonSerializable {
	/**
	 * Currency.
	 *
	 * @var string
	 */
	private $currency;

	/**
	 * Amount value.
	 *
	 * @var string
	 */
	private $value;

	/**
	 * Construct an amount.
	 *
	 * @param string $currency Currency code (ISO 4217).
	 * @param string $value    Amount formatted with correct number of decimals for currency.
	 */
	public function __construct( $currency, $value ) {
		$this->currency = $currency;
		$this->value    = $value;
	}

	/**
	 * Get currency.
	 *
	 * @return string
	 */
	public function get_currency() {
		return $this->currency;
	}

	/**
	 * Get amount.
	 *
	 * @return string
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * Create amount from object.
	 *
	 * @param object $value Object.
	 * @return Amount
	 */
	public static function from_object( object $value ) {
		$object_access = new ObjectAccess( $value );

		return new self(
			$object_access->get_property( 'currency' ),
			$object_access->get_property( 'value' )
		);
	}

	/**
	 * Create amount from JSON string.
	 *
	 * @param object $json JSON object.
	 * @return Amount
	 * @throws InvalidArgumentException Throws invalid argument exception when input JSON is not an object.
	 */
	public static function from_json( $json ) {
		if ( ! is_object( $json ) ) {
			throw new InvalidArgumentException( 'JSON value must be an object.' );
		}

		return self::from_object( $json );
	}

	/**
	 * JSON serialize.
	 *
	 * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return object
	 */
	public function jsonSerialize(): object {
		return (object) [
			'currency' => $this->currency,
			'value'    => $this->value,
		];
	}

	/**
	 * Transform Mollie amount object to WordPress money object.
	 *
	 * @return Money
	 */
	public function to_wp() {
		$transformer = new AmountTransformer();

		return $transformer->transform_mollie_to_wp( $this );
	}
}
