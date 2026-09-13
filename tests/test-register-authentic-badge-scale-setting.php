<?php
/**
 * Test the register_authentic_badge_scale_setting function.
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function AuthenticImages\register_authentic_badge_scale_setting;
use const AuthenticImages\AUTHENTIC_BADGE_SCALE_OPTION;

class Test_Register_Authentic_Badge_Scale_Setting extends WP_UnitTestCase {

	public function test_registers_authentic_badge_scale_setting(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_BADGE_SCALE_OPTION );

		// Act.
		register_authentic_badge_scale_setting();

		// Assert.
		$settings = get_registered_settings();
		$this->assertArrayHasKey( AUTHENTIC_BADGE_SCALE_OPTION, $settings );
	}

	public function test_setting_type_is_integer(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_BADGE_SCALE_OPTION );

		// Act.
		register_authentic_badge_scale_setting();

		// Assert.
		$settings = get_registered_settings();
		$this->assertSame( 'integer', $settings[ AUTHENTIC_BADGE_SCALE_OPTION ]['type'] );
	}

	public function test_setting_default_is_null(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_BADGE_SCALE_OPTION );
		delete_option( AUTHENTIC_BADGE_SCALE_OPTION );
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Act.
		register_authentic_badge_scale_setting();
		$request  = new WP_REST_Request( 'GET', '/wp/v2/settings' );
		$response = rest_do_request( $request );
		$data     = $response->get_data();

		// Assert.
		$this->assertArrayHasKey( AUTHENTIC_BADGE_SCALE_OPTION, $data );
		$this->assertNull( $data[ AUTHENTIC_BADGE_SCALE_OPTION ] );
	}

	public function test_setting_is_shown_in_rest(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_BADGE_SCALE_OPTION );
		register_authentic_badge_scale_setting();
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Act.
		$request  = new WP_REST_Request( 'GET', '/wp/v2/settings' );
		$response = rest_do_request( $request );

		// Assert.
		$this->assertArrayHasKey( AUTHENTIC_BADGE_SCALE_OPTION, $response->get_data() );
	}

	public function test_setting_can_be_updated_via_rest_with_valid_value(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_BADGE_SCALE_OPTION );
		register_authentic_badge_scale_setting();
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Act.
		$request = new WP_REST_Request( 'POST', '/wp/v2/settings' );
		$request->set_param( AUTHENTIC_BADGE_SCALE_OPTION, 140 );
		$response = rest_do_request( $request );
		$data     = $response->get_data();

		// Assert.
		$this->assertSame( 140, $data[ AUTHENTIC_BADGE_SCALE_OPTION ] );
		$this->assertSame( 140, get_option( AUTHENTIC_BADGE_SCALE_OPTION ) );
	}

	public function test_setting_rest_schema_minimum_is_zero(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_BADGE_SCALE_OPTION );
		register_authentic_badge_scale_setting();
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Act.
		$request = new WP_REST_Request( 'POST', '/wp/v2/settings' );
		$request->set_param( AUTHENTIC_BADGE_SCALE_OPTION, -1 );
		$response = rest_do_request( $request );

		// Assert.
		$this->assertSame( 400, $response->get_status() );
	}

	public function test_setting_allows_null_value_via_rest(): void {
		// Arrange.
		update_option( AUTHENTIC_BADGE_SCALE_OPTION, 166 );
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Act.
		$request = new WP_REST_Request( 'POST', '/wp/v2/settings' );
		$request->set_param( AUTHENTIC_BADGE_SCALE_OPTION, null );
		$response = rest_do_request( $request );

		// Assert.
		$this->assertSame( 200, $response->get_status() );
	}

	public function test_setting_rejects_unexpected_value_via_rest(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_BADGE_SCALE_OPTION );
		delete_option( AUTHENTIC_BADGE_SCALE_OPTION );
		register_authentic_badge_scale_setting();
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Act.
		$request = new WP_REST_Request( 'POST', '/wp/v2/settings' );
		$request->set_param( AUTHENTIC_BADGE_SCALE_OPTION, 'unexpected' );
		$response = rest_do_request( $request );

		// Assert.
		$this->assertSame( 400, $response->get_status() );
	}
}
