<?php
/**
 * Test the register_authentic_message_setting function.
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function AuthenticImages\register_authentic_message_setting;
use const AuthenticImages\AUTHENTIC_MESSAGE_OPTION;

class Test_Register_Authentic_Message_Setting extends WP_UnitTestCase {

	public function test_registers_authentic_message_setting(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_MESSAGE_OPTION );

		// Act.
		register_authentic_message_setting();

		// Assert.
		$settings = get_registered_settings();
		$this->assertArrayHasKey( AUTHENTIC_MESSAGE_OPTION, $settings );
	}

	public function test_setting_type_is_string(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_MESSAGE_OPTION );

		// Act.
		register_authentic_message_setting();

		// Assert.
		$settings = get_registered_settings();
		$this->assertSame( 'string', $settings[ AUTHENTIC_MESSAGE_OPTION ]['type'] );
	}

	public function test_setting_default_is_empty_string(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_MESSAGE_OPTION );

		// Act.
		register_authentic_message_setting();

		// Assert.
		$settings = get_registered_settings();
		$this->assertSame( '', $settings[ AUTHENTIC_MESSAGE_OPTION ]['default'] );
	}

	public function test_setting_is_shown_in_rest(): void {
		// Arrange.
		unregister_setting( 'authenticimages', AUTHENTIC_MESSAGE_OPTION );
		register_authentic_message_setting();
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Act.
		$request  = new WP_REST_Request( 'GET', '/wp/v2/settings' );
		$response = rest_do_request( $request );

		// Assert.
		$this->assertArrayHasKey( AUTHENTIC_MESSAGE_OPTION, $response->get_data() );
	}
}
