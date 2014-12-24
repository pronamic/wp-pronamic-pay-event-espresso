<?php

/**
 * Title: WordPress pay Event Espresso extension
 * Description:
 * Copyright: Copyright (c) 2005 - 2014
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0.0
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
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Initialize Event Espresso gateways
	 */
	public static function init_gateways() {
		$gateways = array(
			'pronamic_pay_ideal' => 'Pronamic_WP_Pay_Extensions_EventEspresso_IDealGateway',
		);

		foreach ( $gateways as $gateway ) {
			// @see https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/core/db_models/EEM_Gateways.model.php#L217
			$class_name = 'EE_' . $gateway;

			class_alias( 'Pronamic_WP_Pay_Extensions_EventEspresso_IDealGateway', $class_name );

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
