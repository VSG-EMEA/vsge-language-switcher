<?php
/**
 * Plugin Name:       VSGE Language Switcher
 * Description:       A Plugin that provides the language switcher block for polylang
 * Version:           0.1.4
 * Requires at least: 5.7
 * Tested up to:      6.0
 * Requires PHP:      7.1.0
 * Author:            codekraft
 * Author URI:        https://codekraft.it
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       pls
 */


if ( ! defined( 'PLS_PLUGIN_DIR' ) ) {
	define( 'PLS_PLUGIN_DIR', __DIR__ );
}

if ( ! defined( 'PLS_REGIONS' ) ) {
	define( 'PLS_REGIONS', array(
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
 * Depending on user selection (dropdown or modal) enqueue the scripts,
 * then replaces the [pls_switcher] shortcode with the current language name
 *
 * @param array $attributes - An array of attributes passed to the shortcode.
 * @param string $content - The content of the shortcode.
 *
 * @return string|void The language switcher
 */
function pls_get_languages( $attributes, $content ) {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return;
	}
	// enqueue the script for the selected language switcher
	( ! empty( $attributes['displayAs'] ) && $attributes['displayAs'] === 'dropdown' ) ? pls_enable_dropdown_scripts() : pls_enable_modal_scripts();

	// replace the [pls_switcher] shortcode with the current language switcher component
	return str_replace( "[pls_switcher]", pll_current_language( 'name' ), $content );
}

/**
 * Register the block by passing the location of block.json to register_block_type.
 */
add_action( 'init', 'pls_register_block' );
function pls_register_block() {
	register_block_type( PLS_PLUGIN_DIR, [
		'render_callback' => 'pls_get_languages'
	] );
}

include_once 'inc/functions.php';
include_once 'inc/enqueue.php';
