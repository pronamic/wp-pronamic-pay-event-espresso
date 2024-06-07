<?php
/**
 * Order
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Mollie
 */

namespace Pronamic\WordPress\Mollie;

/**
 * Order class
 */
class Order extends BaseResource {
	/**
	 * Payments.
	 *
	 * @var Payment[]|null
	 */
	private ?array $payments;

	/**
	 * Lines.
	 *
	 * @var Line[]
	 */
	private array $lines;

	/**
	 * Status.
	 *
	 * @var string
	 */
	private string $status;

	/**
	 * Construct order.
	 *
	 * @param string $id Order ID.
	 */
	public function __construct( $id ) {
		parent::__construct( $id );

		$this->lines = [];
	}

	/**
	 * Get lines.
	 *
	 * @return Line[]
	 */
	public function get_lines(): array {
		return $this->lines;
	}

	/**
	 * Set lines.
	 *
	 * @param Line[] $lines Lines.
	 * @return void
	 */
	public function set_lines( $lines ): void {
		$this->lines = $lines;
	}

	/**
	 * Get embedded payments.
	 *
	 * @return Payment[]|null
	 */
	public function get_payments(): ?array {
		return $this->payments;
	}

	/**
	 * Set embedded payments.
	 *
	 * @param Payment[]|null $payments Payments.
	 */
	public function set_payments( ?array $payments ): void {
		$this->payments = $payments;
	}

	/**
	 * Get status.
	 *
	 * @return string
	 */
	public function get_status(): string {
		return $this->status;
	}

	/**
	 * Create order from JSON.
	 *
	 * @link https://docs.mollie.com/reference/v2/orders-api/get-order
	 * @param object $json JSON object.
	 * @return Order
	 */
	public static function from_json( $json ) {
		$object_access = new ObjectAccess( $json );

		$order = new Order( $object_access->get_property( 'id' ) );

		$order->status = $object_access->get_property( 'status' );

		$lines = array_map(
			/**
			 * Get JSON for lines.
			 *
			 * @param object $line Line.
			 * @return Line
			 */
			function ( object $line ) {
				return Line::from_json( $line );
			},
			$object_access->get_property( 'lines' )
		);

		$order->set_lines( $lines );

		if ( property_exists( $json, '_embedded' ) ) {
			if ( property_exists( $json->_embedded, 'payments' ) ) {
				$payments = array_map(
					/**
					 * Get JSON for payments.
					 *
					 * @param object $payment Payment.
					 * @return Payment
					 */
					function ( object $payment ) {
						return Payment::from_json( $payment );
					},
					$json->_embedded->payments
				);

				$order->set_payments( $payments );
			}
		}

		return $order;
	}
}
