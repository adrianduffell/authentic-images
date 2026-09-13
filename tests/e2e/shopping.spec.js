/**
 * Copyright 2026 Adrian Duffell
 * Licensed under the GNU General Public License v2.0 or later.
 */

import { test, expect } from '@wordpress/e2e-test-utils-playwright';
import badgeDimensions from '../../fixtures/badge-dimensions.json' with { type: 'json' };

/**
 * Returns the active theme's stylesheet slug via the WordPress REST API.
 *
 * @param {Object} requestUtils - Playwright REST request utilities.
 * @return {Promise<string>} The active theme slug.
 */
async function getActiveThemeSlug( requestUtils ) {
	const [ activeTheme ] = await requestUtils.rest( {
		method: 'GET',
		path: '/wp/v2/themes',
		params: { status: 'active' },
	} );
	return activeTheme.stylesheet;
}

/**
 * Returns the current viewport as a `{width}x{height}` string.
 *
 * @param {import('@playwright/test').Page} page - The Playwright page object.
 * @return {string} Viewport key, e.g. `'1280x720'`.
 */
function getViewportKey( page ) {
	const { width, height } = page.viewportSize();
	return `${ width }x${ height }`;
}

test( 'Shopping flow', async ( { requestUtils, browser } ) => {
	// Arrange.
	const runId = Date.now();
	const themeSlug = await getActiveThemeSlug( requestUtils );

	const product = await requestUtils.rest( {
		method: 'POST',
		path: '/wc/v3/products',
		data: {
			name: `Order Flow Test Product ${ runId }`,
			type: 'simple',
			status: 'publish',
			regular_price: '9.99',
		},
	} );

	const productData = await requestUtils.rest( {
		method: 'GET',
		path: `/wc/v3/products/${ product.id }`,
	} );

	// Customer flow in isolated context.
	const customerContext = await browser.newContext( {
		storageState: { cookies: [], origins: [] },
	} );
	const customerPage = await customerContext.newPage();

	const viewportKey = getViewportKey( customerPage );
	const fixture = badgeDimensions?.[ themeSlug ]?.[ viewportKey ];

	// Open the product page.
	await customerPage.goto( productData.permalink );

	await expect( customerPage.locator( '#wpadminbar' ) ).toHaveCount( 0 );

	const badge = customerPage.locator( '.authenticimages-badge' );
	await expect( badge ).toBeVisible();
	await expect( badge ).toHaveText( 'Authentic' );
	await expect
		.soft( badge, 'Product font-size' )
		.toHaveCSS( 'font-size', fixture?.productPage?.fontSize );
	await expect
		.soft( badge, 'Product padding' )
		.toHaveCSS( 'padding-top', fixture?.productPage?.padding );

	const message = customerPage.locator( '.authenticimages-message' );
	await expect( message ).toBeVisible();
	await expect( message ).toHaveText(
		'Our product images are not AI-generated'
	);

	await customerContext.close();
} );
