<?php
/** Configuration model and Settings API smoke coverage. */
$plugin_root = dirname( __DIR__, 2 ); $failures = array();
$vls_options = array( 'regions_mode' => 'accordion', 'regions' => array( 'version' => 2, 'groups' => array( array( 'id' => 'europe', 'label' => 'Europe', 'entries' => array( array( 'id' => 'italy', 'label' => 'Italy', 'region' => 'it', 'language' => 'it' ) ) ) ) ) );
function get_option( $name, $default = false ) { global $vls_options; return 'vls_settings' === $name ? $vls_options : $default; }
function wp_unslash( $value ) { return $value; }
function sanitize_text_field( $value ) { return trim( (string) $value ); }
function pll_languages_list( $args = array() ) { return array( 'en', 'it' ); }
function vls_settings_smoke_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }
require $plugin_root . '/inc/class-config.php'; VLS_Config::capture_constant_overrides();

vls_settings_smoke_assert( 'accordion' === VLS_Config::regions_mode(), 'Stored mode must apply without constants.' );
vls_settings_smoke_assert( 'Italy' === VLS_Config::region_model()[0]['entries'][0]['label'], 'Structured stored settings must normalize for runtime.' );
$legacy = array( 'Europe' => array( 'it-IT' => 'Italy' ), 'Americas' => 'en' );
$GLOBALS['vls_options']['regions'] = $legacy;
$legacy_model = VLS_Config::region_model();
vls_settings_smoke_assert( 'it-it' === $legacy_model[0]['entries'][0]['language'], 'Legacy map must preserve an explicit key as language reference without slicing it.' );
$valid = array( 'groups' => array( array( 'id' => 'europe', 'label' => 'Europe', 'entries' => array( array( 'id' => 'italy', 'label' => 'Italy', 'region' => 'it', 'language' => 'it' ) ) ) ) );
vls_settings_smoke_assert( is_array( VLS_Config::normalize_model( $valid ) ), 'Structured region settings must be accepted.' );
$duplicate = $valid; $duplicate['groups'][0]['entries'][] = array( 'id' => 'italy', 'label' => 'Duplicate', 'region' => 'it2', 'language' => 'it' );
vls_settings_smoke_assert( null === VLS_Config::normalize_model( $duplicate ), 'Duplicate destination keys must be rejected.' );
$invalid = $valid; $invalid['groups'][0]['entries'][0]['language'] = 'de';
vls_settings_smoke_assert( null === VLS_Config::normalize_model( $invalid ), 'Unavailable Polylang language mappings must be rejected.' );
vls_settings_smoke_assert( array() === VLS_Config::normalize_model( array( 'groups' => array() ) ), 'Intentional empty region configuration must be supported.' );
$GLOBALS['vls_options'] = array();
vls_settings_smoke_assert( 'select' === VLS_Config::regions_mode(), 'Defaults must apply without a constant or option.' );
define( 'VLS_REGIONS_MODE', 'accordion' ); define( 'VLS_REGIONS', array( 'constant' => 'Constant' ) ); VLS_Config::capture_constant_overrides();
vls_settings_smoke_assert( 'accordion' === VLS_Config::regions_mode() && isset( VLS_Config::regions()['constant'] ), 'Constants must override WordPress options.' );

$settings_page = file_get_contents( $plugin_root . '/inc/class-settings-page.php' );
$plugin_files = implode( "\n", array_map( 'file_get_contents', glob( $plugin_root . '/inc/*.php' ) ) );
vls_settings_smoke_assert( false !== strpos( $settings_page, "'manage_options'" ) && false !== strpos( $settings_page, 'settings_fields(' ), 'Settings API must enforce capability and nonce path.' );
vls_settings_smoke_assert( false !== strpos( $settings_page, 'data-vls-region-editor' ) && false !== strpos( $settings_page, 'This setting is currently controlled by wp-config.php.' ), 'Admin must render structured effective configuration when constants override.' );
vls_settings_smoke_assert( 0 === preg_match( '/(?:file_put_contents|fopen|fwrite)\s*\([^\n]*wp-config\.php/i', $plugin_files ), 'Plugin must never write wp-config.php.' );
if ( $failures ) { fwrite( STDERR, implode( PHP_EOL, $failures ) . PHP_EOL ); exit( 1 ); }
echo "Language switcher settings smoke test passed.\n";
