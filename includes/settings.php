<?php
/**
 * Settings functions.
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

namespace AuthenticImages;

defined( 'ABSPATH' ) || exit;

/**
 * WordPress option key used to store the authentic message.
 *
 * @internal
 */
const AUTHENTIC_MESSAGE_OPTION = 'authenticimages_message';

/**
 * WordPress option key used to store the badge label text.
 *
 * @internal
 */
const AUTHENTIC_BADGE_LABEL_OPTION = 'authenticimages_badge_label';

/**
 * WordPress option key used to store the badge text color.
 *
 * @internal
 */
const AUTHENTIC_BADGE_TEXT_COLOR_OPTION = 'authenticimages_badge_text_color';

/**
 * WordPress option key used to store the badge background color.
 *
 * @internal
 */
const AUTHENTIC_BADGE_BG_COLOR_OPTION = 'authenticimages_badge_bg_color';

/**
 * WordPress option key used to store the badge border radius.
 *
 * @internal
 */
const AUTHENTIC_BADGE_BORDER_RADIUS_OPTION = 'authenticimages_badge_border_radius';

/**
 * WordPress option key used to store the badge border color.
 *
 * @internal
 */
const AUTHENTIC_BADGE_BORDER_COLOR_OPTION = 'authenticimages_badge_border_color';

/**
 * WordPress option key used to store the badge border style.
 *
 * @internal
 */
const AUTHENTIC_BADGE_BORDER_STYLE_OPTION = 'authenticimages_badge_border_style';

/**
 * WordPress option key used to store the badge border width.
 *
 * @internal
 */
const AUTHENTIC_BADGE_BORDER_WIDTH_OPTION = 'authenticimages_badge_border_width';

/**
 * WordPress option key used to store the badge font weight.
 *
 * @internal
 */
const AUTHENTIC_BADGE_FONT_WEIGHT_OPTION = 'authenticimages_badge_font_weight';

/**
 * WordPress option key used to store the badge scale.
 *
 * @internal
 */
const AUTHENTIC_BADGE_SCALE_OPTION = 'authenticimages_badge_scale';

/**
 * WordPress option key used to store the badge density.
 *
 * @internal
 */
const AUTHENTIC_BADGE_DENSITY_OPTION = 'authenticimages_badge_density';

/**
 * Sanitize a CSS property value, rejecting values that contain CSS block delimiters or
 * values that fail sanitize_text_field().
 *
 * Intentionally light-weight as CSS is broad and evolving.
 *
 * Accepts int and float in addition to string so that callers may pass numeric
 * CSS values (e.g. a unitless scale factor) without first converting them.
 * The numeric value is converted to its string representation before the
 * remaining sanitization steps run.
 *
 * @internal
 *
 * @param mixed $value The CSS property value to sanitize. Accepts string, int, or float.
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
 */
function sanitize_css_value( $value ): string {
	if ( is_int( $value ) || ( is_float( $value ) && is_finite( $value ) ) ) {
		// Convert numeric types to string for the CSS pipeline.
		$value = (string) $value;
	}

	if ( ! is_string( $value ) ) {
		return '';
	}

	$value = sanitize_text_field( $value );

	if (
		false !== strpos( $value, ';' ) ||
		false !== strpos( $value, '{' ) ||
		false !== strpos( $value, '}' )
	) {
		return '';
	}

	return $value;
}

/**
 * Sanitize an unsigned integer value.
 *
 * Expects an integer > 0, or null, passed as an int, string, or float.
 * All other values return null.
 *
 * Fractional floats are normalized to int.
 *
 * @internal
 *
 * @param mixed $value The value to sanitize.
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
 */
function sanitize_unsigned_integer( $value ): ?int {
	if ( is_null( $value ) ) {
		return null;
	}

	if ( ! is_scalar( $value ) ) {
		return null;
	}

	if ( $value < 0 ) {
		return null;
	}

	if ( is_int( $value ) ) {
		return $value;
	}

	if ( is_string( $value ) && ctype_digit( $value ) ) {
		return (int) $value;
	}

	if ( is_float( $value ) && is_finite( $value ) ) {
		return (int) $value;
	}

	return null;
}

/**
 * Check whether the settings screen is enabled.
 *
 * @internal
 */
function settings_screen_enabled(): bool {
	return (bool) apply_filters( 'authenticimages_settings_screen_enabled', false );
}

/**
 * Helper to initialize settings.
 *
 * @internal
 */
function init_settings(): void {
	register_authentic_badge_label_setting();
	register_authentic_badge_text_color_setting();
	register_authentic_badge_bg_color_setting();
	register_authentic_badge_border_color_setting();
	register_authentic_badge_border_style_setting();
	register_authentic_badge_border_width_setting();
	register_authentic_badge_border_radius_setting();
	register_authentic_badge_font_weight_setting();
	register_authentic_badge_scale_setting();
	register_authentic_badge_density_setting();
	register_authentic_message_setting();
}

/**
 * Seed option values with defaults.
 *
 * Uses add_option() so that existing values are never overwritten. This
 * preserves the default at the time of installation even if defaults change
 * in future versions.
 *
 * @internal
 */
function seed_settings(): void {
	add_option( AUTHENTIC_BADGE_LABEL_OPTION, __( 'Authentic', 'authenticimages' ) );
	add_option( AUTHENTIC_BADGE_TEXT_COLOR_OPTION, '#111111' );
	add_option( AUTHENTIC_BADGE_BG_COLOR_OPTION, '#D3AF37' );
	add_option( AUTHENTIC_BADGE_BORDER_COLOR_OPTION, '' );
	add_option( AUTHENTIC_BADGE_BORDER_STYLE_OPTION, 'none' );
	add_option( AUTHENTIC_BADGE_BORDER_WIDTH_OPTION, '0' );
	add_option( AUTHENTIC_BADGE_BORDER_RADIUS_OPTION, '2px' );
	add_option( AUTHENTIC_BADGE_FONT_WEIGHT_OPTION, '600' );
	add_option( AUTHENTIC_BADGE_SCALE_OPTION, 166 );
	add_option( AUTHENTIC_BADGE_DENSITY_OPTION, 50 );
	add_option( AUTHENTIC_MESSAGE_OPTION, __( 'Our product images are not AI-generated', 'authenticimages' ) );
}

/**
 * Register the authentic badge label setting.
 *
 * @internal
 */
function register_authentic_badge_label_setting(): void {
	register_setting(
		'authenticimages',
		AUTHENTIC_BADGE_LABEL_OPTION,
		array(
			'type'              => 'string',
			'label'             => __( 'Authentic badge label', 'authenticimages' ),
			'description'       => __( 'Store-wide authentic badge label.', 'authenticimages' ),
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
				),
			),
		)
	);
}

/**
 * Register the authentic badge text color setting.
 *
 * @since 1.0.0
 */
function register_authentic_badge_text_color_setting(): void {
	register_setting(
		'authenticimages',
		AUTHENTIC_BADGE_TEXT_COLOR_OPTION,
		array(
			'type'              => 'string',
			'label'             => __( 'Authentic badge text color', 'authenticimages' ),
			'description'       => __( 'Store-wide authentic badge text color.', 'authenticimages' ),
			'default'           => '',
			'sanitize_callback' => 'sanitize_hex_color',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
				),
			),
		)
	);
}

/**
 * Register the authentic badge background color setting.
 *
 * @since 1.0.0
 */
function register_authentic_badge_bg_color_setting(): void {
	register_setting(
		'authenticimages',
		AUTHENTIC_BADGE_BG_COLOR_OPTION,
		array(
			'type'              => 'string',
			'label'             => __( 'Authentic badge background color', 'authenticimages' ),
			'description'       => __( 'Store-wide authentic badge background color.', 'authenticimages' ),
			'default'           => '',
			'sanitize_callback' => 'sanitize_hex_color',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
				),
			),
		)
	);
}

/**
 * Register the authentic badge border radius setting.
 *
 * @internal
 */
function register_authentic_badge_border_radius_setting(): void {
	register_setting(
		'authenticimages',
		AUTHENTIC_BADGE_BORDER_RADIUS_OPTION,
		array(
			'type'              => 'string',
			'label'             => __( 'Authentic badge border radius', 'authenticimages' ),
			'description'       => __( 'Store-wide authentic badge border radius.', 'authenticimages' ),
			'default'           => '',
			'sanitize_callback' => 'AuthenticImages\sanitize_css_value',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
				),
			),
		)
	);
}

/**
 * Register the authentic badge border color setting.
 *
 * @internal
 */
function register_authentic_badge_border_color_setting(): void {
	register_setting(
		'authenticimages',
		AUTHENTIC_BADGE_BORDER_COLOR_OPTION,
		array(
			'type'              => 'string',
			'label'             => __( 'Authentic badge border color', 'authenticimages' ),
			'description'       => __( 'Store-wide authentic badge border color.', 'authenticimages' ),
			'default'           => '',
			'sanitize_callback' => 'AuthenticImages\sanitize_css_value',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
				),
			),
		)
	);
}

/**
 * Register the authentic badge border style setting.
 *
 * @internal
 */
function register_authentic_badge_border_style_setting(): void {
	register_setting(
		'authenticimages',
		AUTHENTIC_BADGE_BORDER_STYLE_OPTION,
		array(
			'type'              => 'string',
			'label'             => __( 'Authentic badge border style', 'authenticimages' ),
			'description'       => __( 'Store-wide authentic badge border style.', 'authenticimages' ),
			'default'           => '',
			'sanitize_callback' => 'AuthenticImages\sanitize_css_value',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
				),
			),
		)
	);
}

/**
 * Register the authentic badge border width setting.
 *
 * @internal
 */
function register_authentic_badge_border_width_setting(): void {
	register_setting(
		'authenticimages',
		AUTHENTIC_BADGE_BORDER_WIDTH_OPTION,
		array(
			'type'              => 'string',
			'label'             => __( 'Authentic badge border width', 'authenticimages' ),
			'description'       => __( 'Store-wide authentic badge border width.', 'authenticimages' ),
			'default'           => '',
			'sanitize_callback' => 'AuthenticImages\sanitize_css_value',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
				),
			),
		)
	);
}

/**
 * Register the authentic badge font weight setting.
 *
 * @internal
 */
function register_authentic_badge_font_weight_setting(): void {
	register_setting(
		'authenticimages',
		AUTHENTIC_BADGE_FONT_WEIGHT_OPTION,
		array(
			'type'              => 'string',
			'label'             => __( 'Authentic badge font weight', 'authenticimages' ),
			'description'       => __( 'Store-wide authentic badge font weight.', 'authenticimages' ),
			'default'           => '',
			'sanitize_callback' => 'AuthenticImages\sanitize_css_value',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
				),
			),
		)
	);
}

/**
 * Register the authentic badge scale setting.
 *
 * @internal
 */
function register_authentic_badge_scale_setting(): void {
	register_setting(
		'authenticimages',
		AUTHENTIC_BADGE_SCALE_OPTION,
		array(
			'type'              => 'integer',
			'label'             => __( 'Authentic badge scale', 'authenticimages' ),
			'description'       => __( 'Percentage size of the authentic badge relative to the surrounding text cap-height.', 'authenticimages' ),
			'default'           => null,
			'sanitize_callback' => 'AuthenticImages\sanitize_unsigned_integer',
			'show_in_rest'      => array(
				'schema' => array(
					'type'    => 'integer',
					'minimum' => 0,
				),
			),
		)
	);
}

/**
 * Register the authentic badge density setting.
 *
 * @internal
 */
function register_authentic_badge_density_setting(): void {
	register_setting(
		'authenticimages',
		AUTHENTIC_BADGE_DENSITY_OPTION,
		array(
			'type'              => 'integer',
			'label'             => __( 'Authentic badge density', 'authenticimages' ),
			'description'       => __( 'Controls the ratio between font size and padding for the badge. A lower density results in more whitespace. A Higher density results in a larger font.', 'authenticimages' ),
			'default'           => null,
			'sanitize_callback' => 'AuthenticImages\sanitize_unsigned_integer',
			'show_in_rest'      => array(
				'schema' => array(
					'type'    => 'integer',
					'minimum' => 0,
					'maximum' => 100,
				),
			),
		)
	);
}

/**
 * Register the authentic message setting.
 *
 * @internal
 */
function register_authentic_message_setting(): void {
	register_setting(
		'authenticimages',
		AUTHENTIC_MESSAGE_OPTION,
		array(
			'type'              => 'string',
			'label'             => __( 'Authentic message', 'authenticimages' ),
			'description'       => __( 'Message displayed on product pages.', 'authenticimages' ),
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
				),
			),
		)
	);
}
