<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/bb-marketplace/woocommerce/content-product.php
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}

// Ensure visibility
if ( ! $product || ! $product->is_visible() ) {
	return;
}

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();
if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] ) {
	$classes[] = 'first';
}
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] ) {
	$classes[] = 'last';
}
if(!$product->get_price_html()) {
	$classes[] = 'no-price';
}
?>
<li <?php post_class( $classes ); ?>>

	<div class="bm-product-outher">
		<div class="bm-product-inner">

			<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

			<div class="loop-product-image">
				<a class="image-link" href="<?php the_permalink(); ?>">
					<?php
					/**
					 * woocommerce_before_shop_loop_item_title hook
					 *
					 * @hooked woocommerce_show_product_loop_sale_flash - 10
					 * @hooked woocommerce_template_loop_product_thumbnail - 10
					 */
					do_action( 'woocommerce_before_shop_loop_item_title' );
					?>
				</a>

				<div class="product-item-buttons">
					<?php

					/**
					 * woocommerce_after_shop_loop_item hook
					 *
					 * @hooked woocommerce_template_loop_add_to_cart - 10
					 */
					do_action( 'woocommerce_after_shop_loop_item' );

					?>
				</div>
			</div>
			<!-- /.loop-product-image -->

			<a href="<?php the_permalink(); ?>">
				<?php

				/**
				 * woocommerce_shop_loop_item_title hook
				 *
				 * @hooked woocommerce_template_loop_product_title - 10
				 */
				do_action( 'woocommerce_shop_loop_item_title' );

				/**
				 * woocommerce_after_shop_loop_item_title hook
				 *
				 * @hooked woocommerce_template_loop_rating - 5
				 * @hooked woocommerce_template_loop_price - 10
				 */
				do_action( 'woocommerce_after_shop_loop_item_title' );
				?>

			</a>

			<?php
			$vendor_id = $product->post->post_author;
			if(WCV_Vendors::is_vendor($vendor_id)) {
				$shop_name = WCV_Vendors::is_vendor($vendor_id)
					? WCV_Vendors::get_vendor_shop_name($vendor_id)
					: get_bloginfo('name');
				$store_icon_src = wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_icon_id', true ), 'bm-store-icon' );
				$store_icon = '';
				$shop_url = WCV_Vendors::get_vendor_shop_page($vendor_id);
				// see if the array is valid
				if (is_array($store_icon_src)) {
					$store_icon = '<img src="' . $store_icon_src[0] . '" alt="" class="store-icon"/>';
				} else {
					$store_icon = get_avatar( $product->post->post_author, 40 );
				}
			} else {
				$store_icon = get_avatar( $product->post->post_author, 40 );
				$shop_name  = get_bloginfo('name');
				$shop_url 	= '';
			}

			?>
         	<?php if(!urldecode( get_query_var( 'vendor_shop' ) ) && bp_current_action() != 'my_products'): ?>
            <div class="bm-product-author">
                <a href="<?php echo $shop_url; ?>"><?php echo $store_icon; ?></a>
                <a href="<?php echo $shop_url; ?>"><?php echo $shop_name; ?></a>
            </div>
        <?php endif; ?>
		</div>
	</div>
</li>
