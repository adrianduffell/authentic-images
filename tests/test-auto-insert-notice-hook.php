<?php
/**
 * Tests for auto_insert_notice_hook().
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function AuthenticImages\deinit_blocks;
use function AuthenticImages\init_blocks;

class Test_Auto_Insert_Notice_Hook extends WP_UnitTestCase {

	public function test_notice_is_added_after_product_image_gallery(): void {
		// Arrange.
		deinit_blocks();
		init_blocks();
		$template       = new WP_Block_Template();
		$template->slug = 'single-product';

		// Act.
		$result = apply_filters( 'hooked_block_types', array(), 'after', 'woocommerce/product-image-gallery', $template );

		// Assert.
		$this->assertContains( 'authenticimages/authentic-image-notice', $result );
	}

	public function test_notice_is_added_after_product_gallery(): void {
		// Arrange.
		deinit_blocks();
		init_blocks();
		$template       = new WP_Block_Template();
		$template->slug = 'single-product';

		// Act.
		$result = apply_filters( 'hooked_block_types', array(), 'after', 'woocommerce/product-gallery', $template );

		// Assert.
		$this->assertContains( 'authenticimages/authentic-image-notice', $result );
	}

	public function test_notice_is_not_added_before_product_gallery(): void {
		// Arrange.
		deinit_blocks();
		init_blocks();
		$template       = new WP_Block_Template();
		$template->slug = 'single-product';

		// Act.
		$result = apply_filters( 'hooked_block_types', array(), 'before', 'woocommerce/product-gallery', $template );

		// Assert.
		$this->assertNotContains( 'authenticimages/authentic-image-notice', $result );
	}

	public function test_notice_is_not_added_outside_single_product_template(): void {
		// Arrange.
		deinit_blocks();
		init_blocks();
		$template       = new WP_Block_Template();
		$template->slug = 'archive-product';

		// Act.
		$result = apply_filters( 'hooked_block_types', array(), 'after', 'woocommerce/product-gallery', $template );

		// Assert.
		$this->assertNotContains( 'authenticimages/authentic-image-notice', $result );
	}

	public function test_notice_is_not_added_for_an_unsupported_anchor(): void {
		// Arrange.
		deinit_blocks();
		init_blocks();
		$template       = new WP_Block_Template();
		$template->slug = 'single-product';

		// Act.
		$result = apply_filters( 'hooked_block_types', array(), 'after', 'core/heading', $template );

		// Assert.
		$this->assertNotContains( 'authenticimages/authentic-image-notice', $result );
	}

	public function test_existing_hooked_blocks_are_preserved(): void {
		// Arrange.
		deinit_blocks();
		init_blocks();
		$template       = new WP_Block_Template();
		$template->slug = 'single-product';

		// Act.
		$result = apply_filters( 'hooked_block_types', array( 'core/paragraph' ), 'after', 'woocommerce/product-gallery', $template );

		// Assert.
		$this->assertContains( 'core/paragraph', $result );
		$this->assertContains( 'authenticimages/authentic-image-notice', $result );
	}
}
