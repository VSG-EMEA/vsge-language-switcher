<?php
/**
 * Dynamic renderer for the canonical vsge/language-switcher block.
 *
 * @var array  $attributes Block attributes.
 * @var string $content    Saved legacy content.
 */
if ( ! function_exists( 'pll_the_languages' ) && ! vls_has_external_destinations() ) {
	return;
}

$languages = vls_get_languages();
if ( empty( $languages ) ) {
	return;
}

$display_as = isset( $attributes['displayAs'] ) ? $attributes['displayAs'] : 'modal';
if ( ! in_array( $display_as, array( 'modal', 'dropdown', 'dataset' ), true ) ) {
	$display_as = 'modal';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class'                 => 'vls-block vls-block--' . $display_as,
		'data-vls-region-model' => wp_json_encode( VLS_Config::region_model() ),
	)
);

if ( 'dataset' === $display_as ) {
	$dataset = array( 'languages' => array_values( $languages ) );
	echo '<div ' . $wrapper_attributes . '><span class="vls-dataset" data-vls-dataset="' . esc_attr( wp_json_encode( $dataset ) ) . '"></span></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	return;
}

if ( 'dropdown' === $display_as ) {
	echo '<div ' . $wrapper_attributes . '>' . vls_render_language_select( $languages ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	return;
}

VLS_Runtime::require_modal();
$current = reset( $languages );
$current = is_array( $current ) ? $current : array( 'name' => __( 'Language switcher', 'vsge-language-switcher' ) );
foreach ( $languages as $language ) {
	if ( $language['current_lang'] ) {
		$current = $language;
		break;
	}
}
$has_icon   = ! empty( $attributes['buttonIcon'] );
$icon_color = isset( $attributes['iconColor'] ) ? sanitize_hex_color( $attributes['iconColor'] ) : '';
?>
<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<button type="button" class="vls-trigger" data-vls-open-modal aria-haspopup="dialog">
		<?php if ( $has_icon ) : ?>
			<svg class="vls-trigger__icon" viewBox="0 -960 960 960" aria-hidden="true" focusable="false"<?php echo $icon_color ? ' style="color:' . esc_attr( $icon_color ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>><path d="M479.761-126.154q-72.953 0-137.464-27.773-64.51-27.773-112.553-75.817-48.044-48.043-75.817-112.553-27.773-64.511-27.773-137.464 0-73.547 27.773-137.871 27.773-64.324 75.817-112.449 48.043-48.124 112.553-75.945 64.511-27.82 137.464-27.82 73.547 0 137.871 27.828 64.324 27.827 112.449 75.965 48.124 48.137 75.945 112.479 27.82 64.341 27.82 137.574 0 73.192-27.82 137.703-27.821 64.51-75.945 112.553-48.125 48.044-112.449 75.817-64.324 27.773-137.871 27.773Zm.239-34.769q35.84-45.251 58.747-89.722 22.907-44.47 37.599-98.586H384.039q15.461 57.577 37.983 101.663 22.523 44.086 57.978 86.645Zm-47.231-6.616q-28.615-34.115-52.096-82.749-23.481-48.635-34.88-98.943H191.885q34.384 75.731 97.923 123.25 63.538 47.519 142.961 58.442Zm94.462 0q79.038-10.538 142.769-58.25 63.731-47.711 98.5-123.442H614.592q-15.63 51.077-39.111 99.712-23.481 48.634-48.25 81.98ZM177.423-386.154h160.269q-4.538-24.731-6.423-48.269-1.885-23.539-1.885-45.577 0-22.038 1.885-45.577 0-23.538 6.423-48.269H177.423q-7.038 21.269-10.692 45.607-3.654 24.337-3.654 48.239 0 23.902 3.654 48.239 3.654 24.338 10.692 45.607Zm197.692 0h210.154q4.269-24.731 6.346-47.337 2.077-22.607 2.077-46.509t-2.077-46.509q-2.077-22.606-6.346-47.337H375.115q-4.653 24.731-6.73 47.337-2.077 22.607-2.077 46.509t2.077 46.509q2.077 22.606 6.73 47.337Zm247.193 0h160.654q6.653-21.269 10.307-45.607 3.654-24.337 3.654-48.239 0-23.902-3.654-48.239-3.654-24.338-10.307-45.607H622.308q4.538 24.731 6.423 48.269 1.885 23.539 1.885 45.577 0 22.038 1.885 45.577 0 22.038-1.885 45.577 0 22.038-6.423 48.269Zm-7.716-224.615H768.5q-35.154-76.885-96.961-123.25-61.808-46.366-144.308-59.212 28.615 37.962 51.519 85.443 22.904 47.48 35.842 97.019Zm-230.553 0h192.307q-15.846-56.423-39.522-102.625-23.676-46.201-56.824-85.683-33.148 39.482-56.824 85.683-23.676 46.202-39.137 102.625Zm-192.154 0h153.908q12.553-49.539 35.457-97.019 22.904-47.481 51.519-85.443-82.885 13.231-144.5 59.596-61.615 46.366-96.384 122.866Z"/></svg>
		<?php endif; ?>
		<span><?php echo esc_html( $current['name'] ); ?></span>
	</button>
</div>
