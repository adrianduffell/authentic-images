<?php
/**
 * Tests the plugin file.
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use const AuthenticImages\PLUGIN_FILE;

class Test_Plugin_File extends WP_UnitTestCase {

	/**
	 * Test the Update URI plugin header.
	 *
	 * @group updates
	 */
	public function test_update_uri(): void {
		// Arrange.
		$plugin_data = get_plugin_data( PLUGIN_FILE );

		// Act.

		// Assert.
		$this->assertSame( 'https://adrianduffell.store/authenticimages', $plugin_data['UpdateURI'] );
	}

	public function test_requires_plugins(): void {
		// Arrange.
		$plugin_data = get_plugin_data( PLUGIN_FILE );

		// Act.

		// Assert.
		$this->assertSame( 'woocommerce', $plugin_data['RequiresPlugins'] );
	}
}
