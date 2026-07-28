<?php
/**
 * Installation and activation handler.
 *
 * @package The_Another_Blocks_For_Dokan
 * @since 1.0.0
 */

namespace The_Another\Plugin\Blocks_For_Dokan;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Installation and activation handler class.
 */
class Install {

	/**
	 * Get plugin version from plugin file.
	 *
	 * @param string $plugin_file Path to the plugin file.
	 * @return string|null Plugin version or null if not found.
	 */
	private static function get_plugin_version( string $plugin_file ): ?string {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( ! file_exists( $plugin_file ) ) {
			return null;
		}

		$plugin_data = get_plugin_data( $plugin_file, false, false );
		return $plugin_data['Version'] ?? null;
	}

	/**
	 * Detect required plugins that are missing or below the minimum version.
	 *
	 * Deliberately free of translation calls: this runs while the plugin file is
	 * being included, long before `init`, and calling __() that early makes
	 * WordPress 6.7+ emit a `_load_textdomain_just_in_time` notice. Callers turn
	 * the result into human-readable text via {@see self::check_dependencies()}
	 * once it is safe to translate.
	 *
	 * @param bool $use_constants Whether to use defined constants (runtime) or read plugin files (activation).
	 * @return array<int, array{slug: string, required: string, installed: string|null}> Missing dependencies.
	 */
	public static function detect_missing_dependencies( bool $use_constants = true ): array {
		$missing_dependencies = array();

		// Check WooCommerce version.
		if ( $use_constants && defined( 'WC_VERSION' ) ) {
			$wc_version = WC_VERSION;
		} else {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$all_plugins = get_plugins();
			$wc_version  = $all_plugins['woocommerce/woocommerce.php']['Version'] ?? null;
		}

		if ( ! $wc_version || version_compare( $wc_version, THE_ANOTHER_BLOCKS_FOR_DOKAN_MIN_WOOCOMMERCE_VERSION, '<' ) ) {
			$missing_dependencies[] = array(
				'slug'      => 'woocommerce',
				'required'  => THE_ANOTHER_BLOCKS_FOR_DOKAN_MIN_WOOCOMMERCE_VERSION,
				'installed' => $wc_version ? $wc_version : null,
			);
		}

		// Check Dokan version.
		if ( $use_constants && defined( 'DOKAN_PLUGIN_VERSION' ) ) {
			$dokan_version = DOKAN_PLUGIN_VERSION;
		} else {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$all_plugins   = get_plugins();
			$dokan_version = $all_plugins['dokan-lite/dokan.php']['Version'] ?? null;
		}

		if ( ! $dokan_version || version_compare( $dokan_version, THE_ANOTHER_BLOCKS_FOR_DOKAN_MIN_DOKAN_VERSION, '<' ) ) {
			$missing_dependencies[] = array(
				'slug'      => 'dokan-lite',
				'required'  => THE_ANOTHER_BLOCKS_FOR_DOKAN_MIN_DOKAN_VERSION,
				'installed' => $dokan_version ? $dokan_version : null,
			);
		}

		return $missing_dependencies;
	}

	/**
	 * Turn one detected dependency problem into a translated message.
	 *
	 * Must only be called on or after `init`.
	 *
	 * @param array{slug: string, required: string, installed: string|null} $dependency Detected dependency problem.
	 * @return string Translated message.
	 */
	private static function format_dependency_message( array $dependency ): string {
		if ( 'woocommerce' === $dependency['slug'] ) {
			if ( null === $dependency['installed'] ) {
				return sprintf(
					/* translators: %s: minimum WooCommerce version */
					__( 'WooCommerce %s or higher is not installed.', 'the-another-blocks-for-dokan' ),
					$dependency['required']
				);
			}

			return sprintf(
				/* translators: 1: installed version, 2: minimum required version */
				__( 'WooCommerce %1$s is installed, but version %2$s or higher is required.', 'the-another-blocks-for-dokan' ),
				$dependency['installed'],
				$dependency['required']
			);
		}

		if ( null === $dependency['installed'] ) {
			return sprintf(
				/* translators: %s: minimum Dokan version */
				__( 'Dokan Lite %s or higher is not installed.', 'the-another-blocks-for-dokan' ),
				$dependency['required']
			);
		}

		return sprintf(
			/* translators: 1: installed version, 2: minimum required version */
			__( 'Dokan Lite %1$s is installed, but version %2$s or higher is required.', 'the-another-blocks-for-dokan' ),
			$dependency['installed'],
			$dependency['required']
		);
	}

	/**
	 * Check if required plugins meet minimum version requirements.
	 *
	 * Translates the result, so it must only be called on or after `init`. Use
	 * {@see self::detect_missing_dependencies()} for earlier checks.
	 *
	 * @param bool $use_constants Whether to use defined constants (runtime) or read plugin files (activation).
	 * @return array<int, string> Empty array if all requirements met, otherwise translated messages.
	 */
	public static function check_dependencies( bool $use_constants = true ): array {
		return array_map(
			static fn( array $dependency ): string => self::format_dependency_message( $dependency ),
			self::detect_missing_dependencies( $use_constants )
		);
	}

	/**
	 * Activation hook callback to check dependencies.
	 * Prevents plugin activation if dependencies are not met.
	 *
	 * @return void
	 */
	public static function activation_check(): void {
		// During activation, read plugin files directly as constants may not be defined yet.
		$missing_dependencies = self::check_dependencies( false );

		if ( ! empty( $missing_dependencies ) ) {
			// Deactivate the plugin immediately.
			deactivate_plugins( THE_ANOTHER_BLOCKS_FOR_DOKAN_PLUGIN_BASENAME );

			$error_message  = '<h3>' . esc_html__( 'Another Blocks for Dokan - Activation Failed', 'the-another-blocks-for-dokan' ) . '</h3>';
			$error_message .= '<p>' . esc_html__( 'The following requirements are not met:', 'the-another-blocks-for-dokan' ) . '</p>';
			$error_message .= '<ul style="list-style: disc; padding-left: 20px;">';
			foreach ( $missing_dependencies as $dependency ) {
				$error_message .= '<li>' . esc_html( $dependency ) . '</li>';
			}
			$error_message .= '</ul>';
			$error_message .= '<p>' . esc_html__( 'Please install and activate the required plugins, then try again.', 'the-another-blocks-for-dokan' ) . '</p>';

			wp_die(
				wp_kses_post( $error_message ),
				esc_html__( 'Plugin Activation Failed', 'the-another-blocks-for-dokan' ),
				array( 'back_link' => true )
			);
		}
	}

	/**
	 * Runtime dependency check and admin notice.
	 * Shows admin notice if dependencies are not met after activation.
	 *
	 * Called while the plugin file is being included — before `init` — so the
	 * detected problems are kept as raw data here and only translated inside the
	 * `admin_notices` callback, which runs late enough to load the text domain.
	 *
	 * @return bool True if all requirements met, false otherwise.
	 */
	public static function runtime_check(): bool {
		$runtime_missing_dependencies = self::detect_missing_dependencies();

		if ( ! empty( $runtime_missing_dependencies ) ) {
			add_action(
				'admin_notices',
				function () use ( $runtime_missing_dependencies ) {
					?>
					<div class="notice notice-error">
						<p><strong><?php echo esc_html__( 'Another Blocks for Dokan is disabled.', 'the-another-blocks-for-dokan' ); ?></strong></p>
						<p><?php echo esc_html__( 'The following requirements are not met:', 'the-another-blocks-for-dokan' ); ?></p>
						<ul style="list-style: disc; padding-left: 20px;">
							<?php foreach ( $runtime_missing_dependencies as $dependency ) : ?>
								<li><?php echo esc_html( self::format_dependency_message( $dependency ) ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
					<?php
				}
			);
			return false;
		}

		return true;
	}
}