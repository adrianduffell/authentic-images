<?php
/**
 * Test the init_license function.
 *
 * @package AuthenticImages
 * @group license
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function AuthenticImages\deinit_license_settings;
use function AuthenticImages\init_license_settings;
use const AuthenticImages\LICENSE_KEY_OPTION;

class Test_Init_License_Settings extends WP_UnitTestCase {

	public function test_init_license_settings_registers_setting(): void {
		// Arrange.
		deinit_license_settings();

		// Act.
		init_license_settings();

		// Assert.
		$this->assertArrayHasKey( LICENSE_KEY_OPTION, get_registered_settings() );
	}
}
