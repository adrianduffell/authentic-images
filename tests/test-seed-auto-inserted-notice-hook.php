<?php
/**
 * Tests for seed_auto_inserted_notice_hook().
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function AuthenticImages\deinit_blocks;
use function AuthenticImages\init_blocks;

class Test_Seed_Auto_Inserted_Notice_Hook extends WP_UnitTestCase {

	public function test_product_image_gallery_notice_is_inserted_with_its_children(): void {
		// Arrange.
		deinit_blocks();
		init_blocks();
		$template       = new WP_Block_Template();
		$template->slug = 'single-product';
		$content        = '<!-- wp:woocommerce/product-image-gallery /-->';

		// Act.
		$rendered_content = apply_block_hooks_to_content( $content, $template );

		// Assert.
		$this->assertSame( 1, substr_count( $rendered_content, '<!-- wp:authenticimages/authentic-image-notice' ) );
		$this->assertSame( 1, substr_count( $rendered_content, '<!-- wp:authenticimages/authentic-badge' ) );
		$this->assertSame( 1, substr_count( $rendered_content, '<!-- wp:authenticimages/authentic-message' ) );
	}

	public function test_product_gallery_notice_is_inserted_once_with_its_children(): void {
		// Arrange.
		deinit_blocks();
		init_blocks();
		$template       = new WP_Block_Template();
		$template->slug = 'single-product';
		$content        = '<!-- wp:woocommerce/product-gallery /-->';

		// Act.
		$rendered_content = apply_block_hooks_to_content( $content, $template );

		// Assert.
		$this->assertSame( 1, substr_count( $rendered_content, '<!-- wp:authenticimages/authentic-image-notice' ) );
		$this->assertSame( 1, substr_count( $rendered_content, '<!-- wp:authenticimages/authentic-badge' ) );
		$this->assertSame( 1, substr_count( $rendered_content, '<!-- wp:authenticimages/authentic-message' ) );
		$this->assertStringNotContainsString( '"margin"', $rendered_content );
	}

	public function test_saved_enabled_authentic_image_notice_is_not_duplicated(): void {
		// Arrange.
		deinit_blocks();
		init_blocks();
		$template       = new WP_Block_Template();
		$template->slug = 'single-product';
		$content        = '<!-- wp:woocommerce/product-gallery /-->';
		$inserted       = apply_block_hooks_to_content( $content, $template );
		$saved          = apply_block_hooks_to_content( $inserted, $template, 'set_ignored_hooked_blocks_metadata' );

		// Act.
		$rendered_content = apply_block_hooks_to_content( $saved, $template );

		// Assert.
		$this->assertSame( 1, substr_count( $rendered_content, '<!-- wp:authenticimages/authentic-image-notice' ) );
		$this->assertSame( 1, substr_count( $rendered_content, '<!-- wp:authenticimages/authentic-badge' ) );
	}

	public function test_disabled_authentic_image_notice_is_not_inserted(): void {
		// Arrange.
		deinit_blocks();
		init_blocks();
		$template       = new WP_Block_Template();
		$template->slug = 'single-product';
		$content        = '<!-- wp:woocommerce/product-gallery {"metadata":{"ignoredHookedBlocks":["authenticimages/authentic-image-notice"]}} /-->';

		// Act.
		$rendered_content = apply_block_hooks_to_content( $content, $template );

		// Assert.
		$this->assertStringNotContainsString( '<!-- wp:authenticimages/authentic-image-notice', $rendered_content );
		$this->assertStringNotContainsString( '<!-- wp:authenticimages/authentic-badge', $rendered_content );
	}
}
