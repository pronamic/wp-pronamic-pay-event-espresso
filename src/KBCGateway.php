<?php

namespace Pronamic\WordPress\Pay\Extensions\EventEspresso;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Title: WordPress pay Event Espresso 4.6+ KBC gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Reüel van der Steege
 * @version unreleased
 * @since   unreleased
 */
class KBCGateway extends Gateway {
	/**
	 * Payment method.
	 *
	 * @since unreleased
	 *
	 * @var string $payment_method
	 */
	protected $payment_method = PaymentMethods::KBC;
}
