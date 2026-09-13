<?php
/**
 * Tests for register_classic_styles_hook().
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function AuthenticImages\deinit_enqueue;
use function AuthenticImages\enqueue_init;

class Test_Register_Classic_Styles_Hook extends WP_UnitTestCase {

	public function test_registers_classic_badge_style(): void {
		// Arrange.
		deinit_enqueue();
		enqueue_init();

		// Act.
		do_action( 'wp_enqueue_scripts' );

		// Assert.
		$this->assertTrue( wp_style_is( 'authenticimages-classic-badge', 'registered' ) );
	}

	public function test_registers_classic_message_style(): void {
		// Arrange.
		deinit_enqueue();
		enqueue_init();

		// Act.
		do_action( 'wp_enqueue_scripts' );

		// Assert.
		$this->assertTrue( wp_style_is( 'authenticimages-classic-message', 'registered' ) );
	}
}
