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

	// Checks if the $languages is not empty
	if ( ! empty( $languages ) ) {

		// Creates the $output variable with languages container
		$output = sprintf( '<span class="label">%s</span>', esc_html__( 'Select your language', 'vsge-language-switcher' ) );
		$output .= '<select name="language-switcher" id="vls-language-select" class="vls-block-select">';

		// Runs the loop through all languages
		foreach ( $languages as $language ) {
			// Variables containing language data
			$slug     = $language['slug'];
			$name     = $language['name'];
			$url      = $language['url'];
			$selected = $language['current_lang'] ? ' selected="selected" ' : '';

			$output .= sprintf( '<option value="%s" title="%s"%s>%s</option>', $slug, $url, $selected, $name );

		}

		$output .= '</select>';

	}

	return $output;
}

/**
 * It creates a dropdown menu with the regions as options
 *
 * @return string the $output with the language selector.
 */
function regional_switcher() {

	$regions = VLS_REGIONS;

	if ( empty( $regions ) ) {
		return '';
	}

	// Creates the $output variable with regions container
	$output = sprintf( '<span class="label">%s</span>', esc_html__( 'Select your region', 'vsge-language-switcher' ) );

	// Creates the $output variable with languages container
	$output .= '<select name="regional-switcher" id="vls-region-select" class="vls-block-select">';

	$region_selected = ! empty( $_COOKIE[ VLS_NAMESPACE . '_region' ] ) ? sanitize_text_field( $_COOKIE[ VLS_NAMESPACE . '_region' ] ) : 'eu';

	foreach ( $regions as $region_code => $region_states ) {
		if ( is_array( $region_states ) ) {
			$output .= sprintf( '<optgroup label="%s">', esc_attr( $region_states[ array_keys( $region_states )[0] ] ) );

			foreach ( $region_states as $state_code => $state ) {
				$selected = ( $state_code === $region_selected ) ? ' selected="selected" ' : '';
				$output   .= sprintf( '<option value="%s"%s>%s</option>', $state_code, $selected, $state );
			}

			$output .= '</optgroup>';
		} else {
			$selected = ( $region_code === $region_selected ) ? ' selected="selected" ' : '';
			$class    = ( strlen( $region_code ) > 2 ) ? ' class="single-option" ' : '';
			$output   .= sprintf( '<option value="%s"%s%s>%s</option>', $region_code, $selected, $class, $region_states );
		}
	}

	$output .= '</select>';

	return $output;
}

// todo: check if the modal window is needed or not before adding it
add_action( 'wp_footer', 'footer_modal_window' );
function footer_modal_window() {
	?>
	<div id="overlay-wrapper" class="vls-overlay"></div>
	<!-- Language selector -->
	<div id="vls-modal-selector" class="vls-card vls-overlay-card loading">
		<div class="card-header">
			<h4><?php esc_html_e( 'Change region', 'vsge-language-switcher' ); ?></h4>
			<button class="vls-button-close">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="24" width="24">
					<path d="M6.4 19 5 17.6l5.6-5.6L5 6.4 6.4 5l5.6 5.6L17.6 5 19 6.4 13.4 12l5.6 5.6-1.4 1.4-5.6-5.6Z"/>
				</svg>
			</button>
		</div>
		<div class="card-content">
			<div class="select-row">
				<?php echo regional_switcher(); ?>
				<?php echo language_switcher(); ?>
			</div>
			<div class="wp-block-button">
				<button type="submit" id="vls-button-submit" class="wp-block-button__link"><?php esc_html_e( 'Accept', 'vsge-language-switcher' ) ?></button>
			</div>
		</div>
	</div>
	<?php
}

/**
 * returns if the region is eu or uk
 */
if ( ! function_exists( 'vsge_geolocate' ) ) :

	function vsge_geolocate( $ip_address = '' ): string {

		// the database path
		$integration = wc()->integrations->get_integration( 'maxmind_geolocation' );
		$geo_db_path = $integration->get_database_service()->get_database_path();

		$iso_code = '';

		try {
			$reader = new MaxMind\Db\Reader( $geo_db_path );
			$data   = $reader->get( $ip_address );

			if ( isset( $data['country']['iso_code'] ) ) {
				$iso_code = $data['country']['iso_code'];
			}

			$reader->close();
		} catch ( Exception $e ) {
			// $reader->log( $e->getMessage(), 'warning' );
		}

		return strtolower( $iso_code );

	}

endif;


/**
 * returns if the region is eu or uk
 */
if ( ! function_exists( 'vsge_get_the_region' ) ) :

	function vsge_get_the_region(): string {

		if ( isset( $_COOKIE[ VLS_NAMESPACE . '_region' ] ) ) {

			// lowercase and only the first 2 characters
			$region = sanitize_text_field( $_COOKIE[ VLS_NAMESPACE . '_region' ] );

			// if the string of the region is length 2
			if ( strlen( $region ) === 2 ) {
				return strtolower( $region );
			} else {
				return 'default';
			}
		} elseif ( class_exists( 'WC_Geolocation' ) ) {

			// the wc geo ip classes
			$WC_Geoloc = new WC_Geolocation();

			// get the user ip address
			$remote_ip = $WC_Geoloc->get_ip_address();

			// get the region
			$region = vsge_geolocate( $remote_ip );

			switch ( $region ) {
				case 'gb':
					return 'gb';
				case 'fr':
					return 'fr';
				case 'de':
					return 'de';
				default:
					return 'default';
			}
		}

		return 'default';
	}

endif;
