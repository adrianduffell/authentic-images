<?php
/**
 * Test the register_authentic_badge_text_color_setting function.
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function AuthenticImages\register_authentic_badge_text_color_setting;
use const AuthenticImages\AUTHENTIC_BADGE_TEXT_COLOR_OPTION;

class Test_Register_Authentic_Badge_Text_Color_Setting extends WP_UnitTestCase {

	public function test_registers_authentic_badge_text_color_setting(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_BADGE_TEXT_COLOR_OPTION );

		// Act.
		register_authentic_badge_text_color_setting();

		// Assert.
		$settings = get_registered_settings();
		$this->assertArrayHasKey( AUTHENTIC_BADGE_TEXT_COLOR_OPTION, $settings );
	}

	public function test_setting_type_is_string(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_BADGE_TEXT_COLOR_OPTION );

		// Act.
		register_authentic_badge_text_color_setting();

		// Assert.
		$settings = get_registered_settings();
		$this->assertSame( 'string', $settings[ AUTHENTIC_BADGE_TEXT_COLOR_OPTION ]['type'] );
	}

	public function test_setting_is_shown_in_rest(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_BADGE_TEXT_COLOR_OPTION );
		register_authentic_badge_text_color_setting();
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Act.
		$request  = new WP_REST_Request( 'GET', '/wp/v2/settings' );
		$response = rest_do_request( $request );

		// Assert.
		$this->assertArrayHasKey( AUTHENTIC_BADGE_TEXT_COLOR_OPTION, $response->get_data() );
	}

	public function test_setting_default_is_empty_string(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_BADGE_TEXT_COLOR_OPTION );
		delete_option( AUTHENTIC_BADGE_TEXT_COLOR_OPTION );
		register_authentic_badge_text_color_setting();
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Act.
		$request  = new WP_REST_Request( 'GET', '/wp/v2/settings' );
		$response = rest_do_request( $request );
		$data     = $response->get_data();

		// Assert.
		$this->assertSame( '', $data[ AUTHENTIC_BADGE_TEXT_COLOR_OPTION ] );
	}

	public function test_setting_can_be_updated_via_rest(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_BADGE_TEXT_COLOR_OPTION );
		register_authentic_badge_text_color_setting();
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Act.
		$request = new WP_REST_Request( 'POST', '/wp/v2/settings' );
		$request->set_param( AUTHENTIC_BADGE_TEXT_COLOR_OPTION, '#FF0000' );
		$response = rest_do_request( $request );
		$data     = $response->get_data();

		// Assert.
		$this->assertSame( '#FF0000', $data[ AUTHENTIC_BADGE_TEXT_COLOR_OPTION ] );
		$this->assertSame( '#FF0000', get_option( AUTHENTIC_BADGE_TEXT_COLOR_OPTION ) );
	}
}
