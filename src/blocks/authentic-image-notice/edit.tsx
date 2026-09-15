/**
 * Copyright 2026 Adrian Duffell
 * Licensed under the GNU General Public License v2.0 or later.
 */

import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

export const TEMPLATE = [
	[ 'authenticimages/authentic-badge', {} ],
	[ 'authenticimages/authentic-message', {} ],
];

export function Edit(): JSX.Element {
	const blockProps = useBlockProps( {
		className: 'authenticimages-notice',
	} );
	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template: TEMPLATE,
	} );

	return <div { ...innerBlocksProps } />;
}
