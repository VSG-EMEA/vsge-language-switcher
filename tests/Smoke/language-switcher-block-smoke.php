<?php
/**
 * Behavioural bootstrap/render smoke coverage. Run with php directly.
 */
$plugin_root = dirname( __DIR__, 2 );
$failures = array();
$vls_hooks = array();
$vls_registered_blocks = array();
$vls_polylang_enabled = false;

function vls_block_smoke_assert( $condition, $message ) { global $failures; if ( ! $condition ) { $failures[] = $message; } }
function add_action( $hook, $callback ) { global $vls_hooks; $vls_hooks[ $hook ][] = $callback; }
function add_filter( $hook, $callback ) { add_action( $hook, $callback ); }
function plugin_dir_url( $file ) { return 'https://example.test/plugins/vsge-language-switcher/'; }
function plugin_basename( $file ) { return basename( dirname( $file ) ) . '/' . basename( $file ); }
function __( $text ) { return $text; }
function esc_html__( $text ) { return $text; }
function esc_attr__( $text ) { return $text; }
function esc_html( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $text ) { return $text; }
function sanitize_key( $text ) { return strtolower( preg_replace( '/[^a-z0-9_-]/i', '', $text ) ); }
function sanitize_text_field( $text ) { return trim( (string) $text ); }
function sanitize_html_class( $text ) { return preg_replace( '/[^a-z0-9_-]/i', '-', $text ); }
function sanitize_hex_color( $text ) { return preg_match( '/^#[a-f0-9]{6}$/i', $text ) ? $text : null; }
function absint( $value ) { return abs( (int) $value ); }
function selected( $left, $right, $echo = true ) { $value = $left === $right ? ' selected="selected"' : ''; if ( $echo ) { echo $value; } return $value; }
function wp_unslash( $value ) { return $value; }
function wp_json_encode( $value ) { return json_encode( $value ); }
function get_option( $name, $default = false ) { return $default; }
function is_admin() { return false; }
function get_block_wrapper_attributes( $attributes = array() ) { return 'class="' . ( isset( $attributes['class'] ) ? $attributes['class'] : '' ) . '"'; }
function register_block_type( $directory ) { global $vls_registered_blocks; $metadata = json_decode( file_get_contents( $directory . '/block.json' ), true ); $vls_registered_blocks[] = $metadata['name']; WP_Block_Type_Registry::get_instance()->register( $metadata['name'] ); return true; }
function pll_the_languages( $args = array() ) {
	global $vls_polylang_enabled;
	if ( ! $vls_polylang_enabled ) { return array(); }
	return array(
		'en' => array( 'slug' => 'en', 'name' => 'English', 'url' => 'https://example.test/en/', 'locale' => 'en_US', 'current_lang' => true ),
		'it' => array( 'slug' => 'it', 'name' => 'Italiano', 'url' => 'https://example.test/it/', 'locale' => 'it_IT', 'current_lang' => false ),
	);
}
class WP_Block_Type_Registry {
	private static $instance; private $types = array();
	public static function get_instance() { if ( null === self::$instance ) { self::$instance = new self(); } return self::$instance; }
	public function register( $name ) { $this->types[ $name ] = true; }
	public function is_registered( $name ) { return isset( $this->types[ $name ] ); }
}

$source_metadata = json_decode( file_get_contents( $plugin_root . '/src/block.json' ), true );
$build_metadata = json_decode( file_get_contents( $plugin_root . '/build/block.json' ), true );
$editor_bundle = file_get_contents( $plugin_root . '/build/index.js' );
require $plugin_root . '/vsge-language-switcher.php';

vls_block_smoke_assert( 'vsge/language-switcher' === $source_metadata['name'] && 'vsge/language-switcher' === $build_metadata['name'], 'Canonical block name must remain unchanged.' );
vls_block_smoke_assert( 'file:./index.js' === $build_metadata['editorScript'] && file_exists( $plugin_root . '/build/index.asset.php' ), 'Editor metadata and dependency asset must exist.' );
vls_block_smoke_assert( false !== strpos( $editor_bundle, 'registerBlockType' ) && false !== strpos( $editor_bundle, 'vsge/language-switcher' ), 'Editor bundle must register the canonical block.' );
vls_block_smoke_assert( false === strpos( $editor_bundle, 'pll/metabox' ), 'Editor bundle must not use Polylang private stores.' );
vls_block_smoke_assert( isset( $vls_hooks['init'] ), 'Plugin bootstrap must hook normal init.' );
vls_register_blocks();
vls_block_smoke_assert( WP_Block_Type_Registry::get_instance()->is_registered( 'vsge/language-switcher' ), 'PHP registry must contain the block after init registration.' );
vls_block_smoke_assert( 'vsge' === vls_register_block_category( array() )[0]['slug'], 'Plugin must provide its own VSGE block category.' );

$attributes = array( 'displayAs' => 'modal' ); $content = '';
ob_start(); include $plugin_root . '/src/render.php'; $missing_output = ob_get_clean();
vls_block_smoke_assert( '' === $missing_output, 'No languages must render safely without Polylang data.' );

$vls_polylang_enabled = true;
$resolved_locale = vls_find_language( 'it-IT', vls_get_languages() );
vls_block_smoke_assert( is_array( $resolved_locale ) && 'it' === $resolved_locale['slug'], 'Explicit locale references must match without country-code inference.' );
ob_start(); include $plugin_root . '/src/render.php'; $rendered = ob_get_clean();
ob_start(); VLS_Runtime::render_modal(); $modal = ob_get_clean();
vls_block_smoke_assert( false !== strpos( $rendered, 'data-vls-open-modal' ), 'Modal block must render a trigger.' );
vls_block_smoke_assert( false === strpos( $rendered, 'vls-trigger__icon' ), 'A modal trigger must not render an icon when none is selected.' );
$attributes = array( 'displayAs' => 'modal', 'buttonIcon' => 'original', 'iconColor' => '#123456' );
ob_start(); include $plugin_root . '/src/render.php'; $rendered_with_icon = ob_get_clean();
vls_block_smoke_assert( false !== strpos( $rendered_with_icon, 'vls-trigger__icon' ) && false !== strpos( $rendered_with_icon, '#123456' ), 'A selected original icon and its colour must render safely.' );
vls_block_smoke_assert( false !== strpos( $modal, '<dialog' ) && false !== strpos( $modal, 'data-vls-close' ), 'One accessible dialog must render after a modal block.' );
ob_start(); VLS_Runtime::render_modal(); $second_modal = ob_get_clean();
vls_block_smoke_assert( '' === $second_modal, 'Multiple block instances must not emit duplicate dialogs.' );
vls_block_smoke_assert( '<!-- wp:vsge/language-switcher /-->' === '<!-- wp:' . $build_metadata['name'] . ' /-->', 'FSE markup must resolve to the registered block name.' );

if ( $failures ) { fwrite( STDERR, implode( PHP_EOL, $failures ) . PHP_EOL ); exit( 1 ); }
echo "Language switcher block smoke test passed.\n";
