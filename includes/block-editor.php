<?php
/**
 * Block editor integration functions.
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

namespace AuthenticImages;

defined( 'ABSPATH' ) || exit;

/**
 * Helper to initialize block editor integrations.
 *
 * @internal
 */
function init_block_editor(): void {
	add_filter( 'block_editor_settings_all', 'AuthenticImages\append_block_editor_settings_hook', 10, 2 );
}

/**
 * Append data to the block editor settings.
 *
 * - authenticimagesIsBlockTheme: boolean indicating if the current theme is a block theme.
 *
 * Fired by `block_editor_settings_all`.
 *
 * @internal WordPress filter hook
 *
 * @param array<string, mixed>     $settings Existing block editor settings.
 * @param \WP_Block_Editor_Context $_context The block editor context.
 * @return array<string, mixed> Modified block editor settings.
 */
function append_block_editor_settings_hook( array $settings, \WP_Block_Editor_Context $_context ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	$settings['authenticimagesIsBlockTheme'] = wp_is_block_theme();

	return $settings;
}
