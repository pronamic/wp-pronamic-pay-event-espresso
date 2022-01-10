<?php
/**
 * Event Espresso Dependency
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\EventEspresso
 */

namespace Pronamic\WordPress\Pay\Extensions\EventEspresso;

use Pronamic\WordPress\Pay\Dependencies\Dependency;

/**
 * Event Espresso Dependency
 *
 * @author  Reüel van der Steege
 * @version 2.1.4
 * @since   2.1.4
 */
class EventEspressoDependency extends Dependency {
	/**
	 * Is met.
	 *
	 * @link https://github.com/eventespresso/event-espresso-core/blob/4.2.2.reg/espresso.php#L53
	 * @return bool True if dependency is met, false otherwise.
	 */
	public function is_met() {
		if ( ! \defined( '\EVENT_ESPRESSO_VERSION' ) ) {
			return false;
		}

		return \version_compare(
			\EVENT_ESPRESSO_VERSION,
			'4.0.0',
			'>='
		);
	}
}
