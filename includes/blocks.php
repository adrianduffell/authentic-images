<?php
/**
 * Block registration and render callbacks.
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

namespace AuthenticImages;

defined( 'ABSPATH' ) || exit;

/**
 * Helper to initialize block registrations.
 *
 * @internal
 */
function init_blocks(): void {
	register_authentic_image_disclosure_block();
	register_authentic_badge_block();
	register_authentic_message_block();
	add_filter( 'hooked_block_types', 'AuthenticImages\auto_insert_authentic_badge_hook', 10, 4 );
	add_filter( 'hooked_block_types', 'AuthenticImages\auto_insert_authentic_message_hook', 10, 4 );
}

/**
 * Helper to de-initialize blocks back to the uninitialized state.
 *
 * @internal
 */
function deinit_blocks(): void {
	$registry = \WP_Block_Type_Registry::get_instance();

	// Unregister all blocks in the authenticimages namespace.
	foreach ( $registry->get_all_registered() as $block_name => $block_type ) {
		if ( 0 !== strpos( $block_name, 'authenticimages/' ) ) {
			continue;
		}

		unregister_block_type( $block_name );
	}
}

/**
 * Register the Authentic Image Disclosure block type.
 *
 * @internal
 */
function register_authentic_image_disclosure_block(): void {
	register_block_type( plugin_dir_path( __DIR__ ) . 'build/blocks/authentic-image-disclosure/' );
}

/**
 * Register the authentic badge block type.
 *
 * @internal
 */
function register_authentic_badge_block(): void {
	register_block_type(
		plugin_dir_path( __DIR__ ) . 'build/blocks/authentic-badge/',
		array(
			'render_callback' => 'AuthenticImages\render_authentic_badge_callback',
		)
	);
}

/**
 * Auto-insert the authentic badge block after the product price on the single product template.
 *
 * @internal WordPress filter hook
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 * @param string[]                      $hooked_blocks     Block names hooked to the anchor at this position.
 * @param string                        $relative_position Position relative to the anchor block.
 * @param string                        $anchor_block      Anchor block name.
 * @param \WP_Block_Template|array|null $context Block template or post context, or null.
 * @return string[] Filtered hooked block names.
 */
function auto_insert_authentic_badge_hook( $hooked_blocks, $relative_position, $anchor_block, $context ): array {
	if ( 'woocommerce/product-price' !== $anchor_block || 'after' !== $relative_position ) {
		return $hooked_blocks;
	}

	// Only auto-insert the badge on the single product template.
	if ( $context instanceof \WP_Block_Template && 'single-product' === $context->slug ) {
		$hooked_blocks[] = 'authenticimages/authentic-badge';
	}

	return $hooked_blocks;
}

/**
 * Auto-insert the authentic message block as the first child of the product meta block on the single product template.
 *
 * @internal WordPress filter hook
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 * @param string[]                      $hooked_blocks     Block names hooked to the anchor at this position.
 * @param string                        $relative_position Position relative to the anchor block.
 * @param string                        $anchor_block      Anchor block name.
 * @param \WP_Block_Template|array|null $context Block template or post context, or null.
 * @return string[] Filtered hooked block names.
 */
function auto_insert_authentic_message_hook( $hooked_blocks, $relative_position, $anchor_block, $context ): array {
	if ( 'woocommerce/product-meta' !== $anchor_block || 'first_child' !== $relative_position ) {
		return $hooked_blocks;
	}

	// Only auto-insert the message on the single product template.
	if ( $context instanceof \WP_Block_Template && 'single-product' === $context->slug ) {
		$hooked_blocks[] = 'authenticimages/authentic-message';
	}

	return $hooked_blocks;
}

/**
 * Render callback for the authentic badge block.
 *
 * @internal
 * @param array<string, mixed> $attributes Block attributes.
 * @param string               $_content   Block inner content (unused).
 * @param \WP_Block            $block      Block instance.
 * @return string Rendered HTML.
 */
function render_authentic_badge_callback( array $attributes, string $_content, \WP_Block $block ): string { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	$product_id = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : 0;

	if ( ! $product_id ) {
		return '';
	}

	$product = wc_get_product( $product_id );

	if ( ! $product instanceof \WC_Product ) {
		return '';
	}

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => 'authenticimages-badge',
		)
	);

	$label = get_option( AUTHENTIC_BADGE_LABEL_OPTION );

	if ( ! is_string( $label ) || '' === $label ) {
		return '';
	}

	return sprintf(
		'<div %1$s>%2$s</div>',
		$wrapper_attributes,
		wp_kses_post( $label )
	);
}

/**
 * Register the authentic message block type.
 *
 * @internal
 */
function register_authentic_message_block(): void {
	register_block_type(
		plugin_dir_path( __DIR__ ) . 'build/blocks/authentic-message/',
		array(
			'render_callback' => 'AuthenticImages\render_authentic_message_callback',
		)
	);
}

/**
 * Render callback for the authentic message block.
 *
 * @internal
 * @param array<string, mixed> $attributes Block attributes.
 * @param string               $_content   Block inner content (unused).
 * @param \WP_Block            $block      Block instance.
 * @return string Rendered HTML.
 */
function render_authentic_message_callback( array $attributes, string $_content, \WP_Block $block ): string { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	$product_id = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : 0;

	if ( ! $product_id ) {
		return '';
	}

	$product = wc_get_product( $product_id );

	if ( ! $product instanceof \WC_Product ) {
		return '';
	}

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => 'authenticimages-message',
		)
	);

	$message = get_option( AUTHENTIC_MESSAGE_OPTION );

	if ( ! is_string( $message ) || '' === $message ) {
		return '';
	}

	return sprintf(
		'<p %1$s>%2$s</p>',
		$wrapper_attributes,
		wp_kses_post( $message )
	);
}
