<?php
/**
 * Main plugin class.
 *
 * @package AnotherBlocksDokan
 * @since 1.0.0
 */

namespace The_Another\Plugin\Blocks_Dokan;

use The_Another\Plugin\Blocks_Dokan\Templates\Block_Templates_Controller;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
class Blocks {

	/**
	 * Plugin instance.
	 *
	 * @var Blocks|null
	 */
	private static ?Blocks $instance = null;

	/**
	 * Block templates controller.
	 *
	 * @var Block_Templates_Controller
	 */
	private Block_Templates_Controller $templates_controller;

	/**
	 * Block registry.
	 *
	 * @var Block_Registry
	 */
	private Block_Registry $block_registry;

	/**
	 * Get plugin instance.
	 *
	 * @return Blocks
	 */
	public static function get_instance(): Blocks {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->templates_controller = new Block_Templates_Controller();
		$this->block_registry       = new Block_Registry();
	}

	/**
	 * Initialize plugin.
	 *
	 * @return void
	 */
	public function init(): void {
		// Initialize block templates system.
		$this->templates_controller->init();

		// Register all blocks.
		$this->block_registry->register_all_blocks();

		// Initialize other components.
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	private function init_hooks(): void {
		// Enqueue block assets.
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_block_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
	}

	/**
	 * Enqueue block assets (frontend + editor).
	 *
	 * @return void
	 */
	public function enqueue_block_assets(): void {
		// Always enqueue frontend/shared styles (both frontend and editor need them).
		$frontend_style = 'dist/style-blocks.css';
		if ( file_exists( \ANOTHER_BLOCKS_DOKAN_PLUGIN_DIR . $frontend_style ) ) {
			wp_enqueue_style(
				'dokan-blocks-frontend',
				\ANOTHER_BLOCKS_DOKAN_PLUGIN_URL . $frontend_style,
				array(),
				\ANOTHER_BLOCKS_DOKAN_VERSION
			);
		}

		// Enqueue editor-specific styles when in admin/editor context.
		if ( is_admin() ) {
			$editor_style = 'dist/blocks.css';
			if ( file_exists( \ANOTHER_BLOCKS_DOKAN_PLUGIN_DIR . $editor_style ) ) {
				wp_enqueue_style(
					'dokan-blocks-editor',
					\ANOTHER_BLOCKS_DOKAN_PLUGIN_URL . $editor_style,
					array( 'dokan-blocks-frontend' ),
					\ANOTHER_BLOCKS_DOKAN_VERSION
				);
			}
		}
	}

	/**
	 * Enqueue block editor assets.
	 *
	 * @return void
	 */
	public function enqueue_block_editor_assets(): void {
		// Check if build file exists.
		$editor_script      = 'dist/blocks.js';
		$editor_script_path = \ANOTHER_BLOCKS_DOKAN_PLUGIN_DIR . $editor_script;

		if ( file_exists( $editor_script_path ) ) {
			// Load asset file for dependencies and version.
			$asset_file = \ANOTHER_BLOCKS_DOKAN_PLUGIN_DIR . 'dist/blocks.asset.php';
			$asset      = file_exists( $asset_file ) ? require $asset_file : array(
				'dependencies' => array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ),
				'version'      => \ANOTHER_BLOCKS_DOKAN_VERSION,
			);

			wp_enqueue_script(
				'dokan-blocks-editor',
				\ANOTHER_BLOCKS_DOKAN_PLUGIN_URL . $editor_script,
				$asset['dependencies'],
				$asset['version'],
				true
			);

			// Editor styles should be declared in block.json using editorStyle property.
			// WordPress will automatically handle them correctly for the editor iframe.
		}
	}
}
