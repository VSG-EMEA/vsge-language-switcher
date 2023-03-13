const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	entry: {
		'advanced-language-switcher-editor': path.resolve(
			process.cwd(),
			`src/index.js`
		),
		'advanced-language-switcher': path.resolve(
			process.cwd(),
			`src/language-switcher.js`
		),
	},
	output: {
		path: path.join( __dirname, './build' ),
	},
};
