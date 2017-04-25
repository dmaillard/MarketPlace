<?php
/**
 * The Template for displaying a store header
 *
 * Override this template by copying it to yourtheme/wc-vendors/store
 *
 * @package    WCVendors_Pro
 * @version    1.2.3
 */

$store_icon_src 	= wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_icon_id', true ), 'shop_catalog' );
$store_icon 		= '';
$store_banner_src 	= wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_banner_id', true ), 'full');
$store_banner 		= '';

$shop_name = WCV_Vendors::is_vendor($vendor_id)
	? WCV_Vendors::get_vendor_shop_name($vendor_id)
	: get_bloginfo('name');

// see if the array is valid
if ( is_array( $store_icon_src ) ) {
	$store_icon 	= '<img src="'. $store_icon_src[0].'" alt="" class="store-icon" />';
}

// Get store details including social, adddresses and phone number
$twitter_username 	= get_user_meta( $vendor_id , '_wcv_twitter_username', true );
$instagram_username = get_user_meta( $vendor_id , '_wcv_instagram_username', true );
$facebook_url 		= get_user_meta( $vendor_id , '_wcv_facebook_url', true );
$linkedin_url 		= get_user_meta( $vendor_id , '_wcv_linkedin_url', true );
$youtube_url 		= get_user_meta( $vendor_id , '_wcv_youtube_url', true );
$googleplus_url 	= get_user_meta( $vendor_id , '_wcv_googleplus_url', true );
$pinterest_url 		= get_user_meta( $vendor_id , '_wcv_pinterest_url', true );
$snapchat_username 	= get_user_meta( $vendor_id , '_wcv_snapchat_username', true );

$address1 			= ( array_key_exists( '_wcv_store_address1', $vendor_meta ) ) ? $vendor_meta[ '_wcv_store_address1' ] : '';
$address2 			= ( array_key_exists( '_wcv_store_address2', $vendor_meta ) ) ? $vendor_meta[ '_wcv_store_address2' ] : '';
$city	 			= ( array_key_exists( '_wcv_store_city', $vendor_meta ) ) ? $vendor_meta[ '_wcv_store_city' ]  : '';
$state	 			= ( array_key_exists( '_wcv_store_state', $vendor_meta ) ) ? $vendor_meta[ '_wcv_store_state' ] : '';
$phone				= ( array_key_exists( '_wcv_store_phone', $vendor_meta ) ) ? $vendor_meta[ '_wcv_store_phone' ]  : '';
$store_postcode		= ( array_key_exists( '_wcv_store_postcode', $vendor_meta ) ) ? $vendor_meta[ '_wcv_store_postcode' ]  : '';

$address 			= ( $address1 != '') ? $address1 .', ' . $city .', '. $state .', '. $store_postcode : '';

$social_icons = empty( $twitter_username ) && empty( $instagram_username ) && empty( $facebook_url ) && empty( $linkedin_url ) && empty( $youtube_url ) && empty( $googleplus_url ) && empty( $pinterst_url ) && empty( $snapchat_username ) ? false : true;

// This is where you would load your own custom meta fields if you stored any in the settings page for the dashboard

?>

<?php do_action( 'wcv_before_vendor_store_header' ); ?>

<div class="wcv-header-container<?php echo (is_array( $store_icon_src ) || is_array( $store_banner_src ))?' white':''; ?>">

	<div class="wcv-store-grid">

		<div id="banner-wrap">

			<div id="inner-element">

				<?php if ($store_icon != '') : ?>

					<div class="wcv-store-grid__col wcv-store-grid__col--1-of-2 store-brand">
						<?php echo $store_icon; ?>
						<?php do_action( 'wcv_before_vendor_store_title' ); ?>
						<h2><span><?php echo $shop_name; ?></span></h2>
						<?php do_action( 'wcv_after_vendor_store_title' ); ?>
					    <?php if ( $social_icons ) : ?>
						<ul class="social-icons">
							<?php if ( $facebook_url != '') { ?><li><a href="<?php echo $facebook_url; ?>" target="_blank"><i class="fa fa-facebook"></i><?php _e('Facebook', 'buddyboss-marketplace'); ?></a></li><?php } ?>
							<?php if ( $instagram_username != '') { ?><li><a href="//instagram.com/<?php echo $instagram_username; ?>" target="_blank"><i class="fa fa-instagram"></i><?php _e('Instagram', 'buddyboss-marketplace'); ?></a></li><?php } ?>
							<?php if ( $twitter_username != '') { ?><li><a href="//twitter.com/<?php echo $twitter_username; ?>" target="_blank"><i class="fa fa-twitter"></i><?php _e('Twitter', 'buddyboss-marketplace'); ?></a></li><?php } ?>
							<?php if ( $googleplus_url != '') { ?><li><a href="<?php echo $googleplus_url; ?>" target="_blank"><i class="fa fa-google-plus"></i><?php _e('Google +', 'buddyboss-marketplace'); ?></a></li><?php } ?>
							<?php if ( $pinterest_url != '') { ?><li><a href="<?php echo $pinterest_url; ?>" target="_blank"><i class="fa fa-pinterest-square"></i><?php _e('Pinterest', 'buddyboss-marketplace'); ?></a></li><?php } ?>
							<?php if ( $youtube_url != '') { ?><li><a href="<?php echo $youtube_url; ?>" target="_blank"><i class="fa fa-youtube"></i><?php _e('Youtube', 'buddyboss-marketplace'); ?></a></li><?php } ?>
							<?php if ( $linkedin_url != '') { ?><li><a href="<?php echo $linkedin_url; ?>" target="_blank"><i class="fa fa-linkedin"></i><?php _e('Linkedin', 'buddyboss-marketplace'); ?></a></li><?php } ?>
							<?php if ( $snapchat_username != '') { ?><li><a href="//www.snapchat.com/add/<?php echo $snapchat_username; ?>" target="_blank"><i class="fa fa-snapchat" aria-hidden="true"></i><?php _e('Snapchat', 'buddyboss-marketplace'); ?></a></li><?php } ?>
						</ul>
						<?php endif; ?>
					</div>

					<div class="wcv-store-grid__col wcv-store-grid__col--1-of-2 store-info">
						<?php do_action( 'wcv_before_vendor_store_description' ); ?>
						<p><?php echo $vendor_meta['pv_shop_description']; ?></p>
						<?php do_action( 'wcv_after_vendor_store_description' ); ?>
					</div>
				<?php else: ?>

					<div class="wcv-store-grid__col wcv-store-grid__col--2-of-2 store-info">

						<?php do_action( 'wcv_before_vendor_store_title' ); ?>
						<h2><?php echo $shop_name; ?></h2>
						<?php do_action( 'wcv_after_vendor_store_title' ); ?>
						<?php if ( $social_icons ) : ?>
						<ul class="social-icons">
							<?php if ( $facebook_url != '') { ?><li><a href="<?php echo $facebook_url; ?>" target="_blank"><i class="fa fa-facebook"></i><?php _e('Facebook', 'buddyboss-marketplace'); ?></a></li><?php } ?>
							<?php if ( $instagram_username != '') { ?><li><a href="//instagram.com/<?php echo $instagram_username; ?>" target="_blank"><i class="fa fa-instagram"></i><?php _e('Instagram', 'buddyboss-marketplace'); ?></a></li><?php } ?>
							<?php if ( $twitter_username != '') { ?><li><a href="//twitter.com/<?php echo $twitter_username; ?>" target="_blank"><i class="fa fa-twitter"></i><?php _e('Twitter', 'buddyboss-marketplace'); ?></a></li><?php } ?>
							<?php if ( $googleplus_url != '') { ?><li><a href="<?php echo $googleplus_url; ?>" target="_blank"><i class="fa fa-google-plus"></i><?php _e('Google +', 'buddyboss-marketplace'); ?></a></li><?php } ?>
							<?php if ( $pinterest_url != '') { ?><li><a href="<?php echo $pinterest_url; ?>" target="_blank"><i class="fa fa-pinterest-square"></i><?php _e('Pinterest', 'buddyboss-marketplace'); ?></a></li><?php } ?>
							<?php if ( $youtube_url != '') { ?><li><a href="<?php echo $youtube_url; ?>" target="_blank"><i class="fa fa-youtube"></i><?php _e('Youtube', 'buddyboss-marketplace'); ?></a></li><?php } ?>
							<?php if ( $linkedin_url != '') { ?><li><a href="<?php echo $linkedin_url; ?>" target="_blank"><i class="fa fa-linkedin"></i><?php _e('Linkedin', 'buddyboss-marketplace'); ?></a></li><?php } ?>
							<?php if ( $snapchat_username != '') { ?><li><a href="//www.snapchat.com/add/<?php echo $snapchat_username; ?>" target="_blank"><i class="fa fa-snapchat" aria-hidden="true"></i><?php _e('Snapchat', 'buddyboss-marketplace'); ?></a></li><?php } ?>
						</ul>
						<?php endif; ?>
						<?php do_action( 'wcv_before_vendor_store_description' ); ?>
						<p><?php echo $vendor_meta['pv_shop_description']; ?></p>
						<?php do_action( 'wcv_after_vendor_store_description' ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php do_action( 'wcv_after_vendor_store_header' ); ?>
