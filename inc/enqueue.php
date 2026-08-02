<?php

function vls_enqueue_scripts_modal() {
    if ( is_admin() ) {
        $asset = include VLS_PLUGIN_DIR . '/build/index.asset.php';
        wp_enqueue_style( 'vsge-language-switcher-style', VLS_PLUGIN_URL . 'build/index.css' );
        wp_register_script( 'vsge-language-switcher', VLS_PLUGIN_URL . 'build/index.js', $asset['dependencies'], false, true );
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
            'mode'         => VLS_REGIONS_MODE,
            'siteurl'      => get_option( 'siteurl' ),
            'cookiePath'   => COOKIEPATH,
            'cookieDomain' => COOKIE_DOMAIN,
            'namespace'    => VLS_NAMESPACE
        ) ) . ' </script>';
}

add_action( 'wp_footer', 'vsge_language_switcher_block_data' );

/**
 * Redirect to the homepage based on the PLL cookie
 *
 * @return void
 */
function redirect_home_based_on_pll_cookie() {
    // Only run on the frontend
    if ( is_admin() || wp_doing_ajax() ) {
        return;
    }

    // Check if it's the homepage
    if ( isset($_COOKIE['pll_language']) ) {
        $current_lang = substr(pll_current_language(), 0, 2);
        $cookie_lang = sanitize_text_field($_COOKIE['pll_language']);

        // Avoid redirect loop: only redirect if cookie language differs
        if ( $cookie_lang && $cookie_lang !== $current_lang ) {
            $url = pll_home_url($cookie_lang);
            wp_redirect($url);
            exit;
        }
    }
}
//add_action('init', 'redirect_home_based_on_pll_cookie');