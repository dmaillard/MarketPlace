
<?php $free_dashboard_page 	= WC_Vendors::$pv_options->get_option( 'vendor_dashboard_page' ); ?>

<header class="entry-header">
	<h1 class="entry-title"><?php echo get_the_title($free_dashboard_page); ?></h1>
</header>

<?php
	$shop_page_url	 = WCV_Vendors::get_vendor_shop_page( wp_get_current_user()->user_login );
	$settings_page   = get_permalink( WC_Vendors::$pv_options->get_option( 'shop_settings_page' ) );
	$can_submit      = WC_Vendors::$pv_options->get_option( 'can_submit_products' );
	$submit_link = ( $can_submit ) ? admin_url( 'post-new.php?post_type=product' ) : '';
	$edit_link   = ( $can_submit ) ? admin_url( 'edit.php?post_type=product' ) : '';

	wc_get_template( 'links.php', array(
	'shop_page'     => urldecode($shop_page_url),
	'settings_page' => $settings_page,
	'can_submit'    => $can_submit,
	'submit_link'   => $submit_link,
	'edit_link'		=> $edit_link,
	), 'wc-vendors/dashboard/', wcv_plugin_dir . 'templates/dashboard/' );
?>

<h2><?php _e( 'Settings', 'buddyboss-marketplace' ); ?></h2>

<?php if ( function_exists( 'wc_print_notices' ) ) { wc_print_notices(); } ?>

<form method="post">
	<?php

	do_action( 'wcvendors_settings_before_paypal' );

	if ( $paypal_address !== 'false' ) {
		wc_get_template( 'paypal-email-form.php', array(
																'user_id' => $user_id,
														   ), 'wc-vendors/dashboard/settings/', wcv_plugin_dir . 'templates/dashboard/settings/' );
	}

	do_action( 'wcvendors_settings_after_paypal' );

	wc_get_template( 'shop-name.php', array(
													'user_id' => $user_id,
											   ), 'wc-vendors/dashboard/settings/', wcv_plugin_dir . 'templates/dashboard/settings/' );

	do_action( 'wcvendors_settings_after_shop_name' );

	wc_get_template( 'seller-info.php', array(
													  'global_html' => $global_html,
													  'has_html'    => $has_html,
													  'seller_info' => $seller_info,
												 ), 'wc-vendors/dashboard/settings/', wcv_plugin_dir . 'templates/dashboard/settings/' );

	do_action( 'wcvendors_settings_after_seller_info' );

	if ( $shop_description !== 'false' ) {
		wc_get_template( 'shop-description.php', array(
															   'description' => $description,
															   'global_html' => $global_html,
															   'has_html'    => $has_html,
															   'shop_page'   => $shop_page,
															   'user_id'     => $user_id,
														  ), 'wc-vendors/dashboard/settings/', wcv_plugin_dir . 'templates/dashboard/settings/' );

		do_action( 'wcvendors_settings_after_shop_description' );
	}
	?>

	<?php wp_nonce_field( 'save-shop-settings', 'wc-product-vendor-nonce' ); ?>
	<input type="submit" class="btn btn-small" style="float:none;" name="vendor_application_submit"
		   value="<?php _e( 'Save', 'buddyboss-marketplace' ); ?>"/>
</form>
