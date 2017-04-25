<?php
/**
 * The template for displaying the product loop on the vendor stores page. 
 *
 * Override this template by copying it to yourtheme/wc-vendors/store
 *
 * @package    WCVendors_Pro
 * @version    1.0.2
 */

// Products Loop 
$product_args = array(
    'post_type'       => 'product',
    'author'          => $vendor_id, 
    'posts_per_page'  => apply_filters( 'loop_shop_per_page', get_option( 'posts_per_page' ) ),
    'paged'           => $product_paged
);

$product_posts = new WP_Query( $product_args ); ?>

<?php if ( $product_posts->have_posts() ) : ?>

<?php do_action( 'woocommerce_before_shop_loop' ); ?>

<?php woocommerce_product_loop_start(); ?>

	<?php woocommerce_product_subcategories(); ?>

	<?php while ( $product_posts->have_posts() ) : $product_posts->the_post(); ?>

		<?php wc_get_template_part( 'content', 'product' ); ?>

	<?php endwhile; // end of the loop. ?>

<?php woocommerce_product_loop_end(); ?>

<?php do_action( 'woocommerce_after_shop_loop' ); ?>

<?php wc_get_template( 'pagination.php', array(	'current' => $product_paged, 'total' => $product_posts->max_num_pages ), 'wc-vendors/store/', WCVendors_Pro::get_path(). 'templates/store/' ); ?>

<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

<?php wc_get_template( 'loop/no-products-found.php' ); ?>

<?php endif; ?>