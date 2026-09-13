<?php
/**
 * Tests for render_authentic_badge_callback().
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function AuthenticImages\deinit_blocks;
use function AuthenticImages\init_blocks;
use function AuthenticImages\register_authentic_badge_block;
use function AuthenticImages\render_authentic_badge_callback;
use const AuthenticImages\AUTHENTIC_BADGE_LABEL_OPTION;

class Test_Render_Authentic_Badge_Callback extends WP_UnitTestCase {

	public function test_returns_badge_html_for_product(): void {
		// Arrange.
		deinit_blocks();
		register_authentic_badge_block();
		update_option( AUTHENTIC_BADGE_LABEL_OPTION, 'Real images' );
		$product = \WC_Helper_Product::create_simple_product();
		$block   = new WP_Block(
			array(
				'blockName'    => 'authenticimages/authentic-badge',
				'attrs'        => array(),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			),
			array( 'postId' => $product->get_id() )
		);

		// Act.
		$result = $block->render();

		// Assert.
		$this->assertStringContainsString( 'authenticimages-badge', $result );
		$this->assertStringContainsString( 'Real images', $result );
		$this->assertMatchesRegularExpression( '/<div[^>]+class="[^"]*authenticimages-badge/', $result );
	}

	public function test_badge_uses_global_badge_label_option(): void {
		// Arrange.
		deinit_blocks();
		register_authentic_badge_block();
		update_option( AUTHENTIC_BADGE_LABEL_OPTION, 'Authentic' );
		$product = \WC_Helper_Product::create_simple_product();
		$block   = new WP_Block(
			array(
				'blockName'    => 'authenticimages/authentic-badge',
				'attrs'        => array(),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			),
			array( 'postId' => $product->get_id() )
		);

		// Act.
		$result = $block->render();

		// Assert.
		$this->assertStringContainsString( 'Authentic', $result );
		$this->assertStringNotContainsString( 'Real images', $result );
	}

	public function test_returns_empty_string_when_post_id_is_zero(): void {
		// Arrange.
		deinit_blocks();
		register_authentic_badge_block();
		$block = new WP_Block(
			array(
				'blockName'    => 'authenticimages/authentic-badge',
				'attrs'        => array(),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			),
			array()
		);

		// Act.
		$result = render_authentic_badge_callback( array(), '', $block );

		// Assert.
		$this->assertSame( '', $result );
	}

	public function test_badge_is_registered_after_init_blocks(): void {
		// Arrange.
		deinit_blocks();

		// Act.
		init_blocks();

		// Assert.
		$this->assertTrue( \WP_Block_Type_Registry::get_instance()->is_registered( 'authenticimages/authentic-badge' ) );
	}

	public function test_returns_empty_string_when_label_is_empty(): void {
		// Arrange.
		deinit_blocks();
		register_authentic_badge_block();
		update_option( AUTHENTIC_BADGE_LABEL_OPTION, '' );
		$product = \WC_Helper_Product::create_simple_product();
		$block   = new WP_Block(
			array(
				'blockName'    => 'authenticimages/authentic-badge',
				'attrs'        => array(),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			),
			array( 'postId' => $product->get_id() )
		);

		// Act.
		$result = $block->render();

		// Assert.
		$this->assertSame( '', $result );
	}
}
