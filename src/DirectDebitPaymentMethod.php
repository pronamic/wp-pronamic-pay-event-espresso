<?php

namespace Pronamic\WordPress\Pay\Extensions\EventEspresso;

use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Title: WordPress pay Event Espresso 4.6+ direct debit payment method
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Reüel van der Steege
 * @version unreleased
 * @since   unreleased
 */
class DirectDebitPaymentMethod extends PaymentMethod {
	/**
	 * Payment method.
	 *
	 * @since unreleased
	 *
	 * @var string $payment_method
	 */
	protected $payment_method = PaymentMethods::DIRECT_DEBIT;

	/////////////////////////////////////////////////

	/**
	 * Constructs and initializes an Event Espresso payment method
	 *
	 * @param EE_Payment_Method $pm_instance
	 */
	public function __construct( $pm_instance = null ) {
		$this->_gateway            = new DirectDebitGateway();
		$this->_pretty_name        = PaymentMethods::get_name( PaymentMethods::DIRECT_DEBIT );
		$this->_default_button_url = plugins_url( 'images/direct-debit/ee-4-icon.png', Plugin::$file );

		parent::__construct( $pm_instance );
	}

	//////////////////////////////////////////////////

	/**
	 * System name
	 *
	 * We have to override the `system_name` function since we don't follow the
	 * Event Espresso class name syntax.
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/libraries/payment_methods/EE_PMT_Base.lib.php#L575-L583
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/admin_pages/payments/Payments_Admin_Page.core.php#L305
	 */
	public function system_name() {
		return 'Pronamic_DirectDebit';
	}
}
