<?php
/**
 * Tests for register_authentic_badge_block().
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function AuthenticImages\deinit_blocks;
use function AuthenticImages\register_authentic_badge_block;

class Test_Register_Authentic_Badge_Block extends WP_UnitTestCase {

	public function test_badge_registers_editor_script(): void {
		// Arrange.
		deinit_blocks();
		wp_deregister_script( 'authenticimages-authentic-badge-editor-script' );

		// Act.
		register_authentic_badge_block();

		// Assert.
		$this->assertTrue( wp_script_is( 'authenticimages-authentic-badge-editor-script', 'registered' ) );
	}

	public function test_badge_registers_style(): void {
		// Arrange.
		deinit_blocks();
		wp_deregister_style( 'authenticimages-authentic-badge-style' );

		// Act.
		register_authentic_badge_block();

		// Assert.
		$this->assertTrue( wp_style_is( 'authenticimages-authentic-badge-style', 'registered' ) );
	}
}
