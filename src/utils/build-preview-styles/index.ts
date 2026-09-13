/**
 * Copyright 2026 Adrian Duffell
 * Licensed under the GNU General Public License v2.0 or later.
 */

type BuildPreviewStylesParams = {
	label?: string;
	bgColor?: string;
	textColor?: string;
	fontWeight?: string;
	borderColor?: string;
	borderStyle?: string;
	borderWidth?: string;
	borderRadius?: string;
	scale?: number;
	density?: number;
};

export function buildPreviewStyles(
	settings: BuildPreviewStylesParams
): string {
	const entries = {
		'--authenticimages-badge-bg-color': settings.bgColor,
		'--authenticimages-badge-text-color': settings.textColor,
		'--authenticimages-badge-font-weight': settings.fontWeight,
		'--authenticimages-badge-border-color': settings.borderColor,
		'--authenticimages-badge-border-style': settings.borderStyle,
		'--authenticimages-badge-border-width': settings.borderWidth,
		'--authenticimages-badge-border-radius': settings.borderRadius,
	};

	const declarations = [
		`--authenticimages-badge-label: ${
			settings.label ? JSON.stringify( settings.label ) : 'none'
		}`,
		`--authenticimages-badge-scale: ${ settings.scale ?? 'unset' }`,
		`--authenticimages-badge-density: ${ settings.density ?? 'unset' }`,
		...Object.entries( entries ).map(
			( [ key, value ] ) => `${ key }: ${ value || 'unset' }`
		),
	].join( '; ' );

	return `:root { ${ declarations }` + ' }';
}
