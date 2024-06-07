<?php
/**
 * Line type
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Mollie
 */

namespace Pronamic\WordPress\Mollie;

/**
 * Line type class
 *
 * @link https://docs.mollie.com/reference/v2/orders-api/create-order
 */
class LineType {
	/**
	 * Constant for 'digital' type.
	 *
	 * @var string
	 */
	const DIGITAL = 'digital';

	/**
	 * Constant for 'discount' type.
	 *
	 * @var string
	 */
	const DISCOUNT = 'discount';

	/**
	 * Constant for 'gift_card' type.
	 *
	 * @var string
	 */
	const GIFT_CARD = 'gift_card';

	/**
	 * Constant for 'physical' type.
	 *
	 * @var string
	 */
	const PHYSICAL = 'physical';

	/**
	 * Constant for 'shipping_fee' type.
	 *
	 * @var string
	 */
	const SHIPPING_FEE = 'shipping_fee';

	/**
	 * Constant for 'store_credit' type.
	 *
	 * @var string
	 */
	const STORE_CREDIT = 'store_credit';

	/**
	 * Constant for 'surcharge' type.
	 *
	 * @var string
	 */
	const SURCHARGE = 'surcharge';
}
