<?php
/**
 * Emits the shared modal only after a modal block instance was rendered.
 */
class VLS_Runtime {
	/** @var bool */
	private static $modal_required = false;
	/** @var bool */
	private static $modal_rendered = false;

	/** @return void */
	public static function init() {
		add_action( 'wp_footer', array( __CLASS__, 'render_modal' ), 5 );
	}

	/** @return void */
	public static function require_modal() {
		self::$modal_required = true;
	}

	/** @return void */
	public static function render_modal() {
		if ( ! self::$modal_required || self::$modal_rendered || is_admin() ) {
			return;
		}

		self::$modal_rendered = true;
		echo vls_render_modal(); // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

VLS_Runtime::init();
