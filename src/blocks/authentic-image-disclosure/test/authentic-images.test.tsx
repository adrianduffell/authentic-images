/**
 * Copyright 2026 Adrian Duffell
 * Licensed under the GNU General Public License v2.0 or later.
 */

import { render, screen } from '@testing-library/react';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { Edit, TEMPLATE } from '../edit';
import { Save } from '../save';
import metadata from '../block.json';

jest.mock( '@wordpress/block-editor', () => {
	const mockUseBlockProps = Object.assign(
		jest.fn( () => ( {
			'data-testid': 'authentic-images-editor',
		} ) ),
		{
			save: jest.fn( () => ( {
				'data-testid': 'authentic-images-save',
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
	test( 'defaults to a compact relative block gap', () => {
		// Arrange.
		const { style } = metadata.attributes;

		// Act.
		const { blockGap } = style.default.spacing;

		// Assert.
		expect( blockGap ).toBe( '0.33em' );
	} );

	test( 'uses the disclosure block name', () => {
		// Arrange.
		const { name } = metadata;

		// Act.
		const blockName = name;

		// Assert.
		expect( blockName ).toBe(
			'authenticimages/authentic-image-disclosure'
		);
	} );

	test( 'uses the disclosure title in the editor', () => {
		// Arrange.
		const { title } = metadata;

		// Act.
		const blockTitle = title;

		// Assert.
		expect( blockTitle ).toBe( 'Authentic Image Disclosure' );
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
		expect( screen.getByTestId( 'authentic-images-editor' ) ).toBeVisible();
		expect( mockUseInnerBlocksProps ).toHaveBeenCalledWith(
			expect.any( Object ),
			{ template: TEMPLATE }
		);
		const settings = mockUseInnerBlocksProps.mock.calls[ 0 ][ 1 ];
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
		expect( screen.getByTestId( 'authentic-images-save' ) ).toBeVisible();
		expect( mockUseInnerBlocksProps.save ).toHaveBeenCalledWith(
			expect.any( Object )
		);
	} );
} );
