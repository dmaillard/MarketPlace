<?php
/**
 *  Vendor Main Header - Hooked into archive-product page 
*
 *  THIS FILE WILL LOAD ON VENDORS STORE URLs (such as yourdomain.com/vendors/bobs-store/)
 *
 * @author WCVendors
 * @package WCVendors
 * @version 1.3.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

/*
*	Template Variables available 
*   $vendor : 			For pulling additional user details from vendor account.  This is an array.
*   $vendor_id  : 		current vendor user id number
*   $shop_name : 		Store/Shop Name (From Vendor Dashboard Shop Settings)
*   $shop_description : Shop Description (completely sanitized) (From Vendor Dashboard Shop Settings)
*   $seller_info : 		Seller Info(From Vendor Dashboard Shop Settings)
*	$vendor_email :		Vendors email address
*	$vendor_login : 	Vendors user_login name
*	$vendor_shop_link : URL to the vendors store
*/ 

?>

<?php
/**
 * The Template for displaying a single store front header
 *
 * Override this template by copying it to yourtheme/wc-vendors/store
 *
 * @package    WCVendors_Pro
 * @version    1.1.2
 */
$store_icon_src 	= wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_icon_id', true ), 'shop_catalog' );
$store_icon 		= '';
$store_banner_src 	= wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_banner_id', true ), 'full');
$store_banner 		= '';

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
$phone				= get_user_meta( $vendor_id , '_wcv_store_phone', true );

// This is where you would load your own custom meta fields if you stored any in the settings page for the dashboard

?>

<?php do_action( 'wcv_before_vendor_store_header' ); ?>

<div class="wcv-header-container<?php echo (is_array( $store_icon_src ) || is_array( $store_banner_src ))?' white':''; ?>">

    <div id="banner-wrap">

        <div id="inner-element">

            <?php if ($store_icon != '') : ?>

                <div class="all-50 store-brand">
                    <?php echo $store_icon; ?>
                    <?php do_action( 'wcv_before_vendor_store_title' ); ?>
                    <h2><span><?php echo $shop_name; ?></span></h2>
                    <?php do_action( 'wcv_after_vendor_store_title' ); ?>
                    <ul class="social-icons">
                        <?php if ( $facebook_url != '') { ?><li><a href="<?php echo $facebook_url; ?>" target="_blank"><i class="fa fa-facebook"></i><?php _e('Facebook', 'buddyboss-marketplace'); ?></a></li><?php } ?>
                        <?php if ( $instagram_username != '') { ?><li><a href="//instagram.com/<?php echo $instagram_username; ?>" target="_blank"><i class="fa fa-instagram"></i><?php _e('Instagram', 'buddyboss-marketplace'); ?></a></li><?php } ?>
                        <?php if ( $twitter_username != '') { ?><li><a href="//twitter.com/<?php echo $twitter_username; ?>" target="_blank"><i class="fa fa-twitter"></i><?php _e('Twitter', 'buddyboss-marketplace'); ?></a></li><?php } ?>
                        <?php if ( $googleplus_url != '') { ?><li><a href="<?php echo $googleplus_url; ?>" target="_blank"><i class="fa fa-google-plus"></i><?php _e('Google +', 'buddyboss-marketplace'); ?></a></li><?php } ?>
                        <?php if ( $pinterest_url != '') { ?><li><a href="<?php echo $pinterest_url; ?>" target="_blank"><i class="fa fa-pinterest-square"></i><?php _e('Pinterest', 'buddyboss-marketplace'); ?></a></li><?php } ?>
                        <?php if ( $youtube_url != '') { ?><li><a href="<?php echo $youtube_url; ?>" target="_blank"><i class="fa fa-youtube"></i><?php _e('Youtube', 'buddyboss-marketplace'); ?></a></li><?php } ?>
                        <?php if ( $linkedin_url != '') { ?><li><a href="<?php echo $linkedin_url; ?>" target="_blank"><i class="fa fa-linkedin"></i><?php _e('Linkedin', 'buddyboss-marketplace'); ?></a></li><?php } ?>
                    </ul>
                </div>

                <div class="all-50 store-info">
                    <?php do_action( 'wcv_before_vendor_store_description' ); ?>
                    <?php echo $shop_description; ?>
                    <?php do_action( 'wcv_after_vendor_store_description' ); ?>
                </div>
            <?php else: ?>

                <div class="all-100 store-info">

                    <h2><span><?php echo $shop_name; ?></span></h2>
                    <?php echo $shop_description; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php do_action( 'wcv_after_vendor_store_header' ); ?>
