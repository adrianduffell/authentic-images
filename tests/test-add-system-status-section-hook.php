<?php
/**
 * Tests for add_system_status_section_hook().
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function AuthenticImages\init_system_status;

class Test_Add_System_Status_Section_Hook extends WP_UnitTestCase {

	public function test_outputs_heading_without_report_data(): void {
		// Arrange.
		init_system_status();

		// Expect.
		$this->expectOutputRegex( '/<table[^>]*>.*?<thead>.*?<h2>Authentic Images<\/h2>.*?<\/thead>.*?<\/table>/s' );

		// Act.
		do_action( 'woocommerce_system_status_report' );
	}
}
