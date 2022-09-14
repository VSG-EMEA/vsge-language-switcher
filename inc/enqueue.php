<?php

/** Dropdown language selector */
function pls_enable_dropdown_scripts() {
	add_action( 'wp_enqueue_scripts', 'pls_enqueue_scripts_dropdown' );
}


function pls_enqueue_scripts_dropdown() {

}

/** Modal language selector */
function pls_enable_modal_scripts() {
	add_action( 'wp_enqueue_scripts', 'pls_enqueue_scripts_modal' );
}
function pls_enqueue_scripts_modal() {
	$asset = include PLS_PLUGIN_DIR . '/build/polylang-language-switcher.asset.php';

	wp_enqueue_style( 'polylang-language-switcher-style', plugins_url() . '/polylang-language-switcher/build/style-polylang-language-switcher.css');
	wp_enqueue_script( 'polylang-language-switcher', plugins_url() . '/polylang-language-switcher/build/polylang-language-switcher.js', $asset['dependencies'], false, true);

	wp_localize_script(
		'polylang-language-switcher',
		'pls_languages',
		pll_the_languages( array(
			'raw' => true
		) )
	);
}
