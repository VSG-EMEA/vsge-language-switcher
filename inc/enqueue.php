<?php

/** Dropdown language selector */
function pls_enable_dropdown_scripts() {
	add_action( 'wp_enqueue_scripts', 'pls_enqueue_scripts_dropdown' );
}

/** Modal language selector */
function pls_enable_modal_scripts() {
	add_action( 'wp_enqueue_scripts', 'pls_enqueue_scripts_modal' );
}

function pls_enqueue_scripts_modal() {
	$asset = include PLS_PLUGIN_DIR . '/build/advanced-language-switcher.asset.php';

	wp_enqueue_script( 'polylang-advanced-language-switcher', plugins_url() . '/polylang-language-switcher/build/advanced-language-switcher.js', $asset['dependencies'], false, true);

	wp_localize_script(
		'polylang-advanced-language-switcher',
		'pls', array(
			'languages' => pll_the_languages( array(
				'raw' => true,
			) ),
			'siteurl'      => get_option('siteurl' ),
			'cookiePath'   => COOKIEPATH,
			'cookieDomain' => COOKIE_DOMAIN,
		)
	);
}
