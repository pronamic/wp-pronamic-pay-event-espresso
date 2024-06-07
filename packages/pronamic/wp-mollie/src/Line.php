<?php
/**
 * Line
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Mollie
 */

namespace Pronamic\WordPress\Mollie;

use JsonSerializable;
use Pronamic\WordPress\Number\Number;
use stdClass;

/**
 * Line class
 */
class Line implements JsonSerializable {
	/**
	 * The order line's unique identifier.
	 *
	 * @var string|null
	 */
	private $id;

	/**
	 * The type of product bought, for example, a physical or a digital product.
	 *
	 * @see LineType
	 * @var string|null
	 */
	private $type;

	/**
	 * The category of product bought.
	 *
	 * Optional, but required in at least one of the lines to accept `voucher` payments.
	 *
	 * @var string|null
	 */
	private $category;

	/**
	 * Name.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Quantity.
	 *
	 * @var int
	 */
	public $quantity;

	/**
	 * The price of a single item including VAT in the order line.
	 *
	 * @var Amount
	 */
	private $unit_price;

	/**
	 * Any discounts applied to the order line. For example, if you have a two-for-one sale,
	 * you should pass the amount discounted as a positive amount.
	 *
	 * @var Amount|null
	 */
	private $discount_amount;

	/**
	 * The total amount of the line, including VAT and discounts. Adding all `totalAmount`
	 * values together should result in the same amount as the amount top level property.
	 *
	 * The total amount should match the following formula: (unitPrice × quantity) - discountAmount
	 *
	 * @var Amount
	 */
	public $total_amount;

	/**
	 * The VAT rate applied to the order line, for example "21.00" for 21%. The `vatRate` should
	 * be passed as a string and not as a float to ensure the correct number of decimals are passed.
	 *
	 * @var Number
	 */
	public Number $vat_rate;

	/**
	 * The amount of value-added tax on the line. The `totalAmount` field includes VAT, so
	 * the `vatAmount` can be calculated with the formula `totalAmount × (vatRate / (100 + vatRate))`.
	 *
	 * @var Amount
	 */
	public Amount $vat_amount;

	/**
	 * SKU.
	 *
	 * @var string|null
	 */
	private $sku;

	/**
	 * Image url.
	 *
	 * @var string|null
	 */
	private $image_url;

	/**
	 * Product URL.
	 *
	 * @var string|null
	 */
	private $product_url;

	/**
	 * Line constructor.
	 *
	 * @param string $name         Description of the order line.
	 * @param int    $quantity     Quantity.
	 * @param Amount $unit_price   Unit price.
	 * @param Amount $total_amount Total amount, including VAT and  discounts.
	 * @param Number $vat_rate     VAT rate.
	 * @param Amount $vat_amount   Value-added tax amount.
	 */
	public function __construct( string $name, int $quantity, Amount $unit_price, Amount $total_amount, Number $vat_rate, Amount $vat_amount ) {
		$this->name         = $name;
		$this->quantity     = $quantity;
		$this->unit_price   = $unit_price;
		$this->total_amount = $total_amount;
		$this->vat_rate     = $vat_rate;
		$this->vat_amount   = $vat_amount;
	}

	/**
	 * Get identifier.
	 *
	 * @return string|null
	 */
	public function get_id(): ?string {
		return $this->id;
	}

	/**
	 * Set identifier.
	 *
	 * @param string|null $id Identifier.
	 */
	public function set_id( ?string $id ): void {
		$this->id = $id;
	}

	/**
	 * Set type.
	 *
	 * @param string|null $type Type.
	 */
	public function set_type( ?string $type ): void {
		$this->type = $type;
	}

	/**
	 * Set category.
	 *
	 * @param null|string $category Product category.
	 */
	public function set_category( ?string $category ): void {
		$this->category = $category;
	}

	/**
	 * Set discount amount, should not contain any tax.
	 *
	 * @param Amount|null $discount_amount Discount amount.
	 */
	public function set_discount_amount( ?Amount $discount_amount = null ): void {
		$this->discount_amount = $discount_amount;
	}

	/**
	 * Set the SKU of this payment line.
	 *
	 * @param string|null $sku SKU.
	 */
	public function set_sku( ?string $sku ): void {
		$this->sku = $sku;
	}

	/**
	 * Set image URL.
	 *
	 * @param string|null $image_url Image url.
	 */
	public function set_image_url( ?string $image_url ): void {
		$this->image_url = $image_url;
	}

	/**
	 * Set product URL.
	 *
	 * @param string|null $product_url Product URL.
	 */
	public function set_product_url( ?string $product_url = null ): void {
		$this->product_url = $product_url;
	}

	/**
	 * Create line from object.
	 *
	 * @param object $value Object.
	 * @return Line
	 */
	public static function from_object( object $value ) {
		$object_access = new ObjectAccess( $value );

		$line = new self(
			$object_access->get_property( 'name' ),
			$object_access->get_property( 'quantity' ),
			Amount::from_object( $object_access->get_property( 'unitPrice' ) ),
			Amount::from_object( $object_access->get_property( 'totalAmount' ) ),
			Number::from_string( $object_access->get_property( 'vatRate' ) ),
			Amount::from_object( $object_access->get_property( 'vatAmount' ) )
		);

		$line->set_id( $object_access->get_property( 'id' ) );
		$line->set_sku( $object_access->get_property( 'sku' ) );

		return $line;
	}

	/**
	 * Create amount from JSON string.
	 *
	 * @param object $json JSON object.
	 * @return Line
	 * @throws \InvalidArgumentException Throws invalid argument exception when input JSON is not an object.
	 */
	public static function from_json( $json ) {
		if ( ! is_object( $json ) ) {
			throw new \InvalidArgumentException( 'JSON value must be an object.' );
		}

		return self::from_object( $json );
	}

	/**
	 * JSON serialize.
	 *
	 * @return object
	 */
	public function jsonSerialize(): object {
		$object_builder = new ObjectBuilder();

		$object_builder->set_optional( 'id', $this->id );
		$object_builder->set_optional( 'type', $this->type );
		$object_builder->set_optional( 'category', $this->category );
		$object_builder->set_required( 'name', $this->name );
		$object_builder->set_required( 'quantity', $this->quantity );
		$object_builder->set_required( 'unitPrice', $this->unit_price->jsonSerialize() );
		$object_builder->set_optional( 'discountAmount', null === $this->discount_amount ? null : $this->discount_amount->jsonSerialize() );
		$object_builder->set_optional( 'totalAmount', $this->total_amount->jsonSerialize() );
		$object_builder->set_required( 'vatRate', $this->vat_rate->format( 2, '.', '' ) );
		$object_builder->set_required( 'vatAmount', $this->vat_amount->jsonSerialize() );
		$object_builder->set_optional( 'sku', $this->sku );
		$object_builder->set_optional( 'imageUrl', $this->image_url );
		$object_builder->set_optional( 'productUrl', $this->product_url );

		return $object_builder->jsonSerialize();
	}
}
