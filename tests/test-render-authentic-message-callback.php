<?php
/**
 * Tests for render_authentic_message_callback().
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function AuthenticImages\deinit_blocks;
use function AuthenticImages\init_blocks;
use function AuthenticImages\register_authentic_message_block;
use function AuthenticImages\render_authentic_message_callback;
use const AuthenticImages\AUTHENTIC_MESSAGE_OPTION;

class Test_Render_Authentic_Message_Callback extends WP_UnitTestCase {

	public function test_returns_message_html_for_product(): void {
		// Arrange.
		deinit_blocks();
		register_authentic_message_block();
		update_option( AUTHENTIC_MESSAGE_OPTION, 'All of our product images are real.' );
		$product = \WC_Helper_Product::create_simple_product();
		$block   = new WP_Block(
			array(
				'blockName'    => 'authenticimages/authentic-message',
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
		$this->assertStringContainsString( 'authenticimages-message', $result );
		$this->assertStringContainsString( 'All of our product images are real.', $result );

		// Cleanup.
		delete_option( AUTHENTIC_MESSAGE_OPTION );
	}

	public function test_message_uses_global_message_option(): void {
		// Arrange.
		deinit_blocks();
		register_authentic_message_block();
		update_option( AUTHENTIC_MESSAGE_OPTION, 'Every product image shows the actual item.' );
		$product = \WC_Helper_Product::create_simple_product();
		$block   = new WP_Block(
			array(
				'blockName'    => 'authenticimages/authentic-message',
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
		$this->assertStringContainsString( 'Every product image shows the actual item.', $result );
		$this->assertStringNotContainsString( 'All of our product images are real.', $result );

		// Cleanup.
		delete_option( AUTHENTIC_MESSAGE_OPTION );
	}

	public function test_returns_empty_string_when_post_id_is_zero(): void {
		// Arrange.
		deinit_blocks();
		register_authentic_message_block();
		$block = new WP_Block(
			array(
				'blockName'    => 'authenticimages/authentic-message',
				'attrs'        => array(),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			),
			array()
		);

		// Act.
		$result = render_authentic_message_callback( array(), '', $block );

		// Assert.
		$this->assertSame( '', $result );
	}

	public function test_message_is_registered_after_init_blocks(): void {
		// Arrange.
		deinit_blocks();

		// Act.
		init_blocks();

		// Assert.
		$this->assertTrue( \WP_Block_Type_Registry::get_instance()->is_registered( 'authenticimages/authentic-message' ) );
	}

	public function test_empty_option_returns_empty_string(): void {
		// Arrange.
		deinit_blocks();
		register_authentic_message_block();
		update_option( AUTHENTIC_MESSAGE_OPTION, '' );
		$product = \WC_Helper_Product::create_simple_product();
		$block   = new WP_Block(
			array(
				'blockName'    => 'authenticimages/authentic-message',
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

		// Cleanup.
		delete_option( AUTHENTIC_MESSAGE_OPTION );
	}

	public function test_missing_option_returns_empty_string(): void {
		// Arrange.
		deinit_blocks();
		register_authentic_message_block();
		delete_option( AUTHENTIC_MESSAGE_OPTION );
		$product = \WC_Helper_Product::create_simple_product();
		$block   = new WP_Block(
			array(
				'blockName'    => 'authenticimages/authentic-message',
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

	public function test_message_is_wrapped_in_paragraph_tag(): void {
		// Arrange.
		deinit_blocks();
		register_authentic_message_block();
		update_option( AUTHENTIC_MESSAGE_OPTION, 'All of our product images are real.' );
		$product = \WC_Helper_Product::create_simple_product();
		$block   = new WP_Block(
			array(
				'blockName'    => 'authenticimages/authentic-message',
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
		$this->assertMatchesRegularExpression( '/<p\b[^>]*>.+<\/p>/', $result );

		// Cleanup.
		delete_option( AUTHENTIC_MESSAGE_OPTION );
	}
}
