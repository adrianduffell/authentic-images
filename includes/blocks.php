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

const GALLERY_BLOCKS = array( 'woocommerce/product-image-gallery', 'woocommerce/product-gallery' );

/**
 * Helper to initialize block registrations.
 *
 * @internal
 */
function init_blocks(): void {
	register_authentic_image_notice_block();
	register_authentic_badge_block();
	register_authentic_message_block();
	add_filter( 'hooked_block_types', 'AuthenticImages\auto_insert_notice_hook', 10, 4 );
	add_filter( 'hooked_block_authenticimages/authentic-image-notice', 'AuthenticImages\seed_auto_inserted_notice_hook', 10, 5 );
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
 * Register the Authentic Image Notice block type.
 *
 * @internal
 */
function register_authentic_image_notice_block(): void {
	register_block_type( plugin_dir_path( __DIR__ ) . 'build/blocks/authentic-image-notice/' );
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
 * Auto-insert the authentic image notice after supported product gallery blocks.
 *
 * Fired by `hooked_block_types`.
 *
 * @internal WordPress filter hook
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 * @param string[]                      $hooked_blocks     Block names hooked to the anchor at this position.
 * @param string                        $relative_position Position relative to the anchor block.
 * @param string                        $anchor_block      Anchor block name.
 * @param \WP_Block_Template|array|null $context Block template or post context, or null.
 * @return string[] Filtered hooked block names.
 */
function auto_insert_notice_hook( $hooked_blocks, $relative_position, $anchor_block, $context ): array {
	if ( ! in_array( $anchor_block, GALLERY_BLOCKS, true ) ) {
		return $hooked_blocks;
	}

	if ( 'after' !== $relative_position ) {
		return $hooked_blocks;
	}

	if ( ! $context instanceof \WP_Block_Template ) {
		return $hooked_blocks;
	}

	if ( 'single-product' !== $context->slug ) {
		return $hooked_blocks;
	}

	$hooked_blocks[] = 'authenticimages/authentic-image-notice';

	return $hooked_blocks;
}

/**
 * Seed the authentic images block with attributes and badge/message inner blocks on auto-insertion.
 *
 * Fired by `hooked_block_authenticimages/authentic-image-notice`.
 *
 * @internal WordPress filter hook
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 * @param array<string, mixed>|null     $parsed_hooked_block Parsed hooked container block, or null when suppressed.
 * @param string                        $hooked_block_type   Hooked block name.
 * @param string                        $relative_position   Position relative to the anchor block.
 * @param array<string, mixed>          $parsed_anchor_block Parsed anchor block.
 * @param \WP_Block_Template|array|null $context             Block template or post context, or null.
 * @return array<string, mixed>|null Seeded container block, unchanged block, or null.
 */
function seed_auto_inserted_notice_hook( $parsed_hooked_block, $hooked_block_type, $relative_position, $parsed_anchor_block, $context ): ?array {
	if ( null === $parsed_hooked_block ) {
		return null;
	}

	if ( 'authenticimages/authentic-image-notice' !== $hooked_block_type ) {
		return $parsed_hooked_block;
	}

	if ( 'after' !== $relative_position ) {
		return $parsed_hooked_block;
	}

	if ( ! isset( $parsed_anchor_block['blockName'] ) ) {
		return $parsed_hooked_block;
	}

	if ( ! in_array( $parsed_anchor_block['blockName'], GALLERY_BLOCKS, true ) ) {
		return $parsed_hooked_block;
	}

	if ( ! $context instanceof \WP_Block_Template ) {
		return $parsed_hooked_block;
	}

	if ( 'single-product' !== $context->slug ) {
		return $parsed_hooked_block;
	}

	$parsed_hooked_block['attrs']['layout'] = array(
		'type'              => 'flex',
		'flexWrap'          => 'nowrap',
		'justifyContent'    => 'left',
		'verticalAlignment' => 'top',
	);

	$parsed_hooked_block['attrs']['style']['spacing']['blockGap'] = '0.33em';

	$parsed_hooked_block['innerBlocks']  = array(
		array(
			'blockName'    => 'authenticimages/authentic-badge',
			'attrs'        => array(),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		),
		array(
			'blockName'    => 'authenticimages/authentic-message',
			'attrs'        => array(),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		),
	);
	$parsed_hooked_block['innerHTML']    = '';
	$parsed_hooked_block['innerContent'] = array(
		'<div class="wp-block-authenticimages-authentic-image-notice authenticimages-notice">',
		null,
		null,
		'</div>',
	);

	return $parsed_hooked_block;
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
