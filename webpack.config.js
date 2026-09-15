import path from 'node:path';
import defaultConfig from '@wordpress/scripts/config/webpack.config.js';

// The default config is included for the block entries.
// An additional entry is added for the admin scripts at src/index.ts.
export default {
	...defaultConfig,
	entry: async () => ( {
		...( await defaultConfig.entry() ),
		index: path.resolve( process.cwd(), 'src/index.ts' ),
	} ),
};
