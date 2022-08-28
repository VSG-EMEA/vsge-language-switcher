<?php

/**
 * Show Polylang Languages with Custom Markup
 * @return string|void {string} the language switcher
 */
function language_switcher() {

	if ( ! function_exists( 'pll_the_languages' ) ) {
		return;
	}

	// Gets the pll_the_languages() raw code
	$languages = pll_the_languages( array(
		'raw' => true
	) );

	$output = '';

	// Checks if the $languages is not empty
	if ( ! empty( $languages ) ) {

		// Creates the $output variable with languages container
		$output = '<select name="language-switcher" id="pls-language-select" class="pls-block-select">';

		// Runs the loop through all languages
		foreach ( $languages as $language ) {
			// Variables containing language data
			$slug           = $language['slug'];
			$name           = $language['name'];
			$url            = $language['url'];
			$selected       = $language['current_lang'] ? 'selected="selected" ' : '';

			$output .= "<option value=\"$slug\" title=\"$url\" $selected>$name</option>";

		}

		$output .= '</select>';

	}

	return $output;
}

add_action( 'wp_footer', 'footer_modal_window' );
function footer_modal_window() {
	?>
	<div id="overlay-wrapper" class="pls-overlay"></div>
	<!-- Language selector -->
	<div id="pls-modal-selector" class="pls-card pls-overlay-card" style="display: none">
		<div class="card-header">
			<h4><?php if ( function_exists( 'pll_e' ) ) {pll_e( 'Change region' );}?></h4>
			<a class="pls-button-close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="24" width="24"><path d="M6.4 19 5 17.6l5.6-5.6L5 6.4 6.4 5l5.6 5.6L17.6 5 19 6.4 13.4 12l5.6 5.6-1.4 1.4-5.6-5.6Z"/></svg></a>
		</div>
		<div class="card-content">
			<div class="select-row">
				<span class="label"><?php if ( function_exists( 'pll_e' ) ) {pll_e( 'Select your language' );}?></span>
				<?php echo language_switcher(); ?>
			</div>
			<div class="wp-block-button">
				<button type="submit" id="pls-button-submit" class="wp-block-button__link"><?php _e( 'Accept', 'pls' ) ?></button>
			</div>
		</div>
	</div>
	<?php
}
