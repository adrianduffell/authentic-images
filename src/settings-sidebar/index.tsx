/**
 * Copyright 2026 Adrian Duffell
 * Licensed under the GNU General Public License v2.0 or later.
 */

import {
	BaseControl,
	BorderControl,
	CustomSelectControl,
	PanelBody,
	RangeControl,
	TabPanel,
	TextareaControl,
	TextControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { PanelColorSettings } from '@wordpress/block-editor';
import { store as coreStore } from '@wordpress/core-data';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect, useMemo } from '@wordpress/element';
import { PluginSidebar, PluginSidebarMoreMenuItem } from '@wordpress/editor';
import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import { settings } from '@wordpress/icons';
import useSettings from '../use-settings';
import { COMPLEMENTARY_AREA, SIDEBAR_NAME, QUERY_PARAM } from './constants';

type FontWeightOption = {
	name: string;
	key: string;
	style?: {
		fontWeight: string;
	};
};

const FONT_WEIGHTS: FontWeightOption[] = [
	{ name: __( 'Default', 'authenticimages' ), key: '' },
	{ name: __( 'Regular', 'authenticimages' ), key: '400' },
	{ name: __( 'Medium', 'authenticimages' ), key: '500' },
	{ name: __( 'Semi Bold', 'authenticimages' ), key: '600' },
	{ name: __( 'Bold', 'authenticimages' ), key: '700' },
	{ name: __( 'Extra Bold', 'authenticimages' ), key: '800' },
	{ name: __( 'Black', 'authenticimages' ), key: '900' },
];

const bordersEnabled = ( () => {
	try {
		return (
			window.localStorage.getItem( 'authenticimages_borders_enabled' ) ===
			'1'
		);
	} catch {
		return false;
	}
} )();

const withSiteRecord = ( Component: React.ComponentType ) => () => {
	const hasSiteRecord = useSelect(
		( select ) => !! select( coreStore ).getEntityRecord( 'root', 'site' ),
		[]
	);

	return hasSiteRecord ? <Component /> : null;
};

const SettingsSidebar = () => {
	const { enableComplementaryArea } = useDispatch( 'core/interface' );

	useEffect( () => {
		const query = new URLSearchParams( window.location.search );
		if ( query.get( QUERY_PARAM ) !== '1' ) {
			return;
		}

		enableComplementaryArea( 'core', COMPLEMENTARY_AREA );
	}, [ enableComplementaryArea ] );

	const {
		label,
		setLabel,
		textColor,
		setTextColor,
		bgColor,
		setBgColor,
		fontWeight,
		setFontWeight,
		borderColor,
		setBorderColor,
		borderStyle,
		setBorderStyle,
		borderWidth,
		setBorderWidth,
		borderRadius,
		setBorderRadius,
		scale,
		setScale,
		density,
		setDensity,
		message,
		setMessage,
	} = useSettings();

	const border = {
		color: borderColor || undefined,
		style: borderStyle || undefined,
		width: borderWidth || undefined,
	};

	const fontWeightOptions = useMemo(
		() =>
			FONT_WEIGHTS.map( ( option ) => ( {
				...option,
				style: option.key ? { fontWeight: option.key } : undefined,
			} ) ),
		[]
	);

	const selectedFontWeight =
		fontWeightOptions.find(
			( option ) => option.key === ( fontWeight || '' )
		) || fontWeightOptions[ 0 ];

	const renderBadgeSettings = () => (
		<>
			<PanelBody>
				<p style={ { marginBottom: 0 } }>
					{ __(
						'Customize the appearance of the authentic badge. Changes apply to the whole site.',
						'authenticimages'
					) }
				</p>
			</PanelBody>

			<PanelBody title={ __( 'Label', 'authenticimages' ) } initialOpen>
				<BaseControl __nextHasNoMarginBottom={ true }>
					<TextControl
						label={ __( 'Label', 'authenticimages' ) }
						value={ label ?? '' }
						onChange={ ( value ) => setLabel( value ) }
						hideLabelFromVision={ true }
						__next40pxDefaultSize
						__nextHasNoMarginBottom={ true }
					/>
				</BaseControl>
			</PanelBody>

			<PanelColorSettings
				title={ __( 'Color', 'authenticimages' ) }
				initialOpen={ false }
				colorSettings={ [
					{
						value: textColor,
						label: __( 'Text', 'authenticimages' ),
						onChange: ( color: string | undefined ) =>
							setTextColor( color || undefined ),
					},
					{
						value: bgColor,
						label: __( 'Background', 'authenticimages' ),
						onChange: ( backgroundColor: string | undefined ) =>
							setBgColor( backgroundColor || undefined ),
					},
				] }
			/>

			<PanelBody title={ __( 'Typography', 'authenticimages' ) }>
				<BaseControl __nextHasNoMarginBottom={ true }>
					<div style={ { marginBottom: '16px' } }>
						<RangeControl
							label={ __( 'Font size', 'authenticimages' ) }
							value={ density }
							onChange={ ( value ) => {
								if ( typeof value !== 'number' ) {
									return;
								}
								setDensity( value );
							} }
							min={ 0 }
							max={ 100 }
							step={ 1 }
							renderTooltipContent={ ( value ) => `${ value }%` }
							allowReset={ true }
							resetFallbackValue={ 50 }
							withInputField={ false }
							__next40pxDefaultSize
						/>
					</div>
				</BaseControl>

				<BaseControl __nextHasNoMarginBottom={ true }>
					<CustomSelectControl
						label={ __( 'Font weight', 'authenticimages' ) }
						options={ fontWeightOptions }
						value={ selectedFontWeight }
						onChange={ ( { selectedItem } ) => {
							setFontWeight( selectedItem?.key || '' );
						} }
						__next40pxDefaultSize
					/>
				</BaseControl>
			</PanelBody>

			<PanelBody title={ __( 'Dimensions', 'authenticimages' ) }>
				<BaseControl __nextHasNoMarginBottom={ true }>
					<RangeControl
						label={ __( 'Scale', 'authenticimages' ) }
						value={ scale }
						renderTooltipContent={ ( value ) =>
							typeof value === 'number'
								? `${ ( value / 100 ).toFixed( 2 ) }x`
								: ''
						}
						onChange={ ( value ) => {
							if ( typeof value !== 'number' ) {
								return;
							}
							setScale( value );
						} }
						min={ 100 }
						max={ 200 }
						step={ 1 }
						allowReset={ true }
						resetFallbackValue={ 166 }
						withInputField={ false }
						__next40pxDefaultSize
					/>
				</BaseControl>
			</PanelBody>

			<PanelBody title={ __( 'Border', 'authenticimages' ) }>
				{ bordersEnabled && (
					<div style={ { marginBottom: 16 } }>
						<BorderControl
							label={ __( 'Border', 'authenticimages' ) }
							hideLabelFromVision={ true }
							value={ border }
							onChange={ ( value ) => {
								const nextWidth =
									value?.width !== undefined
										? String( value.width )
										: undefined;
								const nextStyle = value?.style || undefined;

								// Auto-apply 'solid' when width > 0 and the user
								// hasn't explicitly set a style yet.
								const effectiveStyle =
									parseFloat( nextWidth || '0' ) > 0 &&
									borderStyle === ''
										? 'solid'
										: nextStyle;

								setBorderColor( value?.color || undefined );
								setBorderStyle( effectiveStyle );
								setBorderWidth( nextWidth );
							} }
						/>
					</div>
				) }

				<BaseControl __nextHasNoMarginBottom={ true }>
					<UnitControl
						label={ __( 'Radius', 'authenticimages' ) }
						value={ borderRadius || undefined }
						onChange={ ( value: string | undefined ) =>
							setBorderRadius( value || undefined )
						}
						min={ 0 }
						__next40pxDefaultSize
					/>
				</BaseControl>
			</PanelBody>
		</>
	);

	const renderMessageSettings = () => (
		<>
			<PanelBody>
				<p
					data-testid="authenticimages-message-tab-description"
					style={ { marginBottom: 0 } }
				>
					{ __(
						'Customize the authentic message. Changes apply to the whole site.',
						'authenticimages'
					) }
				</p>
			</PanelBody>

			<PanelBody title={ __( 'Message', 'authenticimages' ) } initialOpen>
				<BaseControl __nextHasNoMarginBottom={ true }>
					<TextareaControl
						label={ __( 'Message', 'authenticimages' ) }
						hideLabelFromVision={ true }
						value={ message ?? '' }
						onChange={ ( value ) => setMessage( value ) }
						rows={ 2 }
						__nextHasNoMarginBottom={ true }
					/>
				</BaseControl>
			</PanelBody>
		</>
	);

	return (
		<>
			<PluginSidebarMoreMenuItem
				target={ SIDEBAR_NAME }
				icon={ settings }
			>
				{ __( 'Authentic Images settings', 'authenticimages' ) }
			</PluginSidebarMoreMenuItem>
			<PluginSidebar
				name={ SIDEBAR_NAME }
				title={ __( 'Authentic Images settings', 'authenticimages' ) }
				isPinnable={ false }
				icon={ settings }
				className="authenticimages-sidebar"
			>
				<TabPanel
					className="authenticimages-sidebar__tabs"
					activeClass="is-active"
					tabs={ [
						{
							name: 'badge',
							title: __( 'Badge', 'authenticimages' ),
							className: 'authenticimages-sidebar__tab',
						},
						{
							name: 'message',
							title: __( 'Message', 'authenticimages' ),
							className: 'authenticimages-sidebar__tab',
						},
					] }
				>
					{ ( tab ) => {
						if ( tab.name === 'message' ) {
							return renderMessageSettings();
						}
						return renderBadgeSettings();
					} }
				</TabPanel>
			</PluginSidebar>
		</>
	);
};

const isSiteEditor = window.location.pathname.includes( 'site-editor.php' );

// Only register the sidebar in the Site Editor (site-editor.php).
if ( isSiteEditor ) {
	registerPlugin( SIDEBAR_NAME, {
		render: withSiteRecord( SettingsSidebar ),
	} );
}
