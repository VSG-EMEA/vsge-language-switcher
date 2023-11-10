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
 * Text Domain:       vsge-language-switcher
 */


define( 'VLS_PLUGIN_DIR', __DIR__ );
define( 'VLS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'VLS_REGIONS' ) ) {
	define( 'VLS_REGIONS', array(
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

include_once 'inc/render_callback.php';
include_once 'inc/functions.php';
include_once 'inc/enqueue.php';
include_once 'inc/block.php';
