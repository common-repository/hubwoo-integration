<?php
/**
 * Connection with HubSpot.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    hubwoo-integration
 * @subpackage hubwoo-integration/admin
 */

global $hubwoo;

if ( isset( $_POST['hubwoo_activate_connect'] ) && check_admin_referer( 'hubwoo-settings' ) ) {

	unset( $_POST['hubwoo_activate_connect'] );
	woocommerce_update_options( Hubwoo_Admin::hubwoo_general_settings() );
	$message = esc_html__( 'Settings saved successfully', 'hubwoo' );
	$hubwoo->hubwoo_notice( $message, 'success' );
} elseif ( isset( $_GET['action'] ) && ( 'changeaccount' === sanitize_key( $_GET['action'] ) ) ) {
	$hubwoo->hubwoo_switch_account();
}

$oauth_success = $hubwoo->is_oauth_success();

if ( ! $oauth_success && 'yes' === $hubwoo->is_plugin_enable() ) {

	$url         = 'https://app.hubspot.com/oauth/authorize';
	$hapikey     = HUBWOO_CLIENT_ID;
	$hubspot_url = add_query_arg(
		array(
			'client_id'      => $hapikey,
			'optional_scope' => 'integration-sync%20e-commerce',
			'scope'          => 'oauth%20contacts',
			'redirect_uri'   => admin_url() . 'admin.php',
		),
		$url
	);
	?>
		<span class="hubwoo_oauth_span hubwoo_msg_notice">
			<label><?php esc_html_e( 'Please connect your WooCommerce store with HubSpot Account', 'hubwoo' ); ?></label>
			<a href="<?php echo esc_url( $hubspot_url ); ?>" class="button-primary"><?php esc_html_e( 'Authorize', 'hubwoo' ); ?></a>
		</span>
	<?php
}
?>

<?php

if ( ! $oauth_success ) {

	?>
	<div class="hubwoo-connection-container">
		<form class="hubwoo-connect-form hubwoo-connect-table" action="" method="post">
			<?php woocommerce_admin_fields( Hubwoo_Admin::hubwoo_general_settings() ); ?>
			<div class="hubwoo-connect-form-submit">
				<p class="submit">
					<input class="hubwoo__btn" type="submit" name="hubwoo_activate_connect" value="<?php esc_html_e( 'Save', 'hubwoo' ); ?>" class="button-primary" />
				</p>
				<?php wp_nonce_field( 'hubwoo-settings' ); ?>
			</div>
		</form>
	</div>
	<?php
} else {

	?>
		<div class="hubwoo-connect-form-header hubwoo-common-header">
			<h2><?php esc_html_e( 'HubSpot Connection', 'hubwoo' ); ?></h2>
			<div class="hubwoo-header-content">
				<?php esc_html_e( 'Congratulations!! Your woocommerce store has been successfully connected with the below listed portal ID. You can now proceed to next step and run the setup for custom groups and properties.', 'hubwoo' ); ?>
			</div>
			<div class="hubwoo_pro_support_dev">
				<?php $support_dev = get_option( 'hubwoo_suggestions_sent', false ); ?>
				<?php if ( ! $support_dev ) : ?>
					<a href="javascript:void(0);" class="hubwoo_connect_page_actions hubwoo__btn hubwoo_tracking"><?php esc_html_e( 'Support Plugin Development', 'hubwoo' ); ?></a>
				<?php endif; ?>
				<a href="?page=hubwoo&hubwoo_tab=hubwoo_connect&action=reauth" class="hubwoo_connect_page_actions hubwoo__btn"><?php esc_html_e( 'Re-Authorize', 'hubwoo' ); ?></a>
				<a href="?page=hubwoo&hubwoo_tab=hubwoo_connect&action=changeAccount" class="hubwoo_connect_page_actions hubwoo__btn" id="hubwoo_pro_switch" ><?php esc_html_e( 'Change Account', 'hubwoo' ); ?></a>
			</div>
		</div>
		<div class="hubwoo-connection-info">
			<div class="hubwoo-connection-status hubwoo-connection">
				<img src="<?php echo esc_url( HUBWOO_URL . 'admin/images/connected.png' ); ?>">
				<p class="hubwoo-connection-label">
					<?php esc_html_e( 'Connection Status', 'hubwoo' ); ?>
				</p>
				<p class="hubwoo-connection-status-text">
					<?php
					if ( $hubwoo->is_valid_client_ids_stored() ) {

						esc_html_e( 'Connected', 'hubwoo' );
					}
					?>
				</p>
			</div>
			<div class="hubwoo-acc-email hubwoo-connection">
				<img src="<?php echo esc_url( HUBWOO_URL . 'admin/images/email-icon.png' ); ?>">
				<p class="hubwoo-acc-email-label">
					<?php esc_html_e( 'HubSpot Portal ID', 'hubwoo' ); ?>
				</p>
				<p class="hubwoo-connection-status-text">
					<?php
					if ( $hubwoo->is_valid_client_ids_stored() ) {

						$acc_email = $hubwoo->hubwoo_owners_email_info();

						echo esc_html( $acc_email );
					}
					?>
				</p>
			</div>
			<div class="hubwoo-token-info hubwoo-connection">
				<img src="<?php echo esc_url( HUBWOO_URL . 'admin/images/timer.png' ); ?>">
				<p class="hubwoo-token-expiry-label">
					<?php esc_html_e( 'Token Renewal', 'hubwoo' ); ?>
				</p>
				<?php
				if ( $oauth_success ) {

					if ( $hubwoo->is_valid_client_ids_stored() ) {

						$token_timestamp = get_option( 'hubwoo_token_expiry', '' );

						if ( ! empty( $token_timestamp ) ) {

							$exact_timestamp = $token_timestamp - time();

							if ( $exact_timestamp > 0 ) {

								?>
									<p class="hubwoo-acces-token-renewal">
									<?php
										/* translators: %s: timestamp */
										$day_string = sprintf( _n( 'In %s second', 'In %s seconds', $exact_timestamp, 'hubwoo' ), number_format_i18n( $exact_timestamp ) );
									?>
									<span id="hubwoo-day-count" ><?php echo esc_html( $day_string ); ?></span>
									</p>
									<?php
							} else {

								?>
									<p class="hubwoo-acces-token-renewal">
										<a href="javascript:void(0);" class="" id="hubwoo-refresh-token"><?php esc_html_e( 'Refresh Token', 'hubwoo' ); ?></a>
									</p>
									<?php
							}
						} else {

							?>
								<p class="hubwoo-acces-token-renewal">
									<a href="javascript:void(0);" class="" id="hubwoo-refresh-token"><?php esc_html_e( 'Refresh Token', 'hubwoo' ); ?></a>
								</p>
								<?php
						}
					} else {
						?>
							<p class="hubwoo-acces-token-renewal">
								<a href="?page=hubwoo&hubwoo_tab=hubwoo_connect&action=reauth" class="" id="hubwoo-reauthorize"><?php esc_html_e( 'Re-Authorize with HubSpot', 'hubwoo' ); ?></a>
							</p>
							<?php
					}
				}
				?>
			</div>
		</div>
		<?php

		$display = 'none';

		if ( $oauth_success && $hubwoo->is_display_suggestion_popup() ) {

			$display = 'block';
		}

		?>
		<div class="hubwoo_pop_up_wrap" style="display: <?php echo esc_attr( $display ); ?>">
			<div class="pop_up_sub_wrap">
				<p>
					<?php esc_html_e( 'Unlock your free access to our full documentation just by sending us the tracking data. We need your HubSpot Portal ID and email that too only once.', 'hubwoo' ); ?>
				</p>
				<div class="button_wrap">
					<a href="javascript:void(0);" class="hubwoo_accept"><?php esc_html_e( 'Yes support it', 'hubwoo' ); ?></a>
					<a href="javascript:void(0);" class="hubwoo_later"><?php esc_html_e( "I'll decide later", 'hubwoo' ); ?></a>
				</div>
			</div>
		</div>
	<?php
}
