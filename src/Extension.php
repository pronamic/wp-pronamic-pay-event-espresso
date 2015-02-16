<?php

/**
 * Title: WordPress pay Event Espresso extension
 * Description:
 * Copyright: Copyright (c) 2005 - 2015
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0.2
 */
class Pronamic_WP_Pay_Extensions_EventEspresso_Extension {
	/**
	 * Bootstrap
	 */
	public static function bootstrap() {
		add_action( 'plugins_loaded', array( __CLASS__, 'plugins_loaded' ) );
	}

	//////////////////////////////////////////////////

	/**
	 * Is active
	 *
	 * @return boolean
	 */
	public static function is_active() {
		// @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/espresso.php#L53
		return defined( 'EVENT_ESPRESSO_VERSION' ) && version_compare( EVENT_ESPRESSO_VERSION, '4', '>=' );
	}

	//////////////////////////////////////////////////

	/**
	 * Plugins loaded
	 */
	public static function plugins_loaded() {
		if ( self::is_active() ) {
			self::init_gateways();

			// Actions
			add_filter( 'pronamic_payment_source_text_eventespresso',   array( __CLASS__, 'source_text' ), 10, 2 );

			add_action( 'pronamic_payment_status_update_eventespresso_unknown_to_success', array( __CLASS__, 'update_status_unknown_to_success' ), 10, 2 );
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Initialize Event Espresso gateways
	 */
	public static function init_gateways() {
		if ( class_exists( 'EE_Offsite_Gateway' ) ) {
			$gateways = array(
				'pronamic_pay_ideal' => 'Pronamic_WP_Pay_Extensions_EventEspresso_IDealGateway',
			);

			foreach ( $gateways as $gateway => $alias ) {
				// @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/core/db_models/EEM_Gateways.model.php#L217
				$class_name = 'EE_' . $gateway;

				class_alias( $alias, $class_name );

				// @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/core/db_models/EEM_Gateways.model.php#L198-L201
				if ( defined( 'EVENT_ESPRESSO_GATEWAY_DIR' ) ) {
					$gateway_dir   = EVENT_ESPRESSO_GATEWAY_DIR . $gateway;
					$gateway_class = $gateway_dir . '/' . $class_name . '.class.php';

					if ( ! is_readable( $gateway_class ) ) {
						$created = wp_mkdir_p( $gateway_dir );

						if ( $created ) {
							touch( $gateway_class );
						}
					}
				}
			}
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Update lead status of the specified payment
	 *
	 * @param Pronamic_Pay_Payment $payment
	 * @param bool                 $can_redirect
	 */
	public static function update_status_unknown_to_success( Pronamic_Pay_Payment $payment, $can_redirect = false ) {
		$gateway = EEM_Gateways::instance()->get_gateway( 'pronamic_pay_ideal' );

		if ( $gateway ) {
			$transaction_id = $payment->get_source_id();

			// @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/admin_pages/transactions/Transactions_Admin_Page.core.php#L332-L337
			$transaction_model = EEM_Transaction::instance();

			$transaction = $transaction_model->get_one_by_ID( $transaction_id );

			global $pronamic_payment, $pronamic_url;

			$pronamic_payment = $payment;

			$gateway->handle_ipn_for_transaction( $transaction );

			unset( $pronamic_payment );

			// Redirect URL
			if ( $can_redirect ) {
				wp_redirect( $pronamic_url, 303 );

				exit;
			}
	    }
	}

	//////////////////////////////////////////////////

	/**
	 * Source column
	 */
	public static function source_text( $text, Pronamic_Pay_Payment $payment ) {
		$url = add_query_arg( array(
			'page'   => 'espresso_transactions',
			'action' => 'view_transaction',
			'TXN_ID' => $payment->get_source_id(),
		), admin_url( 'admin.php' ) );

		$text  = '';

		$text .= __( 'Event Espresso', 'pronamic_ideal' ) . '<br />';

		$text .= sprintf(
			'<a href="%s">%s</a>',
			esc_attr( $url ),
			sprintf( __( 'Transaction %s', 'pronamic_ideal' ), $payment->get_source_id() )
		);

		return $text;
	}
}
