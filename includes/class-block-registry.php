<?php
/**
 * Block registration handler.
 *
 * @package AnotherBlocksDokan
 * @since 1.0.0
 */

namespace The_Another\Plugin\Blocks_Dokan;

/**
 * Block registry class.
 */
class Block_Registry {

	/**
	 * Registered blocks.
	 *
	 * @var array<string, string>
	 */
	private array $blocks = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->register_block_paths();
	}

	/**
	 * Register all blocks.
	 *
	 * @return void
	 */
	public function register_all_blocks(): void {
		foreach ( $this->blocks as $block_name => $block_dir ) {
			$this->register_block( $block_name, $block_dir );
		}
	}

	/**
	 * Register a single block.
	 *
	 * @param string $block_name Block name (e.g., 'the-another/dokan-store-header').
	 * @param string $block_dir  Block directory path.
	 * @return void
	 */
	private function register_block( string $block_name, string $block_dir ): void {
		$block_json_path = $block_dir . '/block.json';

		if ( ! file_exists( $block_json_path ) ) {
			return;
		}

		// Load render function if it exists.
		$render_file     = $block_dir . '/render.php';
		$render_callback = null;

		if ( file_exists( $render_file ) ) {
			require_once $render_file;

			// Map block name to render function.
			// Use the expected block name from our registry.
			$render_function_map = array(
				'the-another/dokan-store-header'           => 'dokan_render_store_header_block',
				'the-another/dokan-store-sidebar'          => 'dokan_render_store_sidebar_block',
				'the-another/dokan-store-tabs'             => 'dokan_render_store_tabs_block',
				'the-another/dokan-store-terms-conditions' => 'dokan_render_store_terms_conditions_block',
				'the-another/dokan-store-list'                 => 'dokan_render_store_list_block',
				'the-another/dokan-store-query-pagination'     => 'dokan_render_store_query_pagination_block',
				'the-another/dokan-store-card'                 => 'dokan_render_store_card_block',
				'the-another/dokan-store-search'               => 'dokan_render_store_search_block',
				'the-another/dokan-store-name'          => 'dokan_render_store_name_block',
				'the-another/dokan-store-avatar'        => 'dokan_render_store_avatar_block',
				'the-another/dokan-store-rating'        => 'dokan_render_store_rating_block',
				'the-another/dokan-store-address'       => 'dokan_render_store_address_block',
				'the-another/dokan-store-phone'         => 'dokan_render_store_phone_block',
				'the-another/dokan-store-status'        => 'dokan_render_store_status_block',
				'the-another/dokan-store-banner'        => 'dokan_render_store_banner_block',
				'the-another/dokan-product-vendor-info' => 'dokan_render_product_vendor_info_block',
				'the-another/dokan-more-from-seller'    => 'dokan_render_more_from_seller_block',
				'the-another/dokan-store-contact-form'  => 'dokan_render_store_contact_form_block',
				'the-another/dokan-store-location'      => 'dokan_render_store_location_block',
				'the-another/dokan-store-hours'         => 'dokan_render_store_hours_block',
				'the-another/dokan-become-vendor-cta'   => 'dokan_render_become_vendor_cta_block',
			);

			if ( isset( $render_function_map[ $block_name ] ) && function_exists( $render_function_map[ $block_name ] ) ) {
				$render_callback = $render_function_map[ $block_name ];
			}
		}

		// Register block with render callback.
		// WordPress will read the block name from block.json automatically.
		$args = array();
		if ( $render_callback ) {
			$args['render_callback'] = $render_callback;
		}

		// Use register_block_type_from_metadata for better compatibility.
		if ( function_exists( 'register_block_type_from_metadata' ) ) {
			register_block_type_from_metadata( $block_json_path, $args );
		} else {
			register_block_type( $block_json_path, $args );
		}
	}

	/**
	 * Register block paths.
	 *
	 * @return void
	 */
	private function register_block_paths(): void {
		$blocks_dir = \ANOTHER_BLOCKS_DOKAN_PLUGIN_DIR . 'blocks/';

		// Store profile blocks.
		$this->blocks['the-another/dokan-store-header']           = $blocks_dir . 'store-header';
		$this->blocks['the-another/dokan-store-sidebar']          = $blocks_dir . 'store-sidebar';
		$this->blocks['the-another/dokan-store-tabs']             = $blocks_dir . 'store-tabs';
		$this->blocks['the-another/dokan-store-terms-conditions'] = $blocks_dir . 'store-terms-conditions';

		// Vendor listing blocks.
		$this->blocks['the-another/dokan-store-list']          = $blocks_dir . 'store-query-loop';
		$this->blocks['the-another/dokan-store-query-pagination'] = $blocks_dir . 'store-query-pagination';
		$this->blocks['the-another/dokan-store-card']          = $blocks_dir . 'store-card';
		$this->blocks['the-another/dokan-store-search']        = $blocks_dir . 'store-search';

		// Store field blocks (for use inside store query loop).
		$this->blocks['the-another/dokan-store-name']    = $blocks_dir . 'store-name';
		$this->blocks['the-another/dokan-store-avatar']  = $blocks_dir . 'store-avatar';
		$this->blocks['the-another/dokan-store-rating']  = $blocks_dir . 'store-rating';
		$this->blocks['the-another/dokan-store-address'] = $blocks_dir . 'store-address';
		$this->blocks['the-another/dokan-store-phone']   = $blocks_dir . 'store-phone';
		$this->blocks['the-another/dokan-store-status']  = $blocks_dir . 'store-status';
		$this->blocks['the-another/dokan-store-banner']  = $blocks_dir . 'store-banner';

		// Product integration blocks.
		$this->blocks['the-another/dokan-product-vendor-info'] = $blocks_dir . 'product-vendor-info';
		$this->blocks['the-another/dokan-more-from-seller']    = $blocks_dir . 'more-from-seller';

		// Account/registration blocks.
		$this->blocks['the-another/dokan-become-vendor-cta'] = $blocks_dir . 'become-vendor-cta';

		// Widget blocks.
		$this->blocks['the-another/dokan-store-contact-form'] = $blocks_dir . 'store-contact-form';
		$this->blocks['the-another/dokan-store-location']     = $blocks_dir . 'store-location';
		$this->blocks['the-another/dokan-store-hours']        = $blocks_dir . 'store-hours';

		/**
		 * Filter registered block paths.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, string> $blocks Block paths.
		 */
		$this->blocks = apply_filters( 'dokan_blocks_registered_blocks', $this->blocks );
	}

	/**
	 * Check if a block is registered.
	 *
	 * @param string $block_name Block name.
	 * @return bool
	 */
	public function is_registered( string $block_name ): bool {
		return isset( $this->blocks[ $block_name ] );
	}

	/**
	 * Get all registered block names.
	 *
	 * @return array<string>
	 */
	public function get_registered_blocks(): array {
		return array_keys( $this->blocks );
	}
}
