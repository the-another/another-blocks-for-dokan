<?php
/**
 * Store website block render function.
 *
 * @package The_Another_Blocks_For_Dokan
 * @since 1.0.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Store website block render function.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @param string               $content    Block content.
 * @param WP_Block             $block      Block instance.
 * @return string Rendered HTML.
 */
function tanbfd_render_vendor_store_website_block( array $attributes, string $content, WP_Block $block ): string {
	$vendor = \The_Another\Plugin\Blocks_For_Dokan\Renderers\Vendor_Renderer::resolve_vendor_from_context(
		$block->context['dokan/vendor'] ?? null,
		array(
			'website' => 'website',
		)
	);

	if ( empty( $vendor ) || empty( $vendor['id'] ) ) {
		return '';
	}

	$website = isset( $vendor['website'] ) ? (string) $vendor['website'] : '';
	if ( '' === $website ) {
		return '';
	}

	$show_icon       = ! empty( $attributes['showIcon'] );
	$open_in_new_tab = ! empty( $attributes['openInNewTab'] );
	$show_label      = ! empty( $attributes['showLabel'] );
	$label_text      = isset( $attributes['label'] ) ? (string) $attributes['label'] : '';

	$display_url = preg_replace( '#^https?://#', '', $website );
	$display_url = rtrim( $display_url, '/' );

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => 'tanbfd--vendor-store-website',
		)
	);

	$link_attrs = '';
	if ( $open_in_new_tab ) {
		$link_attrs = ' target="_blank" rel="noopener noreferrer"';
	}

	ob_start();
	?>
	<div <?php echo wp_kses_post( $wrapper_attributes ); ?>>
		<dl>
			<?php if ( $show_label && '' !== $label_text ) : ?>
				<dt class="tanbfd--vendor-store-website__label"><?php echo esc_html( $label_text ); ?></dt>
			<?php endif; ?>
			<dd class="tanbfd--vendor-store-website__value">
				<?php if ( $show_icon ) : ?>
					<span class="dashicons dashicons-admin-links" aria-hidden="true"></span>
				<?php endif; ?>
				<a href="<?php echo esc_url( $website ); ?>"<?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static safe attributes constructed above. ?>>
					<?php echo esc_html( $display_url ); ?>
				</a>
			</dd>
		</dl>
	</div>
	<?php
	return (string) ob_get_clean();
}
