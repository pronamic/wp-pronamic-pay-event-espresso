<?php
/**
 * Order refund Lines request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Mollie
 */

namespace Pronamic\WordPress\Mollie;

use JsonSerializable;

/**
 * Order refund Lines request class
 */
class OrderRefundLinesRequest implements JsonSerializable {
	/**
	 * The lines.
	 *
	 * @var OrderRefundLineRequest[]
	 */
	private array $lines = [];

	/**
	 * New line.
	 *
	 * @param string $id Order line identifier.
	 * @return OrderRefundLineRequest
	 */
	public function new_line( string $id ): OrderRefundLineRequest {
		$line = new OrderRefundLineRequest( $id );

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
			 * Get JSON for order refund line.
			 *
			 * @param RefundLine $line Order refund line.
			 * @return object
			 */
			function ( OrderRefundLineRequest $line ) {
				return $line->jsonSerialize();
			},
			$this->lines
		);

		return $objects;
	}
}
