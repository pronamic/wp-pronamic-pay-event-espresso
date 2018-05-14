<?php

namespace Pronamic\WordPress\Pay\Extensions\EventEspresso;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Title: WordPress pay Event Espresso 4.6+ iDEAL gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.1.3
 */
class IDealGateway46 extends Gateway {
	/**
	 * Payment method.
	 *
	 * @since 2.0.0
	 *
	 * @var string $payment_method
	 */
	protected $payment_method = PaymentMethods::IDEAL;
}
