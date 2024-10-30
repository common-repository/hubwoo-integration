<?php
/**
 * Handles all admin ajax requests.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    hubwoo-integration
 * @subpackage hubwoo-integration/includes
 */

/**
 * Handles all admin ajax requests.
 *
 * All the functions required for handling admin ajax requests
 * required by the plugin.
 *
 * @package    hubwoo-integration
 * @subpackage hubwoo-integration/includes
 * @author     MakeWebBetter <webmaster@makewebbetter.com>
 */
class HubWooAjaxHandler {

	/**
	 * Construct.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		// check oauth access token.
		add_action( 'wp_ajax_hubwoo_check_oauth_access_token', array( &$this, 'hubwoo_check_oauth_access_token' ) );
		// get all groups request handler.
		add_action( 'wp_ajax_hubwoo_get_groups', array( &$this, 'hubwoo_get_groups' ) );
		// create a group request handler.
		add_action( 'wp_ajax_hubwoo_create_group_and_property', array( &$this, 'hubwoo_create_group' ) );
		// get group properties.
		add_action( 'wp_ajax_hubwoo_get_group_properties', array( &$this, 'hubwoo_get_group_properties' ) );
		// create property.
		add_action( 'wp_ajax_hubwoo_create_group_property', array( &$this, 'hubwoo_create_group_property' ) );
		// mark setup as completed.
		add_action( 'wp_ajax_hubwoo_setup_completed', array( &$this, 'hubwoo_setup_completed' ) );
		// send mail later.
		add_action( 'wp_ajax_hubwoo_suggest_later', array( &$this, 'hubwoo_suggest_later' ) );
		// send mail later.
		add_action( 'wp_ajax_hubwoo_suggest_accept', array( &$this, 'hubwoo_suggest_accept' ) );
		// admin call to get started.
		add_action( 'wp_ajax_hubwoo_get_started_call', array( &$this, 'hubwoo_get_started_call' ) );

		add_action( 'wp_ajax_hubwoo_clear_mail_choice', array( &$this, 'hubwoo_clear_mail_choice' ) );
		// callback to update old properties.
		add_action( 'wp_ajax_hubwoo_update_old_properties', array( &$this, 'hubwoo_update_old_properties' ) );

		add_action( 'wp_ajax_hubwoo_add_update_option', array( &$this, 'hubwoo_add_update_option' ) );
	}

	/**
	 * Save option when admin click on send later button.
	 *
	 * @since    1.0.0
	 */
	public function hubwoo_suggest_later() {
		check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );
		update_option( 'hubwoo_suggestions_later', true );
		die;
	}

	/**
	 * Save option when admin click on accept button.
	 *
	 * @since    1.0.0
	 */
	public function hubwoo_suggest_accept() {

		check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );
		$status = HubWooConnectionMananager::get_instance()->send_clients_details();

		if ( $status ) {
			update_option( 'hubwoo_suggestions_sent', true );
			echo 'success';
		} else {
			update_option( 'hubwoo_suggestions_later', true );
			echo 'failure';
		}
		wp_die();
	}

	/**
	 * Check oauth access token validation.
	 *
	 * @since    1.0.0
	 */
	public function hubwoo_check_oauth_access_token() {

		$response = array(
			'status'  => true,
			'message' => __( 'Success', 'hubwoo' ),
		);
		// check the nonce sercurity.
		check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

		// checking if access token is expired.
		if ( Hubwoo::is_access_token_expired() ) {

			$hapikey = HUBWOO_CLIENT_ID;
			$hseckey = HUBWOO_SECRET_ID;
			$status  = HubWooConnectionMananager::get_instance()->hubwoo_refresh_token( $hapikey, $hseckey );

			if ( ! $status ) {
				$response['status']  = false;
				$response['message'] = __( 'Something went wrong, please check your API Keys' );
			}
		}
		echo wp_json_encode( $response );
		wp_die();
	}


	/**
	 * Get all groups.
	 */
	public function hubwoo_get_groups() {

		// check the nonce sercurity.
		check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );
		$groups = HubWooContactProperties::get_instance()->_get( 'groups' );
		echo wp_json_encode( $groups );
		wp_die();
	}

	/**
	 * Create a group on ajax request.
	 */
	public function hubwoo_create_group() {
		// check the nonce sercurity.
		check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

		if ( isset( $_POST['createNow'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$create_now = sanitize_text_field( wp_unslash( $_POST['createNow'] ) );
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( 'group' === $create_now && isset( $_POST['groupDetails'] ) ) {
				$group_details = map_deep( wp_unslash( $_POST['groupDetails'] ), 'sanitize_text_field' );
				echo wp_json_encode( HubWooConnectionMananager::get_instance()->create_group( $group_details ) );
				wp_die();
			}
		}
	}

	/**
	 * Create an group property on ajax request.
	 *
	 * @since 1.0.0
	 */
	public function hubwoo_create_group_property() {
		// check the nonce sercurity.
		check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );
		if ( isset( $_POST['groupName'] ) && isset( $_POST['propertyDetails'] ) ) {
			$group_name                    = sanitize_text_field( wp_unslash( $_POST['groupName'] ) );
			$property_details              = map_deep( wp_unslash( $_POST['propertyDetails'] ), 'sanitize_text_field' );
			$property_details['groupName'] = $group_name;
			echo wp_json_encode( HubWooConnectionMananager::get_instance()->create_property( $property_details ) );
			wp_die();
		}
	}

	/**
	 * Get hubwoo group properties by group name.
	 *
	 * @since 1.0.0
	 */
	public function hubwoo_get_group_properties() {
		// check the nonce sercurity.
		check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );
		if ( isset( $_POST['groupName'] ) ) {
			$group_name = sanitize_text_field( wp_unslash( $_POST['groupName'] ) );
			echo wp_json_encode( HubWooContactProperties::get_instance()->_get( 'properties', $group_name ) );
			wp_die();
		}
	}

	/**
	 * Mark setup is completed.
	 *
	 * @since 1.0.0
	 */
	public function hubwoo_setup_completed() {

		check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );
		update_option( 'hubwoo_setup_completed', true );
		update_option( 'hubwoo_free_version', HUBWOO_VERSION );
		update_option( 'hubwoo_free_property_update', true );
		update_option( 'hubwoo_newversion_groups_saved', true );
		return true;
	}

	/**
	 * Mark option when admin has clicked on get started.
	 *
	 * @since 1.0.0
	 */
	public function hubwoo_get_started_call() {

		check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );
		update_option( 'hubwoo_get_started', true );
		return true;
	}

	/**
	 * Clear option for a suggestions sent/or not.
	 *
	 * @since 1.0.0
	 */
	public function hubwoo_clear_mail_choice() {

		check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );
		delete_option( 'hubwoo_suggestions_later' );
		return true;
	}

	/**
	 * Update old version contact properties.
	 *
	 * @since 1.0.0
	 */
	public function hubwoo_update_old_properties() {

		check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

		$update_properties = array();

		$group_properties              = array(
			'name'      => 'customer_group',
			'label'     => __( 'Customer Group/ User role', 'hubwoo' ),
			'type'      => 'string',
			'fieldType' => 'textarea',
			'formField' => false,
		);
		$group_properties['groupName'] = 'customer_group';
		$update_properties[]           = $group_properties;

		if ( get_option( 'hubwoo_abncart_added', false ) ) {

			$group_properties              = array(
				'name'      => 'abandoned_cart_products',
				'label'     => __( 'Abandoned Cart Products', 'hubwoo' ),
				'type'      => 'string',
				'fieldType' => 'textarea',
				'formfield' => false,
			);
			$group_properties['groupName'] = 'abandoned_cart';
			$update_properties[]           = $group_properties;

			$group_properties              = array(
				'name'      => 'abandoned_cart_products_categories',
				'label'     => __( 'Abandoned Cart Products Categories', 'hubwoo' ),
				'type'      => 'string',
				'fieldType' => 'textarea',
				'formfield' => false,
			);
			$group_properties['groupName'] = 'abandoned_cart';
			$update_properties[]           = $group_properties;

			$group_properties              = array(
				'name'      => 'abandoned_cart_products_skus',
				'label'     => __( 'Abandoned Cart Products SKUs', 'hubwoo' ),
				'type'      => 'string',
				'fieldType' => 'textarea',
				'formfield' => false,
			);
			$group_properties['groupName'] = 'abandoned_cart';
			$update_properties[]           = $group_properties;
		}

		$success = true;

		if ( count( $update_properties ) ) {

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

					foreach ( $update_properties as $single_property ) {

						$success = false;

						$response = HubWooConnectionMananager::get_instance()->update_property( $single_property );

						if ( isset( $response['status_code'] ) && 200 === $response['status_code'] ) {

							$success = true;
						}
					}
				}
			}
		}

		echo wp_json_encode( $success );

		wp_die();
	}

	/**
	 * Mark option when property update is done.
	 *
	 * @since 1.0.0
	 */
	public function hubwoo_add_update_option() {

		check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

		update_option( 'hubwoo_free_property_update', true );
		update_option( 'hubwoo_free_version', HUBWOO_VERSION );
		return true;
	}
}

new HubWooAjaxHandler();
