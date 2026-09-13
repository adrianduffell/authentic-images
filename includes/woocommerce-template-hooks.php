<?php
/**
 * WooCommerce template hook functions.
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

namespace AuthenticImages;

defined( 'ABSPATH' ) || exit;

/**
 * Helper to initialize classic theme frontend integrations.
 *
 * @internal
 * @throws \InvalidArgumentException When a filter returns an invalid value.
 */
function init_woocommerce_template_hooks(): void {

	/**
	 * Filters the hook used to display the authentic badge.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name The template hook name to display the authentic badge.
	 */
	$single_product_badge_hook = apply_filters(
		'authenticimages_badge_single_product_hook',
		'woocommerce_single_product_summary'
	);

	if ( ! is_string( $single_product_badge_hook ) || '' === $single_product_badge_hook ) {
		throw new \InvalidArgumentException( 'The authenticimages_badge_single_product_hook filter must return a non-empty string.' );
	}

	/**
	 * Filters the priority used in the template hook to display the authentic badge.
	 *
	 * @since 1.0.0
	 *
	 * @param int $priority The priority to display the authentic badge.
	 */
	$single_product_badge_priority = apply_filters(
		'authenticimages_badge_single_product_priority',
		15
	);

	if ( ! is_int( $single_product_badge_priority ) ) {
		throw new \InvalidArgumentException( 'The authenticimages_badge_single_product_priority filter must return an integer.' );
	}

	add_action( $single_product_badge_hook, 'AuthenticImages\display_authentic_badge_hook', $single_product_badge_priority );
	add_action( 'woocommerce_product_meta_start', 'AuthenticImages\display_authentic_message_hook', 1 );
}

/**
 * Output the authentic badge above the product excerpt on single product pages.
 *
 * Fired by `woocommerce_single_product_summary`.
 *
 * @internal WordPress action hook
 */
function display_authentic_badge_hook(): void {
	$product = wc_get_product( get_the_ID() );

	if ( ! $product instanceof \WC_Product ) {
		return;
	}

	$label = get_option( AUTHENTIC_BADGE_LABEL_OPTION );

	if ( ! is_string( $label ) || '' === $label ) {
		return;
	}

	wp_enqueue_style( 'authenticimages-classic-badge' );

	printf(
		'<p class="authenticimages-badge">%s</p>',
		esc_html( $label )
	);
}

/**
 * Display the authentic message in the product meta area on single product pages (classic themes only).
 *
 * Fired by `woocommerce_product_meta_start`.
 *
 * @internal WordPress action hook
 */
function display_authentic_message_hook(): void {
	$product = wc_get_product( get_the_ID() );

	if ( ! $product instanceof \WC_Product ) {
		return;
	}

	$message = get_option( AUTHENTIC_MESSAGE_OPTION );

	if ( ! is_string( $message ) || '' === $message ) {
		return;
	}

	wp_enqueue_style( 'authenticimages-classic-message' );

	echo '<p class="authenticimages-message">' . esc_html( $message ) . '</p>';
}
