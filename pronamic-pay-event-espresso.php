<?php
/**
 * Plugin Name: Pronamic Pay Event Espresso Add-On
 * Plugin URI: https://www.pronamic.eu/plugins/pronamic-pay-event-espresso/
 * Description: Extend the Pronamic Pay plugin with Event Espresso support to receive payments through a variety of payment providers.
 *
 * Version: 4.1.0
 * Requires at least: 4.7
 *
 * Author: Pronamic
 * Author URI: https://www.pronamic.eu/
 *
 * Text Domain: pronamic-pay-event-espresso
 * Domain Path: /languages/
 *
 * License: GPL-3.0-or-later
 *
 * Depends: wp-pay/core
 *
 * GitHub URI: https://github.com/pronamic/wp-pronamic-pay-event-espresso
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\EventEspresso
 */

add_filter(
	'pronamic_pay_plugin_integrations',
	function ( $integrations ) {
		foreach ( $integrations as $integration ) {
			if ( $integration instanceof \Pronamic\WordPress\Pay\Extensions\EventEspresso\Extension ) {
				return $integrations;
			}
		}

		$integrations[] = new \Pronamic\WordPress\Pay\Extensions\EventEspresso\Extension();

		return $integrations;
	}
);
