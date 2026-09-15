<?php
/**
 * Enqueue functions.
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

namespace AuthenticImages;

defined( 'ABSPATH' ) || exit;

/**
 * Helper to initialize enqueue registrations.
 *
 * @internal
 */
function enqueue_init(): void {
	add_action( 'wp_enqueue_scripts', 'AuthenticImages\register_classic_styles_hook' );
	add_action( 'wp_enqueue_scripts', 'AuthenticImages\register_classic_scripts_hook' );
	add_action( 'enqueue_block_assets', 'AuthenticImages\enqueue_admin_canvas_scripts_hook' );
	add_action( 'wp_head', 'AuthenticImages\output_badge_style_css_variables_hook' );
	add_action( 'admin_enqueue_scripts', 'AuthenticImages\enqueue_admin_styles_hook' );
	add_action( 'enqueue_block_editor_assets', 'AuthenticImages\enqueue_build_assets_hook' );

	register_block_styles();
}

/**
 * Helper to de-initialize enqueue registrations back to the uninitialized state.
 *
 * @internal
 */
function deinit_enqueue(): void {
	remove_action( 'wp_enqueue_scripts', 'AuthenticImages\register_classic_styles_hook' );
	remove_action( 'wp_enqueue_scripts', 'AuthenticImages\register_classic_scripts_hook' );
	remove_action( 'enqueue_block_assets', 'AuthenticImages\enqueue_admin_canvas_scripts_hook' );
	remove_action( 'wp_head', 'AuthenticImages\output_badge_style_css_variables_hook' );
	remove_action( 'admin_enqueue_scripts', 'AuthenticImages\enqueue_admin_styles_hook' );
	remove_action( 'admin_enqueue_scripts', 'AuthenticImages\enqueue_admin_welcome_page_scripts_hook' );
	remove_action( 'enqueue_block_editor_assets', 'AuthenticImages\enqueue_build_assets_hook' );
	wp_deregister_style( 'authenticimages-classic-badge' );
	wp_deregister_style( 'authenticimages-classic-message' );
	wp_deregister_style( 'authenticimages-classic-notice' );
	wp_dequeue_script( 'authenticimages-classic-notice-scripts' );
	wp_deregister_script( 'authenticimages-classic-notice-scripts' );
	wp_dequeue_style( 'authenticimages-admin' );
	wp_deregister_style( 'authenticimages-admin' );
	wp_dequeue_style( 'authenticimages-admin-editor' );
	wp_deregister_style( 'authenticimages-admin-editor' );
	wp_dequeue_script( 'authenticimages-admin-canvas-scripts' );
	wp_deregister_script( 'authenticimages-admin-canvas-scripts' );
	wp_dequeue_script( 'authenticimages-welcome-page' );
	wp_deregister_script( 'authenticimages-welcome-page' );
	wp_dequeue_script( 'authenticimages-editor' );
	wp_deregister_script( 'authenticimages-editor' );
	wp_dequeue_style( 'authenticimages-badge-block' );
	wp_deregister_style( 'authenticimages-badge-block' );
}

/**
 * Enqueue admin canvas scripts.
 *
 * Fired by `enqueue_block_assets`.
 *
 * @internal WordPress action hook
 */
function enqueue_admin_canvas_scripts_hook(): void {
	if ( ! is_admin() ) {
		return;
	}

	/**
	 * Admin canvas scripts.
	 *
	 * @internal
	 */
	wp_enqueue_script(
		'authenticimages-admin-canvas-scripts',
		plugin_dir_url( PLUGIN_FILE ) . 'assets/js/admin-canvas.js',
		array(),
		VERSION,
		false
	);
}

/**
 * Register classic theme front-end stylesheets.
 *
 * Fired by `wp_enqueue_scripts`.
 *
 * @internal WordPress action hook
 */
function register_classic_styles_hook(): void {
	/**
	 * Classic theme front-end badge stylesheet.
	 *
	 * @since 1.0.0
	 */
	wp_register_style(
		'authenticimages-classic-badge',
		plugin_dir_url( PLUGIN_FILE ) . 'assets/css/classic-badge.css',
		array(),
		VERSION
	);

	/**
	 * Classic theme front-end message stylesheet.
	 *
	 * @since 1.0.0
	 */
	wp_register_style(
		'authenticimages-classic-message',
		plugin_dir_url( PLUGIN_FILE ) . 'assets/css/classic-message.css',
		array(),
		VERSION
	);

	/**
	 * Classic theme front-end notice stylesheet.
	 *
	 * @since 1.0.0
	 */
	wp_register_style(
		'authenticimages-classic-notice',
		plugin_dir_url( PLUGIN_FILE ) . 'assets/css/classic-notice.css',
		array(),
		VERSION
	);
}

/**
 * Register classic theme front-end scripts.
 *
 * Fired by `wp_enqueue_scripts`.
 *
 * @internal WordPress action hook
 */
function register_classic_scripts_hook(): void {
	/**
	 * Classic theme front-end notice script.
	 *
	 * @since 1.0.0
	 */
	wp_register_script(
		'authenticimages-classic-notice-scripts',
		plugin_dir_url( PLUGIN_FILE ) . 'assets/js/classic-notice.js',
		array( 'jquery', 'wc-single-product' ),
		VERSION,
		true
	);
}

/**
 * Output badge style settings as CSS variables on front-end pages.
 *
 * Fired by `wp_head`.
 *
 * @internal WordPress action hook
 */
function output_badge_style_css_variables_hook(): void {
	$badge_style_options = array(
		AUTHENTIC_BADGE_BG_COLOR_OPTION,
		AUTHENTIC_BADGE_TEXT_COLOR_OPTION,
		AUTHENTIC_BADGE_BORDER_COLOR_OPTION,
		AUTHENTIC_BADGE_BORDER_STYLE_OPTION,
		AUTHENTIC_BADGE_BORDER_WIDTH_OPTION,
		AUTHENTIC_BADGE_BORDER_RADIUS_OPTION,
		AUTHENTIC_BADGE_FONT_WEIGHT_OPTION,
		AUTHENTIC_BADGE_SCALE_OPTION,
		AUTHENTIC_BADGE_DENSITY_OPTION,
	);

	$declarations = array_map(
		function ( string $option_name ): string {
			$variable_name = '--' . str_replace( '_', '-', $option_name );
			$option_value  = sanitize_css_value( get_option( $option_name, '' ) );

			return $variable_name . ': ' . ( '' !== $option_value ? $option_value : 'unset' );
		},
		$badge_style_options
	);

	echo '<style>:root { ' . esc_html( implode( '; ', $declarations ) ) . '; }</style>';
}

/**
 * Register the block stylesheet so it only loads when the authentic badge block is rendered.
 *
 * The message block doesn't have any styles currently.
 *
 * @internal
 */
function register_block_styles(): void {
	$asset_file = plugin_dir_path( PLUGIN_FILE ) . 'build/index.asset.php';

	if ( ! file_exists( $asset_file ) ) {
		return;
	}

	$asset = require $asset_file;

	/**
	 * Block stylesheet for the authentic badge block.
	 *
	 * @internal
	 */
	wp_enqueue_block_style(
		'authenticimages/authentic-badge',
		array(
			'handle' => 'authenticimages-badge-block',
			'src'    => plugin_dir_url( PLUGIN_FILE ) . 'build/style-index.css',
			'deps'   => array(),
			'ver'    => $asset['version'],
		)
	);
}

/**
 * Enqueue admin-specific stylesheets.
 *
 * Fired by `admin_enqueue_scripts`.
 *
 * @internal WordPress action hook
 */
function enqueue_admin_styles_hook(): void {
	/**
	 * Admin stylesheet.
	 *
	 * @internal
	 */
	wp_enqueue_style(
		'authenticimages-admin',
		plugin_dir_url( PLUGIN_FILE ) . 'assets/css/admin.css',
		array(),
		VERSION
	);
}

/**
 * Enqueue the built JavaScript for the block editor.
 *
 * Fired by `enqueue_block_editor_assets`.
 *
 * @internal WordPress action hook
 */
function enqueue_build_assets_hook(): void {
	$asset_file = plugin_dir_path( PLUGIN_FILE ) . 'build/index.asset.php';

	if ( ! file_exists( $asset_file ) ) {
		return;
	}

	$asset = require $asset_file;

	// Remove wp-editor because it causes a conflict in non-block-editor
	// contexts (e.g. widgets screen) where wp-editor is not normally loaded,
	// and is guaranteed to be loaded in the block editor context anyway.
	$deps = array_diff( $asset['dependencies'], array( 'wp-editor' ) );

	/**
	 * Block editor script.
	 *
	 * @internal
	 */
	wp_enqueue_script(
		'authenticimages-editor',
		plugin_dir_url( PLUGIN_FILE ) . 'build/index.js',
		array_merge( $deps, array( 'wc-blocks-registry' ) ),
		$asset['version'],
		true
	);
}
