<?php
/**
 * Vendor data renderer.
 *
 * @package AnotherBlocksDokan
 * @since 1.0.0
 */

namespace The_Another\Plugin\Blocks_Dokan\Renderers;

/**
 * Vendor renderer class.
 */
class Vendor_Renderer {

	/**
	 * Get vendor data for rendering.
	 *
	 * @param int $vendor_id Vendor ID.
	 * @return array<string, mixed>|null Vendor data or null if not found.
	 */
	public static function get_vendor_data( int $vendor_id ): ?array {
		if ( ! $vendor_id || ! dokan_is_user_seller( $vendor_id ) ) {
			return null;
		}

		$vendor = dokan()->vendor->get( $vendor_id );
		if ( ! $vendor ) {
			return null;
		}

		$store_info = $vendor->get_shop_info();

		return array(
			'id'              => $vendor_id,
			'shop_name'       => $vendor->get_shop_name(),
			'shop_url'        => $vendor->get_shop_url(),
			'avatar'          => $vendor->get_avatar(),
			'banner'          => $vendor->get_banner(),
			'phone'           => $vendor->get_phone(),
			'email'           => $vendor->show_email() ? $vendor->get_email() : '',
			'address'         => dokan_get_seller_short_address( $vendor_id, false ),
			'rating'          => $vendor->get_rating(),
			'social_profiles' => $vendor->get_social_profiles(),
			'store_info'      => $store_info,
			'is_featured'     => $vendor->is_featured(),
		);
	}

	/**
	 * Get vendor store URL.
	 *
	 * @param int $vendor_id Vendor ID.
	 * @return string Store URL or empty string.
	 */
	public static function get_store_url( int $vendor_id ): string {
		if ( ! $vendor_id || ! dokan_is_user_seller( $vendor_id ) ) {
			return '';
		}

		$vendor = dokan()->vendor->get( $vendor_id );
		if ( ! $vendor ) {
			return '';
		}

		return esc_url( $vendor->get_shop_url() );
	}

	/**
	 * Check if vendor info field is hidden.
	 *
	 * @param string $field Field name (address, phone, email).
	 * @return bool True if hidden, false otherwise.
	 */
	public static function is_vendor_info_hidden( string $field ): bool {
		return dokan_is_vendor_info_hidden( $field );
	}

	/**
	 * Get readable seller rating HTML.
	 *
	 * @param int $vendor_id Vendor ID.
	 * @return string Rating HTML.
	 */
	public static function get_seller_rating_html( int $vendor_id ): string {
		if ( ! $vendor_id || ! dokan_is_user_seller( $vendor_id ) ) {
			return '';
		}

		return dokan_get_readable_seller_rating( $vendor_id );
	}

	/**
	 * Check if store is open.
	 *
	 * @param int $vendor_id Vendor ID.
	 * @return bool True if store is open.
	 */
	public static function is_store_open( int $vendor_id ): bool {
		if ( ! function_exists( 'dokan_is_store_open' ) ) {
			return true; // Default to open if function doesn't exist.
		}

		return dokan_is_store_open( $vendor_id );
	}
}
