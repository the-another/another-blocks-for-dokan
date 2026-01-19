<?php
/**
 * Block registration integration tests.
 *
 * @package AnotherBlocksDokan
 * @since 1.0.0
 */

namespace The_Another\Plugin\Blocks_Dokan\Blocks\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;
use Another_Blocks_Dokan\Block_Registry;

/**
 * Block registration test class.
 */
class BlockRegistrationTest extends TestCase {

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
	 * Test all blocks are registered.
	 *
	 * @return void
	 */
	public function test_all_blocks_are_registered(): void {
		Functions\when( 'register_block_type' )->justReturn( true );

		$registry = new Block_Registry();
		$registry->register_all_blocks();

		$expected_blocks = array(
			'the-another/dokan-store-header',
			'the-another/dokan-store-products',
			'the-another/dokan-store-sidebar',
			'the-another/dokan-store-tabs',
			'the-another/dokan-store-list',
			'the-another/dokan-store-card',
			'the-another/dokan-store-search',
			'the-another/dokan-product-vendor-info',
			'the-another/dokan-more-from-seller',
			'the-another/dokan-become-vendor-cta',
			'the-another/dokan-store-contact-form',
			'the-another/dokan-store-location',
			'the-another/dokan-store-hours',
		);

		$registered_blocks = $registry->get_registered_blocks();

		foreach ( $expected_blocks as $block_name ) {
			$this->assertTrue(
				$registry->is_registered( $block_name ),
				"Block {$block_name} should be registered"
			);
			$this->assertContains( $block_name, $registered_blocks );
		}
	}

	/**
	 * Test block registry filter.
	 *
	 * @return void
	 */
	public function test_block_registry_filter(): void {
		Functions\when( 'register_block_type' )->justReturn( true );

		add_filter(
			'dokan_blocks_registered_blocks',
			function ( $blocks ) {
				$blocks['the-another/dokan-custom-block'] = '/path/to/custom-block';
				return $blocks;
			}
		);

		$registry = new Block_Registry();

		$this->assertTrue( $registry->is_registered( 'the-another/dokan-custom-block' ) );
	}
}
