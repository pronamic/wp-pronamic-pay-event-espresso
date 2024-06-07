<?php
/**
 * Mollie order refund request.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Mollie
 */

namespace Pronamic\WordPress\Mollie;

use JsonSerializable;

/**
 * Refund order request class
 */
class OrderRefundRequest implements JsonSerializable {
	/**
	 * An array of objects containing the order line details you want to create a refund for. If you
	 * send an empty array, the entire order will be refunded.
	 *
	 * @var OrderRefundLinesRequest
	 */
	private OrderRefundLinesRequest $lines;

	/**
	 * The description of the refund you are creating. This will be shown to the consumer
	 * on their card or bank statement when possible. Max. 140 characters.
	 *
	 * @link https://docs.mollie.com/reference/v2/refunds-api/create-order-refund
	 * @var string|null
	 */
	public $description;

	/**
	 * Provide any data you like in JSON notation, and we will save the data alongside the payment.
	 * Whenever you fetch the refund with our API, we'll also include the metadata. You can use up
	 * to 1kB of JSON.
	 *
	 * @link https://docs.mollie.com/reference/v2/refunds-api/create-order-refund
	 * @link https://en.wikipedia.org/wiki/Metadata
	 * @var mixed|null
	 */
	private $metadata;

	/**
	 * Construct Mollie order refund request object.
	 *
	 * @param OrderRefundLinesRequest $lines Lines.
	 */
	public function __construct( OrderRefundLinesRequest $lines ) {
		$this->lines = $lines;
	}

	/**
	 * Get description.
	 *
	 * @return string|null
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Set description.
	 *
	 * @param string|null $description Description.
	 * @return void
	 */
	public function set_description( $description ) {
		$this->description = $description;
	}

	/**
	 * Get metadata.
	 *
	 * @link https://docs.mollie.com/reference/v2/payments-api/create-payment
	 * @link https://en.wikipedia.org/wiki/Metadata
	 * @return mixed
	 */
	public function get_metadata() {
		return $this->metadata;
	}

	/**
	 * Set metadata.
	 *
	 * @link https://docs.mollie.com/reference/v2/payments-api/create-payment
	 * @link https://en.wikipedia.org/wiki/Metadata
	 * @param mixed $metadata Metadata.
	 * @return void
	 */
	public function set_metadata( $metadata = null ) {
		$this->metadata = $metadata;
	}

	/**
	 * JSON serialize.
	 *
	 * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return object
	 */
	public function jsonSerialize(): object {
		$object_builder = new ObjectBuilder();

		$object_builder->set_required( 'lines', $this->lines->jsonSerialize() );
		$object_builder->set_optional( 'description', $this->description );
		$object_builder->set_optional( 'metadata', $this->metadata );

		return $object_builder->jsonSerialize();
	}
}
