<?php
/**
 * Abstract base class for Dokan block templates.
 *
 * @package AnotherBlocksDokan
 * @since 1.0.0
 */

namespace The_Another\Plugin\Blocks_Dokan\Templates;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract Dokan template class.
 */
abstract class Abstract_Dokan_Template {

	/**
	 * Template slug.
	 *
	 * @var string
	 */
	const SLUG = '';

	/**
	 * Initialization method.
	 *
	 * @return void
	 */
	public function init(): void {
		add_filter( 'get_block_templates', array( $this, 'register_block_template' ), 11, 3 );
		add_filter( 'pre_get_block_file_template', array( $this, 'get_block_file_template' ), 10, 3 );
	}

	/**
	 * Register block template with WordPress.
	 *
	 * @param array  $templates     Array of block templates.
	 * @param array  $query         Query arguments.
	 * @param string $template_type Template type ('wp_template' or 'wp_template_part').
	 * @return array Modified templates array.
	 */
	public function register_block_template( array $templates, array $query, string $template_type ): array {
		if ( 'wp_template' !== $template_type ) {
			return $templates;
		}

		// Check if this template matches the query.
		if ( ! empty( $query['slug__in'] ) && ! in_array( static::SLUG, $query['slug__in'], true ) ) {
			return $templates;
		}

		$template_path = $this->get_template_file_path();
		if ( ! file_exists( $template_path ) ) {
			return $templates;
		}

		// Create block template object.
		$template_content = file_get_contents( $template_path );
		if ( false === $template_content ) {
			return $templates;
		}

		$template                 = new \WP_Block_Template();
		$template->id             = 'dokan-blocks//' . static::SLUG;
		$template->theme          = 'another-dokan-blocks';
		$template->content        = $template_content;
		$template->slug           = static::SLUG;
		$template->title          = $this->get_template_title();
		$template->description    = $this->get_template_description();
		$template->source         = 'plugin';
		$template->type           = 'wp_template';
		$template->area           = 'uncategorized';
		$template->has_theme_file = false;
		$template->is_custom      = false;

		$templates[] = $template;

		return $templates;
	}

	/**
	 * Get block file template.
	 *
	 * @param \WP_Block_Template|null $template  Template object.
	 * @param string                  $id        Template ID.
	 * @param string                  $template_type Template type.
	 * @return \WP_Block_Template|null Template object or null.
	 */
	public function get_block_file_template( ?\WP_Block_Template $template, string $id, string $template_type ): ?\WP_Block_Template {
		if ( 'wp_template' !== $template_type ) {
			return $template;
		}

		// Check if this is our template.
		$template_id_parts = explode( '//', $id );
		if ( 2 !== count( $template_id_parts ) || 'another-dokan-blocks' !== $template_id_parts[0] || static::SLUG !== $template_id_parts[1] ) {
			return $template;
		}

		$template_path = $this->get_template_file_path();
		if ( ! file_exists( $template_path ) ) {
			return $template;
		}

		$template_content = file_get_contents( $template_path );
		if ( false === $template_content ) {
			return $template;
		}

		$block_template                 = new \WP_Block_Template();
		$block_template->id             = $id;
		$block_template->theme          = 'another-dokan-blocks';
		$block_template->content        = $template_content;
		$block_template->slug           = static::SLUG;
		$block_template->title          = $this->get_template_title();
		$block_template->description    = $this->get_template_description();
		$block_template->source         = 'plugin';
		$block_template->type           = 'wp_template';
		$block_template->area           = 'uncategorized';
		$block_template->has_theme_file = false;
		$block_template->is_custom      = false;

		return $block_template;
	}


	/**
	 * Check if this template should be rendered.
	 *
	 * @return bool
	 */
	abstract protected function should_render_template(): bool;

	/**
	 * Get template title.
	 *
	 * @return string
	 */
	abstract public function get_template_title(): string;

	/**
	 * Get template description.
	 *
	 * @return string
	 */
	abstract public function get_template_description(): string;

	/**
	 * Get template file path.
	 *
	 * @return string
	 */
	protected function get_template_file_path(): string {
		// Map template slug to file name.
		$template_file_map = array(
			'dokan-store'      => 'store.html',
			'dokan-store-list' => 'store-lists.html',
			'dokan-store-toc'  => 'store-toc.html',
		);

		$template_file = isset( $template_file_map[ static::SLUG ] )
			? $template_file_map[ static::SLUG ]
			: static::SLUG . '.html';

		return \ANOTHER_BLOCKS_DOKAN_PLUGIN_DIR . 'templates/' . $template_file;
	}
}
