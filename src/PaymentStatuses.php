<?php

/**
 * Title: WordPress pay Event Espresso 4.6+ payment statuses
 * Description:
 * Copyright: Copyright (c) 2005 - 2015
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.1.0
 * @since 1.1.0
 */
class Pronamic_WP_Pay_Extensions_EventEspresso_PaymentStatuses {
	/**
	 * Status approved
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/db_models/EEM_Payment.model.php#L19-L22
	 */
	APPROVED = 'PAP';

	/**
	 * Pending
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/db_models/EEM_Payment.model.php#L25-L28
	 */
	PENDING = 'PPN';

	/**
	 * Cancelled
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/db_models/EEM_Payment.model.php#L31-L35
	 */
	CANCELLED = 'PCN';

	/**
	 * Declined
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/db_models/EEM_Payment.model.php#L39-L43
	 */
	DECLINED = 'PDC';

	/**
	 * Failed
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/db_models/EEM_Payment.model.php#L47-L51
	 */
	FAILED = 'PFL';
}
