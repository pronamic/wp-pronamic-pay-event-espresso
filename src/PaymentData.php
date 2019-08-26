<?php
/**
 * Payment data
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\EventEspresso
 */

namespace Pronamic\WordPress\Pay\Extensions\EventEspresso;

use EE_Line_Item;
use EE_Transaction;
use Pronamic\WordPress\Pay\Payments\PaymentData as Pay_PaymentData;
use Pronamic\WordPress\Pay\Payments\Item;
use Pronamic\WordPress\Pay\Payments\Items;

/**
 * Title: WordPress pay Event Espresso payment data
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   1.0.0
 */
class PaymentData extends Pay_PaymentData {
	/**
	 * Line item
	 *
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/core/db_classes/EE_Line_Item.class.php#L26
	 * @var EE_Line_Item
	 */
	private $line_item;

	/**
	 * Transaction
	 *
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/core/db_classes/EE_Transaction.class.php#L25
	 * @var EE_Transaction
	 */
	private $transaction;

	/**
	 * Constructs and initializes an Event Espresso payment data object.
	 *
	 * @param Gateway        $gateway     Gateway.
	 * @param EE_Line_Item   $line_item   Event Espresso line item.
	 * @param EE_Transaction $transaction Event Espresso transaction.
	 */
	public function __construct( $gateway, EE_Line_Item $line_item, EE_Transaction $transaction ) {
		parent::__construct();

		$this->gateway     = $gateway;
		$this->line_item   = $line_item;
		$this->transaction = $transaction;

		$this->primary_registrant = $transaction->primary_registration();
		$this->primary_attendee   = $this->primary_registrant->attendee();
	}

	/**
	 * Get source indicator
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_source()
	 * @return string
	 */
	public function get_source() {
		return 'eventespresso';
	}

	/**
	 * Get source ID.
	 *
	 * @return string
	 */
	public function get_source_id() {
		return $this->transaction->ID();
	}

	/**
	 * Get title.
	 *
	 * @return string
	 */
	public function get_title() {
		/* translators: %s: order id */
		return sprintf( __( 'Event Espresso transaction %s', 'pronamic_ideal' ), $this->get_order_id() );
	}

	/**
	 * Get description.
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_description()
	 * @return string
	 */
	public function get_description() {
		$search = array(
			'{transaction_id}',
		);

		$replace = array(
			$this->get_order_id(),
		);

		$description = '';

		if ( method_exists( $this->gateway, 'get_transaction_description' ) ) {
			$description = $this->gateway->get_transaction_description();
		}

		if ( '' === $description ) {
			$description = $this->get_title();
		}

		return str_replace( $search, $replace, $description );
	}

	/**
	 * Get order ID
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_order_id()
	 * @return string
	 */
	public function get_order_id() {
		return $this->transaction->ID();
	}

	/**
	 * Get items
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_items()
	 * @return Items
	 */
	public function get_items() {
		// Items.
		$items = new Items();

		// Item.
		// We only add one total item, because iDEAL cant work with negative price items (discount).
		$item = new Item();
		$item->setNumber( $this->get_order_id() );
		$item->setDescription( $this->get_description() );
		$item->setPrice( $this->transaction->total() );
		$item->setQuantity( 1 );

		$items->addItem( $item );

		return $items;
	}

	/**
	 * Get currency
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_currency_alphabetic_code()
	 * @return string
	 */
	public function get_currency_alphabetic_code() {
		return 'EUR';
	}

	/**
	 * Get email.
	 *
	 * @return string
	 */
	public function get_email() {
		return $this->primary_attendee->email();
	}

	/**
	 * Get customer name.
	 *
	 * @return string
	 */
	public function get_customer_name() {
		return $this->primary_attendee->fname() . ' ' . $this->primary_attendee->lname();
	}

	/**
	 * Get address.
	 *
	 * @return string
	 */
	public function get_address() {
		return null;
	}

	/**
	 * Get city.
	 *
	 * @return string
	 */
	public function get_city() {
		return null;
	}

	/**
	 * Get ZIP.
	 *
	 * @return string
	 */
	public function get_zip() {
		return null;
	}

	/**
	 * Get normal return URL
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_normal_return_url()
	 * @return string
	 */
	public function get_normal_return_url() {
		return null;
	}

	/**
	 * Get cancel URL.
	 *
	 * @return string
	 */
	public function get_cancel_url() {
		return null;
	}

	/**
	 * Get success URL.
	 *
	 * @return string
	 */
	public function get_success_url() {
		return null;
	}

	/**
	 * Get error URL.
	 *
	 * @return string
	 */
	public function get_error_url() {
		return null;
	}
}
