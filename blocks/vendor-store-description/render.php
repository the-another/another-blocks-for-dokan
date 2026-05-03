<?php
/**
 * Store description block render function.
 *
 * @package The_Another_Blocks_For_Dokan
 * @since 1.0.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Store description block render function.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @param string               $content    Block content.
 * @param WP_Block             $block      Block instance.
 * @return string Rendered HTML.
 */
function tanbfd_render_vendor_store_description_block( array $attributes, string $content, WP_Block $block ): string {
	$vendor = \The_Another\Plugin\Blocks_For_Dokan\Renderers\Vendor_Renderer::resolve_vendor_from_context(
		$block->context['dokan/vendor'] ?? null,
		array(
			'description' => 'description',
		)
	);

	if ( empty( $vendor ) || empty( $vendor['id'] ) ) {
		return '';
	}

	$description = isset( $vendor['description'] ) ? (string) $vendor['description'] : '';
	if ( '' === trim( $description ) ) {
		return '';
	}

	$allow_html = ! empty( $attributes['allowHtml'] );

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => 'tanbfd--vendor-store-description',
		)
	);

	$rendered = $allow_html ? wp_kses_post( $description ) : esc_html( wp_strip_all_tags( $description ) );

	ob_start();
	?>
	<div <?php echo wp_kses_post( $wrapper_attributes ); ?>>
		<?php echo $rendered; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitized via wp_kses_post or esc_html above based on allowHtml. ?>
	</div>
	<?php
	return (string) ob_get_clean();
}
