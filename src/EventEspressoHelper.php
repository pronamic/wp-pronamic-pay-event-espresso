<?php
/**
 * Event Espresso Helper
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\EventEspresso
 */

namespace Pronamic\WordPress\Pay\Extensions\EventEspresso;

use EE_Attendee;
use Pronamic\WordPress\Pay\ContactName;
use Pronamic\WordPress\Pay\ContactNameHelper;
use Pronamic\WordPress\Pay\Customer;
use Pronamic\WordPress\Pay\CustomerHelper;

/**
 * Event Espresso Helper
 *
 * @author  Remco Tolsma
 * @version 2.3.2
 * @since   2.3.0
 */
class EventEspressoHelper {
	/**
	 * Get title.
	 *
	 * @param int|null $transaction_id Transaction ID.
	 * @return string
	 */
	public static function get_title( $transaction_id ) {
		/* translators: %s: order id */
		return \sprintf(
			/* translators: %s: transaction ID */
			\__( 'Event Espresso transaction %s', 'pronamic_ideal' ),
			$transaction_id
		);
	}

	/**
	 * Get description.
	 *
	 * @param int|null   $transaction_id Transaction ID.
	 * @param EE_Gateway $gateway        Gateway.
	 * @return string
	 */
	public static function get_description( $transaction_id, $gateway ) {
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
	 *
	 * @param EE_Attendee $attendee Attendee.
	 * @return Customer|null
	 */
	public static function get_customer_from_attendee( $attendee ) {
		return CustomerHelper::from_array(
			array(
				'name'  => self::get_name_from_attendee( $attendee ),
				'email' => $attendee->email(),
			)
		);
	}

	/**
	 * Get name from primary attendee.
	 *
	 * @param EE_Attendee $attendee Attendee.
	 * @return ContactName|null
	 */
	public static function get_name_from_attendee( $attendee ) {
		return ContactNameHelper::from_array(
			array(
				'first_name' => $attendee->fname(),
				'last_name'  => $attendee->lname(),
			)
		);
	}
}
