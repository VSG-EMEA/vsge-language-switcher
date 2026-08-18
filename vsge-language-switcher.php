<?php
/**
 * Plugin Name:       VSGE Language Switcher
 * Description:       A Plugin that provides the language switcher block for polylang
 * Version:           0.4.2
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


if ( ! defined( 'VLS_PLUGIN_DIR' ) ) {
	define( 'VLS_PLUGIN_DIR', __DIR__ );
}
if ( ! defined( 'VLS_PLUGIN_URL' ) ) {
	define( 'VLS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'VLS_NAMESPACE' ) ) {
	define( 'VLS_NAMESPACE', 'vsge' );
}

require_once VLS_PLUGIN_DIR . '/inc/class-config.php';

/*
 * Capture wp-config.php overrides before defining legacy fallback constants.
 * Other VSGE code reads VLS_REGIONS directly, so the fallbacks remain public,
 * while VLS_Config can still let the stored plugin option override a fallback.
 */
VLS_Config::capture_constant_overrides();

if ( ! defined( 'VLS_REGIONS_MODE' ) ) {
	define( 'VLS_REGIONS_MODE', VLS_Config::default_regions_mode() );
}
if ( ! defined( 'VLS_REGIONS' ) ) {
	define( 'VLS_REGIONS', VLS_Config::default_regions() );
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
 * Keep Polylang's language cookie session-scoped. Polylang expects seconds;
 * zero is its documented value for a session cookie.
 */
add_filter( 'pll_cookie_expiration', function() { return 0; } );

/**
 * Include the render callback and functions
 */
include_once VLS_PLUGIN_DIR . '/inc/functions.php';
include_once VLS_PLUGIN_DIR . '/inc/enqueue.php';
include_once VLS_PLUGIN_DIR . '/inc/class-runtime.php';
include_once VLS_PLUGIN_DIR . '/inc/class-settings-page.php';

/**
 * Register the block by passing the location of block.json to register_block_type.
 */
add_action( 'init', 'vls_register_blocks' );
function vls_register_blocks() {
	$block_directory = VLS_PLUGIN_DIR . '/build';

	if ( file_exists( $block_directory . '/block.json' ) ) {
		register_block_type( $block_directory );
	}
}

/**
 * Keep the plugin independently insertable when Companion is inactive.
 *
 * @param array $categories Existing categories.
 * @return array
 */
function vls_register_block_category( $categories ) {
	foreach ( $categories as $category ) {
		if ( isset( $category['slug'] ) && 'vsge' === $category['slug'] ) {
			return $categories;
		}
	}

	array_unshift(
		$categories,
		array(
			'slug'  => 'vsge',
			'title' => __( 'VSGE', 'vsge-language-switcher' ),
			'icon'  => 'translation',
		)
	);

	return $categories;
}

add_filter( 'block_categories_all', 'vls_register_block_category', 10, 1 );
