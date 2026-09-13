<?php
/**
 * Tests for output_badge_style_css_variables_hook().
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function AuthenticImages\deinit_enqueue;
use function AuthenticImages\enqueue_init;
use const AuthenticImages\AUTHENTIC_BADGE_BG_COLOR_OPTION;
use const AuthenticImages\AUTHENTIC_BADGE_BORDER_COLOR_OPTION;
use const AuthenticImages\AUTHENTIC_BADGE_BORDER_RADIUS_OPTION;
use const AuthenticImages\AUTHENTIC_BADGE_BORDER_STYLE_OPTION;
use const AuthenticImages\AUTHENTIC_BADGE_BORDER_WIDTH_OPTION;
use const AuthenticImages\AUTHENTIC_BADGE_DENSITY_OPTION;
use const AuthenticImages\AUTHENTIC_BADGE_FONT_WEIGHT_OPTION;
use const AuthenticImages\AUTHENTIC_BADGE_SCALE_OPTION;
use const AuthenticImages\AUTHENTIC_BADGE_TEXT_COLOR_OPTION;

class Test_Output_Badge_Style_Css_Variables_Hook extends WP_UnitTestCase {

	public function test_outputs_badge_style_css_variables_in_wp_head(): void {
		// Arrange.
		update_option( AUTHENTIC_BADGE_BG_COLOR_OPTION, '#FF0000' );
		update_option( AUTHENTIC_BADGE_TEXT_COLOR_OPTION, '#00FF00' );
		update_option( AUTHENTIC_BADGE_BORDER_COLOR_OPTION, '#123456' );
		update_option( AUTHENTIC_BADGE_BORDER_STYLE_OPTION, 'solid' );
		update_option( AUTHENTIC_BADGE_BORDER_WIDTH_OPTION, '2px' );
		update_option( AUTHENTIC_BADGE_BORDER_RADIUS_OPTION, '4px' );
		update_option( AUTHENTIC_BADGE_FONT_WEIGHT_OPTION, '700' );
		update_option( AUTHENTIC_BADGE_SCALE_OPTION, 140 );
		update_option( AUTHENTIC_BADGE_DENSITY_OPTION, 80 );

		deinit_enqueue();
		enqueue_init();

		// Act.
		ob_start();
		do_action( 'wp_head' );
		$output = ob_get_clean();

		// Assert.
		$this->assertStringContainsString( '--authenticimages-badge-bg-color: #FF0000', $output );
		$this->assertStringContainsString( '--authenticimages-badge-text-color: #00FF00', $output );
		$this->assertStringContainsString( '--authenticimages-badge-border-color: #123456', $output );
		$this->assertStringContainsString( '--authenticimages-badge-border-style: solid', $output );
		$this->assertStringContainsString( '--authenticimages-badge-border-width: 2px', $output );
		$this->assertStringContainsString( '--authenticimages-badge-border-radius: 4px', $output );
		$this->assertStringContainsString( '--authenticimages-badge-font-weight: 700', $output );
		$this->assertStringContainsString( '--authenticimages-badge-scale: 140', $output );
		$this->assertStringContainsString( '--authenticimages-badge-density: 80', $output );
	}

	public function test_uses_unset_when_setting_value_is_empty(): void {
		// Arrange.
		update_option( AUTHENTIC_BADGE_BG_COLOR_OPTION, '' );
		deinit_enqueue();
		enqueue_init();

		// Assert.
		$this->expectOutputRegex( '/--authenticimages-badge-bg-color: unset/' );

		// Act.
		do_action( 'wp_head' );
	}
}
