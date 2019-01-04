<?php
/**
 * Payment statuses
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\EventEspresso
 */

namespace Pronamic\WordPress\Pay\Extensions\EventEspresso;

/**
 * Title: WordPress pay Event Espresso 4.6+ payment statuses
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   1.1.0
 */
class PaymentStatuses {
	/**
	 * Status approved
	 *
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/db_models/EEM_Payment.model.php#L19-L22
	 */
	const APPROVED = 'PAP';

	/**
	 * Pending
	 *
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/db_models/EEM_Payment.model.php#L25-L28
	 */
	const PENDING = 'PPN';

	/**
	 * Cancelled
	 *
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/db_models/EEM_Payment.model.php#L31-L35
	 */
	const CANCELLED = 'PCN';

	/**
	 * Declined
	 *
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/db_models/EEM_Payment.model.php#L39-L43
	 */
	const DECLINED = 'PDC';

	/**
	 * Failed
	 *
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/db_models/EEM_Payment.model.php#L47-L51
	 */
	const FAILED = 'PFL';
}
