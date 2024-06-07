<?php
/**
 * Lines
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Mollie
 */

namespace Pronamic\WordPress\Mollie;

use JsonSerializable;
use Pronamic\WordPress\Money\TaxedMoney;
use Pronamic\WordPress\Number\Number;

/**
 * Lines class
 */
class Lines implements JsonSerializable {
	/**
	 * The lines.
	 *
	 * @var Line[]
	 */
	private array $lines = [];

	/**
	 * New line.
	 *
	 * @param string $name         Description of the order line.
	 * @param int    $quantity     Quantity.
	 * @param Amount $unit_price   Unit price.
	 * @param Amount $total_amount Total amount, including VAT and  discounts.
	 * @param Number $vat_rate     VAT rate.
	 * @param Amount $vat_amount   Value-added tax amount.
	 */
	public function new_line( string $name, int $quantity, Amount $unit_price, Amount $total_amount, Number $vat_rate, Amount $vat_amount ): Line {
		$line = new Line(
			$name,
			$quantity,
			$unit_price,
			$total_amount,
			$vat_rate,
			$vat_amount
		);

		$this->lines[] = $line;

		return $line;
	}

	/**
	 * JSON serialize.
	 *
	 * @return object[]
	 */
	public function jsonSerialize(): array {
		$objects = array_map(
			/**
			 * Get JSON for payment line.
			 *
			 * @param Line $line Payment line.
			 * @return object
			 */
			function ( Line $line ) {
				return $line->jsonSerialize();
			},
			$this->lines
		);

		return $objects;
	}
}
