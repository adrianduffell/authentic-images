/**
 * Copyright 2026 Adrian Duffell
 * Licensed under the GNU General Public License v2.0 or later.
 */

import { render, screen } from '@testing-library/react';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { Edit } from '../edit';
import { Save } from '../save';
import metadata from '../block.json';

jest.mock( '@wordpress/block-editor', () => {
	const mockUseBlockProps = Object.assign(
		jest.fn( () => ( {
			'data-testid': 'authentic-image-notice-editor',
		} ) ),
		{
			save: jest.fn( () => ( {
				'data-testid': 'authentic-image-notice-save',
			} ) ),
		}
	);

	const mockUseInnerBlocksProps = Object.assign(
		jest.fn( ( blockProps ) => blockProps ),
		{ save: jest.fn( ( blockProps ) => blockProps ) }
	);

	return {
		useBlockProps: mockUseBlockProps,
		useInnerBlocksProps: mockUseInnerBlocksProps,
	};
} );

const mockUseBlockProps = jest.mocked( useBlockProps );
const mockUseInnerBlocksProps = jest.mocked( useInnerBlocksProps );

describe( 'Metadata', () => {
	test( 'inserts with a compact relative block gap', () => {
		// Arrange.
		const [ defaultVariation ] = metadata.variations;

		// Act.
		const { blockGap } = defaultVariation.attributes.style.spacing;

		// Assert.
		expect( defaultVariation.name ).toBe( 'default' );
		expect( defaultVariation.isDefault ).toBe( true );
		expect( defaultVariation.scope ).toEqual( [ 'inserter' ] );
		expect( blockGap ).toBe( '0.33em' );
	} );

	test( 'uses the notice block name', () => {
		// Arrange.
		const { name } = metadata;

		// Act.
		const blockName = name;

		// Assert.
		expect( blockName ).toBe( 'authenticimages/authentic-image-notice' );
	} );

	test( 'uses the notice title in the editor', () => {
		// Arrange.
		const { title } = metadata;

		// Act.
		const blockTitle = title;

		// Assert.
		expect( blockTitle ).toBe( 'Authentic Image Notice' );
	} );
} );

describe( 'Edit', () => {
	test( 'provides the badge and message as an unlocked template', () => {
		// Arrange.
		mockUseBlockProps.mockClear();
		mockUseInnerBlocksProps.mockClear();

		// Act.
		render( <Edit /> );

		// Assert.
		expect(
			screen.getByTestId( 'authentic-image-notice-editor' )
		).toBeVisible();
		expect( mockUseBlockProps ).toHaveBeenCalledWith( {
			className: 'authenticimages-notice',
		} );
		expect( mockUseInnerBlocksProps ).toHaveBeenCalledTimes( 1 );
		const settings = mockUseInnerBlocksProps.mock.calls[ 0 ][ 1 ];
		expect( settings.template ).toEqual( [
			[ 'authenticimages/authentic-badge', {} ],
			[ 'authenticimages/authentic-message', {} ],
		] );
		expect( settings ).not.toHaveProperty( 'templateLock' );
	} );
} );

describe( 'Save', () => {
	test( 'saves the inner blocks in the block wrapper', () => {
		// Arrange.
		mockUseBlockProps.save.mockClear();
		mockUseInnerBlocksProps.save.mockClear();

		// Act.
		render( <Save /> );

		// Assert.
		expect(
			screen.getByTestId( 'authentic-image-notice-save' )
		).toBeVisible();
		expect( mockUseBlockProps.save ).toHaveBeenCalledWith( {
			className: 'authenticimages-notice',
		} );
		expect( mockUseInnerBlocksProps.save ).toHaveBeenCalledTimes( 1 );
	} );
} );
