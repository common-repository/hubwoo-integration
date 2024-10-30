<?php
/**
 * Plugin overview file
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    hubwoo-integration
 * @subpackage hubwoo-integration/admin/templated
 */

global $hubwoo;

$GLOBALS['hide_save_button'] = true;

?>
<div class="hubwoo-overview-wrapper">
	<div class="hubwoo-overview-header hubwoo-common-header hubwoo-notice-header">
		<h2><?php esc_html_e( '2 minutes setup guide', 'hubwoo' ); ?></h2>
		<div class="hubwoo-header-content hubwoo-header-content">
			<span class="dashicons dashicons-media-text"></span>
			<?php esc_html_e( 'This free plugin will sync registered users data after completing the setup with limited fields. For unlimited access to users data sync', 'hubwoo' ); ?>
			<a target="_blank" href="<?php echo esc_url( 'https://makewebbetter.com/product/hubspot-woocommerce-integration-pro/?utm_source=MWB-huspot-org&utm_medium=MWB-ORG&utm_campaign=ORG' ); ?>" class="hubwoo-notice-header__link"><?php esc_html_e( 'GO PRO NOW', 'hubwoo' ); ?></a>
		</div>
	</div>
	<div class="hubwoo-overview-body">
		<div class="hubwoo-overview-container">
			<table class="hubwoo-compare">
				<thead>
					<tr>
						<th></th>
						<th class="hubwoo-compare__hy"><?php esc_html_e( 'Pro Version', 'hubwoo' ); ?></th>
						<th class="hubwoo-compare__ny"><?php esc_html_e( 'ORG Version', 'hubwoo' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th><?php esc_html_e( 'Guest Orders Sync (as contacts)', 'hubwoo' ); ?></td>
						<td class="hubwoo-compare__y">✓</td>
						<td class="hubwoo-compare__n">✘</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Full Purchase History', 'hubwoo' ); ?></td>
						<td class="hubwoo-compare__y">✓</td>
						<td class="hubwoo-compare__n">✘</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Active Lists and Enrollments', 'hubwoo' ); ?></td>
						<td class="hubwoo-compare__y">✓</td>
						<td class="hubwoo-compare__n">✘</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Workflows and Enrollments', 'hubwoo' ); ?></td>
						<td class="hubwoo-compare__y">✓</td>
						<td class="hubwoo-compare__n">✘</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td></td>
						<td colspan="2">
							<a target="_blank" href="<?php echo esc_url( 'https://makewebbetter.com/product/hubspot-woocommerce-integration-pro/?utm_source=MWB-huspot-org&utm_medium=MWB-ORG&utm_campaign=ORG' ); ?>" class="hubwoo__tbtn">
								<?php esc_html_e( 'Go Pro Now', 'hubwoo' ); ?>
							</a>
						</td>
					</tr>
				</tfoot>
			</table>
			<?php if ( ! self::hubwoo_get_started() ) { ?>
					<a class="hubwoo__btn hubwoo-overview-get-started" href="javascript:void(0)"><?php esc_html_e( 'Get Started With Free', 'hubwoo' ); ?></a>
				<?php
}
			?>
		</div>
	</div>
</div>
