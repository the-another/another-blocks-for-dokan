<?php
/**
 * Store header block unit tests.
 *
 * @package AnotherBlocksDokan
 * @since 1.0.0
 */

namespace The_Another\Plugin\Blocks_Dokan\Blocks\Tests\Unit\Blocks;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery;
use The_Another\Plugin\Blocks_Dokan\Blocks\Tests\Factories\VendorFactory;

/**
 * Store header block test class.
 */
class StoreHeaderBlockTest extends TestCase {

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
	 * Test render with valid vendor ID.
	 *
	 * @return void
	 */
	public function test_render_with_valid_vendor_id(): void {
		$vendor_id = 123;
		$vendor    = VendorFactory::create( array( 'ID' => $vendor_id ) );

		Functions\when( 'current_user_can' )
			->with( 'read' )
			->andReturn( true );

		Functions\when( 'dokan_is_user_seller' )
			->with( $vendor_id )
			->andReturn( true );

		$dokan_mock  = Mockery::mock( 'Dokan' );
		$vendor_mock = Mockery::mock( 'vendor' );
		$vendor_mock->shouldReceive( 'get' )
			->with( $vendor_id )
			->andReturn( $vendor );

		Functions\when( 'dokan' )->justReturn( $dokan_mock );
		Functions\when( 'dokan_get_seller_short_address' )
			->with( $vendor_id, false )
			->andReturn( '123 Main St' );

		Functions\when( 'get_block_wrapper_attributes' )
			->andReturn( 'class="wp-block-dokan-store-header"' );

		Functions\when( 'dokan_is_vendor_info_hidden' )
			->with( 'address' )
			->andReturn( false );

		Functions\when( 'dokan_is_vendor_info_hidden' )
			->with( 'phone' )
			->andReturn( false );

		Functions\when( 'dokan_is_vendor_info_hidden' )
			->with( 'email' )
			->andReturn( false );

		Functions\when( 'dokan_get_readable_seller_rating' )
			->with( $vendor_id )
			->andReturn( '<div class="rating">4.5</div>' );

		Functions\when( 'dokan_is_store_open' )
			->with( $vendor_id )
			->andReturn( true );

		Functions\when( 'dokan_get_social_profile_fields' )
			->andReturn( array() );

		$attributes = array(
			'vendorId'        => $vendor_id,
			'showBanner'      => true,
			'showContactInfo' => true,
			'showSocialLinks' => true,
			'showStoreHours'  => true,
			'layout'          => 'default',
		);

		$block_mock = Mockery::mock( 'WP_Block' );

		// Load render function.
		require_once DOKAN_BLOCKS_PLUGIN_DIR . 'blocks/store-header/render.php';

		$output = dokan_render_store_header_block( $attributes, '', $block_mock );

		$this->assertNotEmpty( $output );
		$this->assertStringContainsString( 'dokan-store-header', $output );
		$this->assertStringContainsString( 'Test Store', $output );
	}

	/**
	 * Test render escapes output.
	 *
	 * @return void
	 */
	public function test_render_escapes_output(): void {
		$vendor_id = 123;
		$vendor    = VendorFactory::create(
			array(
				'ID'        => $vendor_id,
				'shop_name' => '<script>alert("xss")</script>',
			)
		);

		Functions\when( 'current_user_can' )->andReturn( true );
		Functions\when( 'dokan_is_user_seller' )->andReturn( true );
		Functions\when( 'dokan' )->andReturn( Mockery::mock( 'Dokan' ) );
		Functions\when( 'get_block_wrapper_attributes' )->andReturn( '' );
		Functions\when( 'dokan_get_seller_short_address' )->andReturn( '' );
		Functions\when( 'dokan_is_vendor_info_hidden' )->andReturn( false );
		Functions\when( 'dokan_get_readable_seller_rating' )->andReturn( '' );
		Functions\when( 'dokan_is_store_open' )->andReturn( true );
		Functions\when( 'dokan_get_social_profile_fields' )->andReturn( array() );

		Functions\when( 'esc_html' )
			->andReturnUsing(
				function ( $text ) {
					return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
				}
			);

		require_once DOKAN_BLOCKS_PLUGIN_DIR . 'blocks/store-header/render.php';

		$attributes = array( 'vendorId' => $vendor_id );
		$block_mock = Mockery::mock( 'WP_Block' );

		$output = dokan_render_store_header_block( $attributes, '', $block_mock );

		$this->assertStringNotContainsString( '<script>', $output );
		$this->assertStringContainsString( '&lt;script&gt;', $output );
	}

	/**
	 * Test render with invalid vendor ID.
	 *
	 * @return void
	 */
	public function test_render_with_invalid_vendor_id(): void {
		Functions\when( 'current_user_can' )->andReturn( true );
		Functions\when( 'dokan_is_user_seller' )->with( 0 )->andReturn( false );
		Functions\when( 'get_query_var' )->andReturn( 0 );

		require_once DOKAN_BLOCKS_PLUGIN_DIR . 'blocks/store-header/render.php';

		$attributes = array( 'vendorId' => 0 );
		$block_mock = Mockery::mock( 'WP_Block' );

		$output = dokan_render_store_header_block( $attributes, '', $block_mock );

		$this->assertEmpty( $output );
	}
}
