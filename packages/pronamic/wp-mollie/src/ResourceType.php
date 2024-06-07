<?php
/**
 * Mollie resource.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Mollie
 */

namespace Pronamic\WordPress\Mollie;

/**
 * Resource type class
 */
class ResourceType {
	/**
	 * Constant for payments.
	 *
	 * @var string
	 */
	const PAYMENTS = 'payments';

	/**
	 * Constant for orders.
	 *
	 * @var string
	 */
	const ORDERS = 'orders';
}
