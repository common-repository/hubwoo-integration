<?php
/**
 * Setup for Groups and Properties.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    hubwoo-integration
 * @subpackage hubwoo-integration/admin/templates
 */

global $hubwoo;

$GLOBALS['hide_save_button'] = true;

?>

<?php add_thickbox(); ?>

<div id="hubwoo-setup-process" style="display: none;">
<div class="popupwrap">
<p> <?php esc_html_e( 'We are setting up custom groups and properties for contacts on HubSpot. Please do not navigate or reload the page before our confirmation message.', 'hubwoo' ); ?></p>
<div class="progress">
<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width:0%">
</div>
</div>
<div class="hubwoo-message-area">
</div>
</div> 
</div>

<div class="hubwoo-overview-wrapper">
<?php if ( ! $hubwoo->is_setup_completed() ) { ?>
	<div class="hubwoo-overview-header hubwoo-common-header">
	<h2><?php esc_html_e( 'Complete your basic setup just in one-click', 'hubwoo' ); ?></h2>
	</div>
	<div class="hubwoo-overview-body">
	<div class="hubwoo-overview-container">
	<p><?php esc_html_e( 'We create best practised groups and properties for contacts on HubSpot so that you can manage your follow-ups with real-time data.', 'hubwoo' ); ?></p>
	<span><?php esc_html_e( 'We create several custom groups and properties on HubSpot', 'hubwoo' ); ?></span>
	<div class="hubwoo-all-groups">
	<div class="hubwoo-enabled-groups hubwoo-grouping">
	<h4><?php esc_html_e( 'Groups in Free Version', 'hubwoo' ); ?></h4>
	<ul>
	<li class="hubwoo_enabled"><img src="<?php echo esc_url( HUBWOO_URL . 'admin/images/checked.png' ); ?>"><span><?php esc_html_e( 'Customer Information', 'hubwoo' ); ?></span></li>
	<li class="hubwoo_enabled"><img src="<?php echo esc_url( HUBWOO_URL . 'admin/images/checked.png' ); ?>"><span><?php esc_html_e( 'Last Order', 'hubwoo' ); ?></span></li>
	<li class="hubwoo_enabled"><img src="<?php echo esc_url( HUBWOO_URL . 'admin/images/checked.png' ); ?>"><span><?php esc_html_e( 'RFM Information', 'hubwoo' ); ?></span></li>
	</ul>
	</div>
	<div class="hubwoo-disabled-groups hubwoo-grouping">
	<h4><?php esc_html_e( 'Groups in Paid Version', 'hubwoo' ); ?></h4>
	<ul>
	<li data-toggle="modal" data-target="#myModal" class="hubwoo_disabled"><img src="<?php echo esc_url( HUBWOO_URL . 'admin/images/locked.png' ); ?>"><span><?php esc_html_e( 'Shopping Cart Information', 'hubwoo' ); ?></span></li>
	<li data-toggle="modal" data-target="#myModal" class="hubwoo_disabled"><img src="<?php echo esc_url( HUBWOO_URL . 'admin/images/locked.png' ); ?>"><span><?php esc_html_e( 'Products Bought', 'hubwoo' ); ?></span></li>
	<li data-toggle="modal" data-target="#myModal" class="hubwoo_disabled"><img src="<?php echo esc_url( HUBWOO_URL . 'admin/images/locked.png' ); ?>"><span><?php esc_html_e( 'Categories Bought', 'hubwoo' ); ?></span></li>
	<li data-toggle="modal" data-target="#myModal" class="hubwoo_disabled"><img src="<?php echo esc_url( HUBWOO_URL . 'admin/images/locked.png' ); ?>"><span><?php esc_html_e( 'SKUs Bought', 'hubwoo' ); ?></span></li>
	<li data-toggle="modal" data-target="#myModal" class="hubwoo_disabled"><img src="<?php echo esc_url( HUBWOO_URL . 'admin/images/locked.png' ); ?>"><span><?php esc_html_e( 'ROI Tracking (for PRO Users)', 'hubwoo' ); ?></span></li>
	<li data-toggle="modal" data-target="#myModal" class="hubwoo_disabled"><img src="<?php echo esc_url( HUBWOO_URL . 'admin/images/locked.png' ); ?>"><span><?php esc_html_e( 'Subscription Details', 'hubwoo' ); ?></span></li>
	</ul>
	</div>
	</div>
	<div class="hubwoo-free-setup">
	<a id="hubwoo-run-setup" href="javascript:void(0)"><?php esc_html_e( 'Start Running Setup', 'hubwoo' ); ?></a>
	</div>
	</div>
	</div>
	<?php } else { ?>
		<div class="hubwoo-overview-header hubwoo-common-header">
		<h2><?php esc_html_e( 'Basic Setup Completed', 'hubwoo' ); ?></h2>
		<div class="hubwoo-header-content">
		<div>
		<?php esc_html_e( 'Congratulations, now your upcoming contacts will be auto-synced to HubSpot.', 'hubwoo' ); ?>
		</div>
		<div class="hubwoo-do-more">
		<?php
		/* translators: %s: html vars */
		$message = sprintf( esc_html__( 'A lot more can be achieved through our paid plans. We have a well suitable featured packages for %1$s HubSpot FREE, Starter or Basic, Professional or Enterpirse Users %2$s', 'hubwoo' ), '<strong>', '</strong>' );
		?>
		<?php echo wp_kses_post( $message ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php
		$users        = count_users();
		$total_orders = wp_count_posts( 'shop_order' );
		?>
		<?php
		if ( ! empty( $total_orders ) ) {
			$orders = 0;
			foreach ( $total_orders as $single_count ) {
				$orders += $single_count;
			}
		}
		?>
		<span class="hubwoo_oauth_span hubwoo_msg_notice">
		<label>
		<?php
		/* translators: %1$s,%2$s: total users, orders */
		echo sprintf( esc_html__( 'You have total %1$s users and %2$s orders to sync over HubSpot. Do you want to sync them?', 'hubwoo' ), esc_attr( $users['total_users'] ), esc_attr( $orders ) );
		?>
		</label>
		<a href="<?php esc_url( 'https://makewebbetter.com/product/hubspot-woocommerce-integration-pro/?utm_source=MWB-huspot-org&utm_medium=MWB-ORG&utm_campaign=ORG' ); ?>" class="button-primary"><?php esc_html_e( 'GO PRO NOW', 'hubwoo' ); ?></a>
		</span>
		</div>
		</div>
		<div class="hubwoo-pro-plans">
		<div class="hubwoo-complimentary hubwoo-box">
		<ul>
		<li class="heading"></li>
		<li class="price"><span><?php esc_html_e( '$99', 'hubwoo' ); ?></span><?php esc_html_e( 'One Time Payment', 'hubwoo' ); ?></li>
		<li class="features"><?php esc_html_e( '70+ contact fields', 'hubwoo' ); ?></li>
		<li class="features"><?php esc_html_e( 'Guest Order Sync', 'hubwoo' ); ?></li>
		<li class="features"><?php esc_html_e( 'WooCommerce Subscriptions', 'hubwoo' ); ?></li>
		<li class="features"><?php esc_html_e( 'Products Purchased', 'hubwoo' ); ?></li>
		<li class="features"><?php esc_html_e( 'SKUs Purchased', 'hubwoo' ); ?></li>
		<li class="features"><?php esc_html_e( 'Categories Purchased', 'hubwoo' ); ?></li>
		<li class="upgrade-now"><a href="<?php esc_url( 'https://makewebbetter.com/product/hubspot-woocommerce-integration-pro/?utm_source=MWB-huspot-org&utm_medium=MWB-ORG&utm_campaign=ORG' ); ?>" target="_blank" class="hubwoo__btn"><?php esc_html_e( 'Upgrade Now', 'hubwoo' ); ?></a></li>
		</ul>
		</div>
		<div class="hubwoo-starter-basic hubwoo-box">
		<ul>
		<li class="heading"></li>
		<li class="price"><span><?php esc_html_e( '$149', 'hubwoo' ); ?></span><?php esc_html_e( 'One Time Payment', 'hubwoo' ); ?></li>
		<li class="features"><?php esc_html_e( 'All features of Free Version', 'hubwoo' ); ?></li>
		<li class="features"><?php esc_html_e( '20+ smart lists', 'hubwoo' ); ?></li>
		<li class="features"><?php esc_html_e( 'Order activity list enrolment', 'hubwoo' ); ?></li>
		<li class="features"><?php esc_html_e( 'Customer activity list enrolment', 'hubwoo' ); ?></li>
		<li class="upgrade-now"><a href="<?php echo esc_url( 'https://makewebbetter.com/product/hubspot-woocommerce-integration-pro/?utm_source=MWB-huspot-org&utm_medium=MWB-ORG&utm_campaign=ORG' ); ?>" target="_blank" class="hubwoo__btn"><?php esc_html_e( 'Upgrade Now', 'hubwoo' ); ?></a></li>
		</ul>
		</div>
		<div class="hubwoo-pro hubwoo-box">
		<ul>
		<li class="heading"></li>
		<li class="price"><span><?php esc_html_e( '$199', 'hubwoo' ); ?></span><?php esc_html_e( 'One Time Payment', 'hubwoo' ); ?></li>
		<li class="features"><?php esc_html_e( 'All features of Free & Basic', 'hubwoo' ); ?>
		<li class="features"><?php esc_html_e( '10+ ready to use workflows', 'hubwoo' ); ?></li>
		<li class="features"><?php esc_html_e( 'Order activity workflow enrolment', 'hubwoo' ); ?></li>
		<li class="features"><?php esc_html_e( 'Customer activity workflow enrolment ', 'hubwoo' ); ?></li>
		<li class="upgrade-now"><a href="<?php echo esc_url( 'https://makewebbetter.com/product/hubspot-woocommerce-integration-pro/?utm_source=MWB-huspot-org&utm_medium=MWB-ORG&utm_campaign=ORG' ); ?>" target="_blank" class="hubwoo__btn"><?php esc_html_e( 'Upgrade Now', 'hubwoo' ); ?></a></li>
		</ul>
		</div>
		</div>
		<?php } ?>
		</div>
