<?php
/**
 * Tests for init_blocks().
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function AuthenticImages\deinit_blocks;
use function AuthenticImages\init_blocks;

class Test_Init_Blocks extends WP_UnitTestCase {

	public function test_registers_authentic_image_notice_block(): void {
		// Arrange.
		deinit_blocks();

		// Act.
		init_blocks();

		// Assert.
		$this->assertTrue( \WP_Block_Type_Registry::get_instance()->is_registered( 'authenticimages/authentic-image-notice' ) );
	}

	public function test_registers_authentic_badge_block(): void {
		// Arrange.
		deinit_blocks();

		// Act.
		init_blocks();

		// Assert.
		$this->assertTrue( \WP_Block_Type_Registry::get_instance()->is_registered( 'authenticimages/authentic-badge' ) );
	}

	public function test_registers_authentic_message_block(): void {
		// Arrange.
		deinit_blocks();

		// Act.
		init_blocks();

		// Assert.
		$this->assertTrue( \WP_Block_Type_Registry::get_instance()->is_registered( 'authenticimages/authentic-message' ) );
	}
}
