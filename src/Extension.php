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
		return
			defined( 'EVENT_ESPRESSO_VERSION' )
				&&
			version_compare( EVENT_ESPRESSO_VERSION, '4', '>=' )
		;
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
		class_alias( 'Pronamic_WP_Pay_Extensions_EventEspresso_IDealGateway', 'EE_pronamic_pay_ideal' );

		// Fix fatal error since Event Espresso 3.1.29.1.P
		if ( defined( 'EVENT_ESPRESSO_GATEWAY_DIR' ) ) {
			$gateway   = 'pronamic_pay_ideal';
			$classname = 'EE_' . $gateway;

			$gateway_dir   = EVENT_ESPRESSO_GATEWAY_DIR . $gateway;
			$gateway_class = $gateway_dir . '/' . $classname . '.class.php';

			if ( ! is_readable( $gateway_class ) ) {
				$created = wp_mkdir_p( $gateway_dir );

				if ( $created ) {
					touch( $gateway_class );
				}
			}
		}
	}
}
