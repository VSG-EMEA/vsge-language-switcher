const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	entry: {
		'polylang-language-switcher-editor': path.resolve( process.cwd(), `src/index.js` ),
		'polylang-language-switcher': path.resolve( process.cwd(), `src/pls-language-switcher.js` ),
	},
	output: {
		path: path.join( __dirname, './build' ),
	}
};
