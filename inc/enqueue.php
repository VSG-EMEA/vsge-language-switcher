<?php

/** Dropdown language selector */
function vls_enable_dropdown_scripts() {
	add_action( 'wp_enqueue_scripts', 'vls_enqueue_scripts_dropdown' );
}

/** Modal language selector */
function vls_enable_modal_scripts() {
	add_action( 'wp_enqueue_scripts', 'vls_enqueue_scripts_modal' );
}

function vls_enqueue_scripts_modal() {
	$asset = include VLS_PLUGIN_DIR . '/build/vsge-language-switcher.asset.php';

	wp_enqueue_script( 'vsge-language-switcher', plugins_url() . '/vsge-language-switcher/build/vsge-language-switcher.js', $asset['dependencies'], false, true);

	wp_localize_script(
		'vsge-language-switcher',
		'languageSwitcher', array(
			'languages' => pll_the_languages( array(
				'raw' => true,
			) ),
			'siteurl'      => get_option('siteurl' ),
			'cookiePath'   => COOKIEPATH,
			'cookieDomain' => COOKIE_DOMAIN,
		)
	);
}
