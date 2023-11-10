<?php

/**
 * Register the block by passing the location of block.json to register_block_type.
 */
add_action( 'init', 'vls_register_block' );
function vls_register_block() {
	register_block_type( VLS_PLUGIN_DIR, [
		'render_callback' => 'vls_get_languages'
	] );
}
