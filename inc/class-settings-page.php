<?php
/**
 * Settings API UI with a structured region editor.
 */
class VLS_Settings_Page {
	const PAGE_SLUG = 'vsge-language-switcher';
	const OPTION_GROUP = 'vls_settings_group';

	/** @return void */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	/** @return void */
	public static function add_page() {
		add_options_page( __( 'VSGE Language Switcher', 'vsge-language-switcher' ), __( 'VSGE Language Switcher', 'vsge-language-switcher' ), 'manage_options', self::PAGE_SLUG, array( __CLASS__, 'render_page' ) );
	}

	/** @return void */
	public static function register_settings() {
		register_setting( self::OPTION_GROUP, VLS_Config::OPTION_NAME, array( 'type' => 'array', 'sanitize_callback' => array( 'VLS_Config', 'sanitize_settings' ), 'default' => array() ) );
		add_settings_section( 'vls_general', __( 'General', 'vsge-language-switcher' ), array( __CLASS__, 'render_general_section' ), self::PAGE_SLUG );
		add_settings_field( 'vls_regions_mode', __( 'Region chooser layout', 'vsge-language-switcher' ), array( __CLASS__, 'render_mode_field' ), self::PAGE_SLUG, 'vls_general' );
		add_settings_section( 'vls_regions', __( 'Languages and regions', 'vsge-language-switcher' ), array( __CLASS__, 'render_regions_section' ), self::PAGE_SLUG );
		add_settings_field( 'vls_region_editor', __( 'Region destinations', 'vsge-language-switcher' ), array( __CLASS__, 'render_regions_field' ), self::PAGE_SLUG, 'vls_regions' );
	}

	/** @return void */
	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) { wp_die( esc_html__( 'You do not have permission to manage these settings.', 'vsge-language-switcher' ) ); }
		?>
		<div class="wrap"><h1><?php esc_html_e( 'VSGE Language Switcher', 'vsge-language-switcher' ); ?></h1>
		<style>.vls-region-group{margin:0 0 1em;padding:1em;border:1px solid #c3c4c7}.vls-region-entry{display:flex;flex-wrap:wrap;gap:.75em;align-items:end;padding:.75em 0;border-top:1px solid #dcdcde}.vls-region-entry label,.vls-region-group>p label{display:flex;flex-direction:column;gap:.25em}.vls-region-editor input,.vls-region-editor select{min-width:10rem}.vls-editor-actions{display:inline-flex;gap:.35em;margin-left:.5em}</style>
		<form action="options.php" method="post"><?php settings_fields( self::OPTION_GROUP ); do_settings_sections( self::PAGE_SLUG ); submit_button(); ?></form></div>
		<?php
	}

	/** @return void */
	public static function render_general_section() {
		echo '<p>' . esc_html__( 'Block presentation is selected per block. These settings control the shared region chooser used by modal blocks.', 'vsge-language-switcher' ) . '</p>';
	}

	/** @return void */
	public static function render_regions_section() {
		echo '<p>' . esc_html__( 'A destination keeps market/region and Polylang language separate. Reorder with the arrows; remove entries only when no longer needed.', 'vsge-language-switcher' ) . '</p>';
	}

	/** @return void */
	public static function render_mode_field() {
		$key = 'regions_mode'; $disabled = VLS_Config::is_overridden( $key );
		?>
		<select name="<?php echo esc_attr( VLS_Config::OPTION_NAME . '[' . $key . ']' ); ?>" <?php disabled( $disabled ); ?>>
			<option value="select" <?php selected( VLS_Config::regions_mode(), 'select' ); ?>><?php esc_html_e( 'Select fields with Apply button', 'vsge-language-switcher' ); ?></option>
			<option value="accordion" <?php selected( VLS_Config::regions_mode(), 'accordion' ); ?>><?php esc_html_e( 'Grouped direct links', 'vsge-language-switcher' ); ?></option>
		</select>
		<?php self::render_status( $key ); ?>
		<?php
	}

	/** @return void */
	public static function render_regions_field() {
		$model = VLS_Config::region_model(); $disabled = VLS_Config::is_overridden( 'regions' );
		?>
		<div class="vls-region-editor" data-vls-region-editor data-disabled="<?php echo $disabled ? '1' : '0'; ?>">
			<input type="hidden" name="<?php echo esc_attr( VLS_Config::OPTION_NAME ); ?>[regions][version]" value="2" <?php disabled( $disabled ); ?> />
			<div data-vls-groups><?php foreach ( $model as $index => $group ) { self::render_group( $index, $group, $disabled ); } ?></div>
			<p><button type="button" class="button" data-vls-add-group <?php disabled( $disabled ); ?>><?php esc_html_e( 'Add region group', 'vsge-language-switcher' ); ?></button></p>
		</div>
		<?php self::render_status( 'regions' ); ?>
		<details><summary><?php esc_html_e( 'Advanced: effective raw configuration', 'vsge-language-switcher' ); ?></summary><pre><?php echo esc_html( wp_json_encode( VLS_Config::get( 'regions' ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) ); ?></pre></details>
		<?php if ( ! $disabled ) { self::render_editor_script(); } ?>
		<?php
	}

	/** @param int $index @param array $group @param bool $disabled @return void */
	private static function render_group( $index, $group, $disabled ) {
		$base = VLS_Config::OPTION_NAME . '[regions][groups][' . $index . ']';
		?>
		<fieldset class="vls-region-group" data-vls-group data-vls-group-index="<?php echo esc_attr( $index ); ?>"><legend><?php esc_html_e( 'Region group', 'vsge-language-switcher' ); ?></legend>
			<p><label><?php esc_html_e( 'Machine key', 'vsge-language-switcher' ); ?><input required name="<?php echo esc_attr( $base ); ?>[id]" value="<?php echo esc_attr( $group['id'] ); ?>" <?php disabled( $disabled ); ?> /></label>
			<label><?php esc_html_e( 'Visible label', 'vsge-language-switcher' ); ?><input required name="<?php echo esc_attr( $base ); ?>[label]" value="<?php echo esc_attr( $group['label'] ); ?>" <?php disabled( $disabled ); ?> /></label>
			<span class="vls-editor-actions"><button type="button" class="button" data-vls-move-up <?php disabled( $disabled ); ?>>↑</button><button type="button" class="button" data-vls-move-down <?php disabled( $disabled ); ?>>↓</button><button type="button" class="button-link-delete" data-vls-remove-group <?php disabled( $disabled ); ?>><?php esc_html_e( 'Remove group', 'vsge-language-switcher' ); ?></button></span></p>
			<div data-vls-entries><?php foreach ( $group['entries'] as $entry_index => $entry ) { self::render_entry( $base, $entry_index, $entry, $disabled ); } ?></div>
			<p><button type="button" class="button" data-vls-add-entry <?php disabled( $disabled ); ?>><?php esc_html_e( 'Add destination', 'vsge-language-switcher' ); ?></button></p>
		</fieldset>
		<?php
	}

	/** @param string $base @param int $index @param array $entry @param bool $disabled @return void */
	private static function render_entry( $base, $index, $entry, $disabled ) {
		$name = $base . '[entries][' . $index . ']';
		?>
		<div class="vls-region-entry" data-vls-entry>
			<label><?php esc_html_e( 'Destination key', 'vsge-language-switcher' ); ?><input required name="<?php echo esc_attr( $name ); ?>[id]" value="<?php echo esc_attr( $entry['id'] ); ?>" <?php disabled( $disabled ); ?> /></label>
			<label><?php esc_html_e( 'Label', 'vsge-language-switcher' ); ?><input required name="<?php echo esc_attr( $name ); ?>[label]" value="<?php echo esc_attr( $entry['label'] ); ?>" <?php disabled( $disabled ); ?> /></label>
			<label><?php esc_html_e( 'Region cookie code', 'vsge-language-switcher' ); ?><input required name="<?php echo esc_attr( $name ); ?>[region]" value="<?php echo esc_attr( $entry['region'] ); ?>" <?php disabled( $disabled ); ?> /></label>
			<label><?php esc_html_e( 'Polylang language', 'vsge-language-switcher' ); ?><?php self::render_language_select( $name . '[language]', $entry['language'], $disabled ); ?></label>
			<span class="vls-editor-actions"><button type="button" class="button" data-vls-move-up <?php disabled( $disabled ); ?>>↑</button><button type="button" class="button" data-vls-move-down <?php disabled( $disabled ); ?>>↓</button><button type="button" class="button-link-delete" data-vls-remove-entry <?php disabled( $disabled ); ?>><?php esc_html_e( 'Remove', 'vsge-language-switcher' ); ?></button></span>
		</div>
		<?php
	}

	/** @param string $name @param string $selected_language @param bool $disabled @return void */
	private static function render_language_select( $name, $selected_language, $disabled ) {
		$languages = function_exists( 'pll_languages_list' ) ? pll_languages_list( array( 'fields' => 'slug' ) ) : array();
		if ( ! is_array( $languages ) || empty( $languages ) ) {
			echo '<input required name="' . esc_attr( $name ) . '" value="' . esc_attr( $selected_language ) . '" ' . disabled( $disabled, true, false ) . ' />';
			return;
		}
		echo '<select required name="' . esc_attr( $name ) . '" ' . disabled( $disabled, true, false ) . '>';
		foreach ( $languages as $language ) { echo '<option value="' . esc_attr( $language ) . '" ' . selected( $selected_language, $language, false ) . '>' . esc_html( $language ) . '</option>'; }
		echo '</select>';
	}

	/** @param string $key @return void */
	private static function render_status( $key ) {
		$source = VLS_Config::source( $key );
		echo '<p class="description">' . esc_html__( 'Effective source:', 'vsge-language-switcher' ) . ' ' . esc_html( $source ) . '.</p>';
		$value = VLS_Config::get( $key );
		if ( is_scalar( $value ) ) {
			echo '<p class="description">' . esc_html__( 'Effective value:', 'vsge-language-switcher' ) . ' ' . esc_html( (string) $value ) . '.</p>';
		} elseif ( is_array( $value ) ) {
			echo '<p class="description">' . esc_html__( 'Effective value:', 'vsge-language-switcher' ) . ' ' . esc_html( sprintf( _n( '%d configured group', '%d configured groups', count( VLS_Config::region_model() ), 'vsge-language-switcher' ), count( VLS_Config::region_model() ) ) ) . '.</p>';
		}
		if ( VLS_Config::is_overridden( $key ) ) {
			echo '<p class="notice notice-warning inline"><strong>' . esc_html__( 'This setting is currently controlled by wp-config.php.', 'vsge-language-switcher' ) . '</strong> ' . esc_html( VLS_Config::constant_name( $key ) ) . '</p>';
		}
	}

	/** @return void */
	private static function render_editor_script() {
		?>
		<script>
		(function () { const editor = document.querySelector('[data-vls-region-editor]'); if (!editor) return;
		const group = () => { const id = Date.now(); return `<fieldset class="vls-region-group" data-vls-group data-vls-group-index="${id}"><legend>Region group</legend><p><label>Machine key <input required name="vls_settings[regions][groups][${id}][id]"></label> <label>Visible label <input required name="vls_settings[regions][groups][${id}][label]"></label> <span class="vls-editor-actions"><button type="button" class="button" data-vls-move-up>↑</button><button type="button" class="button" data-vls-move-down>↓</button><button type="button" class="button-link-delete" data-vls-remove-group>Remove group</button></span></p><div data-vls-entries></div><p><button type="button" class="button" data-vls-add-entry>Add destination</button></p></fieldset>`; };
		const entry = (groupIndex) => { const id = Date.now(); return `<div class="vls-region-entry" data-vls-entry><label>Destination key <input required name="vls_settings[regions][groups][${groupIndex}][entries][${id}][id]"></label><label>Label <input required name="vls_settings[regions][groups][${groupIndex}][entries][${id}][label]"></label><label>Region cookie code <input required name="vls_settings[regions][groups][${groupIndex}][entries][${id}][region]"></label><label>Polylang language <input required name="vls_settings[regions][groups][${groupIndex}][entries][${id}][language]"></label><span class="vls-editor-actions"><button type="button" class="button" data-vls-move-up>↑</button><button type="button" class="button" data-vls-move-down>↓</button><button type="button" class="button-link-delete" data-vls-remove-entry>Remove</button></span></div>`; };
		editor.addEventListener('click', (event) => { const target = event.target; if (!(target instanceof HTMLElement)) return; if (target.matches('[data-vls-add-group]')) editor.querySelector('[data-vls-groups]').insertAdjacentHTML('beforeend', group()); if (target.matches('[data-vls-remove-group]')) target.closest('[data-vls-group]').remove(); if (target.matches('[data-vls-add-entry]')) { const current = target.closest('[data-vls-group]'); current.querySelector('[data-vls-entries]').insertAdjacentHTML('beforeend', entry(current.dataset.vlsGroupIndex)); } if (target.matches('[data-vls-remove-entry]')) target.closest('[data-vls-entry]').remove(); if (target.matches('[data-vls-move-up]')) { const item = target.closest('[data-vls-entry], [data-vls-group]'); const previous = item.previousElementSibling; if (previous) previous.before(item); } if (target.matches('[data-vls-move-down]')) { const item = target.closest('[data-vls-entry], [data-vls-group]'); const next = item.nextElementSibling; if (next) next.after(item); } });
	}());
		</script>
		<?php
	}
}
VLS_Settings_Page::init();
