/**
 * Copyright 2026 Adrian Duffell
 * Licensed under the GNU General Public License v2.0 or later.
 */

import type { ReactNode } from 'react';
import { render, screen, fireEvent } from '@testing-library/react';
import { Edit } from '../edit';

jest.mock( '@wordpress/block-editor', () => ( {
	useBlockProps: jest.fn( ( props ) => props ?? {} ),
	InspectorControls: ( { children }: { children: ReactNode } ) => (
		<div>{ children }</div>
	),
	RichText: ( {
		value,
		onChange,
		placeholder,
	}: {
		value: string;
		onChange: ( v: string ) => void;
		placeholder?: string;
		[ key: string ]: unknown;
	} ) => (
		<input
			type="text"
			value={ value }
			placeholder={ placeholder }
			onChange={ ( e ) => onChange( e.target.value ) }
		/>
	),
} ) );

jest.mock( '@wordpress/components', () => ( {
	BaseControl: ( { children }: { children: ReactNode; label?: string } ) => (
		<div>{ children }</div>
	),
	PanelBody: ( { children }: { children: ReactNode } ) => (
		<div>{ children }</div>
	),
	TextControl: ( {
		label,
		value,
		onChange,
	}: {
		label: string;
		value: string;
		onChange: ( v: string ) => void;
	} ) => (
		<input
			aria-label={ label }
			value={ value }
			onChange={ ( e ) => onChange( e.target.value ) }
		/>
	),
} ) );

jest.mock( '@wordpress/i18n', () => ( {
	__: jest.fn( ( str: string ) => str ),
} ) );

jest.mock( '@wordpress/core-data', () => ( {
	useEntityProp: jest.fn(),
} ) );

import { useEntityProp } from '@wordpress/core-data';

const mockUseEntityProp = useEntityProp as jest.Mock;

describe( 'Edit', () => {
	test( 'renders badge with empty label when setting is empty', () => {
		// Arrange.
		const setLabel = jest.fn();
		mockUseEntityProp.mockReturnValue( [ undefined, setLabel, undefined ] );

		// Act.
		render( <Edit /> );

		// Assert.
		expect( screen.getByDisplayValue( '' ) ).toBeInTheDocument();
	} );

	test( 'renders badge with label from global setting', () => {
		// Arrange.
		const setLabel = jest.fn();
		mockUseEntityProp.mockReturnValue( [
			'Authentic',
			setLabel,
			undefined,
		] );

		// Act.
		render( <Edit /> );

		// Assert.
		expect( screen.getByDisplayValue( 'Authentic' ) ).toBeInTheDocument();
	} );

	test( 'calls setLabel with updated label when content changes', () => {
		// Arrange.
		const setLabel = jest.fn();
		mockUseEntityProp.mockReturnValue( [
			'Real images',
			setLabel,
			undefined,
		] );
		render( <Edit /> );
		const input = screen.getByDisplayValue( 'Real images' );

		// Act.
		fireEvent.change( input, { target: { value: 'Authentic images' } } );

		// Assert.
		expect( setLabel ).toHaveBeenCalledWith( 'Authentic images' );
	} );
} );
