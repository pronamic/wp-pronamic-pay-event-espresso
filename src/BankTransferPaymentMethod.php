<?php
/**
 * Bank transfer payment method
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\EventEspresso
 */

namespace Pronamic\WordPress\Pay\Extensions\EventEspresso;

use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Title: WordPress pay Event Espresso 4.6+ bank transfer payment method
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Reüel van der Steege
 * @version 2.1.0
 * @since   2.0.0
 */
class BankTransferPaymentMethod extends PaymentMethod {
	/**
	 * Payment method.
	 *
	 * @since 2.0.0
	 *
	 * @var string $payment_method
	 */
	protected $payment_method = PaymentMethods::BANK_TRANSFER;

	/**
	 * Constructs and initializes an Event Espresso payment method
	 *
	 * @param EE_Payment_Method $pm_instance Event Espresso payment method instance.
	 */
	public function __construct( $pm_instance = null ) {
		$this->_gateway            = new BankTransferGateway();
		$this->_pretty_name        = PaymentMethods::get_name( PaymentMethods::BANK_TRANSFER );
		$this->_default_button_url = plugins_url( 'images/bank-transfer/icon-64x48.png', Plugin::$file );

		parent::__construct( $pm_instance );
	}

	/**
	 * System name
	 *
	 * We have to override the `system_name` function since we don't follow the
	 * Event Espresso class name syntax.
	 *
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/libraries/payment_methods/EE_PMT_Base.lib.php#L575-L583
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/admin_pages/payments/Payments_Admin_Page.core.php#L305
	 */
	public function system_name() {
		return 'Pronamic_BankTransfer';
	}
}
