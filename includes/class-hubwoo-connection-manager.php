<?php
/**
 * All api GET/POST functionalities.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    hubwoo-integration
 * @subpackage hubwoo-integration/includes
 */

/**
 * Handles all hubspot api reqests/response related functionalities of the plugin.
 *
 * Provide a list of functions to manage all the requests
 * that needs in our integration to get/fetch data
 * from/to hubspot.
 *
 * @package    hubwoo-integration
 * @subpackage hubwoo-integration/includes
 * @author     MakeWebBetter <webmaster@makewebbetter.com>
 */
class HubWooConnectionMananager {

	/**
	 * The single instance of the class.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var HubWooConnectionMananager   The single instance of the HubWooConnectionMananager
	 */
	protected static $_instance = null;

	/**
	 * Base url of hubspot api.
	 *
	 * @since 1.0.0
	 * @access Private
	 * @var string base url of the HubSpot API call.
	 */
	private $base_url = 'https://api.hubapi.com';


	/**
	 * Main HubWooConnectionMananager Instance.
	 *
	 * Ensures only one instance of HubWooConnectionMananager is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return HubWooConnectionMananager - Main instance.
	 */
	public static function get_instance() {

		if ( is_null( self::$_instance ) ) {

			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Refreshing access token from refresh token.
	 *
	 * @since 1.0.0
	 * @param string $hapikey Client ID for HubSpot APP.
	 * @param string $hseckey Client Secret for HubSpot APP.
	 */
	public function hubwoo_refresh_token( $hapikey, $hseckey ) {

		$endpoint      = '/oauth/v1/token';
		$refresh_token = get_option( 'hubwoo_refresh_token', false );
		$data          = array(
			'grant_type'    => 'refresh_token',
			'client_id'     => $hapikey,
			'client_secret' => $hseckey,
			'refresh_token' => $refresh_token,
			'redirect_uri'  => admin_url( 'admin.php' ),
		);
		$body          = http_build_query( $data );
		return $this->hubwoo_oauth_post_api( $endpoint, $body, 'refresh' );
	}

	/**
	 * Fetching access token from code.
	 *
	 * @since 1.0.0
	 * @param string $hapikey Client ID for HubSpot APP.
	 * @param string $hseckey Client Secret for HubSpot APP.
	 */
	public function hubwoo_fetch_access_token_from_code( $hapikey, $hseckey ) {
		if ( isset( $_GET['code'] ) ) {
			$code     = sanitize_key( $_GET['code'] );
			$endpoint = '/oauth/v1/token';
			$data     = array(
				'grant_type'    => 'authorization_code',
				'client_id'     => $hapikey,
				'client_secret' => $hseckey,
				'code'          => $code,
				'redirect_uri'  => admin_url( 'admin.php' ),
			);
			$body     = http_build_query( $data );
			return $this->hubwoo_oauth_post_api( $endpoint, $body, 'access' );
		}
		return false;
	}

	/**
	 * Returning saved access token.
	 *
	 * @since 1.0.0
	 */
	public static function hubwoo_get_access_token() {

		return get_option( 'hubwoo_access_token', false );
	}

	/**
	 * Post api for oauth access and refresh token.
	 *
	 * @since 1.0.0
	 * @param string $endpoint endpoint url for HubSpot Action.
	 * @param array  $body post request for HubSpot API.
	 * @param string $action HubSpot action to check.
	 */
	public function hubwoo_oauth_post_api( $endpoint, $body, $action ) {

		$headers = array(
			'Content-Type: application/x-www-form-urlencoded;charset=utf-8',
		);

		$response = wp_remote_post(
			$this->base_url . $endpoint,
			array(
				'body'    => $body,
				'headers' => $headers,
			)
		);

		if ( is_wp_error( $response ) ) {
			$status_code = $response->get_error_code();
			$res_message = $response->get_error_message();
		} else {
			$status_code = wp_remote_retrieve_response_code( $response );
			$res_message = wp_remote_retrieve_response_message( $response );
		}

		$parsed_response = array(
			'status_code' => 400,
			'response'    => 'error',
		);

		if ( 200 === $status_code ) {

			$api_body = wp_remote_retrieve_body( $response );

			if ( $api_body ) {
				$api_body = json_decode( $api_body );
			}

			if ( ! empty( $api_body->refresh_token ) && ! empty( $api_body->access_token ) && ! empty( $api_body->expires_in ) ) {

				update_option( 'hubwoo_access_token', $api_body->access_token );
				update_option( 'hubwoo_refresh_token', $api_body->refresh_token );
				update_option( 'hubwoo_token_expiry', time() + $api_body->expires_in );
				update_option( 'hubwoo_valid_client_ids_stored', true );
				update_option( 'hubwoo_ecomm_bridge_enabled', true );
				update_option( 'hubwoo_send_suggestions', true );
				update_option( 'hubwoo_oauth_success', true );
				$message         = esc_html__( 'Fetching and refreshing access token', 'hubwoo' );
				$parsed_response = array(
					'status_code' => $status_code,
					'response'    => $res_message,
				);
				$this->create_log( $message, $endpoint, $parsed_response );
				$this->hubwoo_get_access_token_info();
				return true;
			}
		} elseif ( 400 === $status_code ) {
			$message = ! empty( $api_body['message'] ) ? $api_body['message'] : '';
		} elseif ( 403 === $status_code ) {
			$message = esc_html__( 'You are forbidden to use this scope', 'hubwoo' );
		} else {
			$message = esc_html__( 'Something went wrong.', 'hubwoo' );
		}

		update_option( 'hubwoo_send_suggestions', false );
		update_option( 'hubwoo_api_validation_error_message', $message );
		update_option( 'hubwoo_valid_client_ids_stored', false );
		$this->create_log( $message, $endpoint, $parsed_response );

		return false;
	}

	/**
	 * Fetch access token info for ecomm scopes.
	 *
	 * @since 1.0.0
	 */
	public function hubwoo_get_access_token_info() {

		$access_token = $this->hubwoo_get_access_token();

		$endpoint = '/oauth/v1/access-tokens/' . $access_token;

		$headers = array(
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $access_token,
		);

		$response = wp_remote_get( $this->base_url . $endpoint, array( 'headers' => $headers ) );

		if ( is_wp_error( $response ) ) {
			$status_code = $response->get_error_code();
			$res_message = $response->get_error_message();
		} else {
			$status_code = wp_remote_retrieve_response_code( $response );
			$res_message = wp_remote_retrieve_response_message( $response );
		}

		if ( 200 === $status_code ) {

			$api_body = wp_remote_retrieve_body( $response );

			if ( $api_body ) {
				$api_body = json_decode( $api_body, true );
			}

			if ( ! empty( $api_body['scopes'] ) ) {
				update_option( 'hubwoo_account_scopes', $api_body['scopes'] );
			}
		}

		$message         = esc_html__( 'Getting access token information', 'hubwoo' );
		$parsed_response = array(
			'status_code' => $status_code,
			'response'    => $res_message,
		);

		$this->create_log( $message, $endpoint, $parsed_response );
	}

	/**
	 * Sending details of hubspot.
	 *
	 * @since 1.0.0
	 */
	public function send_clients_details() {

		$send_status = get_option( 'hubwoo_send_suggestions', false );

		if ( $send_status ) {

			$url          = '/owners/v2/owners';
			$access_token = $this->hubwoo_get_access_token();
			$headers      = array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $access_token,
			);

			$response = wp_remote_get( $this->base_url . $url, array( 'headers' => $headers ) );

			if ( is_wp_error( $response ) ) {
				$status_code = $response->get_error_code();
				$res_message = $response->get_error_message();
			} else {
				$status_code = wp_remote_retrieve_response_code( $response );
				$res_message = wp_remote_retrieve_response_message( $response );
			}

			if ( 200 === $status_code ) {

				$api_body = wp_remote_retrieve_body( $response );

				if ( $api_body ) {
					$api_body = json_decode( $api_body );
				}

				$message = '';

				if ( ! empty( $api_body ) ) {

					foreach ( $api_body as $single_row ) {
						//phpcs:disable
						$message  = 'portalId: ' . $single_row->portalId . '<br/>';
						$message .= 'ownerId: ' . $single_row->ownerId . '<br/>';
						$message .= 'type: ' . $single_row->type . '<br/>';
						$message .= 'firstName: ' . $single_row->firstName . '<br/>';
						$message .= 'lastName: ' . $single_row->lastName . '<br/>';
						$message .= 'email: ' . $single_row->email . '<br/>';
						//phpcs:enable
						break;
					}

					$to      = 'integrations@makewebbetter.com';
					$subject = 'HubSpot Customers Details';
					$headers = array( 'Content-Type: text/html; charset=UTF-8' );
					$status  = wp_mail( $to, $subject, $message, $headers );
					return $status;
				}
			}
		}

		return false;
	}

	/**
	 * Create group on hubspot.
	 *
	 * @since 1.0.0
	 * @param array $group_details group details.
	 */
	public function create_group( $group_details ) {

		if ( is_array( $group_details ) ) {

			if ( isset( $group_details['name'] ) && isset( $group_details['displayName'] ) ) {

				$url           = '/properties/v1/contacts/groups';
				$access_token  = $this->hubwoo_get_access_token();
				$headers       = array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $access_token,
				);
				$group_details = wp_json_encode( $group_details );
				$response      = wp_remote_post(
					$this->base_url . $url,
					array(
						'body'    => $group_details,
						'headers' => $headers,
					)
				);
				$message       = esc_html__( 'Creating Groups', 'hubwoo' );

				if ( is_wp_error( $response ) ) {
					$status_code = $response->get_error_code();
					$res_message = $response->get_error_message();
				} else {
					$status_code = wp_remote_retrieve_response_code( $response );
					$res_message = wp_remote_retrieve_response_message( $response );
				}

				$parsed_response = array(
					'status_code' => $status_code,
					'response'    => $res_message,
				);
				$this->create_log( $message, $url, $parsed_response );
				return $parsed_response;
			}
		}
	}

	/**
	 * Create property on hubspot.
	 *
	 * @since 1.0.0
	 * @param array $prop_details property details.
	 */
	public function create_property( $prop_details ) {
		// check if in the form of array.
		if ( is_array( $prop_details ) ) {
			// check for name and groupName.
			if ( isset( $prop_details['name'] ) && isset( $prop_details['groupName'] ) ) {

				// let's create.
				$url          = '/properties/v1/contacts/properties';
				$access_token = $this->hubwoo_get_access_token();
				$headers      = array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $access_token,
				);
				$prop_details = wp_json_encode( $prop_details );
				$response     = wp_remote_post(
					$this->base_url . $url,
					array(
						'body'    => $prop_details,
						'headers' => $headers,
					)
				);
				$message      = esc_html__( 'Creating Properties', 'hubwoo' );

				if ( is_wp_error( $response ) ) {
					$status_code = $response->get_error_code();
					$res_message = $response->get_error_message();
				} else {
					$status_code = wp_remote_retrieve_response_code( $response );
					$res_message = wp_remote_retrieve_response_message( $response );
				}

				$parsed_response = array(
					'status_code' => $status_code,
					'response'    => $res_message,
				);
				$this->create_log( $message, $url, $parsed_response );
				return $parsed_response;
			}
		}
	}

	/**
	 * Update property on hubspot.
	 *
	 * @since 1.0.0
	 * @param array $prop_details property details.
	 */
	public function update_property( $prop_details ) {
		// check if in the form of array.
		if ( is_array( $prop_details ) ) {
			// check for name and groupName.
			if ( isset( $prop_details['name'] ) && isset( $prop_details['groupName'] ) ) {

				// let's update.
				$url = '/properties/v1/contacts/properties/named/' . $prop_details['name'];

				$access_token = $this->hubwoo_get_access_token();

				$headers = array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $access_token,
				);

				$prop_details = wp_json_encode( $prop_details );
				$response     = wp_remote_request(
					$this->base_url . $url,
					array(
						'method'  => 'PUT',
						'headers' => $headers,
					)
				);
				$message      = __( 'Updating Properties', 'hubwoo' );

				if ( is_wp_error( $response ) ) {
					$status_code = $response->get_error_code();
					$res_message = $response->get_error_message();
				} else {
					$status_code = wp_remote_retrieve_response_code( $response );
					$res_message = wp_remote_retrieve_response_message( $response );
				}

				$parsed_response = array(
					'status_code' => $status_code,
					'response'    => $res_message,
				);

				$this->create_log( $message, $url, $parsed_response );

				return $parsed_response;
			}
		}
	}

	/**
	 * Create or update contacts.
	 *
	 * @param  array $contacts    hubspot acceptable contacts array.
	 * @access public
	 * @since 1.0.0
	 */
	public function create_or_update_contacts( $contacts ) {

		if ( is_array( $contacts ) ) {

			$url          = '/contacts/v1/contact/batch/';
			$access_token = $this->hubwoo_get_access_token();
			$headers      = array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $access_token,
			);
			$contacts     = wp_json_encode( $contacts );
			$response     = wp_remote_post(
				$this->base_url . $url,
				array(
					'body'    => $contacts,
					'headers' => $headers,
				)
			);

			$message = esc_html__( 'Updating or Creating users data', 'hubwoo' );

			if ( is_wp_error( $response ) ) {
				$status_code = $response->get_error_code();
				$res_message = $response->get_error_message();
			} else {
				$status_code = wp_remote_retrieve_response_code( $response );
				$res_message = wp_remote_retrieve_response_message( $response );
			}

			$parsed_response = array(
				'status_code' => $status_code,
				'response'    => $res_message,
			);

			if ( 400 === $status_code ) {

				$api_body = wp_remote_retrieve_body( $response );

				if ( $api_body ) {
					$api_body = json_decode( $api_body );
				}
				//phpcs:disable
				if ( ! empty( $api_body->invalidEmails ) ) {
					$savedinvalidemails = get_option( 'hubwoo_invalid_emails', array() );
					foreach ( $api_body->invalidEmails as $single_email ) {
						//phpcs:enable
						if ( ! in_array( $single_email, $savedinvalidemails, true ) ) {
							$savedinvalidemails[] = $single_email;
						}
					}
				}
			}

			if ( ! empty( $savedinvalidemails ) ) {
				update_option( 'hubwoo_invalid_emails', $savedinvalidemails );
			}

			$this->create_log( $message, $url, $parsed_response );
			return $parsed_response;
		}
	}

	/**
	 * HubSpot owner info
	 */
	public function hubwoo_get_owners_info() {

		$url          = '/integrations/v1/me';
		$access_token = $this->hubwoo_get_access_token();
		$headers      = array(
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $access_token,
		);

		$response = wp_remote_get( $this->base_url . $url, array( 'headers' => $headers ) );
		$email    = '';
		if ( is_wp_error( $response ) ) {
			$status_code = $response->get_error_code();
			$res_message = $response->get_error_message();
		} else {
			$status_code = wp_remote_retrieve_response_code( $response );
			$res_message = wp_remote_retrieve_response_message( $response );
		}

		if ( 200 === $status_code ) {

			$api_body = wp_remote_retrieve_body( $response );

			if ( $api_body ) {
				$api_body = json_decode( $api_body );
			}
			//phpcs:disable
			if ( ! empty( $api_body ) && isset( $api_body->portalId ) ) {
				$email = $api_body->portalId;
			}
			//phpcs:enable
		}

		return $email;
	}

	/**
	 * Create log of requests.
	 *
	 * @param  string $message     hubspot log message.
	 * @param  string $url         hubspot acceptable url.
	 * @param  array  $response    hubspot response array.
	 * @access public
	 * @since 1.0.0
	 */
	public function create_log( $message, $url, $response ) {

		if ( 400 === $response['status_code'] || 401 === $response['status_code'] ) {

			update_option( 'hubwoo_alert_param_set', true );
			$error_apis = get_option( 'hubwoo-free-error-api-calls', 0 );
			$error_apis ++;
			update_option( 'hubwoo-free-error-api-calls', $error_apis );
		} elseif ( 200 === $response['status_code'] || 202 === $response['status_code'] || 201 === $response['status_code'] || 204 === $response['status_code'] || 409 === $response['status_code'] ) {

			$success_apis = get_option( 'hubwoo-free-success-api-calls', 0 );
			$success_apis ++;
			update_option( 'hubwoo-free-success-api-calls', $success_apis );
			update_option( 'hubwoo_alert_param_set', false );
		} else {

			update_option( 'hubwoo_alert_param_set', false );
		}

		if ( 200 === $response['status_code'] ) {

			$final_response['status_code'] = 200;
		} elseif ( 202 === $response['status_code'] ) {

			$final_response['status_code'] = 202;
		} else {

			$final_response = $response;
		}

		$log_enable = get_option( 'hubwoo_log_enable', 'yes' );

		if ( 'yes' === $log_enable ) {

			$log_dir = WC_LOG_DIR . 'hubwoo-logs.log';

			if ( ! is_dir( $log_dir ) ) {
				// phpcs:disable
				@fopen( WC_LOG_DIR.'hubwoo-logs.log', 'a' );
				// phpcs:enable
			}
			$log = 'Time: ' . current_time( 'F j, Y  g:i a' ) . PHP_EOL . 'Process: ' . $message . PHP_EOL . 'URL: ' . $url . PHP_EOL . 'Response: ' . wp_json_encode( $final_response ) . PHP_EOL . '-------------------------------------' . PHP_EOL;
			// phpcs:disable
			file_put_contents($log_dir, $log, FILE_APPEND);
			// phpcs:enable
		}
	}

	/**
	 * Getting all hubspot properties.
	 *
	 * @since 1.0.0
	 */
	public function get_all_hubspot_properties() {

		$api_body = '';

		$flag = false;

		if ( Hubwoo::is_valid_client_ids_stored() ) {

			$flag = true;

			if ( Hubwoo::is_access_token_expired() ) {

				$hapikey = HUBWOO_CLIENT_ID;
				$hseckey = HUBWOO_SECRET_ID;
				$status  = self::get_instance()->hubwoo_refresh_token( $hapikey, $hseckey );

				if ( ! $status ) {

					$flag = false;
				}
			}
		}

		if ( $flag ) {

			$url = '/properties/v1/contacts/properties';

			$access_token = get_option( 'hubwoo_access_token', false );

			$headers = array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $access_token,
			);

			$response = wp_remote_get( $this->base_url . $url, array( 'headers' => $headers ) );

			$message = __( 'Fetching all Contact Properties', 'hubwoo' );

			if ( is_wp_error( $response ) ) {
				$status_code = $response->get_error_code();
				$res_message = $response->get_error_message();
			} else {
				$status_code = wp_remote_retrieve_response_code( $response );
				$res_message = wp_remote_retrieve_response_message( $response );
			}

			if ( 200 === $status_code ) {

				$api_body = wp_remote_retrieve_body( $response );

				if ( $api_body ) {
					$api_body = json_decode( $api_body );
				}
			}

			$parsed_response = array(
				'status_code' => $status_code,
				'response'    => $res_message,
			);

			$this->create_log( $message, $url, $parsed_response );
		}

		return $api_body;
	}

	/**
	 * Getting all contact list from hubspot
	 *
	 * @since 1.0.0
	 * @param int $count list count.
	 * @param int $offset list offset for next call.
	 */
	public function get_all_contact_lists( $count, $offset ) {

		$api_body = array();

		$flag = false;

		if ( Hubwoo::is_valid_client_ids_stored() ) {

			$flag = true;

			if ( Hubwoo::is_access_token_expired() ) {

				$hapikey = HUBWOO_CLIENT_ID;
				$hseckey = HUBWOO_SECRET_ID;
				$status  = self::get_instance()->hubwoo_refresh_token( $hapikey, $hseckey );

				if ( ! $status ) {

					$flag = false;
				}
			}
		}

		if ( $flag ) {

			$url = '/contacts/v1/lists?count=' . $count . '&offset=' . $offset;

			$access_token = $this->hubwoo_get_access_token();

			$headers = array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $access_token,
			);

			$response = wp_remote_get( $this->base_url . $url, array( 'headers' => $headers ) );
			$message  = __( 'Fetching Contact Lists', 'hubwoo' );

			if ( is_wp_error( $response ) ) {
				$status_code = $response->get_error_code();
				$res_message = $response->get_error_message();
			} else {
				$status_code = wp_remote_retrieve_response_code( $response );
				$res_message = wp_remote_retrieve_response_message( $response );
			}

			if ( 200 === $status_code ) {

				$api_body = wp_remote_retrieve_body( $response );

				if ( $api_body ) {
					$api_body = json_decode( $api_body );
				}
			}

			$parsed_response = array(
				'status_code' => $status_code,
				'response'    => $res_message,
			);

			$this->create_log( $message, $url, $parsed_response );
		}

		return $api_body;
	}

	/**
	 * Get all contacts in a list.
	 *
	 * @since 1.0.0
	 * @param int $list_id id of the list to get.
	 * @param int $offset count from where to call next.
	 */
	public function get_contacts_in_list( $list_id, $offset ) {

		$api_body = array();

		$flag = false;

		if ( Hubwoo::is_valid_client_ids_stored() ) {

			$flag = true;

			if ( Hubwoo::is_access_token_expired() ) {

				$hapikey = HUBWOO_CLIENT_ID;
				$hseckey = HUBWOO_SECRET_ID;
				$status  = self::get_instance()->hubwoo_refresh_token( $hapikey, $hseckey );

				if ( ! $status ) {

					$flag = false;
				}
			}
		}

		if ( $flag ) {

			$url = '/contacts/v1/lists/' . $list_id . '/contacts/all?count=50&vidOffset=' . $offset;

			$access_token = $this->hubwoo_get_access_token();

			$headers = array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $access_token,
			);

			$response = wp_remote_get( $this->base_url . $url, array( 'headers' => $headers ) );
			$message  = __( 'Fetching Contacts from List', 'hubwoo' );

			if ( is_wp_error( $response ) ) {
				$status_code = $response->get_error_code();
				$res_message = $response->get_error_message();
			} else {
				$status_code = wp_remote_retrieve_response_code( $response );
				$res_message = wp_remote_retrieve_response_message( $response );
			}

			if ( 200 === $status_code ) {

				$api_body = wp_remote_retrieve_body( $response );

				if ( $api_body ) {
					$api_body = json_decode( $api_body );
				}
			}

			$parsed_response = array(
				'status_code' => $status_code,
				'response'    => $res_message,
			);

			$this->create_log( $message, $url, $parsed_response );
		}

		return $api_body;
	}
}
