<?php

/**
 * Title: WordPress pay Event Espresso payment data
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.1.5
 * @since 1.0.0
 */
class Pronamic_WP_Pay_Extensions_EventEspresso_PaymentData extends Pronamic_WP_Pay_PaymentData {
	/**
	 * Line item
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/core/db_classes/EE_Line_Item.class.php#L26
	 * @var EE_Line_Item
	 */
	private $line_item;

	/**
	 * Transaction
	 *
	 * @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/core/db_classes/EE_Transaction.class.php#L25
	 * @var EE_Transaction
	 */
	private $transaction;

	//////////////////////////////////////////////////

	/**
	 * Constructs and initializes an WooCommerce iDEAL data proxy
	 *
	 * @param WC_Order $order
	 */
	public function __construct( $gateway, EE_Line_Item $line_item, EE_Transaction $transaction ) {
		parent::__construct();

		$this->gateway     = $gateway;
		$this->line_item   = $line_item;
		$this->transaction = $transaction;

		$this->primary_registrant = $transaction->primary_registration();
		$this->primary_attendee   = $this->primary_registrant->attendee();
	}

	//////////////////////////////////////////////////

	/**
	 * Get source indicator
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_source()
	 * @return string
	 */
	public function get_source() {
		return 'eventespresso';
	}

	public function get_source_id() {
		return $this->transaction->ID();
	}

	//////////////////////////////////////////////////

	public function get_title() {
		return sprintf( __( 'Event Espresso transaction %s', 'pronamic_ideal' ), $this->get_order_id() );
	}

	/**
	 * Get description
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
	 * @return Pronamic_IDeal_Items
	 */
	public function get_items() {
		// Items
		$items = new Pronamic_IDeal_Items();

		// Item
		// We only add one total item, because iDEAL cant work with negative price items (discount)
		$item = new Pronamic_IDeal_Item();
		$item->setNumber( $this->get_order_id() );
		$item->setDescription( $this->get_description() );
		$item->setPrice( $this->transaction->total() );
		$item->setQuantity( 1 );

		$items->addItem( $item );

		return $items;
	}

	//////////////////////////////////////////////////
	// Currency
	//////////////////////////////////////////////////

	/**
	 * Get currency
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_currency_alphabetic_code()
	 * @return string
	 */
	public function get_currency_alphabetic_code() {
		return 'EUR';
	}

	//////////////////////////////////////////////////
	// Customer
	//////////////////////////////////////////////////

	public function get_email() {
		return $this->primary_attendee->email();
	}

	public function get_customer_name() {
		return $this->primary_attendee->fname() . ' ' . $this->primary_attendee->lname();
	}

	public function get_address() {
		return null;
	}

	public function get_city() {
		return null;
	}

	public function get_zip() {
		return null;
	}

	//////////////////////////////////////////////////
	// URL's
	//////////////////////////////////////////////////

	/**
	 * Get normal return URL
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_normal_return_url()
	 * @return string
	 */
	public function get_normal_return_url() {
		return null;
	}

	public function get_cancel_url() {
		return null;
	}

	public function get_success_url() {
		return null;
	}

	public function get_error_url() {
		return null;
	}
}
