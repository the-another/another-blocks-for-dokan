<?php
/**
 * Store address block render function.
 *
 * @package The_Another_Blocks_For_Dokan
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Format address array into a string.
 *
 * @param array $address Address array.
 * @return string Formatted address.
 */
function tanbfd_format_address( array $address ): string {
	$parts = array();

	if ( ! empty( $address['street_1'] ) ) {
		$parts[] = $address['street_1'];
	}
	if ( ! empty( $address['street_2'] ) ) {
		$parts[] = $address['street_2'];
	}

	$city_state_zip = array();
	if ( ! empty( $address['city'] ) ) {
		$city_state_zip[] = $address['city'];
	}
	if ( ! empty( $address['state'] ) ) {
		$city_state_zip[] = $address['state'];
	}
	if ( ! empty( $address['zip'] ) ) {
		$city_state_zip[] = $address['zip'];
	}

	if ( ! empty( $city_state_zip ) ) {
		$parts[] = implode( ', ', $city_state_zip );
	}

	if ( ! empty( $address['country'] ) ) {
		$parts[] = $address['country'];
	}

	return ! empty( $parts ) ? implode( ', ', $parts ) : '';
}

/**
 * Store address block render function.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @param string               $content    Block content.
 * @param WP_Block             $block      Block instance.
 * @return string Rendered HTML.
 */
function tanbfd_render_vendor_store_address_block( array $attributes, string $content, WP_Block $block ): string {
	// Get vendor data from context, falling back to page context detection.
	$vendor = \The_Another\Plugin\Blocks_For_Dokan\Renderers\Vendor_Renderer::resolve_vendor_from_context(
		$block->context['dokan/vendor'] ?? null,
		array(
			'address' => 'address',
		)
	);

	if ( empty( $vendor ) || empty( $vendor['id'] ) ) {
		return '<div class="tanbfd--vendor-store-address">123 Main St, City, Country</div>';
	}

	$address            = $vendor['address'] ?? array();
	$show_icon          = $attributes['showIcon'] ?? true;
	$show_google_maps   = $attributes['showGoogleMaps'] ?? false;
	$show_apple_maps    = $attributes['showAppleMaps'] ?? false;
	$show_openstreetmap = $attributes['showOpenStreetMap'] ?? false;
	$button_style       = (string) ( $attributes['buttonStyle'] ?? '' );
	$button_size        = (string) ( $attributes['buttonSize'] ?? '' );
	$show_label         = ! empty( $attributes['showLabel'] );
	$label_text         = isset( $attributes['label'] ) ? (string) $attributes['label'] : '';

	$button_wrapper_classes = array( 'wp-block-button', 'tanbfd--vendor-store-address__map-link' );
	if ( '' !== $button_style ) {
		$button_wrapper_classes[] = 'is-style-' . sanitize_html_class( $button_style );
	}

	$button_link_classes = array( 'wp-block-button__link', 'wp-element-button' );
	if ( '' !== $button_size ) {
		$button_link_classes[] = 'has-' . sanitize_html_class( $button_size ) . '-font-size';
	}

	// Format the address.
	$formatted_address = '';
	if ( is_array( $address ) ) {
		$formatted_address = tanbfd_format_address( $address );
	} elseif ( is_string( $address ) ) {
		$formatted_address = $address;
	}

	// If no address, return empty.
	if ( empty( $formatted_address ) ) {
		return '';
	}

	$map_query    = trim( wp_strip_all_tags( $formatted_address ) );
	$map_services = array();
	if ( '' !== $map_query ) {
		if ( $show_google_maps ) {
			$map_services['google'] = array(
				'label' => __( 'Google Maps', 'the-another-blocks-for-dokan' ),
				'url'   => 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $map_query ),
			);
		}
		if ( $show_apple_maps ) {
			$map_services['apple'] = array(
				'label' => __( 'Apple Maps', 'the-another-blocks-for-dokan' ),
				'url'   => 'https://maps.apple.com/?q=' . rawurlencode( $map_query ),
			);
		}
		if ( $show_openstreetmap ) {
			$map_services['osm'] = array(
				'label' => __( 'OpenStreetMap', 'the-another-blocks-for-dokan' ),
				'url'   => 'https://www.openstreetmap.org/search?query=' . rawurlencode( $map_query ),
			);
		}
	}

	// Get wrapper attributes.
	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => 'tanbfd--vendor-store-address',
		)
	);

	ob_start();
	?>
	<div <?php echo wp_kses_post( $wrapper_attributes ); ?>>
		<dl>
			<?php if ( $show_label && '' !== $label_text ) : ?>
				<dt class="tanbfd--vendor-store-address__label"><?php echo esc_html( $label_text ); ?></dt>
			<?php endif; ?>
			<dd class="tanbfd--vendor-store-address__value">
				<span class="tanbfd--vendor-store-address__text">
					<?php if ( $show_icon ) : ?>
						<span class="dashicons dashicons-location" aria-hidden="true"></span>
					<?php endif; ?>
					<?php echo wp_kses_post( $formatted_address ); ?>
				</span>
				<?php if ( ! empty( $map_services ) ) : ?>
					<div class="wp-block-buttons tanbfd--vendor-store-address__map-links">
						<?php foreach ( $map_services as $service_key => $service ) : ?>
							<?php
							$service_button_classes = array_merge(
								$button_wrapper_classes,
								array( 'tanbfd--vendor-store-address__map-link--' . sanitize_html_class( $service_key ) )
							);
							?>
							<div class="<?php echo esc_attr( implode( ' ', $service_button_classes ) ); ?>">
								<a
									class="<?php echo esc_attr( implode( ' ', $button_link_classes ) ); ?>"
									href="<?php echo esc_url( $service['url'] ); ?>"
									target="_blank"
									rel="noopener noreferrer"
								>
									<?php echo esc_html( $service['label'] ); ?>
								</a>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</dd>
		</dl>
	</div>
	<?php
	return ob_get_clean();
}
