<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/bb-marketplace/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php
	/**
	 * woocommerce_before_single_product hook
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<?php
	$current_user = bp_loggedin_user_id();
	$product_id = get_the_ID();
	$vendor_id 		= WCV_Vendors::get_vendor_from_product( $product_id );
	$is_vendor 		= WCV_Vendors::is_vendor( $vendor_id );
	$shop_url  = WCV_Vendors::get_vendor_shop_page( $vendor_id );
	$shop_name = $is_vendor ? WCV_Vendors::get_vendor_sold_by( $vendor_id ) : get_bloginfo( 'name' );
	$store_icon_src 	= wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_icon_id', true ), array( 100, 100 ) );
	$store_icon 		= '';

	// see if the array is valid
	if ( is_array( $store_icon_src ) ) {
		$store_icon 	= '<img src="'. $store_icon_src[0].'" alt="" class="store-icon" style="max-width:100%;" />';
	}

	global $product;

	$upsells = $product->get_upsells();
	$class = '';
	if ( sizeof( $upsells ) != 0 ) {
		$class = 'has-upsells';
	}
?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class($class); ?>>

	<?php if (WC_Vendors::$pv_options->get_option( 'shop_headers_enabled' ) ): ?>

	<div class="store-summary table">
		<div class="store-desc table-cell">
			<?php echo '<a href="'.$shop_url.'" class="store-icon">'. $store_icon .'</a>'; ?>
			<?php echo '<a href="'.$shop_url.'" class="store-name">'. $shop_name .'</a>'; ?>
			<?php
			if ( $current_user && $current_user != $vendor_id ):
				$favorites = get_user_meta(get_current_user_id(), "favorite_shops", true);

                $text = __('Add to Favorites', 'buddyboss-marketplace' );
                $class = '';
                if((is_array($favorites) && in_array($vendor_id, $favorites))) {
                    $text = __('Remove from Favorites', 'buddyboss-marketplace' );
                    $class = ' favorited';
                }
			?>
			<?php echo '<p><a href="#" class="bm-add-to-favs fa-heart-o'.$class.'" data-id="'. $vendor_id. '">'.$text.'</a></p>'; ?>
			<?php endif;?>
		</div>
		<!-- /.store-desc -->
		<div class="store-products table-cell">
			<?php
            // Products Loop
			$product_args = array(
				'post_type'       => 'product',
				'author'          => $vendor_id,
				'posts_per_page'  => 2,
                'meta_query'      => array(
                    array(
                        'key'     => '_visibility',
                        'value'   => 'visible'
                    ),
                    array(
                        'key'       => '_visibility',
                        'value'     => 'visible',
                        'compare'   => 'NOT EXISTS',
                    ),
                    'relation'      => 'OR',
                ),
			);

			//Exclude out of stock items in listing of products on single product page
			if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
				$product_args['meta_query'] = array(
					array(
					'key'       => '_stock_status',
					'value'     => 'outofstock',
					'compare'   => 'NOT IN'
				));
			}

			$product_posts = new WP_Query( $product_args );

			$total_count = $product_posts->found_posts;
			$count = 0;
			?>

			<?php if ( $product_posts->have_posts() ) : ?>

				<?php while ( $product_posts->have_posts() && $count < 2 ) : $product_posts->the_post(); ?>
					<?php global $product; ?>
					<a href="<?php echo get_the_permalink($product->id) ?>">
						<?php

						if ( has_post_thumbnail() ) {
							echo get_the_post_thumbnail( $product->id, 'bm-store-archive' );
						} elseif ( wc_placeholder_img_src() ) {
							echo wc_placeholder_img( array(135, 150, 1) );
						}
						?>
					</a>

					<?php $count++; ?>

				<?php endwhile; // end of the loop. ?>

			<?php endif; ?>

			<div class="product-count">
				<img src="<?php echo buddyboss_bm()->assets_url . '/images/135x150.png' ?>">
				<a href="<?php echo $shop_url; ?>" class="overlay">
					<div class="table">
						<div class="table-cell">
							<span class="number"><?php echo $total_count; ?></span>
							<span class="text"><?php printf( _n( 'item', 'items', $total_count, 'buddyboss-marketplace' ), $total_count ); ?></span>
						</div>
					</div>
				</a>
			</div>

		</div>
		<!-- /.store-items -->
	</div>
	<!-- /.store-summary -->

	<?php wp_reset_query(); ?>

	<?php endif; ?>


	<div class="product-main-area">

		<?php
			/**
			 * woocommerce_before_single_product_summary hook
			 *
			 * @hooked woocommerce_show_product_sale_flash - 10
			 * @hooked woocommerce_show_product_images - 20
			 */
			do_action( 'woocommerce_before_single_product_summary' );
		?>

		<div class="summary entry-summary">

			<div class="product-vendor">
				<?php $user_link = function_exists( 'bp_core_get_user_domain' ) ? bp_core_get_user_domain( $vendor_id ) : '#'; ?>
				<div class="about-owner">
					<div class="table">
						<div class="table-cell owner-avatar">
							<a href="<?php echo $user_link; ?>"><?php echo get_avatar( $vendor_id, 40 ); ?></a>
						</div>
						<div class="table-cell owner-name">
							<span><?php _e('Shop Owner', 'buddyboss-marketplace');?></span>
							<a href="<?php echo $user_link; ?>"><?php echo bp_core_get_user_displayname( $vendor_id ); ?></a>
						</div>
					</div>
				</div>

				<?php
				$url = '';
                $next_url = '';
				if(!is_user_logged_in()) {
					$url = get_permalink( get_option('woocommerce_myaccount_page_id') );
                    $next_url = '/compose/?r=' . bp_core_get_username($vendor_id);
                    $next_url = add_query_arg( array( 'qproduct' => get_the_ID() ), $next_url );
				} elseif ( bp_is_active( 'messages' ) && ($current_user != $vendor_id )) {
					$url = wp_nonce_url(bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username($vendor_id));
                    $url = add_query_arg( array( 'qproduct' => get_the_ID() ), $url );
				}
				if($url) {
				?>
				<div class="generic-button" id="send-private-message">
					<a class="send-message" href="<?php echo $url; ?>" data-next="<?php echo $next_url; ?>"
					   title="<?php _e('Send a private message to this user.', 'buddyboss-marketplace'); ?>"><?php _e('Ask a question', 'buddyboss-marketplace'); ?></a>
				</div>
				<?php
				}
				?>
			</div>

			<?php
				/**
				 * woocommerce_single_product_summary hook
				 *
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked woocommerce_template_single_rating - 10
				 * @hooked woocommerce_template_single_price - 10
				 * @hooked woocommerce_template_single_excerpt - 20
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked woocommerce_template_single_meta - 40
				 * @hooked woocommerce_template_single_sharing - 50
				 */
				do_action( 'woocommerce_single_product_summary' );
			?>

		</div><!-- .summary -->


		<meta itemprop="url" content="<?php the_permalink(); ?>" />

	</div>
	<!-- /.product-main-area -->

	<?php
	/**
	 * woocommerce_after_single_product_summary hook
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 * @hooked woocommerce_upsell_display - 15
	 * @hooked woocommerce_output_related_products - 20
	 */
	do_action( 'woocommerce_after_single_product_summary' );
	?>

</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'bm_after_single_product_summary' ); ?>

<?php do_action( 'woocommerce_after_single_product' ); ?>
