<?php
/**
 * Customizer integration functions.
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

namespace AuthenticImages;

defined( 'ABSPATH' ) || exit;

/**
 * Default badge text colour (dark).
 *
 * @internal
 */
const AUTHENTIC_BADGE_TEXT_COLOUR_DEFAULT = '#111111';

/**
 * Default badge background colour (yellow).
 *
 * @internal
 */
const AUTHENTIC_BADGE_BG_COLOUR_DEFAULT = '#D3AF37';

/**
 * Helper to initialize customizer integration.
 *
 * @internal
 */
function init_customizer(): void {
	add_action( 'customize_register', 'AuthenticImages\register_customizer_hook' );
}

/**
 * Register the Authentic Images customizer section, settings and controls.
 *
 * @param \WP_Customize_Manager $wp_customize Customizer manager instance.
 * @internal WordPress action hook
 */
function register_customizer_hook( \WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'authenticimages',
		array(
			'title' => __( 'Authentic Images', 'authenticimages' ),
			'panel' => 'woocommerce',
		)
	);

	$wp_customize->add_setting(
		AUTHENTIC_BADGE_LABEL_OPTION,
		array(
			'type'              => 'option',
			'default'           => __( 'Authentic', 'authenticimages' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		AUTHENTIC_BADGE_LABEL_OPTION,
		array(
			'label'   => __( 'Badge label', 'authenticimages' ),
			'section' => 'authenticimages',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		AUTHENTIC_BADGE_BG_COLOR_OPTION,
		array(
			'type'              => 'option',
			'default'           => AUTHENTIC_BADGE_BG_COLOUR_DEFAULT,
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new \WP_Customize_Color_Control(
			$wp_customize,
			AUTHENTIC_BADGE_BG_COLOR_OPTION,
			array(
				'label'   => __( 'Badge background color', 'authenticimages' ),
				'section' => 'authenticimages',
			)
		)
	);

	$wp_customize->add_setting(
		AUTHENTIC_BADGE_TEXT_COLOR_OPTION,
		array(
			'type'              => 'option',
			'default'           => AUTHENTIC_BADGE_TEXT_COLOUR_DEFAULT,
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new \WP_Customize_Color_Control(
			$wp_customize,
			AUTHENTIC_BADGE_TEXT_COLOR_OPTION,
			array(
				'label'   => __( 'Badge text color', 'authenticimages' ),
				'section' => 'authenticimages',
			)
		)
	);

	$wp_customize->add_setting(
		AUTHENTIC_MESSAGE_OPTION,
		array(
			'type'              => 'option',
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		AUTHENTIC_MESSAGE_OPTION,
		array(
			'label'   => __( 'Message', 'authenticimages' ),
			'section' => 'authenticimages',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		AUTHENTIC_BADGE_SCALE_OPTION,
		array(
			'type'              => 'option',
			'default'           => 166,
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		AUTHENTIC_BADGE_SCALE_OPTION,
		array(
			'label'   => __( 'Badge scale', 'authenticimages' ),
			'section' => 'authenticimages',
			'type'    => 'select',
			'choices' => array(
				100 => '1.00x',
				125 => '1.25x',
				133 => '1.33x',
				150 => '1.50x',
				166 => '1.66x',
				170 => '1.70x',
				180 => '1.80x',
				190 => '1.90x',
				200 => '2.00x',
			),
		)
	);
}
