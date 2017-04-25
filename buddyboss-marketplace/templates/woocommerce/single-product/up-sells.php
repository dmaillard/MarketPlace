<?php
/**
 * Single Product Up-Sells
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

$upsells = $product->get_upsell_ids();

if ( sizeof( $upsells ) == 0 ) {
	return;
}

$meta_query = WC()->query->get_meta_query();

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => $posts_per_page,
	'orderby'             => $orderby,
	'post__in'            => $upsells,
	'post__not_in'        => array( $product->id ),
	'meta_query'          => $meta_query
);

$products = new WP_Query( $args );

$woocommerce_loop['columns'] = $columns;

$vendor_id 		= WCV_Vendors::get_vendor_from_product( $product->id );
$is_vendor 		= WCV_Vendors::is_vendor( $vendor_id );
$shop_url  = WCV_Vendors::get_vendor_shop_page( $vendor_id );

$shop_name = $is_vendor ? WCV_Vendors::get_vendor_sold_by( $vendor_id ) : get_bloginfo( 'name' );
$store_icon_src 	= wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_icon_id', true ), array( 100, 100 ) );
$store_icon 		= '';

// see if the array is valid
if ( is_array( $store_icon_src ) ) {
	$store_icon 	= '<img src="'. $store_icon_src[0].'" alt="" class="store-icon" style="max-width:100%;" />';
}

if ( $products->have_posts() ) : ?>

	<div class="upsells products">

		<?php echo '<a href="'.$shop_url.'" class="store-icon">'. $store_icon .'</a>'; ?>
		<?php echo '<a href="'.$shop_url.'" class="store-name">'. $shop_name .'</a>'; ?>

		<?php woocommerce_product_loop_start(); ?>

			<?php while ( $products->have_posts() ) : $products->the_post(); ?>

				<?php wc_get_template_part( 'content', 'product' ); ?>

			<?php endwhile; // end of the loop. ?>

		<?php woocommerce_product_loop_end(); ?>

	</div>

<?php endif;

wp_reset_postdata();
