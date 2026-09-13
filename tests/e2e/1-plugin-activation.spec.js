/**
 * Copyright 2026 Adrian Duffell
 * Licensed under the GNU General Public License v2.0 or later.
 */

import { test, expect } from '@wordpress/e2e-test-utils-playwright';

test( 'plugin can be activated', async ( { page, admin, requestUtils } ) => {
	// Arrange.
	await requestUtils.rest( {
		path: '/wp/v2/plugins/authenticimages/authenticimages',
		method: 'PUT',
		data: {
			status: 'inactive',
		},
	} );

	// Act: activate the plugin from the WP plugins screen.
	await admin.visitAdminPage( 'plugins.php' );
	await page
		.locator( 'tr[data-slug="authentic-images"]' )
		.getByRole( 'link', { name: 'Activate' } )
		.click();

	// Assert: plugin activation success message is shown.
	await expect( page.getByText( 'Plugin activated' ) ).toBeVisible();
} );
