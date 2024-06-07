<?php
/**
 * Mollie statuses.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Mollie
 */

namespace Pronamic\WordPress\Mollie;

/**
 * Statuses class
 *
 * @link https://docs.mollie.com/payments/status-changes
 */
class Statuses {
	/**
	 * Authorized.
	 *
	 * @var string
	 */
	const AUTHORIZED = 'authorized';

	/**
	 * Open.
	 *
	 * @var string
	 */
	const OPEN = 'open';

	/**
	 * Canceled.
	 *
	 * @var string
	 */
	const CANCELED = 'canceled';

	/**
	 * Paid.
	 *
	 * @var string
	 */
	const PAID = 'paid';

	/**
	 * Expired.
	 *
	 * @var string
	 */
	const EXPIRED = 'expired';

	/**
	 * Failed.
	 *
	 * @since 2.0.3
	 * @var string
	 */
	const FAILED = 'failed';

	/**
	 * Pending.
	 *
	 * @var string
	 */
	const PENDING = 'pending';
}
