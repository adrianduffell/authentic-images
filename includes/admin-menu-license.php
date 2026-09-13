<?php
/**
 * Admin menu functions for the welcome page.
 *
 * @package AuthenticImages
 * @subpackage License
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

namespace AuthenticImages;

defined( 'ABSPATH' ) || exit;

/**
 * Admin page slug for the welcome page.
 *
 * @internal
 */
const WELCOME_PAGE_SLUG = 'authenticimages-welcome';

/**
 * Cookie used to dismiss the welcome screen on the current device.
 *
 * @internal
 */
const DISMISS_COOKIE = 'AUTHENTICIMAGES_DISMISS_SETUP';

/**
 * Helper to initialize license features.
 *
 * @internal
 */
function init_admin_menu(): void {
	$direct = isset( $_GET['page'] ) && 'authenticimages-welcome' === wp_unslash( $_GET['page'] ); // phpcs:ignore

	if ( isset( $_COOKIE[ DISMISS_COOKIE ] ) && ! $direct ) {
		return;
	}

	$license_status = get_license_status();

	if ( in_array( $license_status, array( 'active', 'error', 'expired' ), true ) && ! $direct ) {
		return;
	}

	add_action( 'admin_menu', 'AuthenticImages\add_welcome_menu_hook' );
}

/**
 * Helper to de-initialize license features back to the uninitialized state.
 *
 * @internal
 */
function deinit_admin_menu(): void {
	remove_action( 'admin_menu', 'AuthenticImages\add_welcome_menu_hook' );
}

/**
 * Register the welcome admin page when no valid license is active.
 *
 * Fired by `admin_menu`.
 *
 * @internal WordPress action hook
 */
function add_welcome_menu_hook(): void {
	add_menu_page(
		__( 'Welcome to Authentic Images', 'authenticimages' ),
		__( 'Authentic Images Setup', 'authenticimages' ),
		'manage_options',
		WELCOME_PAGE_SLUG,
		'AuthenticImages\render_welcome_page',
		'dashicons-admin-generic',
		0
	);
}

/**
 * Render the welcome page.
 *
 * @internal
 */
function render_welcome_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<div id="authenticimages-welcome-page-root"></div>
	</div>
	<?php
}
