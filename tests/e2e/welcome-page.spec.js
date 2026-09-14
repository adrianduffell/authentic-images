/**
 * Copyright 2026 Adrian Duffell
 * Licensed under the GNU General Public License v2.0 or later.
 */

import { test, expect } from '@wordpress/e2e-test-utils-playwright';

const licenseKey = process.env.AUTHENTICIMAGES_LICENSE_KEY;

test.describe.configure( { mode: 'serial' } );

test(
	'show the welcome page navigation menu item',
	{ tag: '@premium-license' },
	async ( { page, admin, requestUtils } ) => {
		// Arrange.
		await requestUtils.rest( {
			path: '/wp/v2/settings',
			method: 'POST',
			data: {
				authenticimages_license_key: '',
			},
		} );

		// Act.
		await admin.visitAdminPage( 'index.php' );

		// Assert.
		await expect(
			page.getByRole( 'link', { name: 'Authentic Images' } )
		).toBeVisible();
	}
);

test(
	'dismissing the welcome screen can be undone',
	{ tag: '@premium-license' },
	async ( { page, admin, requestUtils } ) => {
		// Arrange.
		await page.context().clearCookies( {
			name: 'AUTHENTICIMAGES_DISMISS_SETUP',
		} );
		await requestUtils.rest( {
			path: '/wp/v2/settings',
			method: 'POST',
			data: {
				authenticimages_license_key: '',
			},
		} );
		await admin.visitAdminPage(
			'admin.php',
			'page=authenticimages-welcome'
		);

		// Act.
		await page.getByRole( 'button', { name: 'Dismiss' } ).click();
		await page.getByRole( 'button', { name: 'Undo' } ).click();

		// Assert.
		await expect(
			page.getByRole( 'heading', { name: 'Welcome to Authentic Images' } )
		).toBeVisible();
	}
);

test(
	'dismiss the welcome screen hides the navigation menu item',
	{ tag: '@premium-license' },
	async ( { page, admin, requestUtils } ) => {
		// Arrange.
		await page.context().clearCookies( {
			name: 'AUTHENTICIMAGES_DISMISS_SETUP',
		} );
		await requestUtils.rest( {
			path: '/wp/v2/settings',
			method: 'POST',
			data: {
				authenticimages_license_key: '',
			},
		} );
		await admin.visitAdminPage(
			'admin.php',
			'page=authenticimages-welcome'
		);

		// Act.
		await page.getByRole( 'button', { name: 'Dismiss' } ).click();

		// Assert.
		await expect(
			page.getByRole( 'heading', { name: 'Setup dismissed' } )
		).toBeVisible();
		await expect(
			page.getByRole( 'link', { name: 'Learn more' } )
		).toHaveAttribute(
			'href',
			'https://authenticimages.zip/help/license-key'
		);
		await expect(
			page.getByRole( 'button', { name: 'Undo' } )
		).toBeVisible();

		// Act.
		await admin.visitAdminPage( 'index.php' );

		// Assert.
		await expect(
			page.getByRole( 'link', { name: 'Authentic Images' } )
		).toHaveCount( 0 );
	}
);

test(
	'add a license key from the welcome page',
	{ tag: '@premium-license' },
	async ( { page, admin, requestUtils } ) => {
		test.skip(
			! licenseKey,
			'AUTHENTICIMAGES_LICENSE_KEY environment variable not found.'
		);

		// Arrange.
		await page.context().clearCookies( {
			name: 'AUTHENTICIMAGES_DISMISS_SETUP',
		} );
		await requestUtils.rest( {
			path: '/wp/v2/settings',
			method: 'POST',
			data: {
				authenticimages_license_key: '',
			},
		} );
		await admin.visitAdminPage(
			'admin.php',
			'page=authenticimages-welcome'
		);

		// Act.
		await page
			.getByRole( 'textbox', { name: 'Premium license key' } )
			.fill( licenseKey );
		await expect(
			page.getByRole( 'button', { name: 'Activate site' } )
		).toBeEnabled();
		await page.getByRole( 'button', { name: 'Activate site' } ).click();

		// Assert.
		await expect(
			page.getByRole( 'heading', { name: '🎉 Success!' } )
		).toBeVisible();
	}
);

test(
	'hide the welcome page navigation menu item',
	{ tag: '@premium-license' },
	async ( { page, admin } ) => {
		test.skip(
			! licenseKey,
			'AUTHENTICIMAGES_LICENSE_KEY environment variable not found.'
		);

		// Act.
		await admin.visitAdminPage( 'index.php' );

		// Assert.
		await expect(
			page.getByRole( 'link', { name: 'Authentic Images' } )
		).toHaveCount( 0 );
	}
);
