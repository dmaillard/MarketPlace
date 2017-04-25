<?php
/**
 * BuddyBoss MarketPlace - Store Index
 *
 * @package WordPress
 * @subpackage BuddyBoss MarketPlace
 */
get_header(); ?>

    <div id="primary" class="site-content">

        <div id="content" role="main" class="woo-content">

        <?php

        // Filter variables
        $order = isset($_GET['sorder'])?$_GET['sorder']:'active';

        $querystring = bp_ajax_querystring( 'members' );

        $defaults = array(
            'type'            => 'active',
            'action'          => 'active'
        );

        $ch_querystring = wp_parse_args( $querystring, $defaults );

        $ch_querystring['type'] = $order;
        $ch_querystring['action'] = $order;

        $querystring = build_query($ch_querystring);

        // Search variable
        $searchby = isset( $_GET['bm_index_search'] )?$_GET['bm_index_search']:'';

        ?>

        <?php $members_query = bp_has_members( 'include='.buddyboss_bm()->vendors_controller->bm_get_vendor_users_string(). '&' . $querystring ); ?>

        <div class="store-filters table">
            <form id="search-shops" role="search" action="" method="get" class="table-cell page-search">
                <input type="text" name="bm_index_search" placeholder="<?php _e('Search sellers', 'buddyboss-marketplace'); ?>" value="<?php echo $searchby; ?>"/>
                <input type="hidden" name="subject" value="sellers"/>
                <input type="submit" alt="Search" value="<?php _e('Search', 'buddyboss-marketplace'); ?>" />
            </form>

            <form id="filter-shops" action="" method="GET" class="table-cell filter-dropdown">

                <label><?php _e('Sort by:', 'buddyboss-marketplace'); ?></label>
                <select name="sorder" id="order">

                    <option value="active" <?php selected( $order, 'active' ); ?>><?php _e('Most Active', 'buddyboss-marketplace'); ?></option>

                    <option value="newest" <?php selected( $order, 'newest' ); ?>><?php _e('Most Recent', 'buddyboss-marketplace'); ?></option>

                    <option value="alphabetical" <?php selected( $order, 'alphabetical' ); ?>><?php _e('Alphabetical', 'buddyboss-marketplace'); ?></option>

                </select>

            </form>
        </div>

        <p class="store-count">
            <?php
            global $members_template;

            if ( empty( $members_template->type ) )
                $members_template->type = '';

            $total     = bp_core_number_format( $members_template->total_member_count );
            $of = ($members_template->pag_num > $total)?$total:$members_template->pag_num;
            ?>
            <?php printf( __('Browsing all sellers <span>(%1s of %2s)</span>', 'buddyboss-marketplace' ), $of , $total ); ?>
        </p>

        <?php if ( $members_query ) : ?>

            <?php do_action( 'bm_before_stores_loop' ); ?>


                <?php while ( bp_members() ) : bp_the_member(); ?>

                    <?php
                    $vendor_id 			= bp_get_member_user_id();
                    $state	 	= get_user_meta( $vendor_id, '_wcv_store_state',		true );
                    $shop_name = WCV_Vendors::is_vendor( $vendor_id )
                        ? WCV_Vendors::get_vendor_shop_name( $vendor_id )
                        : get_bloginfo( 'name' );
                    $store_icon_src 	= wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_icon_id', true ), array( 50, 50 ) );
                    $store_icon 		= '';
                    $shop_url  = WCV_Vendors::get_vendor_shop_page( $vendor_id );
                    // see if the array is valid
                    if ( is_array( $store_icon_src ) ) {
                        $store_icon 	= '<img src="'. $store_icon_src[0].'" alt="" class="store-icon" style="max-width:100%;" />';
                    }
                    $product_args = array(
                        'post_type'       => 'product',
                        'posts_per_page'  => -1,
                        'author'          => $vendor_id,
                        'meta_query' => array(
                            array(
                             'key' => '_private_listing',
                             'compare' => 'NOT EXISTS'
                            ),
                        )
                    );

                    $product_posts = new WP_Query( $product_args );

                    $total_count = $product_posts->post_count;

                    $count = $sum = 0;

                    if(class_exists('WCVendors_Pro_Ratings_Controller')) {
                        if ($product_posts->have_posts()):

                            while ($product_posts->have_posts()) :
                                $product_posts->the_post();
                                $ratings_average = WCVendors_Pro_Ratings_Controller::get_ratings_average($product->post->post_author);
                                $percent = $ratings_average/5;
                                $sum += $percent;
                            endwhile;

                        endif;
                    }
                    ?>

                    <article class="table bm-seller-item">
                        <div class="table-cell seller-desc">
                            <div class="table">
                                <div class="table-cell follow">
                                    <?php
                                    $current_user = bp_loggedin_user_id();
                                    if ( $current_user && $current_user != $vendor_id ):

                                        $favorites = get_user_meta($current_user, "favorite_shops", true);

                                        $tooltip = __('Add to Favorites', 'buddyboss-marketplace');
                                        $class = '';
                                        if((is_array($favorites) && in_array($vendor_id, $favorites))) {
                                            $class = ' favorited';
                                            $tooltip = __('Remove from Favorites', 'buddyboss-marketplace');
                                        }
                                        echo '<p><a href="#" class="boss-tooltip bm-add-to-favs'.$class.'" data-tooltip="'.$tooltip.'" data-id="'. $vendor_id. '"><i class="fa fa-heart-o"></i></a></p>';
                                    endif; ?>
                                </div>
                                <div class="table-cell avatar">
                                    <a href="<?php bp_member_permalink(); ?>"><?php echo get_avatar( $vendor_id, 56 ); ?></a>
                                </div>
                                <div class="table-cell name">
                                    <a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
                                    <?php BuddyBoss_BM_Templates::bm_user_social_links(bp_get_member_user_id()); ?>
                                </div>
                                <div class="table-cell details">
                                    <?php if($state != '') echo '<div>'.$state.'</div>'; ?>
                                    <?php if(class_exists('WCVendors_Pro_Ratings_Controller')): ?>
                                        <?php if(buddyboss_bm()->option('show-sold')) { ?>
                                            <?php $orders = WCVendors_Pro_Vendor_Controller::get_orders2( bp_get_member_user_id() ); ?>
                                            <div class="count-sale"><?php echo __('Sold: ', 'buddyboss-marketplace').count($orders); ?></div>
                                        <?php } ?>

                                        <?php if($total_count > 0): ?>
                                            <div class="percentage-feedback"><?php echo __('Feedback: ', 'buddyboss-marketplace').round(100*$sum/$total_count, 2).__('%', 'buddyboss-marketplace'); ?></div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="table-cell seller-shop">

                          <div class="table">
                              <div class="table-cell seller-shop-desc">
                                  <div class="table">
                                      <div class="table-cell owner-avatar">
                                          <a href="<?php echo $shop_url; ?>"><?php if($store_icon) { echo $store_icon; } else { echo get_avatar( $vendor_id, 56 ); } ?></a>
                                      </div>
                                      <div class="table-cell owner-name">
                                          <span><?php _e('Shop', 'buddyboss-marketplace');?></span>
                                          <a href="<?php echo $shop_url; ?>"><?php echo $shop_name; ?></a>
                                      </div>
                                  </div>
                              </div>

                              <div class="table-cell seller-shop-products">

                                  <?php if ( $product_posts->have_posts() ) : ?>

                                      <?php while ( $product_posts->have_posts() && $count < 3 ) : $product_posts->the_post(); ?>

                                          <?php global $product;?>

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

                                  <?php for ( $i = $count; $i < 3; $i++ ) { ?>
                                      <div>
                                          <img src="<?php echo buddyboss_bm()->assets_url . '/images/135x150.png' ?>">
                                      </div>
                                  <?php } ?>

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
                            </div>
                        </div>
                    </article>

                <?php endwhile; ?>


            <?php do_action( 'bm_after_stores_loop' ); ?>

            <?php bp_member_hidden_fields(); ?>

            <div id="pag-bottom" class="pagination">

                <ul class="pagination-links" id="member-dir-pag-bottom">

                    <?php bp_members_pagination_links(); ?>

                </ul>

            </div>

        <?php else: ?>

            <div id="message" class="info">
                <p><?php _e( "Sorry, no sellers were found.", 'buddyboss-marketplace' ); ?></p>
            </div>

        <?php endif; ?>

        </div>

    </div>

<?php get_footer();
