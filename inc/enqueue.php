<?php

function vls_enqueue_scripts_modal() {
    if ( is_admin() ) {
        $asset = include VLS_PLUGIN_DIR . '/build/index.asset.php';
        wp_enqueue_style( 'vsge-language-switcher-style', VLS_PLUGIN_URL . 'build/index.css' );

        wp_enqueue_script( 'vsge-language-switcher', VLS_PLUGIN_URL . 'build/index.js', $asset['dependencies'], false, true );
        wp_localize_script( 'vsge-language-switcher', 'languageSwitcher', array(
            'languages'    => pll_the_languages( array(
                'raw' => true,
            ) ),
            'regions'     => VLS_REGIONS,
            'siteurl'      => get_option( 'siteurl' ),
            'cookiePath'   => COOKIEPATH,
            'cookieDomain' => COOKIE_DOMAIN,
            'namespace'    => VLS_NAMESPACE
        ) );
    }
}

add_action( 'enqueue_block_assets', 'vls_enqueue_scripts_modal' );


/**
 * The language_switcher block frontend dataset.
 *
 * @return void
 */
function vsge_language_switcher_block_data(): void {
    echo '<script id="vsge-mapbox-block-data">var languageSwitcher = ' . json_encode( array(
            'languages'    => pll_the_languages( array(
                'raw' => true,
            ) ),
            'regions'      => VLS_REGIONS,
            'siteurl'      => get_option( 'siteurl' ),
            'cookiePath'   => COOKIEPATH,
            'cookieDomain' => COOKIE_DOMAIN,
            'namespace'    => VLS_NAMESPACE
        ) ) . ' </script>';
}

add_action( 'wp_footer', 'vsge_language_switcher_block_data' );