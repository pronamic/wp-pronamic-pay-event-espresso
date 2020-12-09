<?php
/**
 * Event Espresso Helper
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\EventEspresso
 */

namespace Pronamic\WordPress\Pay\Extensions\EventEspresso;

use Pronamic\WordPress\Pay\ContactName;
use Pronamic\WordPress\Pay\Customer;

/**
 * Event Espresso Helper
 *
 * @author  Remco Tolsma
 * @version 2.3.0
 * @since   2.3.0
 */
class EventEspressoHelper {
	/**
	 * Get title.
	 *
	 * @return string
	 */
	public function get_title( $transaction_id ) {
		/* translators: %s: order id */
		return \sprintf(
			\__( 'Event Espresso transaction %s', 'pronamic_ideal' ),
			$transaction_id
		);
	}

	/**
	 * Get description.
	 *
	 * @return string
	 */
	public function get_description( $transaction_id, $gateway ) {
		$search = array(
			'{transaction_id}',
		);

		$replace = array(
			$transaction_id,
		);

		$description = '';

		if ( method_exists( $gateway, 'get_transaction_description' ) ) {
			$description = $gateway->get_transaction_description();
		}

		if ( '' === $description ) {
			$description = self::get_title( $transaction_id );
		}

		return str_replace( $search, $replace, $description );
	}

	/**
	 * Get customer from attendee.
	 */
	public static function get_customer_from_attendee( $attendee ) {
		$name  = self::get_name_from_attendee( $attendee );
		$email = $attendee->email();

		$customer_data = array(
			$name,
			$email,
		);

		$customer_data = \array_filter( $customer_data );

		if ( empty( $customer_data ) ) {
			return null;
		}

		$customer = new Customer();

		$customer->set_name( $name );

		if ( ! empty( $email ) ) {
			$customer->set_email( $email );
		}

		return $customer;
	}

	/**
	 * Get name from primary attendee.
	 */
	public static function get_name_from_attendee( $attendee ) {
		$first_name = $attendee->fname();
		$last_name  = $attendee->lname();

		$name_data = array(
			$first_name,
			$last_name,
		);

		$name_data = \array_filter( $name_data );

		if ( empty( $name_data ) ) {
			return null;
		}

		$name = new ContactName();

		if ( ! empty( $first_name ) ) {
			$name->set_first_name( $first_name );
		}

		if ( ! empty( $last_name ) ) {
			$name->set_last_name( $last_name );
		}
		
		return $name;
	}
}
