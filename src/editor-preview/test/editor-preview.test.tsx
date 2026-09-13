/**
 * Copyright 2026 Adrian Duffell
 * Licensed under the GNU General Public License v2.0 or later.
 */

import { render, act } from '@testing-library/react';
import { useEntityProp } from '@wordpress/core-data';
import EditorPreview from '../index';

jest.mock( '@wordpress/core-data', () => ( {
	useEntityProp: jest.fn(),
} ) );

const mockUseEntityProp = useEntityProp as jest.Mock;

function setupEntityPropMock(
	overrides: Record< string, [ string | number | undefined, jest.Mock ] > = {}
) {
	mockUseEntityProp.mockImplementation(
		( _kind: string, _name: string, key: string ) => {
			if ( overrides[ key ] ) {
				return [ ...overrides[ key ], undefined ];
			}

			return [ undefined, jest.fn(), undefined ];
		}
	);
}

describe( 'EditorPreview', () => {
	afterEach( () => {
		jest.clearAllMocks();
	} );

	test( 'renders the preview style tag into the document head', () => {
		// Arrange.
		setupEntityPropMock();

		// Act.
		render( <EditorPreview /> );

		// Assert.
		expect(
			document.head.querySelector( '#authenticimages-preview-vars' )
		).not.toBeNull();
	} );

	test( 'renders CSS vars from settings', () => {
		// Arrange.
		setupEntityPropMock( {
			authenticimages_badge_label: [ 'Authentic', jest.fn() ],
			authenticimages_badge_bg_color: [ '#ff0000', jest.fn() ],
			authenticimages_badge_text_color: [ '#ffffff', jest.fn() ],
			authenticimages_badge_scale: [ 140, jest.fn() ],
			authenticimages_badge_density: [ 80, jest.fn() ],
		} );

		// Act.
		render( <EditorPreview /> );

		// Assert.
		const styleEl = document.head.querySelector(
			'#authenticimages-preview-vars'
		);

		expect( styleEl?.textContent ).toContain(
			'--authenticimages-badge-label: "Authentic"'
		);
		expect( styleEl?.textContent ).toContain(
			'--authenticimages-badge-bg-color: #ff0000'
		);
		expect( styleEl?.textContent ).toContain(
			'--authenticimages-badge-text-color: #ffffff'
		);
		expect( styleEl?.textContent ).toContain(
			'--authenticimages-badge-scale: 140'
		);
		expect( styleEl?.textContent ).toContain(
			'--authenticimages-badge-density: 80'
		);
	} );

	test( 'falls back unset for undefined style values', () => {
		// Arrange.
		setupEntityPropMock();

		// Act.
		render( <EditorPreview /> );

		// Assert.
		const styleEl = document.head.querySelector(
			'#authenticimages-preview-vars'
		);

		expect( styleEl?.textContent ).toContain(
			'--authenticimages-badge-bg-color: unset'
		);
		expect( styleEl?.textContent ).toContain(
			'--authenticimages-badge-text-color: unset'
		);
	} );

	test( 'falls back to unset for empty string style values', () => {
		// Arrange.
		setupEntityPropMock( {
			authenticimages_badge_bg_color: [ '', jest.fn() ],
			authenticimages_badge_border_style: [ '', jest.fn() ],
			authenticimages_badge_font_weight: [ '', jest.fn() ],
		} );

		// Act.
		render( <EditorPreview /> );

		// Assert.
		const styleEl = document.head.querySelector(
			'#authenticimages-preview-vars'
		);

		expect( styleEl?.textContent ).toContain(
			'--authenticimages-badge-bg-color: unset'
		);
		expect( styleEl?.textContent ).toContain(
			'--authenticimages-badge-border-style: unset'
		);
		expect( styleEl?.textContent ).toContain(
			'--authenticimages-badge-font-weight: unset'
		);
	} );

	describe( 'iframe portal targeting', () => {
		test( 'portals style tag into canvas document head when ready event fires', () => {
			// Arrange.
			const canvasDoc = document.implementation.createHTMLDocument();

			setupEntityPropMock();
			render( <EditorPreview /> );

			// Act.
			act( () => {
				window.dispatchEvent(
					new CustomEvent( 'authenticimagesCanvasReady', {
						detail: {
							document: canvasDoc,
						},
					} )
				);
			} );

			// Assert.
			expect(
				canvasDoc.head.querySelector( '#authenticimages-preview-vars' )
			).not.toBeNull();
			expect(
				document.head.querySelector( '#authenticimages-preview-vars' )
			).toBeNull();
		} );

		test( 're-portals style tag to replacement canvas document head when ready event fires again', () => {
			// Arrange.
			const canvasDoc1 = document.implementation.createHTMLDocument();
			const canvasDoc2 = document.implementation.createHTMLDocument();

			setupEntityPropMock();
			render( <EditorPreview /> );

			act( () => {
				window.dispatchEvent(
					new CustomEvent( 'authenticimagesCanvasReady', {
						detail: {
							document: canvasDoc1,
						},
					} )
				);
			} );

			expect(
				canvasDoc1.head.querySelector( '#authenticimages-preview-vars' )
			).not.toBeNull();

			// Act.
			act( () => {
				window.dispatchEvent(
					new CustomEvent( 'authenticimagesCanvasReady', {
						detail: {
							document: canvasDoc2,
						},
					} )
				);
			} );

			// Assert.
			expect(
				canvasDoc2.head.querySelector( '#authenticimages-preview-vars' )
			).not.toBeNull();
			expect(
				canvasDoc1.head.querySelector( '#authenticimages-preview-vars' )
			).toBeNull();
		} );
	} );
} );
