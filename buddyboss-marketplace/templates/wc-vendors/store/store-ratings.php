<?php
/**
 * Display the vendor store ratings 
 * 
 * Override this template by copying it to yourtheme/wc-vendors/store
 *
 * @package    WCVendors_Pro
 * @version    1.2.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$vendor_shop 		= urldecode( get_query_var( 'vendor_shop' ) );
$vendor_id   		= buddyboss_bm()->get_vendor_id( $vendor_shop ); 
$vendor_feedback 	= WCVendors_Pro_Ratings_Controller::get_vendor_feedback( $vendor_id );
$vendor_shop_url	= WCV_Vendors::get_vendor_shop_page( $vendor_id ); 

get_header( 'shop' ); ?>

	<?php do_action( 'woocommerce_before_main_content' ); ?>

    <div class="bm-store-ratings">
        <h1 class="page-title"><?php _e( 'Customer Ratings', 'wcvendors-pro' ); ?></h1>

        <?php if ( $vendor_feedback ) { 

            foreach ( $vendor_feedback as $vf ) {

                $customer 		= get_userdata( $vf->customer_id ); 
                $rating 		= $vf->rating; 
                $rating_title 	= $vf->rating_title; 
                $comment 		= $vf->comments;
                $post_date		= date_i18n( get_option( 'date_format' ), strtotime( $vf->postdate ) );  
                $customer_name 	= ucfirst( $customer->display_name ); 
                $product_link	= get_permalink( $vf->product_id );
                $product_title	= get_the_title( $vf->product_id ); 

                // This outputs the star rating 
                $stars = ''; 
                for ($i = 1; $i<=stripslashes( $rating ); $i++) { $stars .= "<i class='fa fa-star'></i>"; } 
                for ($i = stripslashes( $rating ); $i<5; $i++) { $stars .=  "<i class='fa fa-star-o'></i>"; }
                ?> 

                <h3><?php if ( ! empty( $rating_title ) ) { echo $rating_title.' :: '; } ?> <?php echo $stars; ?></h3>

                <p><?php _e( 'Product:', 'wcvendors-pro'); ?><a href="<?php echo $product_link; ?>" target="_blank"><?php echo $product_title; ?></a></p>
                <span><?php __( 'Posted on', 'wcvendors-pro'); ?> <?php echo $post_date; ?></span> <?php __( 'by', 'wcvendors-pro'); echo $customer_name; ?><br />
                <p><?php echo $comment; ?></p>
                <hr />

                <?php 
            }


        } else {  echo __( 'No ratings have been submitted for this vendor yet.', 'wcvendors-pro' ); }  ?>	

        <h3><a href="<?php echo $vendor_shop_url; ?>"><?php _e( 'Return to store', 'wcvendors-pro' ); ?></a></h3>

        <?php do_action( 'woocommerce_after_main_content' ); ?>
    </div>

	<?php do_action( 'woocommerce_sidebar' ); ?>

<?php get_footer( 'shop' ); ?>