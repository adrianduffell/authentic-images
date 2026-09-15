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
 */
function init_woocommerce_template_hooks(): void {
	add_action( 'woocommerce_after_template_part', 'AuthenticImages\display_authentic_image_notice_hook', 10, 1 );
}

/**
 * Display the authentic image notice after the classic product image gallery template.
 *
 * Fired by `woocommerce_after_template_part`.
 *
 * @internal WordPress action hook
 * @param string $template_name Template part name.
 */
function display_authentic_image_notice_hook( string $template_name ): void {
	if ( 'single-product/product-image.php' !== $template_name ) {
		return;
	}

	$product = wc_get_product( get_the_ID() );

	if ( ! $product instanceof \WC_Product ) {
		return;
	}

	$label = get_option( AUTHENTIC_BADGE_LABEL_OPTION );

	if ( ! is_string( $label ) ) {
		$label = '';
	}

	$message = get_option( AUTHENTIC_MESSAGE_OPTION );

	if ( ! is_string( $message ) ) {
		$message = '';
	}

	if ( '' === $label && '' === $message ) {
		return;
	}

	wp_enqueue_style( 'authenticimages-classic-badge' );
	wp_enqueue_style( 'authenticimages-classic-message' );
	wp_enqueue_style( 'authenticimages-classic-notice' );
	wp_enqueue_script( 'authenticimages-classic-notice-scripts' );

	echo '<p class="authenticimages-notice">';

	if ( '' !== $label ) {
		printf( '<span class="authenticimages-badge">%s</span>', esc_html( $label ) );
	}

	if ( '' !== $message ) {
		printf( '<span class="authenticimages-message">%s</span>', esc_html( $message ) );
	}

	echo '</p>';
}
