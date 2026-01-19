<?php
/**
 * Template registration integration tests.
 *
 * @package AnotherBlocksDokan
 * @since 1.0.0
 */

namespace The_Another\Plugin\Blocks_Dokan\Blocks\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;
use Another_Blocks_Dokan\Templates\Block_Templates_Controller;

/**
 * Template registration test class.
 */
class TemplateRegistrationTest extends TestCase {

	/**
	 * Set up test.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Tear down test.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Test templates are initialized.
	 *
	 * @return void
	 */
	public function test_templates_are_initialized(): void {
		Functions\when( 'wp_is_block_theme' )->justReturn( true );
		Functions\when( 'get_block_templates' )->justReturn( array() );
		Functions\when( 'add_filter' )->justReturn( true );
		Functions\when( 'add_action' )->justReturn( true );

		$controller = new Block_Templates_Controller();
		$controller->init();

		$templates = $controller->get_templates();

		$this->assertNotEmpty( $templates );
		$this->assertCount( 2, $templates ); // Store and Store List templates.
	}

	/**
	 * Test template filter.
	 *
	 * @return void
	 */
	public function test_template_filter(): void {
		Functions\when( 'wp_is_block_theme' )->justReturn( true );
		Functions\when( 'get_block_templates' )->justReturn( array() );
		Functions\when( 'add_filter' )->justReturn( true );
		Functions\when( 'add_action' )->justReturn( true );
		Functions\when( 'file_exists' )->andReturn( true );

		add_filter(
			'dokan_blocks_registered_templates',
			function ( $templates ) {
				$this->assertIsArray( $templates );
				return $templates;
			}
		);

		$controller = new Block_Templates_Controller();
		$controller->init();

		$this->assertNotEmpty( $controller->get_templates() );
	}
}
