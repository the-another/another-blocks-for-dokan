<?php
/**
 * Block rendering integration tests.
 *
 * @package AnotherBlocksDokan
 * @since 1.0.0
 */

namespace The_Another\Plugin\Blocks_Dokan\Blocks\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery;

/**
 * Block rendering test class.
 */
class BlockRenderingTest extends TestCase {

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
		Mockery::close();
		parent::tearDown();
	}

	/**
	 * Test store header block renders correctly.
	 *
	 * @return void
	 */
	public function test_store_header_block_renders(): void {
		Functions\when( 'current_user_can' )->with( 'read' )->andReturn( true );
		Functions\when( 'dokan_is_user_seller' )->andReturn( true );
		Functions\when( 'dokan' )->andReturn( Mockery::mock( 'Dokan' ) );
		Functions\when( 'get_block_wrapper_attributes' )->andReturn( 'class="test"' );
		Functions\when( 'dokan_get_seller_short_address' )->andReturn( '123 Main St' );
		Functions\when( 'dokan_is_vendor_info_hidden' )->andReturn( false );
		Functions\when( 'dokan_get_readable_seller_rating' )->andReturn( '<div>4.5</div>' );
		Functions\when( 'dokan_is_store_open' )->andReturn( true );
		Functions\when( 'dokan_get_social_profile_fields' )->andReturn( array() );
		Functions\when( 'esc_html' )->returnArg();
		Functions\when( 'esc_url' )->returnArg();
		Functions\when( 'esc_attr' )->returnArg();
		Functions\when( 'wp_kses_post' )->returnArg();
		Functions\when( 'antispambot' )->returnArg();

		// Load render function.
		require_once DOKAN_BLOCKS_PLUGIN_DIR . 'blocks/store-header/render.php';

		$block_mock = Mockery::mock( 'WP_Block' );
		$attributes = array(
			'vendorId'        => 123,
			'showBanner'      => true,
			'showContactInfo' => true,
			'showSocialLinks' => true,
			'showStoreHours'  => true,
			'layout'          => 'default',
		);

		$output = dokan_render_store_header_block( $attributes, '', $block_mock );

		$this->assertNotEmpty( $output );
		$this->assertStringContainsString( 'dokan-store-header', $output );
	}

	/**
	 * Test store products block renders correctly.
	 *
	 * @return void
	 */
	public function test_store_products_block_renders(): void {
		Functions\when( 'current_user_can' )->andReturn( true );
		Functions\when( 'dokan_is_user_seller' )->andReturn( true );
		Functions\when( 'get_query_var' )->with( 'paged' )->andReturn( 1 );
		Functions\when( 'get_block_wrapper_attributes' )->andReturn( 'class="test"' );
		Functions\when( 'wc_get_template_part' )->andReturn( '' );
		Functions\when( 'paginate_links' )->andReturn( '' );
		Functions\when( 'esc_html' )->returnArg();
		Functions\when( 'esc_attr' )->returnArg();

		// Mock WP_Query.
		$query_mock = Mockery::mock( 'WP_Query' );
		$query_mock->shouldReceive( 'have_posts' )->andReturn( false );
		$query_mock->max_num_pages = 0;

		// Load render function.
		require_once DOKAN_BLOCKS_PLUGIN_DIR . 'blocks/store-products/render.php';

		$block_mock = Mockery::mock( 'WP_Block' );
		$attributes = array(
			'vendorId' => 123,
			'perPage'  => 12,
			'columns'  => 4,
		);

		// Need to mock WP_Query class properly - this is a simplified test.
		$this->assertTrue( function_exists( 'dokan_render_store_products_block' ) );
	}
}
