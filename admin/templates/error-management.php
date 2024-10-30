<?php
/**
 * The file to show error logs and error counts
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    hubwoo-integration
 * @subpackage hubwoo-integration/admin/templates
 */

global $hubwoo;

$success_calls = get_option( 'hubwoo-free-success-api-calls', 0 );
$failed_calls  = get_option( 'hubwoo-free-error-api-calls', 0 );
$error_class   = '';
?>

<div class="hubwoo-connect-form-header hubwoo-common-header">
	<h2><?php esc_html_e( 'Error Tracking', 'hubwoo' ); ?></h2>
	<div class="hubwoo-header-content">
		<?php esc_html_e( 'Track your HubSpot API usage and count on daily basis. In case, failed API calls keeps on increasing then please connect with us using our Chat Now option.', 'hubwoo' ); ?>
	</div>
</div>
<?php
if ( get_option( 'hubwoo_alert_param_set' ) ) {
		$error_class = 'hubwoo_extn_no';
} else {
	$error_class = 'hubwoo_extn_yes';
}
?>
<div class="hubwoo-extn-status <?php echo esc_attr( $error_class ); ?>">
	<div class="hubwoo-notice-sym">
		<span></span>
	</div>
	<p><?php esc_html_e( 'Extension Current Status', 'hubwoo' ); ?></p>
</div>

<div class="hubwoo-error-info">
	<div class="hubwoo-error">
		<p class="hubwoo-total-calls">
			<?php esc_html_e( 'Total API Calls', 'hubwoo' ); ?>
		</p>
		<p class="hubwoo-error-text">
			<?php
			esc_html_e( 'Count: ', 'hubwoo' );
			echo esc_html( $success_calls + $failed_calls );
			?>
		</p>
	</div>
	<div class="hubwoo-error">
		<p class="hubwoo-success-calls">
			<?php esc_html_e( 'Success API Calls', 'hubwoo' ); ?>
		</p>
		<p class="hubwoo-error-text">
			<?php
			esc_html_e( 'Count: ', 'hubwoo' );
			echo esc_html( $success_calls );
			?>
		</p>
	</div>
	<div class="hubwoo-error">
		<p class="hubwoo-failed-calls">
			<?php esc_html_e( 'Failed API Calls', 'hubwoo' ); ?>
		</p>
		<p class="hubwoo-error-text">
			<?php
			esc_html_e( 'Count: ', 'hubwoo' );
			echo esc_html( $failed_calls );
			?>
		</p>
	</div>
</div>
<div>
	<h4><?php esc_html_e( 'List of Invalid Emails for HubSpot API', 'hubwoo' ); ?></h4>
	<?php
		$invalid_emails = get_option( 'hubwoo_invalid_emails', array() );
	if ( ! empty( $invalid_emails ) ) {
		?>
			<table class="hubwoo-emails-table">
				<tr>
					<th><?php esc_html_e( 'Email', 'hubwoo' ); ?></th>
				</tr>
			<?php
			foreach ( $invalid_emails as $single_email ) {
				?>
						<tr>
							<td><?php echo esc_html( $single_email ); ?></td>
						</tr>
					<?php
			}
			?>
			</table>
			<?php
	} else {
		?>
				<p><?php esc_html_e( 'No emails yet', 'hubwoo' ); ?></p>
			<?php
	}
	?>
</div>
