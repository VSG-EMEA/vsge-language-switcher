<?php
/**
 * Public rendering helpers for VSGE Language Switcher.
 */

/** @return array */
function vls_get_languages() {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return array();
	}

	$languages = pll_the_languages( array( 'raw' => 1, 'echo' => 0, 'hide_empty' => 0 ) );
	if ( ! is_array( $languages ) ) {
		return array();
	}

	$normalized = array();
	foreach ( $languages as $language ) {
		if ( ! is_array( $language ) || empty( $language['slug'] ) || empty( $language['url'] ) ) {
			continue;
		}
		$slug = sanitize_key( $language['slug'] );
		if ( '' === $slug ) {
			continue;
		}
		$normalized[ $slug ] = array(
			'slug'           => $slug,
			'name'           => isset( $language['name'] ) ? (string) $language['name'] : $slug,
			'url'            => (string) $language['url'],
			'locale'         => isset( $language['locale'] ) ? (string) $language['locale'] : '',
			'current_lang'   => ! empty( $language['current_lang'] ),
			'no_translation' => ! empty( $language['no_translation'] ),
		);
	}

	return $normalized;
}

/**
 * Exact language slug/locale matching for legacy entries. Never infers a
 * language by slicing a country or market key.
 *
 * @param string $reference Language slug or locale.
 * @param array  $languages Language data.
 * @return array|null
 */
function vls_find_language( $reference, $languages ) {
	$reference = str_replace( '_', '-', strtolower( trim( (string) $reference ) ) );
	if ( '' === $reference ) {
		return null;
	}
	foreach ( $languages as $language ) {
		$slug   = str_replace( '_', '-', strtolower( $language['slug'] ) );
		$locale = str_replace( '_', '-', strtolower( $language['locale'] ) );
		if ( $reference === $slug || $reference === $locale ) {
			return $language;
		}
	}
	return null;
}

/** @param array $entry @return string */
function vls_destination_type( $entry ) {
	return isset( $entry['type'] ) && 'external' === $entry['type'] ? 'external' : 'internal';
}

/** @return bool */
function vls_has_external_destinations() {
	foreach ( VLS_Config::region_model() as $group ) {
		foreach ( $group['entries'] as $entry ) {
			if ( 'external' === vls_destination_type( $entry ) && ! empty( $entry['external_url'] ) ) {
				return true;
			}
		}
	}
	return false;
}

/** @return string */
function vls_current_region() {
	$cookie = isset( $_COOKIE[ VLS_NAMESPACE . '_region' ] ) ? wp_unslash( $_COOKIE[ VLS_NAMESPACE . '_region' ] ) : '';
	return sanitize_text_field( (string) $cookie );
}

/** @param array $languages Language data. @return string */
function vls_render_language_select( $languages ) {
	if ( empty( $languages ) ) {
		return '';
	}
	$output = '<label for="vls-language-select">' . esc_html__( 'Language', 'vsge-language-switcher' ) . '</label>';
	$output .= '<select id="vls-language-select" class="vls-control" data-vls-language-select>';
	foreach ( $languages as $language ) {
		$output .= sprintf(
			'<option value="%1$s" data-url="%2$s"%3$s>%4$s</option>',
			esc_attr( $language['slug'] ), esc_url( $language['url'] ), selected( $language['current_lang'], true, false ), esc_html( $language['name'] )
		);
	}
	return $output . '</select>';
}

/** @param array $model Normalized region model. @return string */
function vls_render_region_select( $model ) {
	if ( empty( $model ) ) {
		return '';
	}
	$current = vls_current_region();
	$output  = '<label for="vls-region-select">' . esc_html__( 'Region', 'vsge-language-switcher' ) . '</label>';
	$output .= '<select id="vls-region-select" class="vls-control" data-vls-region-select>';
	foreach ( $model as $group ) {
		if ( empty( $group['entries'] ) ) {
			continue;
		}
		$output .= '<optgroup label="' . esc_attr( $group['label'] ) . '">';
		foreach ( $group['entries'] as $entry ) {
			$type = vls_destination_type( $entry );
			if ( 'external' === $type ) {
				if ( empty( $entry['external_url'] ) ) {
					continue;
				}
				$output .= sprintf( '<option value="%1$s" data-type="external" data-url="%2$s">%3$s</option>', esc_attr( $entry['id'] ), esc_url( $entry['external_url'] ), esc_html( $entry['label'] ) );
				continue;
			}
			$language = vls_find_language( $entry['language'], vls_get_languages() );
			if ( null === $language ) {
				continue;
			}
			$output .= sprintf(
				'<option value="%1$s" data-type="internal" data-region="%1$s" data-language="%2$s" data-url="%3$s"%4$s>%5$s</option>',
				esc_attr( $entry['region'] ), esc_attr( $language['slug'] ), esc_url( $language['url'] ), selected( $current, $entry['region'], false ), esc_html( $entry['label'] )
			);
		}
		$output .= '</optgroup>';
	}
	return $output . '</select>';
}

/**
 * @param array $model Normalized region model.
 * @param array $languages Language data.
 * @return string
 */
function vls_render_region_accordion( $model, $languages ) {
	$output = '<div class="vsge-accordion-list vls-language-accordion">';
	foreach ( $model as $index => $group ) {
		$panel_id = 'vls-region-panel-' . sanitize_html_class( $group['id'] ) . '-' . absint( $index );
		$output  .= '<section class="vsge-accordion-row vls-accordion-item">';
		$output  .= sprintf(
			'<button type="button" class="vsge-accordion-toggle vls-accordion-trigger" aria-expanded="false" aria-controls="%1$s"><span class="vsge-accordion-title">%2$s</span><span class="vsge-accordion-icon" aria-hidden="true">⌄</span></button>',
			esc_attr( $panel_id ), esc_html( $group['label'] )
		);
		$output .= '<div id="' . esc_attr( $panel_id ) . '" class="vsge-accordion-panel vls-accordion-panel" hidden><ul>';
		foreach ( $group['entries'] as $entry ) {
			$type = vls_destination_type( $entry );
			if ( 'external' === $type ) {
				if ( empty( $entry['external_url'] ) ) {
					continue;
				}
				$output .= sprintf( '<li><a href="%1$s" data-vls-region-link data-type="external" data-url="%1$s">%2$s</a></li>', esc_url( $entry['external_url'] ), esc_html( $entry['label'] ) );
				continue;
			}
			$language = vls_find_language( $entry['language'], $languages );
			if ( null === $language ) {
				continue;
			}
			$current       = $language['current_lang'] ? ' class="is-current"' : '';
			$current_label = $language['current_lang'] ? '<span class="screen-reader-text"> ' . esc_html__( '(current language)', 'vsge-language-switcher' ) . '</span>' : '';
			$output       .= sprintf(
				'<li%1$s><a href="%2$s" data-vls-region-link data-type="internal" data-region="%3$s" data-url="%2$s" data-language="%4$s">%5$s%6$s</a></li>',
				$current, esc_url( $language['url'] ), esc_attr( $entry['region'] ), esc_attr( $language['slug'] ), esc_html( $entry['label'] ), $current_label
			);
		}
		$output .= '</ul></div></section>';
	}
	return $output . '</div>';
}

/** @return string */
function vls_render_modal() {
	$languages = vls_get_languages();
	if ( empty( $languages ) && ! vls_has_external_destinations() ) {
		return '';
	}
	$model  = VLS_Config::region_model();
	$layout = VLS_Config::regions_mode();
	$output = '<dialog id="vls-language-dialog" class="vls-dialog" aria-labelledby="vls-dialog-title">';
	$output .= '<div class="vls-dialog__header"><h2 id="vls-dialog-title">' . esc_html__( 'Choose language and region', 'vsge-language-switcher' ) . '</h2>';
	$output .= '<button type="button" class="vls-dialog__close" data-vls-close aria-label="' . esc_attr__( 'Close language and region selector', 'vsge-language-switcher' ) . '">×</button></div><div class="vls-dialog__content">';
	if ( 'accordion' === $layout && ! empty( $model ) ) {
		$output .= vls_render_region_accordion( $model, $languages );
	} else {
		$output .= '<div class="vls-select-grid">' . vls_render_region_select( $model ) . vls_render_language_select( $languages ) . '</div>';
		$output .= '<div class="vls-dialog__actions"><button type="button" class="vls-apply" data-vls-apply>' . esc_html__( 'Apply', 'vsge-language-switcher' ) . '</button></div>';
	}
	return $output . '</div></dialog>';
}

/** Retained public helper. @return string */
function language_switcher() { return vls_render_language_select( vls_get_languages() ); }
/** Retained public helper. @return string */
function regional_switcher() { return vls_render_region_select( VLS_Config::region_model() ); }
/** Retained public helper. @return string */
function language_accordion() { return vls_render_region_accordion( VLS_Config::region_model(), vls_get_languages() ); }
/** Retained public helper. @return string */
function vsge_get_the_region() { return vls_current_region() ?: 'default'; }
