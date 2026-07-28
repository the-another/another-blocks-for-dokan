<?php
/**
 * Dependency check tests.
 *
 * @package The_Another_Blocks_For_Dokan
 * @since 1.0.0
 */

namespace The_Another\Plugin\Blocks_For_Dokan\Blocks\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;
use The_Another\Plugin\Blocks_For_Dokan\Install;

if ( ! defined( 'THE_ANOTHER_BLOCKS_FOR_DOKAN_MIN_WOOCOMMERCE_VERSION' ) ) {
	define( 'THE_ANOTHER_BLOCKS_FOR_DOKAN_MIN_WOOCOMMERCE_VERSION', '10.0.0' );
}

if ( ! defined( 'THE_ANOTHER_BLOCKS_FOR_DOKAN_MIN_DOKAN_VERSION' ) ) {
	define( 'THE_ANOTHER_BLOCKS_FOR_DOKAN_MIN_DOKAN_VERSION', '4.0.0' );
}

/**
 * Install test class.
 */
class InstallTest extends TestCase {

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
	 * Dependency detection runs at plugin load time, before `init`, so it must
	 * not touch the text domain.
	 *
	 * @return void
	 */
	public function test_detecting_missing_dependencies_translates_nothing(): void {
		$translated = array();
		Functions\when( '__' )->alias(
			function ( $text, $domain = 'default' ) use ( &$translated ) {
				if ( 'the-another-blocks-for-dokan' === $domain ) {
					$translated[] = $text;
				}
				return $text;
			}
		);
		Functions\when( 'get_plugins' )->justReturn( array() );

		$missing = Install::detect_missing_dependencies( false );

		$this->assertSame(
			array(
				array(
					'slug'      => 'woocommerce',
					'required'  => THE_ANOTHER_BLOCKS_FOR_DOKAN_MIN_WOOCOMMERCE_VERSION,
					'installed' => null,
				),
				array(
					'slug'      => 'dokan-lite',
					'required'  => THE_ANOTHER_BLOCKS_FOR_DOKAN_MIN_DOKAN_VERSION,
					'installed' => null,
				),
			),
			$missing
		);

		$this->assertSame(
			array(),
			$translated,
			'detect_missing_dependencies() runs before `init` and must not call __().'
		);
	}

	/**
	 * Runtime checks must not translate at plugin load time.
	 *
	 * This runs while the plugin file is being included, which is earlier than
	 * `plugins_loaded`, so it must defer every translation to the
	 * `admin_notices` callback it registers.
	 *
	 * @return void
	 */
	public function test_runtime_check_defers_translation_to_the_admin_notice(): void {
		$translated = array();
		Functions\when( '__' )->alias(
			function ( $text, $domain = 'default' ) use ( &$translated ) {
				if ( 'the-another-blocks-for-dokan' === $domain ) {
					$translated[] = $text;
				}
				return $text;
			}
		);
		Functions\when( 'esc_html__' )->returnArg();
		Functions\when( 'esc_html' )->returnArg();
		Functions\when( 'get_plugins' )->justReturn( array() );

		$notice = null;
		Functions\when( 'add_action' )->alias(
			function ( $hook, $callback ) use ( &$notice ) {
				if ( 'admin_notices' === $hook ) {
					$notice = $callback;
				}
				return true;
			}
		);

		$this->assertFalse( Install::runtime_check() );

		$this->assertSame(
			array(),
			$translated,
			'runtime_check() runs before `init` and must not call __().'
		);

		$this->assertIsCallable( $notice, 'An admin_notices callback should have been registered.' );

		ob_start();
		$notice();
		$output = (string) ob_get_clean();

		$this->assertStringContainsString( 'WooCommerce 10.0.0 or higher is not installed.', $output );
		$this->assertStringContainsString( 'Dokan Lite 4.0.0 or higher is not installed.', $output );
	}

	/**
	 * An outdated (but present) dependency is reported with its version.
	 *
	 * @return void
	 */
	public function test_outdated_dependency_is_detected_with_installed_version(): void {
		Functions\when( '__' )->returnArg();
		Functions\when( 'get_plugins' )->justReturn(
			array(
				'woocommerce/woocommerce.php' => array( 'Version' => '9.1.0' ),
				'dokan-lite/dokan.php'        => array( 'Version' => '4.2.0' ),
			)
		);

		$missing = Install::detect_missing_dependencies( false );

		$this->assertCount( 1, $missing );
		$this->assertSame( 'woocommerce', $missing[0]['slug'] );
		$this->assertSame( '9.1.0', $missing[0]['installed'] );
	}

	/**
	 * Satisfied dependencies produce no findings.
	 *
	 * @return void
	 */
	public function test_satisfied_dependencies_report_nothing(): void {
		Functions\when( '__' )->returnArg();
		Functions\when( 'get_plugins' )->justReturn(
			array(
				'woocommerce/woocommerce.php' => array( 'Version' => '10.4.0' ),
				'dokan-lite/dokan.php'        => array( 'Version' => '4.2.0' ),
			)
		);

		$this->assertSame( array(), Install::detect_missing_dependencies( false ) );
		$this->assertSame( array(), Install::check_dependencies( false ) );
	}

	/**
	 * Human-readable messages are still produced by check_dependencies().
	 *
	 * @return void
	 */
	public function test_check_dependencies_renders_messages(): void {
		Functions\when( '__' )->returnArg();
		Functions\when( 'get_plugins' )->justReturn(
			array( 'dokan-lite/dokan.php' => array( 'Version' => '3.0.0' ) )
		);

		$this->assertSame(
			array(
				'WooCommerce 10.0.0 or higher is not installed.',
				'Dokan Lite 3.0.0 is installed, but version 4.0.0 or higher is required.',
			),
			Install::check_dependencies( false )
		);
	}
}
