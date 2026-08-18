<?php
/**
 * Resolves configuration and normalizes legacy VLS_REGIONS safely.
 */
class VLS_Config {
	const OPTION_NAME = 'vls_settings';
	/** @var array */
	private static $constant_overrides = array();

	/** @return void */
	public static function capture_constant_overrides() {
		self::$constant_overrides = array(
			'regions_mode' => defined( 'VLS_REGIONS_MODE' ),
			'regions' => defined( 'VLS_REGIONS' ),
		);
	}

	/** @return string */
	public static function default_regions_mode() { return 'select'; }
	/** @return array */
	public static function default_regions() {
		return array(
			'europe' => array( 'europe' => 'Europe', 'gb' => 'United Kingdom', 'fr' => 'France', 'de' => 'Germany' ),
			'middle_east_africa' => 'Middle East / Africa',
			'asia_pacific' => 'Asia / Pacific',
			'americas' => 'Americas',
		);
	}

	/** @param string $key @return mixed */
	public static function get( $key ) {
		if ( self::is_overridden( $key ) ) {
			return constant( self::constant_name( $key ) );
		}
		$options = self::stored_options();
		if ( array_key_exists( $key, $options ) ) {
			return $options[ $key ];
		}
		return 'regions' === $key ? self::default_regions() : self::default_regions_mode();
	}

	/** @return string */
	public static function regions_mode() {
		$value = self::get( 'regions_mode' );
		return in_array( $value, array( 'select', 'accordion' ), true ) ? $value : self::default_regions_mode();
	}

	/**
	 * Compatibility raw map. New settings use a normalized model, while existing
	 * constants and legacy stored arrays continue to be readable.
	 *
	 * @return array
	 */
	public static function regions() {
		$value = self::get( 'regions' );
		if ( ! is_array( $value ) ) {
			return array();
		}
		if ( isset( $value['version'], $value['groups'] ) && 2 === (int) $value['version'] ) {
			$legacy = array();
			foreach ( $value['groups'] as $group ) {
				$entries = array();
				foreach ( isset( $group['entries'] ) ? $group['entries'] : array() as $entry ) {
					$entries[ $entry['region'] ] = $entry['label'];
				}
				$legacy[ $group['id'] ] = $entries;
			}
			return $legacy;
		}
		return $value;
	}

	/** @return array */
	public static function region_model() {
		$value = self::get( 'regions' );
		if ( ! is_array( $value ) ) {
			return array();
		}
		if ( isset( $value['version'], $value['groups'] ) && 2 === (int) $value['version'] ) {
			return isset( $value['groups'] ) && is_array( $value['groups'] ) ? $value['groups'] : array();
		}
		return self::legacy_to_model( $value );
	}

	/** @param string $key @return bool */
	public static function is_overridden( $key ) { return ! empty( self::$constant_overrides[ $key ] ); }
	/** @param string $key @return string */
	public static function source( $key ) {
		if ( self::is_overridden( $key ) ) { return 'wp-config.php'; }
		return array_key_exists( $key, self::stored_options() ) ? 'WordPress setting' : 'default';
	}
	/** @param string $key @return string */
	public static function constant_name( $key ) {
		$constants = array( 'regions_mode' => 'VLS_REGIONS_MODE', 'regions' => 'VLS_REGIONS' );
		return isset( $constants[ $key ] ) ? $constants[ $key ] : '';
	}
	/** @return array */
	public static function stored_options() {
		$options = function_exists( 'get_option' ) ? get_option( self::OPTION_NAME, array() ) : array();
		return is_array( $options ) ? $options : array();
	}

	/** @param mixed $input @return array */
	public static function sanitize_settings( $input ) {
		$current = self::stored_options();
		$input = is_array( $input ) ? $input : array();
		$output = $current;
		if ( array_key_exists( 'regions_mode', $input ) && ! self::is_overridden( 'regions_mode' ) ) {
			$output['regions_mode'] = in_array( $input['regions_mode'], array( 'select', 'accordion' ), true ) ? $input['regions_mode'] : self::default_regions_mode();
		}
		if ( array_key_exists( 'regions', $input ) && ! self::is_overridden( 'regions' ) ) {
			$model = self::normalize_model( $input['regions'] );
			if ( null === $model ) {
				if ( function_exists( 'add_settings_error' ) ) {
					add_settings_error( self::OPTION_NAME, 'invalid_regions', __( 'Each region and destination needs a unique key, label, region code, and valid Polylang language.', 'vsge-language-switcher' ) );
				}
			} else {
				$output['regions'] = array( 'version' => 2, 'groups' => $model );
			}
		}
		return $output;
	}

	/** @param mixed $value @return array|null */
	public static function normalize_model( $value ) {
		if ( ! is_array( $value ) || ! isset( $value['groups'] ) || ! is_array( $value['groups'] ) ) {
			return null;
		}
		$groups = array();
		$group_ids = array();
		$available_languages = self::available_language_codes();
		foreach ( $value['groups'] as $group ) {
			if ( ! is_array( $group ) ) { return null; }
			$id = self::machine_key( isset( $group['id'] ) ? $group['id'] : '' );
			$label = sanitize_text_field( isset( $group['label'] ) ? wp_unslash( $group['label'] ) : '' );
			if ( '' === $id || '' === $label || isset( $group_ids[ $id ] ) ) { return null; }
			$group_ids[ $id ] = true;
			$entries = array();
			$entry_ids = array();
			foreach ( isset( $group['entries'] ) && is_array( $group['entries'] ) ? $group['entries'] : array() as $entry ) {
				if ( ! is_array( $entry ) ) { return null; }
				$entry_id = self::machine_key( isset( $entry['id'] ) ? $entry['id'] : '' );
				$entry_label = sanitize_text_field( isset( $entry['label'] ) ? wp_unslash( $entry['label'] ) : '' );
				$region = self::region_code( isset( $entry['region'] ) ? $entry['region'] : '' );
				$language = sanitize_text_field( isset( $entry['language'] ) ? wp_unslash( $entry['language'] ) : '' );
				if ( '' === $entry_id || '' === $entry_label || '' === $region || '' === $language || isset( $entry_ids[ $entry_id ] ) ) { return null; }
				if ( ! empty( $available_languages ) && ! in_array( strtolower( $language ), $available_languages, true ) ) { return null; }
				$entry_ids[ $entry_id ] = true;
				$entries[] = array( 'id' => $entry_id, 'label' => $entry_label, 'region' => $region, 'language' => $language );
			}
			$groups[] = array( 'id' => $id, 'label' => $label, 'entries' => $entries );
		}
		return $groups;
	}

	/** @param array $legacy @return array */
	private static function legacy_to_model( $legacy ) {
		$model = array();
		foreach ( $legacy as $group_key => $definition ) {
			$group_id = self::machine_key( $group_key );
			if ( '' === $group_id ) { continue; }
			$entries = array();
			if ( is_array( $definition ) ) {
				foreach ( $definition as $region => $label ) {
					$region = self::region_code( $region );
					if ( '' === $region ) { continue; }
					$entries[] = array( 'id' => $region, 'label' => sanitize_text_field( (string) $label ), 'region' => $region, 'language' => $region );
				}
			} else {
				$entries[] = array( 'id' => $group_id, 'label' => sanitize_text_field( (string) $definition ), 'region' => $group_id, 'language' => (string) $definition );
			}
			$model[] = array( 'id' => $group_id, 'label' => sanitize_text_field( (string) $group_key ), 'entries' => $entries );
		}
		return $model;
	}

	/** @return array */
	private static function available_language_codes() {
		if ( ! function_exists( 'pll_languages_list' ) ) { return array(); }
		$languages = pll_languages_list( array( 'fields' => 'slug' ) );
		return is_array( $languages ) ? array_map( 'strtolower', $languages ) : array();
	}

	/** @param mixed $value @return string */
	private static function machine_key( $value ) {
		$value = strtolower( sanitize_text_field( (string) $value ) );
		$value = preg_replace( '/[^a-z0-9_-]+/', '-', $value );
		return trim( $value, '-' );
	}

	/** @param mixed $value @return string */
	private static function region_code( $value ) {
		$value = strtolower( sanitize_text_field( (string) $value ) );
		return preg_match( '/^[a-z0-9_-]+$/', $value ) ? $value : '';
	}
}
