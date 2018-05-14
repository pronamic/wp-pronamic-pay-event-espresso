<?php

namespace Pronamic\WordPress\Pay\Extensions\EventEspresso;

use PHPUnit_Framework_TestCase;

/**
 * Title: WordPress pay Event Espresso Payment Statuses test
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Reüel van der Steege
 * @version 2.0.0
 * @since   2.0.0
 */
class PaymentStatusesTest extends PHPUnit_Framework_TestCase {
	/**
	 * Test class.
	 */
	public function test_class() {
		$this->assertTrue( class_exists( __NAMESPACE__ . '\PaymentStatuses' ) );
	}
}
