<?php
/**
 * Tests for deinit_enqueue().
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function AuthenticImages\deinit_enqueue;
use function AuthenticImages\enqueue_init;

class Test_Deinit_Enqueue extends WP_UnitTestCase {

	public function test_removes_register_classic_styles_hook(): void {
		// Arrange.
		enqueue_init();

		// Act.
		deinit_enqueue();

		// Assert.
		$this->assertFalse( has_action( 'wp_enqueue_scripts', 'AuthenticImages\register_classic_styles_hook' ) );
	}

	public function test_removes_output_badge_style_css_variables_hook(): void {
		// Arrange.
		enqueue_init();

		// Act.
		deinit_enqueue();

		// Assert.
		$this->assertFalse( has_action( 'wp_head', 'AuthenticImages\output_badge_style_css_variables_hook' ) );
	}

	public function test_removes_enqueue_admin_canvas_scripts_hook(): void {
		// Arrange.
		enqueue_init();

		// Act.
		deinit_enqueue();

		// Assert.
		$this->assertFalse( has_action( 'enqueue_block_assets', 'AuthenticImages\enqueue_admin_canvas_scripts_hook' ) );
	}

	public function test_removes_admin_enqueue_scripts_styles_hook(): void {
		// Arrange.
		enqueue_init();

		// Act.
		deinit_enqueue();

		// Assert.
		$this->assertFalse( has_action( 'admin_enqueue_scripts', 'AuthenticImages\enqueue_admin_styles_hook' ) );
	}

	public function test_removes_admin_enqueue_scripts_welcome_page_scripts_hook(): void {
		// Arrange.
		enqueue_init();

		// Act.
		deinit_enqueue();

		// Assert.
		$this->assertFalse( has_action( 'admin_enqueue_scripts', 'AuthenticImages\enqueue_admin_welcome_page_scripts_hook' ) );
	}

	public function test_removes_enqueue_block_editor_assets_hook(): void {
		// Arrange.
		enqueue_init();

		// Act.
		deinit_enqueue();

		// Assert.
		$this->assertFalse( has_action( 'enqueue_block_editor_assets', 'AuthenticImages\enqueue_build_assets_hook' ) );
	}

	public function test_deregisters_block_styles(): void {
		// Arrange.
		wp_register_style( 'authenticimages-badge-block', false, array(), 'test' );

		// Act.
		deinit_enqueue();

		// Assert.
		$this->assertFalse( wp_style_is( 'authenticimages-badge-block', 'registered' ) );
	}

	public function test_safely_handles_block_styles_not_registered(): void {
		// Arrange.

		// Act.
		deinit_enqueue();

		// Assert.
		$this->assertFalse( wp_style_is( 'authenticimages-badge-block', 'registered' ) );
	}

	public function test_deregisters_admin_styles(): void {
		// Arrange.
		wp_register_style( 'authenticimages-admin', false, array(), 'test' );

		// Act.
		deinit_enqueue();

		// Assert.
		$this->assertFalse( wp_style_is( 'authenticimages-admin', 'registered' ) );
	}

	public function test_safely_handles_admin_styles_not_registered(): void {
		// Arrange - 'authenticimages-admin' is not registered.

		// Act.
		deinit_enqueue();

		// Assert.
		$this->assertFalse( wp_style_is( 'authenticimages-admin', 'registered' ) );
	}

	public function test_deregisters_admin_editor_styles(): void {
		// Arrange.
		wp_register_style( 'authenticimages-admin-editor', false, array(), 'test' );

		// Act.
		deinit_enqueue();

		// Assert.
		$this->assertFalse( wp_style_is( 'authenticimages-admin-editor', 'registered' ) );
	}

	public function test_safely_handles_admin_editor_styles_not_registered(): void {
		// Arrange - 'authenticimages-admin-editor' is not registered.

		// Act.
		deinit_enqueue();

		// Assert.
		$this->assertFalse( wp_style_is( 'authenticimages-admin-editor', 'registered' ) );
	}

	public function test_deregisters_welcome_page_script(): void {
		// Arrange.
		wp_register_script( 'authenticimages-welcome-page', false, array(), 'test', true );

		// Act.
		deinit_enqueue();

		// Assert.
		$this->assertFalse( wp_script_is( 'authenticimages-welcome-page', 'registered' ) );
	}

	public function test_safely_handles_welcome_page_script_not_registered(): void {
		// Arrange - 'authenticimages-welcome-page' is not registered.

		// Act.
		deinit_enqueue();

		// Assert.
		$this->assertFalse( wp_script_is( 'authenticimages-welcome-page', 'registered' ) );
	}

	public function test_deregisters_build_script(): void {
		// Arrange.
		wp_register_script( 'authenticimages-editor', false, array(), 'test', true );

		// Act.
		deinit_enqueue();

		// Assert.
		$this->assertFalse( wp_script_is( 'authenticimages-editor', 'registered' ) );
	}

	public function test_safely_handles_build_script_not_registered(): void {
		// Arrange - 'authenticimages-editor' is not registered.

		// Act.
		deinit_enqueue();

		// Assert.
		$this->assertFalse( wp_script_is( 'authenticimages-editor', 'registered' ) );
	}

	public function test_deregisters_admin_canvas_script(): void {
		// Arrange.
		wp_register_script( 'authenticimages-admin-canvas-scripts', false, array(), 'test', false );

		// Act.
		deinit_enqueue();

		// Assert.
		$this->assertFalse( wp_script_is( 'authenticimages-admin-canvas-scripts', 'registered' ) );
	}

	public function test_safely_handles_admin_canvas_script_not_registered(): void {
		// Arrange - 'authenticimages-admin-canvas-scripts' is not registered.

		// Act.
		deinit_enqueue();

		// Assert.
		$this->assertFalse( wp_script_is( 'authenticimages-admin-canvas-scripts', 'registered' ) );
	}
}
