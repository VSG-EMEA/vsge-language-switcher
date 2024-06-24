<?php
/**
 * Plugin Name:       VSGE Language Switcher
 * Description:       A Plugin that provides the language switcher block for polylang
 * Version:           0.4.0
 * Requires at least: 5.8
 * Tested up to:      6.5
 * Requires PHP:      7.1.0
 * Author:            codekraft
 * Author URI:        https://codekraft.it
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       vsge-language-switcher
 * Domain Path:       languages/
 */


define( 'VLS_PLUGIN_DIR', __DIR__ );
define( 'VLS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'VLS_NAMESPACE', 'vsge' );

if ( ! defined( 'VLS_REGIONS' ) ) {
	define( 'VLS_REGIONS', array(
		'europe' => array(
			'europe' => 'Europe',
			'gb' => 'United Kingdom',
			'fr' => 'France',
			'de' => 'Germany',
		),
		'middle_east_africa' => 'Middle East / Africa',
		'asia_pacific' => 'Asia / Pacific',
		'americas' => 'Americas',
	) );
}

/**
 * Adding actions to the init hook.
 */
add_action(
	'plugins_loaded',
	function() {
		load_plugin_textdomain( 'vsge-language-switcher', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
);

/**
 * Set the language cookie expiration to "Session"
 */
add_filter( 'pll_cookie_expiration', function() { return 'Session'; } );

/**
 * Include the render callback and functions
 */
include_once VLS_PLUGIN_DIR . '/inc/functions.php';
include_once VLS_PLUGIN_DIR . '/inc/enqueue.php';

/**
 * Register the block by passing the location of block.json to register_block_type.
 */
add_action( 'init', 'register_blocks' );
function register_blocks() {
	register_block_type( dirname(__FILE__) . '/build' );
}
