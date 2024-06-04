<?php
/**
 * Depending on user selection (dropdown or modal) enqueue the scripts,
 * then replaces the [vls_switcher] shortcode with the current language name
 *
 * @param array $attributes - An array of attributes passed to the shortcode.
 * @param string $content - The content of the shortcode.
 *
 * @return string|void The language switcher
 */
if ( ! function_exists( 'pll_the_languages' ) ) {
	return;
}
?>
<div
	<?php echo get_block_wrapper_attributes(); // phpcs:ignore ?>
>
<?php
// replace the [vls_switcher] shortcode with the current language switcher component
if ( ! empty( $attributes['displayAs'] ) ) {
	if ( $attributes['displayAs'] === 'dropdown' ) {
		// replace the [vls_dropdown] shortcode with the dropdown
		echo pll_the_languages( array( 'dropdown' => true ) );
	} else if ( $attributes['displayAs'] === 'dataset' ) {
		// replace the [vls_dataset] shortcode with the list
		$dataset = array( 'languages' => pll_the_languages( array( 'raw' => true ) ), 'currentLanguage' => pll_current_language() );
		echo "<span class='vls-dataset' data-languages-raw=' " . json_encode( $dataset, JSON_FORCE_OBJECT ) . "'></span>";
	} else {
		echo 'yomama';
		echo str_replace( "[vls_switcher]", pll_current_language( 'name' ), $content );
	}
}
?>
</div>

