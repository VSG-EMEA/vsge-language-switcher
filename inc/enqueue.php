<?php
/**
 * Bootstrap the shared selector for the external header trigger too.
 *
 * Block metadata registers these handles on init. Enqueuing the same handles
 * here is idempotent when a canonical block is also rendered on the page.
 *
 * @return void
 */
function vls_enqueue_frontend_assets() {
	if ( is_admin() ) {
		return;
	}

	if ( wp_script_is( 'vsge-language-switcher-view-script', 'registered' ) ) {
		wp_enqueue_script( 'vsge-language-switcher-view-script' );
	}

	if ( wp_style_is( 'vsge-language-switcher-view-style', 'registered' ) ) {
		wp_enqueue_style( 'vsge-language-switcher-view-style' );
	}

	VLS_Runtime::require_modal();
}

add_action( 'wp_enqueue_scripts', 'vls_enqueue_frontend_assets' );
