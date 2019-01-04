<?php
/**
 * Payment method
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\EventEspresso
 */

namespace Pronamic\WordPress\Pay\Extensions\EventEspresso;

use EE_Billing_Info_Form;
use EE_Form_Section_HTML;
use EE_Payment_Method;
use EE_Payment_Method_Form;
use EE_PMT_Base;
use EE_Registry;
use EE_Select_Input;
use EE_Text_Input;
use EE_Transaction;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Title: WordPress pay Event Espresso 4.6+ payment method
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.1.1
 * @since   1.1.0
 */
class PaymentMethod extends EE_PMT_Base {
	/**
	 * Payment method.
	 *
	 * @since 2.0.0
	 *
	 * @var string $payment_method
	 */
	protected $payment_method = null;

	/**
	 * Constructs and initializes an Event Espresso payment method
	 *
	 * @param EE_Payment_Method $pm_instance Event Espresso payment method instance.
	 */
	public function __construct( $pm_instance = null ) {
		if ( null === $this->payment_method ) {
			$this->_gateway            = new Gateway();
			$this->_pretty_name        = __( 'Pronamic', 'pronamic_ideal' );
			$this->_default_button_url = plugins_url( 'images/credit-card/icon-64x48.png', Plugin::$file );
		}

		parent::__construct( $pm_instance );
	}

	/**
	 * Creates the billing form for this payment method type.
	 *
	 * @param \EE_Transaction $transaction Event Espresso transaction.
	 * @return EE_Billing_Info_Form|null
	 */
	public function generate_new_billing_form( EE_Transaction $transaction = null ) {
		$config_id = $this->_gateway->get_config_id();

		$gateway = Plugin::get_gateway( $config_id );

		if ( $gateway ) {
			$gateway->set_payment_method( $this->payment_method );

			if ( null === $this->payment_method && $gateway->payment_method_is_required() ) {
				$gateway->set_payment_method( PaymentMethods::IDEAL );
			}

			$form = new EE_Billing_Info_Form(
				$this->_pm_instance,
				array(
					'name'        => 'Pronamic_WP_Pay_Billing_Form',
					'subsections' => array(
						'html' => new EE_Form_Section_HTML( $gateway->get_input_html() ),
					),
				)
			);

			return $form;
		}

		return null;
	}

	/**
	 * Gets the form for all the settings related to this payment method type
	 *
	 * @return EE_Payment_Method_Form
	 */
	public function generate_new_settings_form() {
		EE_Registry::instance()->load_helper( 'Template' );

		$config_options = Plugin::get_config_select_options( $this->payment_method );

		// Fix for incorrect normalization strategy
		// @link https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/libraries/form_sections/inputs/EE_Form_Input_With_Options_Base.input.php#L89-L113.
		$select_option = $config_options[0];

		unset( $config_options[0] );

		$config_options = array( 'select' => $select_option ) + $config_options;

		$form = new EE_Payment_Method_Form(
			array(
				'extra_meta_inputs' => array(
					'config_id'               => new EE_Select_Input(
						$config_options,
						array(
							'html_label_text' => __( 'Configuration', 'pronamic_ideal' ),
							'default'         => get_option( 'pronamic_pay_config_id' ),
						)
					),
					'transaction_description' => new EE_Text_Input(
						array(
							'html_label_text' => __( 'Transaction description', 'pronamic_ideal' ),
							/* translators: %s: <code>{transaction_id}</code> */
							'html_help_text'  => sprintf( __( 'Available tags: %s', 'pronamic_ideal' ), sprintf( '<code>%s</code>', '{transaction_id}' ) ),
							'default'         => __( 'Event Espresso transaction {transaction_id}', 'pronamic_ideal' ),
						)
					),
				),
			)
		);

		return $form;
	}

	/**
	 * System name
	 *
	 * We have to override the `system_name` function since we don't follow the
	 * Event Espresso class name syntax.
	 *
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/core/libraries/payment_methods/EE_PMT_Base.lib.php#L575-L583
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.6.17.p/admin_pages/payments/Payments_Admin_Page.core.php#L305
	 *
	 * @return string
	 */
	public function system_name() {
		return 'Pronamic';
	}
}
