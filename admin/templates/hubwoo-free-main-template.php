<?php
/**
 * Main template file of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    hubwoo-integration
 * @subpackage hubwoo-integration/admin/templates
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit(); // Exit if accessed directly.
}

global $hubwoo;
$active_tab   = isset( $_GET['hubwoo_tab'] ) ? sanitize_key( $_GET['hubwoo_tab'] ) : 'hubwoo_overview';
$default_tabs = $hubwoo->hubwoo_default_tabs();
?>
<div class="hub-woo-main-wrapper">
	<div class="hubwoo-go-pro">
		<div class="hubwoo-go-pro-banner">
			<div class="hubwoo-inner-container">
				<div class="hubwoo-name-wrapper">
					<p><?php esc_html_e( 'Integration with HubSpot for WooCommerce', 'hubwoo' ); ?></p></div>
				<div class="hubwoo-static-menu">
					<ul>
						<li>
							<a href="<?php echo esc_url( 'https://makewebbetter.com/contact-us/' ); ?>" target="_blank">
								<span class="dashicons dashicons-phone"></span>
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( 'https://docs.makewebbetter.com/hubspot-woocommerce-integration/' ); ?>" target="_blank">
								<span class="dashicons dashicons-media-document"></span>
							</a>
						</li>
						<li class="hubwoo-main-menu-button">
							<a id="hubwoo-go-pro-link" href="<?php echo esc_url( 'https://makewebbetter.com/product/hubspot-woocommerce-integration-pro/?utm_source=MWB-huspot-org&utm_medium=MWB-ORG&utm_campaign=ORG' ); ?>" class="" title="" target="_blank"><?php esc_html_e( 'GO PRO NOW', 'hubwoo' ); ?></a>
						</li>
						<li>
							<a id="hubwoo-skype-link" href="<?php echo esc_url( 'https://join.skype.com/invite/IKVeNkLHebpC' ); ?>" target="_blank">
								<img src="<?php echo esc_url( HUBWOO_URL . 'admin/images/skype_logo.png' ); ?>" style="height: 15px;width: 15px;" ><?php esc_html_e( 'Chat Now', 'hubwoo' ); ?>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="hubwoo-main-template">
		<div class="hubwoo-body-template">
			<div class="hubwoo-mobile-nav">
				<span class="dashicons dashicons-menu"></span>
			</div>
			<div class="hubwoo-navigator-template">
				<div class="hubwoo-navigations">
					<?php
					if ( is_array( $default_tabs ) && count( $default_tabs ) ) {

						foreach ( $default_tabs as $tab_key => $single_tab ) {

							$tab_classes = 'hubwoo-nav-tab ';

							$dependency = $single_tab['dependency'];

							if ( ! empty( $active_tab ) && $active_tab === $tab_key ) {

								$tab_classes .= 'nav-tab-active';
							}

							if ( 'hubwoo_lists' === $tab_key || 'hubwoo_workflows' === $tab_key || 'hubwoo_abncart' === $tab_key || 'hubwoo_coupons' === $tab_key || 'hubwoo_deals' === $tab_key || 'hubwoo_ocs' === $tab_key ) {

								if ( ! empty( $dependency ) && ! $hubwoo->check_dependencies( $dependency ) ) {

									$tab_classes .= ' hubwoo-tab-disabled';
									$tab_classes .= ' hubwoo-lock';
									?>
											<div class="hubwoo-tabs"><a class="<?php echo esc_attr( $tab_classes ); ?>" id="<?php echo esc_attr( $tab_key ); ?>" href="javascript:void(0);"><?php echo esc_html( $single_tab['name'] ); ?><img class="hubwoo-disable-white" src="<?php echo esc_url( HUBWOO_URL . 'admin/images/lock.png' ); ?>"><img class="hubwoo-disable-grey" src="<?php echo esc_url( HUBWOO_URL . 'admin/images/lock-g.png' ); ?>"></a></div>
										<?php
								} else {

									$tab_classes .= ' hubwoo-lock';
									?>
											<div class="hubwoo-tabs"><a class="<?php echo esc_attr( $tab_classes ); ?>" id="<?php echo esc_attr( $tab_key ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=hubwoo' ) . '&hubwoo_tab=' . esc_attr( $tab_key ) ); ?>"><?php echo esc_html( $single_tab['name'] ); ?><img src="<?php echo esc_url( HUBWOO_URL . 'admin/images/lock.png' ); ?>"></a></div>
										<?php
								}
							} else {

								if ( ! empty( $dependency ) && ! $hubwoo->check_dependencies( $dependency ) ) {

									$tab_classes .= ' hubwoo-tab-disabled';
									?>
											<div class="hubwoo-tabs"><a class="<?php echo esc_attr( $tab_classes ); ?>" id="<?php echo esc_attr( $tab_key ); ?>" href="javascript:void(0);"><?php echo esc_html( $single_tab['name'] ); ?></a></div>

										<?php
								} else {

									?>
											<div class="hubwoo-tabs"><a class="<?php echo esc_attr( $tab_classes ); ?>" id="<?php echo esc_attr( $tab_key ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=hubwoo' ) . '&hubwoo_tab=' . esc_attr( $tab_key ) ); ?>"><?php echo esc_html( $single_tab['name'] ); ?></a></div>

										<?php
								}
							}
						}
					}
					?>
				</div>
			</div>
			<div class="hubwoo-content-template">
				<div class="hubwoo-content-container">
					<?php
						// if submenu is directly clicked on woocommerce.
					if ( empty( $active_tab ) ) {

						$active_tab = 'hubwoo_overview';
					}

						// look for the path based on the tab id in the admin templates.
						$tab_content_path = 'admin/templates/' . $active_tab . '.php';

						$hubwoo->load_template_view( $tab_content_path );
					?>
				</div>
			</div>
		</div>
		<div style="display: none;" class="loading-style-bg" id="hubwoo_loader">
			<img src="<?php echo esc_url( HUBWOO_URL . 'admin/images/loader.gif' ); ?>">
		</div>
	</div>
</div>
