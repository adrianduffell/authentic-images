<?php
/**
 * Tests for register_authentic_image_notice_block().
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function AuthenticImages\deinit_blocks;
use function AuthenticImages\init_blocks;

class Test_Register_Authentic_Image_Notice_Block extends WP_UnitTestCase {

	public function test_notice_is_registered_after_init_blocks(): void {
		// Arrange.
		deinit_blocks();

		// Act.
		init_blocks();

		// Assert.
		$this->assertTrue( \WP_Block_Type_Registry::get_instance()->is_registered( 'authenticimages/authentic-image-notice' ) );
	}
}
