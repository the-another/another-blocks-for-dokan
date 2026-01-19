<?php
/**
 * Store rendering utilities.
 *
 * @package AnotherBlocksDokan
 * @since 1.0.0
 */

namespace The_Another\Plugin\Blocks_Dokan\Renderers;

use The_Another\Plugin\Blocks_Dokan\Helpers\Context_Detector;
use The_Another\Plugin\Blocks_Dokan\Renderers\Vendor_Renderer;

/**
 * Store renderer class.
 */
class Store_Renderer {

	/**
	 * Render store header HTML.
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 * @return string Rendered HTML.
	 */
	public static function render_store_header( array $attributes ): string {
		$vendor_id = self::get_vendor_id_from_attributes( $attributes );
		if ( ! $vendor_id ) {
			return '';
		}

		$vendor_data = Vendor_Renderer::get_vendor_data( $vendor_id );
		if ( ! $vendor_data ) {
			return '';
		}

		$show_banner       = $attributes['showBanner'] ?? true;
		$show_contact_info = $attributes['showContactInfo'] ?? true;
		$show_social_links = $attributes['showSocialLinks'] ?? true;
		$show_store_hours  = $attributes['showStoreHours'] ?? true;
		$layout            = $attributes['layout'] ?? 'default';

		ob_start();
		?>
		<div class="dokan-store-header dokan-store-header-<?php echo esc_attr( $layout ); ?>">
			<?php if ( $show_banner && ! empty( $vendor_data['banner'] ) ) : ?>
				<div class="dokan-store-banner">
					<img src="<?php echo esc_url( $vendor_data['banner'] ); ?>" alt="<?php echo esc_attr( $vendor_data['shop_name'] ); ?>" />
				</div>
			<?php endif; ?>

			<div class="dokan-store-info">
				<?php if ( ! empty( $vendor_data['avatar'] ) ) : ?>
					<div class="dokan-store-avatar">
						<img src="<?php echo esc_url( $vendor_data['avatar'] ); ?>" alt="<?php echo esc_attr( $vendor_data['shop_name'] ); ?>" />
					</div>
				<?php endif; ?>

				<h1 class="dokan-store-name">
					<?php echo esc_html( $vendor_data['shop_name'] ); ?>
				</h1>

				<?php if ( $show_contact_info ) : ?>
					<ul class="dokan-store-contact-info">
						<?php if ( ! Vendor_Renderer::is_vendor_info_hidden( 'address' ) && ! empty( $vendor_data['address'] ) ) : ?>
							<li class="dokan-store-address">
								<?php echo wp_kses_post( $vendor_data['address'] ); ?>
							</li>
						<?php endif; ?>

						<?php if ( ! Vendor_Renderer::is_vendor_info_hidden( 'phone' ) && ! empty( $vendor_data['phone'] ) ) : ?>
							<li class="dokan-store-phone">
								<a href="tel:<?php echo esc_attr( $vendor_data['phone'] ); ?>">
									<?php echo esc_html( $vendor_data['phone'] ); ?>
								</a>
							</li>
						<?php endif; ?>

						<?php if ( ! Vendor_Renderer::is_vendor_info_hidden( 'email' ) && ! empty( $vendor_data['email'] ) ) : ?>
							<li class="dokan-store-email">
								<a href="mailto:<?php echo esc_attr( antispambot( $vendor_data['email'] ) ); ?>">
									<?php echo esc_html( antispambot( $vendor_data['email'] ) ); ?>
								</a>
							</li>
						<?php endif; ?>

						<li class="dokan-store-rating">
							<?php echo wp_kses_post( Vendor_Renderer::get_seller_rating_html( $vendor_id ) ); ?>
						</li>

						<?php if ( $show_store_hours ) : ?>
							<li class="dokan-store-hours">
								<?php
								if ( Vendor_Renderer::is_store_open( $vendor_id ) ) {
									echo esc_html__( 'Store Open', 'another-dokan-blocks' );
								} else {
									echo esc_html__( 'Store Closed', 'another-dokan-blocks' );
								}
								?>
							</li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $show_social_links && ! empty( $vendor_data['social_profiles'] ) ) : ?>
					<ul class="dokan-store-social-links">
						<?php
						$social_fields = dokan_get_social_profile_fields();
						foreach ( $social_fields as $key => $field ) {
							if ( ! empty( $vendor_data['social_profiles'][ $key ] ) ) {
								?>
								<li>
									<a href="<?php echo esc_url( $vendor_data['social_profiles'][ $key ] ); ?>" target="_blank" rel="noopener noreferrer">
										<span class="dokan-social-icon dokan-social-<?php echo esc_attr( $key ); ?>">
											<?php echo esc_html( $field['title'] ?? $key ); ?>
										</span>
									</a>
								</li>
								<?php
							}
						}
						?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get vendor ID from block attributes or context.
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 * @return int|null Vendor ID or null.
	 */
	private static function get_vendor_id_from_attributes( array $attributes ): ?int {
		if ( ! empty( $attributes['vendorId'] ) ) {
			$vendor_id = absint( $attributes['vendorId'] );
			if ( $vendor_id > 0 && dokan_is_user_seller( $vendor_id ) ) {
				return $vendor_id;
			}
		}

		// Try to auto-detect from context.
		$vendor_id = Context_Detector::get_vendor_id();
		if ( $vendor_id ) {
			return $vendor_id;
		}

		return null;
	}
}
