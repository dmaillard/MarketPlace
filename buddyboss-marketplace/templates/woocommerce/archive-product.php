<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/bb-marketplace/woocommerce/archive-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'shop' ); ?>

	<?php
		/**
		 * woocommerce_before_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		do_action( 'woocommerce_before_main_content' );
	?>

	<?php

	    global $_wp_additional_image_sizes;

		$vendor_shop 		= urldecode( get_query_var( 'vendor_shop' ) );
		$vendor_id   		= buddyboss_bm()->get_vendor_id( $vendor_shop );
		$address1 			= get_user_meta( $vendor_id, '_wcv_store_address1', true );
		$address2 			= get_user_meta( $vendor_id, '_wcv_store_address2', true );
		$phone	 			= get_user_meta( $vendor_id, '_wcv_store_phone', true );
		$city	 			= get_user_meta( $vendor_id, '_wcv_store_city', true );
		$state	 			= get_user_meta( $vendor_id, '_wcv_store_state', true );
		$store_querycode	= get_user_meta( $vendor_id, '_wcv_store_postcode', true );
		$address 			= ($address1 != '') ? $address1 .', ' . $city .', '. $state .', '. $store_querycode : '';
		$user_link 			= function_exists( 'bp_core_get_user_domain' ) ? bp_core_get_user_domain( $vendor_id ) : '#';

		// Verified vendor
		if(get_user_meta( $vendor_id )) {
			$vendor_meta = array_map( function( $a ){ return $a[0]; }, get_user_meta( $vendor_id ) );
			$verified_vendor 			= ( array_key_exists( '_wcv_verified_vendor', $vendor_meta ) ) ? $vendor_meta[ '_wcv_verified_vendor' ] : false;
		}
		$verified_vendor_label = '';
		if(class_exists('WCVendors_Pro')) {
			$verified_vendor_label 		= WCVendors_Pro::get_option( 'verified_vendor_label' );
		}
		// $verified_vendor_icon_src 	= WCVendors_Pro::get_option( 'verified_vendor_icon_src' );
	?>


	<?php if($vendor_shop): ?>
	<div class="shop-sidebar">
		<aside class="show-owner-widget widget">
			<div class="owner-info">
				<h3><?php _e('Shop Owner', 'buddyboss-marketplace'); ?></h3>
				<!-- Follow Vendor -->
                <div class="inner-avatar-wrap owner-avatar author-follow">
                    <?php
                    $current_user_id     = bp_loggedin_user_id();
                    $displayed_user_id     = $vendor_id;
                    if ( $current_user_id != $displayed_user_id ) {
                        if ( function_exists( 'bp_follow_add_follow_button' ) ) {
                            $args = array(
                                'leader_id' => $displayed_user_id
                            );
                            bp_follow_add_follow_button( $args );
                        } elseif ( bp_is_active( 'friends' ) ) {
                            bp_add_friend_button( $displayed_user_id );
                        }
                    }
                    ?>
                    <a class="boss-avatar-container" href="<?php echo bp_core_get_user_domain( $displayed_user_id ); ?>">
                        <?php echo get_avatar( $vendor_id, 90 ); ?>
                    </a>
                </div>
				<a href="<?php echo $user_link; ?>" class="owner-name"><?php echo bp_core_get_user_displayname( $vendor_id ); ?></a>

				<div class="shop-rating">
				<?php do_action( 'wcv_before_vendor_store_rating' ); ?>
				<?php if ( class_exists('WCVendors_Pro') && ! WCVendors_Pro::get_option( 'ratings_management_cap' ) ) echo WCVendors_Pro_Ratings_Controller::ratings_link( $vendor_id, true ); ?>
				<?php do_action( 'wcv_after_vendor_store_rating' ); ?>
				<?php if ( $verified_vendor && $verified_vendor_label ) : ?>
					<div class="wcv-verified-vendor">
						<i class="fa fa-check-circle-o fa-lg" aria-hidden="true"></i> &nbsp; <?php echo $verified_vendor_label; ?>
					</div>
				<?php endif; ?>
				</div>

				<div class="wcv-store-address-container">
					<div class="store-address">
						<?php if ( $address != '' ) {  ?><a href="http://maps.google.com/maps?&q=<?php echo $address; ?>"><address><i class="fa fa-location-arrow"></i><?php echo $address; ?></address></a><?php } ?>
					</div>
					<div class="store-phone">
						<?php if ($phone != '')  { ?><a href="tel:<?php echo $phone; ?>"><i class="fa fa-phone"></i><?php echo $phone; ?></a><?php } ?>
					</div>
				</div>
			</div>
			<!-- /.owner-info -->
			<?php
			$current_user	 = get_current_user_id();

			$url = '';
            $next_url = '';
			if(!$current_user) {
				$url = get_permalink( get_option('woocommerce_myaccount_page_id') );
                $next_url = '/compose/?r=' . bp_core_get_username($vendor_id);
			} elseif ( bp_is_active( 'messages' ) && $current_user && ($current_user != $vendor_id )) {
				$url = wp_nonce_url(bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username($vendor_id));
			}
			if($url) {
			?>
			<div class="generic-button" id="send-private-message">
				<a class="send-message" href="<?php echo $url; ?>" data-next="<?php echo $next_url; ?>"
				   title="<?php _e('Send a private message to this user.', 'buddyboss-marketplace'); ?>"><?php _e('Ask a question', 'buddyboss-marketplace'); ?></a>
			</div>
			<?php } ?>
		</aside>
		<!-- /.show-owner-widget -->

		<aside class="show-categories widget">
			<div class="cat-title">
				<?php _e('Shop Sections', 'buddyboss-marketplace'); ?>
			</div>
			<div class="cat-list">
				<?php BuddyBoss_BM_Templates::bm_filter_by_category($vendor_id); ?>
			</div>
		</aside>
		<!-- /.show-owner-widget -->
	</div>
	<!-- /.shop-sidebar -->
	<?php endif; ?>

	<?php
        $location_search = (isset($_GET['location_search']) && $_GET['location_search']==true);
        $is_filter = isset($_GET['min_price']) || isset($_GET['filter_attribute']);
    ?>

	<?php if(!$vendor_shop && !$location_search && !$is_filter): ?>
		<?php

		$args = array(
			'taxonomy'     => 'product_cat',
			'orderby'      => 'name',
			'show_count'   => 1,
			'hierarchical' => 1,
			'hide_empty'   => 0,
		);

		$all_categories = get_categories( $args );
		$featured = array();
		$count = 0;
		foreach ($all_categories as $cat) {
			$term_meta = get_option("taxonomy_" . $cat->term_id);
			if ($count < 4 && isset($term_meta["show_on_cat"]) && $term_meta["show_on_cat"] == "on") {
				$featured[] = $cat;
				$count++;
			}
		}
		$total = count($featured);
		$i = 0;
		if($total) {
			$image_sizes = array();
			if($total == 1) {
				$image_sizes[0] = 'large';
			} else if ($total == 2) {
				$image_sizes[0] = 'cat-half';
				$image_sizes[1] = 'cat-half';
			} else if ($total == 3) {
				$image_sizes[0] = 'cat-half';
				$image_sizes[1] = 'cat-fourth';
				$image_sizes[2] = 'cat-fourth';
			} else {
				$image_sizes[0] = 'cat-half';
				$image_sizes[1] = 'cat-eighth';
				$image_sizes[2] = 'cat-eighth';
				$image_sizes[3] = 'cat-fourth';
			}
			echo '<h3 class="bm-featured-title">'. sprintf(_n('Top Category', 'Top Categories', $total, 'buddyboss-marketplace'), $total) .'</h3>';
			echo '<div class="featured-cats style1 items-'.$total.'">';
			foreach ($featured as $cat) {
//				if ($cat->category_parent == 0) {
					$category_id = $cat->term_id;
					$thumbnail_id = get_woocommerce_term_meta($cat->term_id, 'thumbnail_id', true);
					$count = $cat->count;
					?>
					<a href="<?php echo get_term_link($cat->slug, 'product_cat'); ?>"
					   class="bm-f-category block-<?php echo $image_sizes[$i]; ?>">
						<?php
						if($thumbnail_id) {
							echo wp_get_attachment_image($thumbnail_id, $image_sizes[$i]);
						} else {
							$query_args = array(
								'post_type'           => 'product',
								'post_status'         => 'publish',
								'ignore_sticky_posts' => 1,
								'orderby'             => 'date',
								'order'               => 'desc',
								'posts_per_page'      => 1,
								'tax_query' => array(
									array(
										'taxonomy' => 'product_cat',
										'terms'    => $category_id,
										'field'    => 'term_id',
									)
								)
							);

							$products = new WP_Query( $query_args );
							if(has_post_thumbnail($products->post->ID)){
								echo get_the_post_thumbnail($products->post->ID, $image_sizes[$i]);
							} else {
								echo '<img src="' . buddyboss_bm()->assets_url . '/images/'.$_wp_additional_image_sizes[$image_sizes[$i]]['width'].'x'.$_wp_additional_image_sizes[$image_sizes[$i]]['height'].'.png">';
							}
						}
						?>
						<div class="f-cat-des table">
							<div class="table-cell">
								<h5>
									<?php echo $cat->name; ?>
								</h5>
								<?php printf(_n('%s Item', '%s Items', $count, 'buddyboss-marketplace'), $count); ?>
							</div>
						</div>
					</a>
					<?php
//				}
				$i++;
				if($i == 4) $i = 0;
			}
			echo '</div>';
		}
		?>
		<!-- /.featured-cats -->
	<?php endif; ?>

	<div class="shop-main-area">

		<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

			<h1 class="page-title"><?php woocommerce_page_title(); ?></h1>

		<?php endif; ?>

		<?php
			if(!$location_search && !$is_filter) {
				/**
				 * woocommerce_archive_description hook
				 *
				 * @hooked woocommerce_taxonomy_archive_description - 10
				 * @hooked woocommerce_product_archive_description - 10
				 */
				do_action('woocommerce_archive_description');
			}

//            global $wp_query;
//            $searchby = isset( $_GET['bm_store_search'] )?$_GET['bm_store_search']:'';
//            $wp_query->set('s' ,$searchby);
//            $products = new WP_Query($wp_query->query_vars);
		?>

		<?php if ( have_posts() ) : ?>

			<?php
				/**
				 * woocommerce_before_shop_loop hook
				 *
				 * @hooked woocommerce_result_count - 20
				 * @hooked woocommerce_catalog_ordering - 30
				 */
				do_action( 'woocommerce_before_shop_loop' );
			?>

			<?php woocommerce_product_loop_start(); ?>

				<?php woocommerce_product_subcategories(); ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

			<?php
				/**
				 * woocommerce_after_shop_loop hook
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action( 'woocommerce_after_shop_loop' );
			?>

		<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

			<?php wc_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif; ?>

		<?php wp_reset_postdata(); ?>

	</div>
	<!-- /.shop-main-area -->
	<?php
		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>


	<?php
		/**
		 * woocommerce_sidebar hook
		 *
		 * @hooked woocommerce_get_sidebar - 10
		 */
		//do_action( 'woocommerce_sidebar' );
	?>

<?php get_footer( 'shop' ); ?>
