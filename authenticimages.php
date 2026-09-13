<?php
/**
 * Plugin Name: Authentic Images
 * Description: Adds AI declaration to WooCommerce. Declare the authenticity of product images with a badge and promo message.
 * Version: 0.1.0-dev
 * Author: Adrian Duffell
 * Author URI: https://adrianduffell.com
 * Text Domain: authenticimages
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires Plugins: woocommerce
 * Requires at least: 7.0
 * Requires PHP: 7.4
 * Update URI: https://adrianduffell.store/authenticimages
 *
 * WC requires at least: 10.8.0
 * WC tested up to: 11.0.0
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

namespace AuthenticImages;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin version.
 *
 * @internal
 */
const VERSION = '0.1.0-dev';

/**
 * Plugin file path.
 *
 * @internal
 */
const PLUGIN_FILE = __FILE__;

require_once __DIR__ . '/includes/activate.php';
require_once __DIR__ . '/includes/system-status.php';
// #ifdef LICENSE
require_once __DIR__ . '/includes/admin-menu-license.php';
// #endif
require_once __DIR__ . '/includes/settings.php';
// #ifdef LICENSE
require_once __DIR__ . '/includes/settings-license.php';
require_once __DIR__ . '/includes/admin-plugins-license.php';
require_once __DIR__ . '/includes/enqueue-license.php';
// #endif
require_once __DIR__ . '/includes/block-editor.php';
require_once __DIR__ . '/includes/blocks.php';
require_once __DIR__ . '/includes/customizer.php';
require_once __DIR__ . '/includes/woocommerce-template-hooks.php';
require_once __DIR__ . '/includes/enqueue.php';
// #ifdef UPDATES
require_once __DIR__ . '/includes/update-plugin.php';
// #endif

/**
 * Initialize the plugin.
 *
 * Fired by `init`.
 *
 * @internal WordPress action hook
 */
function init_hook(): void {
	// #ifdef LICENSE
	init_license_settings();
	// #endif
	// #ifdef UPDATES
	init_update_plugin();
	// #endif
	init_settings();
	init_blocks();
	init_block_editor();

	// #ifdef LICENSE
	if ( is_admin() ) {
		// The admin menu hooks need to run before admin_init.
		init_admin_menu();
	}
	// #endif
	if ( ! wp_is_block_theme() ) {
		init_customizer();
		init_woocommerce_template_hooks();
	}
	enqueue_init();
	// #ifdef LICENSE
	license_enqueue_init();
	// #endif
}

/**
 * Initialize the plugin's wp-admin dashboard features.
 *
 * Fired by `admin_init`.
 *
 * @internal WordPress action hook
 */
function admin_init_hook(): void {
	// #ifdef LICENSE
	init_license();
	// #endif
	init_system_status();
}

/**
 * Register the plugin's init hooks once WooCommerce is active.
 *
 * Fired by `woocommerce_loaded`.
 *
 * @internal WordPress action hook
 */
function woocommerce_loaded_hook(): void {
	add_action( 'init', 'AuthenticImages\init_hook', 20 );
	add_action( 'admin_init', 'AuthenticImages\admin_init_hook' );
}

add_action( 'woocommerce_loaded', 'AuthenticImages\woocommerce_loaded_hook' );

/**
 * Plugin activation hook.
 *
 * @internal
 */
function activate(): void {
	\wc_get_logger()->info( 'Activating Authentic Images plugin.' );

	try {
		seed_activated_at_option();
		seed_settings();
	} catch ( \RuntimeException $e ) {
		\wc_get_logger()->error( $e->getMessage() );
	}
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\activate' );

add_action(
	'before_woocommerce_init',
	function (): void {
		if ( ! class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			return;
		}
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', PLUGIN_FILE, true );
	}
);
