/**
 * Copyright 2026 Adrian Duffell
 * Licensed under the GNU General Public License v2.0 or later.
 */

import { registerPlugin } from '@wordpress/plugins';

// #ifdef LICENSE
import './welcome-page';
// #endif
import './settings-sidebar';
import EditorPreview from './editor-preview';
import './blocks/authentic-image-notice';
import './blocks/authentic-badge';
import './blocks/authentic-message';

registerPlugin( 'authenticimages-editor-preview', {
	render: EditorPreview,
} );
