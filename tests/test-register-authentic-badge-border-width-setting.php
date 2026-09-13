<?php
/**
 * Test the register_authentic_badge_border_width_setting function.
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function AuthenticImages\register_authentic_badge_border_width_setting;
use const AuthenticImages\AUTHENTIC_BADGE_BORDER_WIDTH_OPTION;

class Test_Register_Authentic_Badge_Border_Width_Setting extends WP_UnitTestCase {

	public function test_registers_authentic_badge_border_width_setting(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_BADGE_BORDER_WIDTH_OPTION );

		// Act.
		register_authentic_badge_border_width_setting();

		// Assert.
		$settings = get_registered_settings();
		$this->assertArrayHasKey( AUTHENTIC_BADGE_BORDER_WIDTH_OPTION, $settings );
	}

	public function test_setting_type_is_string(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_BADGE_BORDER_WIDTH_OPTION );

		// Act.
		register_authentic_badge_border_width_setting();

		// Assert.
		$settings = get_registered_settings();
		$this->assertSame( 'string', $settings[ AUTHENTIC_BADGE_BORDER_WIDTH_OPTION ]['type'] );
	}

	public function test_setting_is_shown_in_rest(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_BADGE_BORDER_WIDTH_OPTION );
		register_authentic_badge_border_width_setting();
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Act.
		$request  = new WP_REST_Request( 'GET', '/wp/v2/settings' );
		$response = rest_do_request( $request );

		// Assert.
		$this->assertArrayHasKey( AUTHENTIC_BADGE_BORDER_WIDTH_OPTION, $response->get_data() );
	}

	public function test_setting_can_be_updated_via_rest(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_BADGE_BORDER_WIDTH_OPTION );
		register_authentic_badge_border_width_setting();
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Act.
		$request = new WP_REST_Request( 'POST', '/wp/v2/settings' );
		$request->set_param( AUTHENTIC_BADGE_BORDER_WIDTH_OPTION, '1px' );
		$response = rest_do_request( $request );
		$data     = $response->get_data();

		// Assert.
		$this->assertSame( '1px', $data[ AUTHENTIC_BADGE_BORDER_WIDTH_OPTION ] );
		$this->assertSame( '1px', get_option( AUTHENTIC_BADGE_BORDER_WIDTH_OPTION ) );
	}
}
