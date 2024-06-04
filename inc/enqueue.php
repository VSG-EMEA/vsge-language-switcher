<?php

function vls_enqueue_scripts_modal() {
	$asset = include VLS_PLUGIN_DIR . '/build/frontend.asset.php';
	wp_enqueue_style( 'vsge-language-switcher-style', VLS_PLUGIN_URL . 'build/style-frontend.css' );

	wp_enqueue_script( 'vsge-language-switcher', VLS_PLUGIN_URL . 'build/frontend.js', $asset['dependencies'], false, true );
	wp_localize_script( 'vsge-language-switcher', 'languageSwitcher', array(
		'languages'    => pll_the_languages( array(
			'raw' => true,
		) ),
		'siteurl'      => get_option( 'siteurl' ),
		'cookiePath'   => COOKIEPATH,
		'cookieDomain' => COOKIE_DOMAIN,
		'namespace'    => VLS_NAMESPACE
	) );
}
