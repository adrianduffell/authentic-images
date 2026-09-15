<?php
/**
 * Tests for display_authentic_image_notice_hook().
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function AuthenticImages\init_woocommerce_template_hooks;
use const AuthenticImages\AUTHENTIC_BADGE_LABEL_OPTION;
use const AuthenticImages\AUTHENTIC_MESSAGE_OPTION;

class Test_Display_Authentic_Image_Notice_Hook extends WP_UnitTestCase {

	public function test_hook_is_registered_after_init_woocommerce_template_hooks(): void {
		// Arrange.
		remove_action( 'woocommerce_product_meta_start', 'AuthenticImages\display_authentic_image_notice_hook', 1 );

		// Act.
		init_woocommerce_template_hooks();

		// Assert.
		$this->assertFalse( has_action( 'woocommerce_single_product_summary', 'AuthenticImages\display_authentic_badge_hook' ) );
		$this->assertFalse( has_action( 'woocommerce_product_meta_start', 'AuthenticImages\display_authentic_message_hook' ) );
		$this->assertSame( 1, has_action( 'woocommerce_product_meta_start', 'AuthenticImages\display_authentic_image_notice_hook' ) );
	}

	public function test_displays_complete_notice_for_product(): void {
		// Arrange.
		update_option( AUTHENTIC_BADGE_LABEL_OPTION, 'Authentic' );
		update_option( AUTHENTIC_MESSAGE_OPTION, 'All of our product images are real.' );
		$product         = \WC_Helper_Product::create_simple_product();
		$GLOBALS['post'] = get_post( $product->get_id() );
		init_woocommerce_template_hooks();

		// Expect.
		$this->expectOutputRegex( '/<p class="authenticimages-notice">.*<span class="authenticimages-badge">Authentic<\/span>.*<span class="authenticimages-message">All of our product images are real\.<\/span>.*<\/p>/s' );

		// Act.
		do_action( 'woocommerce_product_meta_start' );
	}

	public function test_displays_notice_message_for_product(): void {
		// Arrange.
		update_option( AUTHENTIC_MESSAGE_OPTION, 'All of our product images are real.' );
		$product         = \WC_Helper_Product::create_simple_product();
		$GLOBALS['post'] = get_post( $product->get_id() );
		init_woocommerce_template_hooks();

		// Expect.
		$this->expectOutputRegex( '/authenticimages-message/' );

		// Act.
		do_action( 'woocommerce_product_meta_start' );
	}

	public function test_notice_message_contains_text(): void {
		// Arrange.
		update_option( AUTHENTIC_MESSAGE_OPTION, 'All of our product images are real.' );
		$product         = \WC_Helper_Product::create_simple_product();
		$GLOBALS['post'] = get_post( $product->get_id() );
		init_woocommerce_template_hooks();

		// Expect.
		$this->expectOutputRegex( '/All of our product images are real\./' );

		// Act.
		do_action( 'woocommerce_product_meta_start' );
	}

	public function test_does_not_display_notice_message_when_option_is_empty(): void {
		// Arrange.
		update_option( AUTHENTIC_MESSAGE_OPTION, '' );
		$product         = \WC_Helper_Product::create_simple_product();
		$GLOBALS['post'] = get_post( $product->get_id() );
		init_woocommerce_template_hooks();

		// Expect.
		$this->expectOutputRegex( '/^(?!.*authenticimages-message).*/s' ); // Does not contain the authentic message.

		// Act.
		do_action( 'woocommerce_product_meta_start' );
	}

	public function test_does_not_display_notice_message_when_option_does_not_exist(): void {
		// Arrange.
		delete_option( AUTHENTIC_MESSAGE_OPTION );
		$product         = \WC_Helper_Product::create_simple_product();
		$GLOBALS['post'] = get_post( $product->get_id() );
		init_woocommerce_template_hooks();

		// Expect.
		$this->expectOutputRegex( '/^(?!.*authenticimages-message).*/s' ); // Does not contain the authentic message.

		// Act.
		do_action( 'woocommerce_product_meta_start' );
	}

	public function test_does_not_display_notice_when_post_is_not_a_product(): void {
		// Arrange.
		$post_id         = self::factory()->post->create();
		$GLOBALS['post'] = get_post( $post_id );
		init_woocommerce_template_hooks();

		// Expect.
		$this->expectOutputRegex( '/^(?!.*authenticimages-message).*/s' ); // Does not contain the authentic message.

		// Act.
		do_action( 'woocommerce_product_meta_start' );
	}
}
