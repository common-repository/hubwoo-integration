<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    hubwoo-integration
 * @subpackage hubwoo-integration/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    hubwoo-integration
 * @subpackage hubwoo-integration/includes
 * @author     MakeWebBetter <webmaster@makewebbetter.com>
 */
class Hubwoo {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Hubwoo_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'HUBWOO_VERSION' ) ) {

			$this->version = HUBWOO_VERSION;
		} else {

			$this->version = '4.0.1';
		}

		$this->plugin_name = 'hubwoo';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Hubwoo_Loader. Orchestrates the hooks of the plugin.
	 * - Hubwoo_i18n. Defines internationalization functionality.
	 * - Hubwoo_Admin. Defines all hooks for the admin area.
	 * - Hubwoo_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hubwoo-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hubwoo-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-hubwoo-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-hubwoo-public.php';

		$this->loader = new Hubwoo_Loader();

		/**
		 * The class responsible for all api actions with hubspot.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hubwoo-connection-manager.php';

		/**
		 * The class contains all the information related to customer groups and properties.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hubwoo-contact-properties.php';

		/**
		 * The class contains are readymade contact details to send it to
		 * hubspot.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hubwoo-customer.php';

		/**
		 * The class responsible for property values.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hubwoo-property-callbacks.php';

		/**
		 * The class responsible for handling ajax requests.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hubwoo-ajax-handler.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Hubwoo_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Hubwoo_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Hubwoo_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_filter( 'cron_schedules', $plugin_admin, 'hubwoo_set_cron_schedule_time' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'hubwoo_redirect_from_hubspot' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'hubwoo_add_privacy_message' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'hubwoo_property_update' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'hubwoo_reauthorize' );

		$plugin_enable = get_option( 'hubwoo_settings_enable', 'yes' );

		if ( 'yes' === $plugin_enable ) {

			$this->loader->add_action( 'hubwoo_cron_schedule', $plugin_admin, 'hubwoo_cron_schedule' );
			$this->loader->add_action( 'admin_init', $plugin_admin, 'hubwoo_update_new_addons_groups_properties' );
			$this->loader->add_action( 'woocommerce_order_status_changed', $plugin_admin, 'hubwoo_update_user' );
			$this->loader->add_action( 'woocommerce_update_order', $plugin_admin, 'hubwoo_update_user' );
			$this->loader->add_action( 'woocommerce_process_shop_order_meta', $plugin_admin, 'hubwoo_update_user' );
			$this->loader->add_action( 'set_user_role', $plugin_admin, 'hubwoo_add_user_to_update', 10 );
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Hubwoo_Public( $this->get_plugin_name(), $this->get_version() );

		$plugin_enable = get_option( 'hubwoo_settings_enable', 'yes' );

		if ( 'yes' === $plugin_enable ) {

			$this->loader->add_action( 'profile_update', $plugin_public, 'hubwoo_woocommerce_save_account_details' );
			$this->loader->add_action( 'user_register', $plugin_public, 'hubwoo_woocommerce_save_account_details' );
			$this->loader->add_action( 'woocommerce_checkout_update_user_meta', $plugin_public, 'hubwoo_woocommerce_save_account_details' );
			$this->loader->add_action( 'woocommerce_customer_save_address', $plugin_public, 'hubwoo_woocommerce_save_account_details' );
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {

		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Hubwoo_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {

		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {

		return $this->version;
	}

	/**
	 * Predefined default hubwoo tabs.
	 *
	 * @return  Array       An key=>value pair of hubspot tabs.
	 */
	public function hubwoo_default_tabs() {

		$default_tabs = array();

		$default_tabs['hubwoo_overview'] = array(
			'name'       => esc_html__( 'Get started', 'hubwoo' ),
			'dependency' => '',
		);
		$default_tabs['hubwoo_connect']  = array(
			'name'       => esc_html__( 'Connect', 'hubwoo' ),
			'dependency' => array( 'hubwoo_get_started' ),
		);
		$default_tabs['hubwoo_setup']    = array(
			'name'       => esc_html__( 'Setup', 'hubwoo' ),
			'dependency' => array( 'hubwoo_get_started', 'is_oauth_success', 'is_valid_client_ids_stored' ),
		);

		$common_dependency = array( 'is_oauth_success', 'is_valid_client_ids_stored', 'is_setup_completed' );

		$default_tabs['realtime-sync']    = array(
			'name'       => esc_html__( 'Real-time Sync', 'hubwoo' ),
			'dependency' => $common_dependency,
		);
		$default_tabs['general-settings'] = array(
			'name'       => esc_html__( 'General Settings', 'hubwoo' ),
			'dependency' => $common_dependency,
		);
		$default_tabs['hubwoo_deals']     = array(
			'name'       => esc_html__( 'HubSpot Deals', 'hubwoo' ),
			'dependency' => $common_dependency,
		);
		$default_tabs['hubwoo_lists']     = array(
			'name'       => esc_html__( 'Lists & Workflows', 'hubwoo' ),
			'dependency' => $common_dependency,
		);
		$default_tabs['hubwoo_ocs']       = array(
			'name'       => esc_html__( 'One-Click Sync', 'hubwoo' ),
			'dependency' => $common_dependency,
		);
		$default_tabs['hubwoo_abncart']   = array(
			'name'       => esc_html__( 'Abandoned Cart', 'hubwoo' ),
			'dependency' => $common_dependency,
		);
		$default_tabs['error-management'] = array(
			'name'       => esc_html__( 'Error Tracking', 'hubwoo' ),
			'dependency' => '',
		);

		return $default_tabs;
	}

	/**
	 * Checking dependencies for tabs.
	 *
	 * @since     1.0.0
	 * @param array $dependency set of dependencies to check.
	 */
	public function check_dependencies( $dependency = array() ) {

		$flag = true;

		global $hubwoo;

		if ( count( $dependency ) ) {

			foreach ( $dependency as $single_dependency ) {

				$flag &= $hubwoo->$single_dependency();
			}
		}

		return $flag;
	}

	/**
	 * Locate and load appropriate tempate.
	 *
	 * @since   1.0.0
	 * @param string $path path file for inclusion.
	 * @param array  $params parameters to pass to the file for access.
	 */
	public function load_template_view( $path, $params = array() ) {

		$file_path = HUBWOO_ABSPATH . $path;

		if ( file_exists( $file_path ) ) {

			include $file_path;
		} else {

			/* translators: %s: file path */
			$notice = sprintf( esc_html__( 'Unable to locate file at location "%s". Some features may not work properly in Integration with HubSpot for WooCommerce. Please contact us!', 'hubwoo' ), $file_path );
			$this->hubwoo_notice( $notice, 'error' );
		}
	}

	/**
	 * Show admin notices.
	 *
	 * @param  string $message    Message to display.
	 * @param  string $type       notice type, accepted values - error/update/update-nag.
	 * @since  1.0.0
	 */
	public static function hubwoo_notice( $message, $type = 'error' ) {

		$classes = 'notice ';

		switch ( $type ) {

			case 'update':
				$classes .= 'updated';
				break;

			case 'update-nag':
				$classes .= 'update-nag';
				break;

			case 'success':
				$classes .= 'notice-success is-dismissible';
				break;

			default:
				$classes .= 'error';
		}

		$notice  = '<div class="' . esc_attr( $classes ) . '">';
		$notice .= '<p>' . esc_html( $message ) . '</p>';
		$notice .= '</div>';

		echo wp_kses_post( $notice );
	}

	/**
	 * Check if access token is expired.
	 */
	public static function is_access_token_expired() {

		$get_expiry = get_option( 'hubwoo_token_expiry', false );

		if ( $get_expiry ) {

			$current_time = time();

			if ( $current_time > $get_expiry ) {

				return true;
			}
		}
		return false;
	}

	/**
	 * Check if valid hubspot client Ids is stored.
	 */
	public static function is_valid_client_ids_stored() {

		$hapikey = HUBWOO_CLIENT_ID;
		$hseckey = HUBWOO_SECRET_ID;

		if ( $hapikey && $hseckey ) {

			return get_option( 'hubwoo_valid_client_ids_stored', false );
		}

		return false;
	}

	/**
	 * Plugin developement suggestion popup
	 *
	 * @since 1.0.0
	 */
	public function is_display_suggestion_popup() {

		$suggest = get_option( 'hubwoo_send_suggestions', false );

		if ( $suggest ) {

			$success = get_option( 'hubwoo_suggestions_sent', false );

			if ( ! $success ) {

				$later = get_option( 'hubwoo_suggestions_later', false );

				if ( ! $later ) {

					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Verify if the hubspot setup is completed.
	 *
	 * @since 1.0.0
	 */
	public static function is_setup_completed() {

		return get_option( 'hubwoo_setup_completed', false );
	}

	/**
	 * Get plugin enable settings
	 *
	 * @since 1.0.0
	 */
	public static function is_plugin_enable() {

		return get_option( 'hubwoo_settings_enable', 'yes' );
	}

	/**
	 * Verify if the oauth has been succesfull.
	 *
	 * @since 1.0.0
	 */
	public static function is_oauth_success() {

		return get_option( 'hubwoo_oauth_success', false );
	}

	/**
	 * Checking for get started call is executed or not.
	 *
	 * @since 1.0.0
	 */
	public static function hubwoo_get_started() {

		if ( self::is_setup_completed() ) {

			return true;
		} else {

			return get_option( 'hubwoo_get_started', false );
		}
	}

	/**
	 * Getting owners email info.
	 *
	 * @since 1.0.0
	 */
	public function hubwoo_owners_email_info() {

		$owner_email = get_option( 'hubwoo_owners_portal', '' );

		if ( empty( $owner_email ) ) {

			if ( self::is_valid_client_ids_stored() ) {

				$flag = true;

				if ( self::is_access_token_expired() ) {

					$hapikey = HUBWOO_CLIENT_ID;
					$hseckey = HUBWOO_SECRET_ID;
					$status  = HubWooConnectionMananager::get_instance()->hubwoo_refresh_token( $hapikey, $hseckey );

					if ( ! $status ) {

						$flag = false;
					}
				}

				if ( $flag ) {

					$owner_email = HubWooConnectionMananager::get_instance()->hubwoo_get_owners_info();

					if ( ! empty( $owner_email ) ) {

						update_option( 'hubwoo_owners_portal', $owner_email );
					}
				}
			}
		}

		return $owner_email;
	}

	/**
	 * Clear saved options when a account is switched.
	 */
	public function hubwoo_switch_account() {

		delete_option( 'hubwoo_get_started' );
		delete_option( 'hubwoo_access_token' );
		delete_option( 'hubwoo_refresh_token' );
		delete_option( 'hubwoo_token_expiry' );
		delete_option( 'hubwoo_valid_client_ids_stored' );
		delete_option( 'hubwoo-free-success-api-calls' );
		delete_option( 'hubwoo-free-error-api-calls' );
		delete_option( 'hubwoo_send_suggestions' );
		delete_option( 'hubwoo_oauth_success' );
		delete_option( 'hubwoo_owners_portal' );
		delete_option( 'hubwoo_suggestions_later' );
		delete_option( 'hubwoo_setup_completed' );
		delete_option( 'hubwoo_free_version' );
		delete_option( 'hubwoo_free_property_update' );
		delete_option( 'hubwoo_newversion_groups_saved' );
		delete_option( 'hubwoo_ecomm_bridge_enabled' );
		delete_option( 'hubwoo_suggestions_sent' );
		delete_option( 'hubwoo_account_scopes' );
		delete_option( 'hubwoo_invalid_emails' );
		delete_option( 'hubwoo_alert_param_set' );

		wp_safe_redirect( admin_url( 'admin.php' ) . '?page=hubwoo' );
		exit();
	}
}
