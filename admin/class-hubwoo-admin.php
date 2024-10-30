<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    hubwoo-integration
 * @subpackage hubwoo-integration/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    hubwoo-integration
 * @subpackage hubwoo-integration/admin
 * @author     MakeWebBetter <webmaster@makewebbetter.com>
 */
class Hubwoo_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// let's modularize our codebase, all the admin actions in one function.
		$this->admin_actions();
	}

	/**
	 * All admin actions.
	 *
	 * @since 1.0.0
	 */
	public function admin_actions() {

		// add submenu hubspot in woocommerce top menu.
		add_action( 'admin_menu', array( &$this, 'add_hubwoo_submenu' ) );
	}

	/**
	 * Add hubspot submenu in woocommerce menu.
	 *
	 * @since 1.0.0
	 */
	public function add_hubwoo_submenu() {

		add_submenu_page( 'woocommerce', esc_html__( 'HubSpot', 'hubwoo' ), esc_html__( 'HubSpot', 'hubwoo' ), 'manage_woocommerce', 'hubwoo', array( &$this, 'hubwoo_configurations' ) );
	}

	/**
	 * All the configuration related fields and settings.
	 *
	 * @since 1.0.0
	 */
	public function hubwoo_configurations() {

		include_once HUBWOO_ABSPATH . 'admin/templates/hubwoo-free-main-template.php';
	}

	/**
	 * General setting tab fields.
	 *
	 * @return array  woocommerce_admin_fields acceptable fields in array.
	 * @since 1.0.0
	 */
	public static function hubwoo_general_settings() {

		$basic_settings = array();

		$log_url = '<a target="_blank" href="' . esc_url( admin_url( 'admin.php' ) . '?page=wc-status&tab=logs' ) . '">' . esc_html__( 'Here', 'hubwoo' ) . '</a>';

		$basic_settings[] = array(
			'title' => esc_html__( 'Connect With HubSpot', 'hubwoo' ),
			'id'    => 'hubwoo_settings_title',
			'type'  => 'title',
		);
		$basic_settings[] = array(
			'title'   => esc_html__( 'Enable/Disable', 'hubwoo' ),
			'id'      => 'hubwoo_settings_enable',
			'class'   => 'hubwoo_common_checkbox',
			'desc'    => esc_html__( 'Turn on/off the integration', 'hubwoo' ),
			'type'    => 'checkbox',
			'default' => 'yes',
		);
		$basic_settings[] = array(
			'title'   => esc_html__( 'Enable/Disable', 'hubwoo' ),
			'id'      => 'hubwoo_log_enable',
			'class'   => 'hubwoo_common_checkbox',
			/* translators: %s: log url */
			'desc'    => sprintf( esc_html__( 'Enable logging of the requests. You can view HubSpot log file from %s', 'hubwoo' ), $log_url ),
			'type'    => 'checkbox',
			'default' => 'yes',
		);
		$basic_settings[] = array(
			'type' => 'sectionend',
			'id'   => 'hubwoo_settings_end',
		);
		return $basic_settings;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		$screen = get_current_screen();

		if ( isset( $screen->id ) && 'woocommerce_page_hubwoo' === $screen->id ) {

			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/hubwoo-admin.min.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$screen = get_current_screen();

		if ( isset( $screen->id ) && 'woocommerce_page_hubwoo' === $screen->id ) {

			wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/hubwoo-admin.min.js', array( 'jquery' ), $this->version, false );

			wp_localize_script(
				$this->plugin_name,
				'hubwooi18n',
				array(
					'ajaxUrl'                => admin_url( 'admin-ajax.php' ),
					'hubwooSecurity'         => wp_create_nonce( 'hubwoo_security' ),
					'hubwooWentWrong'        => esc_html__( 'Something went wrong, please try again later!', 'hubwoo' ),
					'hubwooSuccess'          => esc_html__( 'Setup is completed successfully!', 'hubwoo' ),
					'hubwooCreatingGroup'    => esc_html__( 'Created group', 'hubwoo' ),
					'hubwooCreatingProperty' => esc_html__( 'Created property', 'hubwoo' ),
					'hubwooSetupCompleted'   => esc_html__( 'Setup completed!', 'hubwoo' ),
					'hubwooMailFailure'      => esc_html__( 'Mail not sent', 'hubwoo' ),
					'hubwooMailSuccess'      => esc_html__( 'Mail sent successfully', 'hubwoo' ),
					'hubwooConnectTab'       => admin_url( 'admin.php' ) . '?page=hubwoo&hubwoo_tab=hubwoo_connect',
					'hubwooUpdateFail'       => esc_html__( 'Error while updating properties. Check the logs and try again.', 'hubwoo' ),
					'hubwooUpdateSuccess'    => esc_html__( 'All properties updated successfully.', 'hubwoo' ),
					'hubwooAccountSwitch'    => esc_html__( 'Want to continue to switch to new HubSpot account? This cannot be reverted and will require running the whole setup again.', 'hubwoo' ),
				)
			);

			wp_enqueue_script( $this->plugin_name );
		}
	}

	/**
	 * Update schedule data with custom time.
	 *
	 * @since    1.0.0
	 * @param      string $schedules       Schedule data.
	 */
	public function hubwoo_set_cron_schedule_time( $schedules ) {

		if ( ! isset( $schedules['mwb-hubwoo-five-min'] ) ) {

			$schedules['mwb-hubwoo-five-min'] = array(
				'interval' => 5 * 60,
				'display'  => esc_html__( 'Once every 5 minutes', 'hubwoo' ),
			);
		}

		return $schedules;
	}

	/**
	 * Schedule Executes when user data is update.
	 *
	 * @since 1.0.0
	 */
	public function hubwoo_cron_schedule() {

		if ( Hubwoo::is_setup_completed() ) {

			$args['meta_query'] = array(
				array(
					'key'     => 'hubwoo_user_data_change',
					'value'   => 'yes',
					'compare' => '==',
				),
			);

			$args['number'] = 5;

			$hubwoo_updated_user = get_users( $args );

			$contacts = array();

			$hubwoo_users = array();

			$hubwoo_users = apply_filters( 'hubwoo_users', $hubwoo_updated_user );

			$hubwoo_unique_users = array();

			foreach ( $hubwoo_users as $key => $value ) {

				if ( in_array( $value->ID, $hubwoo_unique_users, true ) ) {

					continue;
				} else {

					$hubwoo_unique_users[] = $value->ID;
				}
			}

			if ( isset( $hubwoo_unique_users ) && null !== $hubwoo_unique_users && count( $hubwoo_unique_users ) ) {

				foreach ( $hubwoo_unique_users as $key => $id ) {

					$hubwoo_customer = new HubWooCustomer( $id );

					$email = $hubwoo_customer->get_email();

					if ( empty( $email ) ) {

						delete_user_meta( $id, 'hubwoo_user_data_change' );
						continue;
					}

					$properties = $hubwoo_customer->get_contact_properties();

					$properties = apply_filters( 'hubwoo_map_new_properties', $properties, $id );

					$properties_data = array(
						'email'      => $email,
						'properties' => $properties,
					);

					$contacts[] = $properties_data;

					delete_user_meta( $id, 'hubwoo_user_data_change' );

					if ( self::hubwoo_check_for_cart( $properties ) ) {
						update_user_meta( $id, 'hubwoo_pro_user_cart_sent', 'yes' );
					}
				}
			}

			if ( count( $contacts ) && Hubwoo::is_valid_client_ids_stored() ) {

				$valid_token = true;

				if ( Hubwoo::is_access_token_expired() ) {

					$hapikey = HUBWOO_CLIENT_ID;
					$hseckey = HUBWOO_SECRET_ID;
					$status  = HubWooConnectionMananager::get_instance()->hubwoo_refresh_token( $hapikey, $hseckey );

					if ( ! $status ) {

						$valid_token = false;
					}
				}

				if ( $valid_token ) {

					$response = HubWooConnectionMananager::get_instance()->create_or_update_contacts( $contacts );

					if ( ( count( $contacts ) > 1 ) && isset( $response['status_code'] ) && 400 === $response['status_code'] ) {

						$response = self::hubwoo_split_contact_batch( $contacts );
					}
				}
			}

			$hubwoo_guest_cart = get_option( 'mwb_hubwoo_guest_user_cart', array() );

			$guest_abandoned_carts = array();

			if ( ! empty( $hubwoo_guest_cart ) ) {

				foreach ( $hubwoo_guest_cart as $key => &$single_cart ) {

					if ( ! empty( $single_cart['email'] ) ) {

						if ( ! empty( $single_cart['sent'] ) && 'yes' === $single_cart['sent'] ) {
							if ( empty( $single_cart['cartData'] ) || empty( $single_cart['cartData']['cart'] ) ) {
								unset( $hubwoo_guest_cart[ $key ] );
							}
							continue;
						}

						$guest_user_properties = apply_filters( 'hubwoo_pro_track_guest_cart', array(), $single_cart['email'] );

						if ( self::hubwoo_check_for_cart( $guest_user_properties ) ) {

							$single_cart['sent'] = 'yes';
						} elseif ( ! self::hubwoo_check_for_cart( $guest_user_properties ) && self::hubwoo_check_for_cart_contents( $guest_user_properties ) ) {

							$single_cart['sent'] = 'yes';
						}

						$guest_abandoned_carts[] = array(
							'email'      => $single_cart['email'],
							'properties' => $guest_user_properties,
						);
					}
				}

				update_option( 'mwb_hubwoo_guest_user_cart', $hubwoo_guest_cart );
			}

			if ( count( $guest_abandoned_carts ) ) {

				$chunked_array = array_chunk( $guest_abandoned_carts, 25, false );

				if ( ! empty( $chunked_array ) ) {

					foreach ( $chunked_array as $single_chunk ) {

						if ( Hubwoo::is_valid_client_ids_stored() ) {

							$flag = true;

							if ( Hubwoo::is_access_token_expired() ) {

								$hapikey = HUBWOO_CLIENT_ID;
								$hseckey = HUBWOO_SECRET_ID;
								$status  = HubWooConnectionMananager::get_instance()->hubwoo_refresh_token( $hapikey, $hseckey );

								if ( ! $status ) {

									$flag = false;
								}
							}

							if ( $flag ) {

								$response = HubWooConnectionMananager::get_instance()->create_or_update_contacts( $single_chunk );

								if ( ( count( $single_chunk ) > 1 ) && isset( $response['status_code'] ) && 400 === $response['status_code'] ) {

									$response = self::hubwoo_split_contact_batch( $single_chunk );
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Check if the outgoing properties has cart abndoned or not.
	 *
	 * @since 1.0.0
	 * @param array $properties list of all properties with names and values.
	 */
	public static function hubwoo_check_for_cart( $properties ) {

		$flag = false;

		if ( ! empty( $properties ) ) {

			foreach ( $properties as $single_record ) {

				if ( ! empty( $single_record['property'] ) ) {

					if ( 'current_abandoned_cart' === $single_record['property'] ) {

						$flag = ( 'yes' === $single_record['value'] ) ? true : false;
						break;
					}
				}
			}
		}

		return $flag;
	}

	/**
	 * Check if the outgoing properties has cart contents filled or not.
	 *
	 * @since 1.0.0
	 * @param array $properties list of all properties with names and values.
	 */
	public static function hubwoo_check_for_cart_contents( $properties ) {

		$flag = false;

		if ( ! empty( $properties ) ) {

			foreach ( $properties as $single_record ) {

				if ( ! empty( $single_record['property'] ) ) {

					if ( 'abandoned_cart_products' == $single_record['property'] ) {

						if ( empty( $single_record['value'] ) ) {

							$flag = true;
							break;
						}
					}
				}
			}
		}

		return $flag;
	}

	/**
	 * Generating access token
	 *
	 * @since    1.0.0
	 */
	public function hubwoo_redirect_from_hubspot() {

		if ( isset( $_GET['code'] ) ) {
			$hapikey = HUBWOO_CLIENT_ID;
			$hseckey = HUBWOO_SECRET_ID;

			if ( $hapikey && $hseckey ) {

				if ( ! Hubwoo::is_valid_client_ids_stored() ) {

					$response = HubWooConnectionMananager::get_instance()->hubwoo_fetch_access_token_from_code( $hapikey, $hseckey );
				}

				wp_safe_redirect( admin_url( 'admin.php' ) . '?page=hubwoo&hubwoo_tab=hubwoo_connect' );
				exit();
			}
		}
	}
	/**
	 * Adding more groups and properties for add-ons
	 *
	 * @since    1.1.0
	 */
	public function hubwoo_update_new_addons_groups_properties() {

		if ( Hubwoo::is_setup_completed() ) {

			$new_grp     = get_option( 'hubwoo_pro_newgroups_saved', false );
			$hubwoo_lock = get_option( 'hubwoo_lock', false );

			if ( $new_grp && ! $hubwoo_lock ) {

				if ( Hubwoo::is_valid_client_ids_stored() ) {

					$flag = true;

					if ( Hubwoo::is_access_token_expired() ) {

						$hapikey = HUBWOO_CLIENT_ID;
						$hseckey = HUBWOO_SECRET_ID;
						$status  = HubWooConnectionMananager::get_instance()->hubwoo_refresh_token( $hapikey, $hseckey );

						if ( ! $status ) {

							$flag = false;
						}
					}

					if ( $flag ) {

						update_option( 'hubwoo_lock', true );

						$groups     = array();
						$properties = array();

						$groups = apply_filters( 'hubwoo_new_contact_groups', $groups );

						foreach ( $groups as $key => $value ) {

							HubWooConnectionMananager::get_instance()->create_group( $value );

							$properties = apply_filters( 'hubwoo_new_active_group_properties', $properties, $value['name'] );

							foreach ( $properties as $key1 => $value1 ) {

								$value1['groupName'] = $value['name'];
								HubWooConnectionMananager::get_instance()->create_property( $value1 );
							}
						}

						update_option( 'hubwoo_pro_newgroups_saved', false );
						update_option( 'hubwoo_lock', false );
					}
				}
			}
		}
	}

	/**
	 * Alert notice on hubspot 404/400 error.
	 *
	 * @since 1.0.0
	 */
	public function hubwoo_dashboard_alert_notice() {

		if ( Hubwoo::is_valid_client_ids_stored() ) {

			$hubwoo_setup = get_option( 'hubwoo_setup_completed', false );
			$hubwoo_alert = get_option( 'hubwoo_alert_param_set', false );

			if ( $hubwoo_alert && $hubwoo_setup ) {

				$message = esc_html__( 'Something went wrong with HubSpot WooComerce Integration. Please check the logs.', 'hubwoo' );
				Hubwoo::hubwoo_notice( $message, 'error' );
			}
		}
	}

	/**
	 * Woocommerce privacy policy.
	 *
	 * @since 1.0.0
	 */
	public function hubwoo_add_privacy_message() {

		if ( function_exists( 'wp_add_privacy_policy_content' ) ) {

			$gdpr_url = esc_url( 'https://www.hubspot.com/data-privacy/gdpr' );

			$content = '<p>' . esc_html__( 'We use your email to send your Orders related data over HubSpot.', 'hubwoo' ) . '</p>';

			$content .= '<p>' . esc_html__( 'HubSpot is an inbound marketing and sales platform that helps companies attract visitors, convert leads, and close customers.', 'hubwoo' ) . '</p>';

			$content .= '<p>' . esc_html__( 'Please see the ', 'hubwoo' ) . '<a href="' . $gdpr_url . '" target="_blank" >' . esc_html__( 'HubSpot Data Privacy', 'hubwoo' ) . '</a>' . esc_html__( ' for more details.', 'hubwoo' ) . '</p>';

			if ( $content ) {

				wp_add_privacy_policy_content( esc_html__( 'Integration with HubSpot for WooCommerce', 'hubwoo' ), $content );
			}
		}
	}

	/**
	 * Redirection for reauth with hubspot app.
	 *
	 * @since 1.0.0
	 */
	public function hubwoo_reauth_with_new_app() {

		if ( isset( $_GET['action'] ) && sanitize_key( $_GET['action'] ) == 'reauth' ) {

			delete_option( 'hubwoo_oauth_success' );
			delete_option( 'hubwoo_valid_client_ids_stored' );
			$url         = esc_url( 'https://app.hubspot.com/oauth/authorize' );
			$hapikey     = HUBWOO_CLIENT_ID;
			$hubspot_url = add_query_arg(
				array(
					'client_id'    => $hapikey,
					'scope'        => 'oauth%20contacts',
					'redirect_uri' => admin_url( 'admin.php' ),
				),
				$url
			);
			wp_safe_redirect( $hubspot_url );
			exit();
		}
	}

	/**
	 * Notice for property update in order to maintain compatibility with new version.
	 *
	 * @since 1.0.0
	 */
	public function hubwoo_property_update() {

		if ( Hubwoo::is_setup_completed() ) {

			$property_update = get_option( 'hubwoo_free_property_update', false );

			if ( ! $property_update ) {

				$update_link = '<a href="' . esc_url( admin_url( 'admin.php' ) . '?page=hubwoo&hubwoo_tab=general-settings' ) . '">' . esc_html__( 'Click Here', 'hubwoo' ) . '</a>';
				/* translators: %s: update link */
				$message = sprintf( esc_html__( 'Please wait. We recommend you to update HubSpot properties to make it compatible with our latest version. %s ', 'hubwoo' ), $update_link );
				Hubwoo::hubwoo_notice( $message, 'error' );
			}
		}
	}

	/**
	 * Re-authorize with HubSpot account.
	 */
	public function hubwoo_reauthorize() {

		if ( isset( $_GET['action'] ) && ( ( 'reauth' === sanitize_key( $_GET['action'] ) ) ) ) {

			delete_option( 'hubwoo_oauth_success' );
			delete_option( 'hubwoo_valid_client_ids_stored' );

			$url     = 'https://app.hubspot.com/oauth/authorize';
			$hapikey = HUBWOO_CLIENT_ID;

			$hubspot_url = add_query_arg(
				array(
					'client_id'      => $hapikey,
					'optional_scope' => 'integration-sync%20e-commerce',
					'scope'          => 'oauth%20contacts',
					'redirect_uri'   => admin_url( 'admin.php' ),
				),
				$url
			);

			// phpcs:disable
			wp_redirect( $hubspot_url );
			exit;
			// phpcs:enable
		}
	}


	/**
	 * Fallback to split batch upload of contacts in small sizes.
	 *
	 * @since 1.0.0
	 * @param array $contacts list of contacts.
	 */
	public static function hubwoo_split_contact_batch( $contacts ) {

		$contacts_chunk = array_chunk( $contacts, ceil( count( $contacts ) / 2 ) );

		$response_chunk = array();

		if ( Hubwoo::is_valid_client_ids_stored() ) {

			$flag = true;

			if ( Hubwoo::is_access_token_expired() ) {

				$hapikey = HUBWOO_CLIENT_ID;
				$hseckey = HUBWOO_SECRET_ID;
				$status  = HubWooConnectionMananager::get_instance()->hubwoo_refresh_token( $hapikey, $hseckey );

				if ( ! $status ) {

					$flag = false;
				}
			}

			if ( $flag ) {

				if ( isset( $contacts_chunk[0] ) ) {

					$response_chunk = HubWooConnectionMananager::get_instance()->create_or_update_contacts( $contacts_chunk[0] );

					if ( isset( $response_chunk['status_code'] ) && 400 === $response_chunk['status_code'] ) {

						$response_chunk = self::hubwoo_single_contact_upload( $contacts_chunk[0] );
					}
				}
				if ( isset( $contacts_chunk[1] ) ) {

					$response_chunk = HubWooConnectionMananager::get_instance()->create_or_update_contacts( $contacts_chunk[1] );

					if ( isset( $response_chunk['status_code'] ) && 400 === $response_chunk['status_code'] ) {

						$response_chunk = self::hubwoo_single_contact_upload( $contacts_chunk[1] );
					}
				}
			}
		}

		return $response_chunk;
	}

	/**
	 * Fallback for single contact upload.
	 *
	 * @since 1.0.0
	 * @param array $contacts list of contact.
	 */
	public static function hubwoo_single_contact_upload( $contacts ) {

		if ( Hubwoo::is_valid_client_ids_stored() ) {

			$flag = true;

			if ( Hubwoo::is_access_token_expired() ) {

				$hapikey = HUBWOO_CLIENT_ID;
				$hseckey = HUBWOO_SECRET_ID;
				$status  = HubWooConnectionMananager::get_instance()->hubwoo_refresh_token( $hapikey, $hseckey );

				if ( ! $status ) {

					$flag = false;
				}
			}
		}

		if ( $flag ) {

			foreach ( $contacts as $single_contact ) {

				$response = HubWooConnectionMananager::get_instance()->create_or_update_contacts( array( $single_contact ) );
				sleep( 1 );
			}
		}

		return $response;
	}

	/**
	 * Update user meta when a order is updated.
	 *
	 * @param string $order_id id of the order.
	 */
	public function hubwoo_update_user( $order_id ) {

		if ( ! empty( $order_id ) ) {

			$user_id = (int) get_post_meta( $order_id, '_customer_user', true );

			if ( 0 !== $user_id && $user_id > 0 ) {

				update_user_meta( $user_id, 'hubwoo_user_data_change', 'yes' );
			}
		}
	}

	/**
	 * Update user meta when a user role is changed.
	 *
	 * @param string $user_id id of the user.
	 */
	public function hubwoo_add_user_to_update( $user_id ) {

		if ( ! empty( $user_id ) ) {

			update_user_meta( $user_id, 'hubwoo_user_data_change', 'yes' );
		}
	}
}
