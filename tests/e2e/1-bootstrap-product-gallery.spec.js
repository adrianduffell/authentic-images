/**
 * Copyright 2026 Adrian Duffell
 * Licensed under the GNU General Public License v2.0 or later.
 */

import { test as setup, expect } from '@wordpress/e2e-test-utils-playwright';

const productGallery = process.env.PRODUCT_GALLERY;

/**
 * Fetches the active Single Product template.
 *
 * @param {Object} requestUtils Playwright request utilities.
 * @return {Promise<Object>} Single Product template.
 */
async function getSingleProductTemplate( requestUtils ) {
	const templates = await requestUtils.rest( {
		method: 'GET',
		path: '/wp/v2/templates',
		params: {
			context: 'edit',
		},
	} );
	const template = templates.find(
		( candidate ) => candidate.slug === 'single-product'
	);

	expect( template ).toBeDefined();

	return template;
}

/**
 * Resets the active Single Product template and returns its default version.
 *
 * @param {Object} requestUtils Playwright request utilities.
 * @return {Promise<Object>} Default Single Product template.
 */
async function resetSingleProductTemplate( requestUtils ) {
	const template = await getSingleProductTemplate( requestUtils );

	if ( template.wp_id ) {
		await requestUtils.rest( {
			method: 'DELETE',
			path: `/wp/v2/templates/${ template.id }`,
			params: { force: true },
		} );
	}

	return getSingleProductTemplate( requestUtils );
}

setup( 'use the classic product gallery', async ( { requestUtils } ) => {
	setup.skip(
		productGallery !== 'classic',
		'PRODUCT_GALLERY is not set to classic'
	);

	// Arrange.
	const template = await resetSingleProductTemplate( requestUtils );

	// Act.
	const content = template.content.raw;

	// Assert.
	expect( content ).toContain( '<!-- wp:woocommerce/product-image-gallery' );
	expect( content ).not.toContain( '<!-- wp:woocommerce/product-gallery' );
} );

setup(
	'use the new product gallery',
	async ( { page, admin, editor, requestUtils } ) => {
		setup.skip(
			productGallery !== 'new',
			'PRODUCT_GALLERY is not set to new'
		);

		// Arrange.
		const template = await resetSingleProductTemplate( requestUtils );
		await admin.visitSiteEditor( {
			postId: template.id,
			postType: 'wp_template',
			canvas: 'edit',
		} );
		await editor.selectBlocks(
			'[data-type="woocommerce/product-image-gallery"]'
		);
		await editor.openDocumentSettingsSidebar();

		// Act.
		await page
			.getByText( 'Use the Product Gallery block', { exact: true } )
			.click();
		await editor.saveSiteEditorEntities( {
			isOnlyCurrentEntityDirty: true,
		} );

		// Assert.
		const updatedTemplate = await getSingleProductTemplate( requestUtils );
		expect( updatedTemplate.content.raw ).toContain(
			'<!-- wp:woocommerce/product-gallery'
		);
		expect( updatedTemplate.content.raw ).not.toContain(
			'<!-- wp:woocommerce/product-image-gallery'
		);
	}
);
