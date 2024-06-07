<?php
/**
 * Order refund line request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Mollie
 */

namespace Pronamic\WordPress\Mollie;

use JsonSerializable;
use stdClass;

/**
 * Order refund line request class
 */
class OrderRefundLineRequest implements JsonSerializable {
	/**
	 * The order line's unique identifier.
	 *
	 * @var string
	 */
	private string $id;

	/**
	 * Quantity.
	 *
	 * @var int|null
	 */
	private ?int $quantity;

	/**
	 * The amount that you want to refund. In almost all cases, Mollie can determine the amount automatically. The
	 * amount is required only if you are partially refunding an order line which has a non-zero discount amount.
	 *
	 * @var Amount|null
	 */
	private ?Amount $amount;

	/**
	 * Order refund line request constructor.
	 *
	 * @param string $id Order line identifier.
	 */
	public function __construct( string $id ) {
		$this->id = $id;
	}

	/**
	 * Get identifier.
	 *
	 * @return string
	 */
	public function get_id(): string {
		return $this->id;
	}

	/**
	 * Get quantity.
	 *
	 * @return int|null
	 */
	public function get_quantity(): ?int {
		return $this->quantity;
	}

	/**
	 * Set quantity.
	 *
	 * @param int|null $quantity Quantity to refund.
	 */
	public function set_quantity( ?int $quantity ): void {
		$this->quantity = $quantity;
	}

	/**
	 * Get amount.
	 *
	 * @return Amount|null
	 */
	public function get_amount(): ?Amount {
		return $this->amount;
	}

	/**
	 * Set amount.
	 *
	 * @param Amount|null $amount Amount to refund.
	 */
	public function set_amount( ?Amount $amount ): void {
		$this->amount = $amount;
	}

	/**
	 * JSON serialize.
	 *
	 * @return object
	 */
	public function jsonSerialize(): object {
		$object_builder = new ObjectBuilder();

		$object_builder->set_required( 'id', $this->id );
		$object_builder->set_optional( 'quantity', $this->quantity );
		$object_builder->set_optional( 'amount', null === $this->amount ? null : $this->amount->jsonSerialize() );

		return $object_builder->jsonSerialize();
	}
}
