/**
 * Copyright 2026 Adrian Duffell
 * Licensed under the GNU General Public License v2.0 or later.
 */

import { test, expect } from '@wordpress/e2e-test-utils-playwright';

test( 'system status shows heading', async ( { page, admin } ) => {
	// Act.
	await admin.visitAdminPage( 'admin.php', 'page=wc-status' );

	// Assert.
	await expect(
		page.getByRole( 'heading', { name: 'Authentic Images' } )
	).toBeVisible();
} );
