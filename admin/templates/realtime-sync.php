<?php
/**
 * Real time sync and cron instructions.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    hubwoo-integration
 * @subpackage hubwoo-integration/admin/templates
 */

?>
<div class="hubwoo-fields-header hubwoo-common-header">
<h2><?php esc_html_e( 'Real-time Syncing from WooCommerce to HubSpot', 'hubwoo' ); ?></h2>
<div class="hubwoo-header-content">
<?php esc_html_e( 'The working of the plugin mainly depends on the execution of WordPress crons. In most of the systems, the cron works properly and the background jobs are executed properly without any delays. But some systems experience issues/problems in execution of the cron. For confirmation on the proper working of plugin, we have following:', 'hubwoo' ); ?>
</div>
<div>
<ol>
<li><?php esc_html_e( 'Please once check the status of WordPress cron from WooCommerce > Status > System Status', 'hubwoo' ); ?></li>
<li><?php esc_html_e( 'If the plugin cron is not showing up in cron events list ( event name: hubwoo_cron_schedule ) , try deactivating and activating the plugin again.', 'hubwoo' ); ?></li>
<li><?php esc_html_e( 'If the crons are not working on your system, then we have tried to provide you help to setup server crons.', 'hubwoo' ); ?></li>
<li><?php esc_html_e( 'You can ask your hosting support to help you in setting up the server crons which will be executed in every 5 minutes interval.', 'hubwoo' ); ?></li>
<li><?php esc_html_e( 'If in case you get any difficulties, please connect with us using our Chat Now option', 'hubwoo' ); ?></li>
</ol>
</div>
</div>
