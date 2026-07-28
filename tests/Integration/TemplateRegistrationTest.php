<?php
/**
 * Template registration integration tests.
 *
 * @package The_Another_Blocks_For_Dokan
 * @since 1.0.0
 */

namespace The_Another\Plugin\Blocks_For_Dokan\Blocks\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;
use The_Another\Plugin\Blocks_For_Dokan\Blocks;
use The_Another\Plugin\Blocks_For_Dokan\Templates\Block_Templates_Controller;

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
		Functions\when( '__' )->returnArg();
		Functions\when( 'register_block_template' )->justReturn( null );
		Functions\when( 'apply_filters' )->alias(
			function ( $filter, $value ) {
				return $value;
			}
		);

		$controller = new Block_Templates_Controller();
		$controller->init();

		$templates = $controller->get_templates();

		$this->assertNotEmpty( $templates );
		$this->assertCount( 2, $templates );
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
		Functions\when( '__' )->returnArg();
		Functions\when( 'register_block_template' )->justReturn( null );
		Functions\when( 'apply_filters' )->alias(
			function ( $filter, $value ) {
				$this->assertIsArray( $value );
				return $value;
			}
		);

		$controller = new Block_Templates_Controller();
		$controller->init();

		$this->assertNotEmpty( $controller->get_templates() );
	}

	/**
	 * Template registration must not run at `plugins_loaded`.
	 *
	 * Registering a block template reads the template title/description, which
	 * are `__()` calls against the plugin text domain. WordPress 6.7+ emits a
	 * `_doing_it_wrong` notice when a text domain is loaded just-in-time before
	 * the `init` action, so `Blocks::init()` must defer the templates
	 * controller onto `init` rather than invoking it inline.
	 *
	 * @return void
	 */
	public function test_template_registration_is_deferred_to_the_init_hook(): void {
		$instance = new \ReflectionProperty( Blocks::class, 'instance' );
		$instance->setAccessible( true );
		$instance->setValue( null, null );

		Functions\when( '__' )->returnArg();
		Functions\when( 'has_action' )->justReturn( false );
		Functions\when( 'has_filter' )->justReturn( false );
		Functions\when( 'add_action' )->justReturn( true );
		Functions\when( 'add_filter' )->justReturn( true );
		Functions\when( 'wp_register_script' )->justReturn( true );
		Functions\when( 'apply_filters' )->alias(
			function ( $filter, $value ) {
				return $value;
			}
		);

		$registered = array();
		Functions\when( 'register_block_template' )->alias(
			function ( $template_name ) use ( &$registered ) {
				$registered[] = $template_name;
				return null;
			}
		);

		$plugin = Blocks::get_instance();

		// Simulates the `plugins_loaded` callback in the main plugin file.
		$plugin->init();

		$this->assertSame(
			array(),
			$registered,
			'No block template may be registered during `plugins_loaded`: reading their'
				. ' __() titles that early triggers the WP 6.7+ just-in-time textdomain notice.'
		);

		$deferred = array_values(
			array_filter(
				$plugin->get_hook_manager()->get_registered_hooks(),
				static function ( array $hook ): bool {
					return 'action' === $hook['type']
						&& 'init' === $hook['hook']
						&& is_array( $hook['callback'] )
						&& $hook['callback'][0] instanceof Block_Templates_Controller
						&& 'init' === $hook['callback'][1];
				}
			)
		);

		$this->assertCount(
			1,
			$deferred,
			'Blocks::init() should defer Block_Templates_Controller::init() onto the `init` action.'
		);

		// Firing `init` must still perform the registration.
		( $deferred[0]['callback'] )();

		$this->assertSame(
			array(
				'the-another-blocks-for-dokan//dokan-store',
				'the-another-blocks-for-dokan//dokan-store-toc',
				'the-another-blocks-for-dokan//dokan-store-list',
			),
			$registered,
			'All three templates should still be registered once `init` fires.'
		);
	}
}
