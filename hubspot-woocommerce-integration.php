<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://makewebbetter.com/
 * @since             1.0.0
 * @package           hubwoo-integration
 *
 * @wordpress-plugin
 * Plugin Name:       Integration with HubSpot for WooCommerce
 * Plugin URI:        https://makewebbetter.com
 * Description:       A very powerful plugin to integrate your WooCommerce store with HubSpot seemlesly.
 * Version:                 4.0.1
 * Requires at least:       4.4
 * Tested up to:            5.3
 * WC requires at least:    3.0.0
 * WC tested up to:         3.7.0
 * Author:            MakeWebBetter
 * Author URI:        https://makewebbetter.com
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       hubwoo
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {

	die;
}

$activated = true;

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {

	$activated = false;
}

/**
 * Checking if WooCommerce is active
 */
if ( $activated ) {

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-hubwoo-activator.php
	 */
	function activate_hubwoo() {

		require_once plugin_dir_path( __FILE__ ) . 'includes/class-hubwoo-activator.php';
		Hubwoo_Activator::activate();
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-hubwoo-deactivator.php
	 */
	function deactivate_hubwoo() {

		require_once plugin_dir_path( __FILE__ ) . 'includes/class-hubwoo-deactivator.php';
		Hubwoo_Deactivator::deactivate();
	}

	register_activation_hook( __FILE__, 'activate_hubwoo' );
	register_deactivation_hook( __FILE__, 'deactivate_hubwoo' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-hubwoo.php';

	/**
	 * Define HubWoo constants.
	 *
	 * @since 1.0.0
	 */
	function hubwoo_define_constants() {

		hubwoo_define( 'HUBWOO_ABSPATH', dirname( __FILE__ ) . '/' );
		hubwoo_define( 'HUBWOO_URL', plugin_dir_url( __FILE__ ) . '/' );
		hubwoo_define( 'HUBWOO_VERSION', '4.0.1' );
		hubwoo_define( 'HUBWOO_CLIENT_ID', '769fa3e6-79b1-412d-b69c-6b8242b2c62a' );
		hubwoo_define( 'HUBWOO_SECRET_ID', '2893dd41-017e-4208-962b-12f7495d16b0' );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string $name name for the constant.
	 * @param string $value value for the constant.
	 * @since 1.0.0
	 */
	function hubwoo_define( $name, $value ) {

		if ( ! defined( $name ) ) {

			define( $name, $value );
		}
	}

	/**
	 * Setting Page Link.
	 *
	 * @param  array  $actions actions for the plugin.
	 * @param  string $plugin_file name of the plugin.
	 * @since 1.0.0
	 */
	function hubwoo_admin_settings( $actions, $plugin_file ) {

		static $plugin;

		if ( ! isset( $plugin ) ) {

			$plugin = plugin_basename( __FILE__ );
		}

		if ( $plugin === $plugin_file ) {

			$settings = array(
				'settings' => '<a href="' . esc_url( admin_url( 'admin.php' ) . '?page=hubwoo' ) . '">' . esc_html__( 'Settings', 'hubwoo' ) . '</a>',
			);
			$actions  = array_merge( $settings, $actions );
		}

		return $actions;
	}

	// add link for settings.
	add_filter( 'plugin_action_links', 'hubwoo_admin_settings', 10, 2 );

	/**
	 * Adding more rows in plugin data.
	 *
	 * @param  array  $links all links added for the plugin.
	 * @param  string $file name of the plugin.
	 * @since 1.0.0
	 */
	function hubwoo_free_plugin_row_meta( $links, $file ) {

		if ( strpos( $file, 'hubspot-woocommerce-integration.php' ) !== false ) {

			$row_meta = array(
				'docs'  => '<a style="color:#FFF;background-color:green;padding:5px;border-radius:6px;" href="https://docs.makewebbetter.com/hubspot-woocommerce-integration/">' . esc_html__( 'Go to Docs', 'hubwoo' ) . '</a>',
				'goPro' => '<a style="color:#FFF;background-color:#FF7A59;padding:5px;border-radius:6px;" href="https://makewebbetter.com/product/hubspot-woocommerce-integration-pro/?utm_source=MWB-huspot-org&utm_medium=MWB-ORG&utm_campaign=ORG"><strong>' . esc_html__( 'Go Premium', 'hubwoo' ) . '</strong></a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}

	add_filter( 'plugin_row_meta', 'hubwoo_free_plugin_row_meta', 10, 2 );

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_hubwoo() {

		// define contants if not defined..
		hubwoo_define_constants();
		$hub_woo = new Hubwoo();
		$hub_woo->run();
		$GLOBALS['hubwoo'] = $hub_woo;
	}
	run_hubwoo();
} else {

	/**
	 * Show warning message if woocommerce is not install
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter<webmaster@makewebbetter.com>
	 * @link https://makewebbetter.com
	 */
	function hubwoo_plugin_error_notice() {

		?>
		<div class="error notice is-dismissible">
		<p><?php esc_html_e( 'Woocommerce is not activated, Please activate Woocommerce first to install HubSpot WooCommerce Integration.', 'hubwoo' ); ?></p>
		</div>
		<style>
		#message{display:none;}
		</style>
		<?php
	}

	add_action( 'admin_init', 'hubwoo_plugin_deactivate' );


	/**
	 * Call Admin notices
	 *
	 * @author MakeWebBetter<webmaster@makewebbetter.com>
	 * @link https://www.makewebbetter.com/
	 */
	function hubwoo_plugin_deactivate() {

		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'hubwoo_plugin_error_notice' );
	}
}
?>
