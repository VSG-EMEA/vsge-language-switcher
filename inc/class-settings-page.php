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
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
	}

	/** @param string $hook_suffix @return void */
	public static function enqueue_assets( $hook_suffix ) {
		$stylesheet = VLS_PLUGIN_DIR . '/build/index.css';
		if ( 'settings_page_' . self::PAGE_SLUG !== $hook_suffix || ! file_exists( $stylesheet ) ) {
			return;
		}
		wp_enqueue_style( 'vls-settings', VLS_PLUGIN_URL . 'build/index.css', array(), filemtime( $stylesheet ) );
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
		<div class="wrap vls-settings-page"><h1><?php esc_html_e( 'VSGE Language Switcher', 'vsge-language-switcher' ); ?></h1>
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
			<div class="vls-admin-regions" data-vls-groups><?php foreach ( $model as $index => $group ) { self::render_group( $index, $group, $disabled ); } ?></div>
			<p class="vls-admin-regions__add"><button type="button" class="button button-secondary" data-vls-add-group <?php disabled( $disabled ); ?>><?php esc_html_e( 'Add region group', 'vsge-language-switcher' ); ?></button></p>
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
		<section class="vls-region-group" data-vls-group data-vls-group-index="<?php echo esc_attr( $index ); ?>">
			<header class="vls-region-group__header">
				<div class="vls-region-group__identity">
					<h3 class="vls-region-group__title" data-vls-group-title><?php echo esc_html( $group['label'] ); ?></h3>
				</div>
				<div class="vls-editor-actions vls-region-group__actions">
					<button type="button" class="vls-editor-action vls-editor-action--icon" data-vls-move-up data-vls-action="move-up" aria-label="<?php esc_attr_e( 'Move region group up', 'vsge-language-switcher' ); ?>" title="<?php esc_attr_e( 'Move region group up', 'vsge-language-switcher' ); ?>" <?php disabled( $disabled ); ?>><span class="dashicons dashicons-arrow-up-alt2" aria-hidden="true"></span></button>
					<button type="button" class="vls-editor-action vls-editor-action--icon" data-vls-move-down data-vls-action="move-down" aria-label="<?php esc_attr_e( 'Move region group down', 'vsge-language-switcher' ); ?>" title="<?php esc_attr_e( 'Move region group down', 'vsge-language-switcher' ); ?>" <?php disabled( $disabled ); ?>><span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span></button>
					<button type="button" class="vls-editor-action vls-editor-action--icon vls-editor-action--remove" data-vls-remove-group data-vls-action="remove" aria-label="<?php esc_attr_e( 'Remove region group', 'vsge-language-switcher' ); ?>" title="<?php esc_attr_e( 'Remove region group', 'vsge-language-switcher' ); ?>" <?php disabled( $disabled ); ?>><span class="dashicons dashicons-trash" aria-hidden="true"></span></button>
				</div>
			</header>
			<div class="vls-region-group__fields">
				<input type="hidden" name="<?php echo esc_attr( $base ); ?>[id]" value="<?php echo esc_attr( $group['id'] ); ?>" <?php disabled( $disabled ); ?> />
				<label class="vls-editor-field"><span class="vls-editor-field__label"><?php esc_html_e( 'Visible label', 'vsge-language-switcher' ); ?></span><input class="regular-text" required data-vls-group-label-input name="<?php echo esc_attr( $base ); ?>[label]" value="<?php echo esc_attr( $group['label'] ); ?>" <?php disabled( $disabled ); ?> /></label>
			</div>
			<section class="vls-region-group__destinations">
				<h4 class="vls-region-group__destinations-title"><?php esc_html_e( 'Destinations', 'vsge-language-switcher' ); ?></h4>
				<div data-vls-entries><?php foreach ( $group['entries'] as $entry_index => $entry ) { self::render_entry( $base, $entry_index, $entry, $disabled ); } ?></div>
				<p class="vls-region-group__add"><button type="button" class="button button-secondary" data-vls-add-entry <?php disabled( $disabled ); ?>><?php esc_html_e( 'Add destination', 'vsge-language-switcher' ); ?></button></p>
			</section>
		</section>
		<?php
	}

	/** @param string $base @param int $index @param array $entry @param bool $disabled @return void */
	private static function render_entry( $base, $index, $entry, $disabled ) {
		$name = $base . '[entries][' . $index . ']';
		$type = isset( $entry['type'] ) && 'external' === $entry['type'] ? 'external' : 'internal';
		?>
		<section class="vls-region-entry" data-vls-entry>
			<header class="vls-region-entry__header">
				<h5 class="vls-region-entry__title" data-vls-entry-title><?php echo esc_html( $entry['label'] ); ?></h5>
				<div class="vls-editor-actions vls-region-entry__actions">
					<button type="button" class="vls-editor-action vls-editor-action--icon" data-vls-move-up data-vls-action="move-up" aria-label="<?php esc_attr_e( 'Move destination up', 'vsge-language-switcher' ); ?>" title="<?php esc_attr_e( 'Move destination up', 'vsge-language-switcher' ); ?>" <?php disabled( $disabled ); ?>><span class="dashicons dashicons-arrow-up-alt2" aria-hidden="true"></span></button>
					<button type="button" class="vls-editor-action vls-editor-action--icon" data-vls-move-down data-vls-action="move-down" aria-label="<?php esc_attr_e( 'Move destination down', 'vsge-language-switcher' ); ?>" title="<?php esc_attr_e( 'Move destination down', 'vsge-language-switcher' ); ?>" <?php disabled( $disabled ); ?>><span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span></button>
					<button type="button" class="vls-editor-action vls-editor-action--icon vls-editor-action--remove" data-vls-remove-entry data-vls-action="remove" aria-label="<?php esc_attr_e( 'Remove destination', 'vsge-language-switcher' ); ?>" title="<?php esc_attr_e( 'Remove destination', 'vsge-language-switcher' ); ?>" <?php disabled( $disabled ); ?>><span class="dashicons dashicons-trash" aria-hidden="true"></span></button>
				</div>
			</header>
			<div class="vls-region-entry__fields">
				<input type="hidden" name="<?php echo esc_attr( $name ); ?>[id]" value="<?php echo esc_attr( $entry['id'] ); ?>" <?php disabled( $disabled ); ?> />
				<label class="vls-editor-field"><span class="vls-editor-field__label"><?php esc_html_e( 'Visible label', 'vsge-language-switcher' ); ?></span><input class="regular-text" required data-vls-entry-label-input name="<?php echo esc_attr( $name ); ?>[label]" value="<?php echo esc_attr( $entry['label'] ); ?>" <?php disabled( $disabled ); ?> /></label>
				<label class="vls-editor-field vls-region-entry__type"><span class="vls-editor-field__label"><?php esc_html_e( 'Destination type', 'vsge-language-switcher' ); ?></span><select name="<?php echo esc_attr( $name ); ?>[type]" data-vls-destination-type <?php disabled( $disabled ); ?>><option value="internal" <?php selected( $type, 'internal' ); ?>><?php esc_html_e( 'Internal region/language', 'vsge-language-switcher' ); ?></option><option value="external" <?php selected( $type, 'external' ); ?>><?php esc_html_e( 'External URL', 'vsge-language-switcher' ); ?></option></select></label>
				<div class="vls-region-entry__internal" data-vls-internal-fields <?php if ( 'external' === $type ) { echo ' hidden'; } ?>><label class="vls-editor-field"><span class="vls-editor-field__label"><?php esc_html_e( 'Region cookie code', 'vsge-language-switcher' ); ?></span><input class="regular-text" required name="<?php echo esc_attr( $name ); ?>[region]" value="<?php echo esc_attr( isset( $entry['region'] ) ? $entry['region'] : '' ); ?>" <?php disabled( $disabled || 'external' === $type ); ?> /></label><label class="vls-editor-field"><span class="vls-editor-field__label"><?php esc_html_e( 'Polylang language', 'vsge-language-switcher' ); ?></span><?php self::render_language_select( $name . '[language]', isset( $entry['language'] ) ? $entry['language'] : '', $disabled || 'external' === $type ); ?></label></div>
				<div class="vls-region-entry__external" data-vls-external-fields <?php if ( 'external' !== $type ) { echo ' hidden'; } ?>><label class="vls-editor-field"><span class="vls-editor-field__label"><?php esc_html_e( 'External URL', 'vsge-language-switcher' ); ?></span><input type="url" class="regular-text" required name="<?php echo esc_attr( $name ); ?>[external_url]" value="<?php echo esc_attr( isset( $entry['external_url'] ) ? $entry['external_url'] : '' ); ?>" <?php disabled( $disabled || 'external' !== $type ); ?> /></label></div>
			</div>
		</section>
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
		(function () {
			const editor = document.querySelector('[data-vls-region-editor]');
			if (!editor) return;
			const settingName = <?php echo wp_json_encode( VLS_Config::OPTION_NAME ); ?>;
			const strings = <?php echo wp_json_encode( array( 'regionGroup' => __( 'Region group', 'vsge-language-switcher' ), 'destination' => __( 'Destination', 'vsge-language-switcher' ), 'groupSubject' => __( '%s region group', 'vsge-language-switcher' ), 'destinationSubject' => __( '%s destination', 'vsge-language-switcher' ), 'removeGroup' => __( 'Remove group', 'vsge-language-switcher' ), 'removeDestination' => __( 'Remove destination', 'vsge-language-switcher' ), 'visibleLabel' => __( 'Visible label', 'vsge-language-switcher' ), 'destinations' => __( 'Destinations', 'vsge-language-switcher' ), 'addDestination' => __( 'Add destination', 'vsge-language-switcher' ), 'destinationType' => __( 'Destination type', 'vsge-language-switcher' ), 'internal' => __( 'Internal region/language', 'vsge-language-switcher' ), 'external' => __( 'External URL', 'vsge-language-switcher' ), 'regionCookieCode' => __( 'Region cookie code', 'vsge-language-switcher' ), 'polylangLanguage' => __( 'Polylang language', 'vsge-language-switcher' ), 'moveUp' => __( 'Move %s up', 'vsge-language-switcher' ), 'moveDown' => __( 'Move %s down', 'vsge-language-switcher' ), 'remove' => __( 'Remove %s', 'vsge-language-switcher' ) ) ); ?>;
			let nextId = Date.now();
			const nextIndex = () => nextId++;
			const actionLabel = (template, subject) => template.replace('%s', subject);
			const updateActions = (item, subject) => {
				item.querySelectorAll('[data-vls-action]').forEach((button) => {
					let label = actionLabel(strings.remove, subject);
					if (button.dataset.vlsAction === 'move-up') label = actionLabel(strings.moveUp, subject);
					if (button.dataset.vlsAction === 'move-down') label = actionLabel(strings.moveDown, subject);
					button.setAttribute('aria-label', label);
					button.setAttribute('title', label);
				});
			};
			const inputValue = (item, selector) => item.querySelector(selector)?.value.trim() || '';
			const updateGroupIdentity = (group) => {
				const label = inputValue(group, '[data-vls-group-label-input]');
				group.querySelector('[data-vls-group-title]').textContent = label || strings.regionGroup;
				updateActions(group, actionLabel(strings.groupSubject, label || strings.regionGroup));
			};
			const updateEntryIdentity = (entry) => {
				const label = inputValue(entry, '[data-vls-entry-label-input]');
				entry.querySelector('[data-vls-entry-title]').textContent = label || strings.destination;
				updateActions(entry, actionLabel(strings.destinationSubject, label || strings.destination));
			};
			const updateDestinationFields = (entry) => {
				const select = entry.querySelector('[data-vls-destination-type]');
				const external = select && select.value === 'external';
				const internalFields = entry.querySelector('[data-vls-internal-fields]');
				const externalFields = entry.querySelector('[data-vls-external-fields]');
				if (internalFields) internalFields.hidden = external;
				if (externalFields) externalFields.hidden = !external;
				[ internalFields, externalFields ].forEach((fields) => fields?.querySelectorAll('input, select').forEach((field) => { field.disabled = editor.dataset.disabled === '1' || (external ? fields === internalFields : fields === externalFields); field.required = !field.disabled; }));
			};
			const groupTemplate = () => {
				const id = nextIndex();
				return `<section class="vls-region-group" data-vls-group data-vls-group-index="${id}">
					<header class="vls-region-group__header">
						<div class="vls-region-group__identity"><h3 class="vls-region-group__title" data-vls-group-title>${strings.regionGroup}</h3></div>
						<div class="vls-editor-actions vls-region-group__actions"><button type="button" class="vls-editor-action vls-editor-action--icon" data-vls-move-up data-vls-action="move-up" aria-label="${actionLabel(strings.moveUp, strings.regionGroup)}" title="${actionLabel(strings.moveUp, strings.regionGroup)}"><span class="dashicons dashicons-arrow-up-alt2" aria-hidden="true"></span></button><button type="button" class="vls-editor-action vls-editor-action--icon" data-vls-move-down data-vls-action="move-down" aria-label="${actionLabel(strings.moveDown, strings.regionGroup)}" title="${actionLabel(strings.moveDown, strings.regionGroup)}"><span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span></button><button type="button" class="vls-editor-action vls-editor-action--icon vls-editor-action--remove" data-vls-remove-group data-vls-action="remove" aria-label="${actionLabel(strings.remove, strings.regionGroup)}" title="${actionLabel(strings.remove, strings.regionGroup)}"><span class="dashicons dashicons-trash" aria-hidden="true"></span></button></div>
					</header>
					<div class="vls-region-group__fields">
						<label class="vls-editor-field"><span class="vls-editor-field__label">${strings.visibleLabel}</span><input class="regular-text" required data-vls-group-label-input name="${settingName}[regions][groups][${id}][label]"></label>
					</div>
					<section class="vls-region-group__destinations"><h4 class="vls-region-group__destinations-title">${strings.destinations}</h4><div data-vls-entries></div><p class="vls-region-group__add"><button type="button" class="button button-secondary" data-vls-add-entry>${strings.addDestination}</button></p></section>
				</section>`;
			};
			const entryTemplate = (groupIndex) => {
				const id = nextIndex();
				return `<section class="vls-region-entry" data-vls-entry>
					<header class="vls-region-entry__header"><h5 class="vls-region-entry__title" data-vls-entry-title>${strings.destination}</h5><div class="vls-editor-actions vls-region-entry__actions"><button type="button" class="vls-editor-action vls-editor-action--icon" data-vls-move-up data-vls-action="move-up" aria-label="${actionLabel(strings.moveUp, strings.destination)}" title="${actionLabel(strings.moveUp, strings.destination)}"><span class="dashicons dashicons-arrow-up-alt2" aria-hidden="true"></span></button><button type="button" class="vls-editor-action vls-editor-action--icon" data-vls-move-down data-vls-action="move-down" aria-label="${actionLabel(strings.moveDown, strings.destination)}" title="${actionLabel(strings.moveDown, strings.destination)}"><span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span></button><button type="button" class="vls-editor-action vls-editor-action--icon vls-editor-action--remove" data-vls-remove-entry data-vls-action="remove" aria-label="${actionLabel(strings.remove, strings.destination)}" title="${actionLabel(strings.remove, strings.destination)}"><span class="dashicons dashicons-trash" aria-hidden="true"></span></button></div></header>
					<div class="vls-region-entry__fields">
						<label class="vls-editor-field"><span class="vls-editor-field__label">${strings.visibleLabel}</span><input class="regular-text" required data-vls-entry-label-input name="${settingName}[regions][groups][${groupIndex}][entries][${id}][label]"></label>
						<label class="vls-editor-field vls-region-entry__type"><span class="vls-editor-field__label">${strings.destinationType}</span><select name="${settingName}[regions][groups][${groupIndex}][entries][${id}][type]" data-vls-destination-type><option value="internal" selected>${strings.internal}</option><option value="external">${strings.external}</option></select></label>
						<div class="vls-region-entry__internal" data-vls-internal-fields><label class="vls-editor-field"><span class="vls-editor-field__label">${strings.regionCookieCode}</span><input class="regular-text" required name="${settingName}[regions][groups][${groupIndex}][entries][${id}][region]"></label><label class="vls-editor-field"><span class="vls-editor-field__label">${strings.polylangLanguage}</span><input required name="${settingName}[regions][groups][${groupIndex}][entries][${id}][language]"></label></div>
						<div class="vls-region-entry__external" data-vls-external-fields hidden><label class="vls-editor-field"><span class="vls-editor-field__label">${strings.external}</span><input type="url" class="regular-text" name="${settingName}[regions][groups][${groupIndex}][entries][${id}][external_url]"></label></div>
					</div>
				</section>`;
			};
			editor.querySelectorAll('[data-vls-group]').forEach(updateGroupIdentity);
			editor.querySelectorAll('[data-vls-entry]').forEach((entry) => { updateEntryIdentity(entry); updateDestinationFields(entry); });
			editor.addEventListener('input', (event) => {
				if (!(event.target instanceof HTMLElement)) return;
				if (event.target.matches('[data-vls-group-label-input]')) updateGroupIdentity(event.target.closest('[data-vls-group]'));
				if (event.target.matches('[data-vls-entry-label-input]')) updateEntryIdentity(event.target.closest('[data-vls-entry]'));
			});
			editor.addEventListener('change', (event) => { if (event.target.matches('[data-vls-destination-type]')) updateDestinationFields(event.target.closest('[data-vls-entry]')); });
			editor.addEventListener('click', (event) => {
				const target = event.target;
				if (!(target instanceof HTMLElement)) return;
				if (target.matches('[data-vls-add-group]')) { const groups = editor.querySelector('[data-vls-groups]'); groups.insertAdjacentHTML('beforeend', groupTemplate()); updateGroupIdentity(groups.lastElementChild); }
				if (target.matches('[data-vls-remove-group]')) target.closest('[data-vls-group]')?.remove();
				if (target.matches('[data-vls-add-entry]')) { const current = target.closest('[data-vls-group]'); const entries = current.querySelector('[data-vls-entries]'); entries.insertAdjacentHTML('beforeend', entryTemplate(current.dataset.vlsGroupIndex)); const newEntry = entries.lastElementChild; updateEntryIdentity(newEntry); updateDestinationFields(newEntry); }
				if (target.matches('[data-vls-remove-entry]')) target.closest('[data-vls-entry]')?.remove();
				if (target.matches('[data-vls-move-up]')) { const item = target.closest('[data-vls-entry], [data-vls-group]'); const previous = item.previousElementSibling; if (previous) previous.before(item); }
				if (target.matches('[data-vls-move-down]')) { const item = target.closest('[data-vls-entry], [data-vls-group]'); const next = item.nextElementSibling; if (next) next.after(item); }
			});
		}());
		</script>
		<?php
	}
}
VLS_Settings_Page::init();
