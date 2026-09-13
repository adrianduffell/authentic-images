<?php
/**
 * System status functions.
 *
 * @package AuthenticImages
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

namespace AuthenticImages;

defined( 'ABSPATH' ) || exit;

/**
 * Helper to initialize system status.
 *
 * @internal
 */
function init_system_status(): void {
	add_action( 'woocommerce_system_status_report', 'AuthenticImages\add_system_status_section_hook', 99 );
}

/**
 * Add info to the WooCommerce system status report.
 *
 * Fired by `woocommerce_system_status_report`.
 *
 * @internal WordPress action hook
 */
function add_system_status_section_hook(): void {
	echo '<table class="wc_status_table widefat" cellspacing="0">';
	echo '<thead><tr><th colspan="3" data-export-label="Authentic Images">	<h2>' . esc_html__( 'Authentic Images', 'authenticimages' ) . '</h2></th></tr></thead><tbody>';

	$report_items = array();

	foreach ( $report_items as $id => $report_item ) {
		$label = $report_item[0];
		$value = $report_item[1];

		printf(
			'<tr><td>%1$s</td><td class="help"></td><td data-testid="%3$s">%2$s</td></tr>',
			esc_html( (string) $label ),
			esc_html( (string) $value ),
			esc_attr( $id )
		);
	}

	echo '</tbody></table>';
}
