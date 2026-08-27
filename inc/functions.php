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

/**
 * Resolve the normalized destination configuration into selectable actions.
 * Both select and accordion presentations consume the same region model; this
 * only supplies the resolved URL needed by a native select option.
 *
 * @param array $model Normalized region model.
 * @param array $languages Language data.
 * @return array
 */
function vls_selectable_region_groups( $model, $languages ) {
	$groups = array();
	foreach ( $model as $group ) {
		$entries = array();
		foreach ( $group['entries'] as $entry ) {
			$type = vls_destination_type( $entry );
			if ( 'external' === $type ) {
				if ( empty( $entry['external_url'] ) ) {
					continue;
				}
				$entries[] = array(
					'id'    => $entry['id'],
					'label' => $entry['label'],
					'type'  => 'external',
					'url'   => $entry['external_url'],
				);
				continue;
			}

			$language = vls_find_language( $entry['language'], $languages );
			if ( null === $language ) {
				continue;
			}
			$entries[] = array(
				'id'           => $entry['id'],
				'label'        => $entry['label'],
				'type'         => 'internal',
				'region'       => $entry['region'],
				'language'     => $language['slug'],
				'url'          => $language['url'],
				'current_lang' => $language['current_lang'],
			);
		}
		if ( $entries ) {
			$groups[] = array(
				'id'      => $group['id'],
				'label'   => $group['label'],
				'entries' => $entries,
			);
		}
	}

	return $groups;
}

/** @param array $groups Selectable region groups. @return array */
function vls_current_destination_selection( $groups ) {
	$current_region = vls_current_region();
	foreach ( $groups as $group ) {
		foreach ( $group['entries'] as $entry ) {
			if ( 'internal' === $entry['type'] && $current_region === $entry['region'] ) {
				return array( 'group' => $group['id'], 'destination' => $entry['id'] );
			}
		}
	}
	foreach ( $groups as $group ) {
		foreach ( $group['entries'] as $entry ) {
			if ( ! empty( $entry['current_lang'] ) ) {
				return array( 'group' => $group['id'], 'destination' => $entry['id'] );
			}
		}
	}

	return array(
		'group'       => $groups[0]['id'],
		'destination' => $groups[0]['entries'][0]['id'],
	);
}

/** @param array $entry Selectable destination. @param string $selected_destination @return string */
function vls_render_country_option( $entry, $selected_destination = '' ) {
	$attributes = sprintf(
		'value="%1$s" data-type="%2$s" data-url="%3$s"',
		esc_attr( $entry['id'] ), esc_attr( $entry['type'] ), esc_url( $entry['url'] )
	);
	if ( 'internal' === $entry['type'] ) {
		$attributes .= sprintf( ' data-region="%1$s" data-language="%2$s"', esc_attr( $entry['region'] ), esc_attr( $entry['language'] ) );
	}

	return sprintf( '<option %1$s%2$s>%3$s</option>', $attributes, selected( $selected_destination, $entry['id'], false ), esc_html( $entry['label'] ) );
}

/** @param array $groups Selectable region groups. @param string $selected_group @return string */
function vls_render_region_select( $groups, $selected_group = '' ) {
	if ( empty( $groups ) ) {
		return '';
	}
	$output  = '<label for="vls-region-select">' . esc_html__( 'Region', 'vsge-language-switcher' ) . '</label>';
	$output .= '<select id="vls-region-select" class="vls-control" data-vls-region-select>';
	foreach ( $groups as $group ) {
		$output .= sprintf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $group['id'] ), selected( $selected_group, $group['id'], false ), esc_html( $group['label'] ) );
	}

	return $output . '</select>';
}

/** @param array $group Selectable region group. @param string $selected_destination @return string */
function vls_render_country_select( $group, $selected_destination = '' ) {
	$output  = '<label for="vls-country-select">' . esc_html__( 'Country', 'vsge-language-switcher' ) . '</label>';
	$output .= '<select id="vls-country-select" class="vls-control" data-vls-country-select>';
	foreach ( $group['entries'] as $entry ) {
		$output .= vls_render_country_option( $entry, $selected_destination );
	}

	return $output . '</select>';
}

/**
 * Render dependent Region and Country selects from the shared normalized
 * configuration. The Language configured on an internal destination is
 * resolved into its URL here rather than exposed as a separate user choice.
 *
 * @param array $model Normalized region model.
 * @param array $languages Language data.
 * @return string
 */
function vls_render_region_destination_selects( $model, $languages ) {
	$groups = vls_selectable_region_groups( $model, $languages );
	if ( empty( $groups ) ) {
		return '';
	}
	$selection = vls_current_destination_selection( $groups );
	$selected_group = $groups[0];
	foreach ( $groups as $group ) {
		if ( $selection['group'] === $group['id'] ) {
			$selected_group = $group;
			break;
		}
	}

	$output  = '<div class="vls-select-grid">';
	$output .= vls_render_region_select( $groups, $selection['group'] );
	$output .= vls_render_country_select( $selected_group, $selection['destination'] );
	$output .= '</div>';
	foreach ( $groups as $group ) {
		$output .= '<template data-vls-country-options data-vls-region-group="' . esc_attr( $group['id'] ) . '">';
		foreach ( $group['entries'] as $entry ) {
			$output .= vls_render_country_option( $entry );
		}
		$output .= '</template>';
	}

	return $output;
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
			'<button type="button" class="vsge-accordion-toggle vls-accordion-trigger" aria-expanded="false" aria-controls="%1$s"><span class="vsge-accordion-title">%2$s</span><svg class="vsge-accordion-icon vls-accordion-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9.29 6.71 13.59 12l-4.3 5.29L10.71 18l4.88-6-4.88-6Z"/></svg></button>',
			esc_attr( $panel_id ), esc_html( $group['label'] )
		);
		$output .= '<div id="' . esc_attr( $panel_id ) . '" class="vsge-accordion-panel vls-accordion-panel" hidden><ul>';
		foreach ( $group['entries'] as $entry ) {
			$type = vls_destination_type( $entry );
			if ( 'external' === $type ) {
				if ( empty( $entry['external_url'] ) ) {
					continue;
				}
				$output .= sprintf( '<li class="vsge-accordion-panel__item"><a class="vsge-accordion-panel__link" href="%1$s" data-vls-region-link data-type="external" data-url="%1$s">%2$s</a></li>', esc_url( $entry['external_url'] ), esc_html( $entry['label'] ) );
				continue;
			}
			$language = vls_find_language( $entry['language'], $languages );
			if ( null === $language ) {
				continue;
			}
			$current       = $language['current_lang'] ? ' is-current' : '';
			$current_label = $language['current_lang'] ? '<span class="screen-reader-text"> ' . esc_html__( '(current language)', 'vsge-language-switcher' ) . '</span>' : '';
			$output       .= sprintf(
				'<li class="vsge-accordion-panel__item%1$s"><a class="vsge-accordion-panel__link" href="%2$s" data-vls-region-link data-type="internal" data-region="%3$s" data-url="%2$s" data-language="%4$s">%5$s%6$s</a></li>',
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
	$output .= '<button type="button" class="vls-dialog__close" data-vls-close aria-label="' . esc_attr__( 'Close language and region selector', 'vsge-language-switcher' ) . '"><svg class="vls-dialog__close-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6.4 19 5 17.6l5.6-5.6L5 6.4 6.4 5l5.6 5.6L17.6 5 19 6.4 13.4 12l5.6 5.6-1.4 1.4-5.6-5.6Z"/></svg></button></div><div class="vls-dialog__content">';
	if ( 'accordion' === $layout && ! empty( $model ) ) {
		$output .= vls_render_region_accordion( $model, $languages );
	} else {
		$output .= vls_render_region_destination_selects( $model, $languages );
		$output .= '<div class="vls-dialog__actions"><button type="button" class="wp-block-button__link vls-apply" data-vls-apply>' . esc_html__( 'Apply', 'vsge-language-switcher' ) . '</button></div>';
	}
	return $output . '</div></dialog>';
}

/** Retained public helper. @return string */
function language_switcher() { return vls_render_language_select( vls_get_languages() ); }
/** Retained public helper. @return string */
function regional_switcher() { return vls_render_region_select( vls_selectable_region_groups( VLS_Config::region_model(), vls_get_languages() ) ); }
/** Retained public helper. @return string */
function language_accordion() { return vls_render_region_accordion( VLS_Config::region_model(), vls_get_languages() ); }
/** Retained public helper. @return string */
function vsge_get_the_region() { return vls_current_region() ?: 'default'; }
