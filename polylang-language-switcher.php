<?php

/**
 * Plugin Name:       Polylang Language Switcher
 * Description:       A Simple plugin that provides the language switcher block for polylang
 * Version:           0.1.1
 * Requires at least: 5.7
 * Tested up to:      6.0
 * Requires PHP:      7.1.0
 * Author:            codekraft
 * Author URI:        https://codekraft.it
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       pls
 */

function pls_get_languages() {
	return sprintf('<div id="pls-language-switcher">%s</div>', pll_current_language('name'));
}

/**
 * Register the block by passing the location of block.json to register_block_type.
 */
add_action( 'init', 'pls_register_block' );
function pls_register_block() {
	register_block_type( __DIR__ . '/build', [
		'render_callback' => 'pls_get_languages'
	] );
}

add_action( 'wp_enqueue_scripts', 'pls_enqueue_scripts' );
function pls_enqueue_scripts() {

	wp_enqueue_style( 'polylang-language-switcher-style', plugins_url() . '/polylang-language-switcher/build/style-polylang-language-switcher.css');
	wp_enqueue_script( 'polylang-language-switcher', plugins_url() . '/polylang-language-switcher/build/polylang-language-switcher.js', array(), false, true);

	wp_localize_script(
		'polylang-language-switcher',
		'pls_languages',
		pll_the_languages( array(
			'raw' => true
		) )
	);
}

include_once 'inc/functions.php';
