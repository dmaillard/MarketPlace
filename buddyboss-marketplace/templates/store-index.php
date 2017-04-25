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
        $order = isset($_GET['storeorder'])?$_GET['storeorder']:'active';

        $querystring = bp_ajax_querystring( 'members' );

        $defaults = array(
            'type'            => 'active',
            'action'          => 'active'
        );

        $ch_querystring = wp_parse_args( $querystring, $defaults );

        if($order != 'alphabetical') {

            $ch_querystring['type'] = $order;
            $ch_querystring['action'] = $order;
        } else {
            $ch_querystring['type'] = false;
            $ch_querystring['action'] = false;
        }

        $querystring = build_query($ch_querystring);

        // Search variable
        $searchby = isset( $_GET['bm_index_search'] )?$_GET['bm_index_search']:'';


        ?>

        <?php $members_query = bp_has_members( 'meta_key=pv_shop_name&include='.buddyboss_bm()->vendors_controller->bm_get_vendor_users_string(). '&' . $querystring ); ?>

        <div class="store-filters table">
            <form id="search-shops" role="search" action="" method="get" class="table-cell page-search">
                <input type="text" name="bm_index_search" placeholder="<?php _e('Search stores', 'buddyboss-marketplace'); ?>" value="<?php echo $searchby; ?>"/>
                <input type="submit" alt="Search" value="<?php _e('Search', 'buddyboss-marketplace'); ?>" />
            </form>

            <form id="filter-shops" action="<?php echo _get_page_link(); ?>" method="GET" class="table-cell filter-dropdown">

                <label><?php _e('Sort by:', 'buddyboss-marketplace'); ?></label>
                <select name="storeorder" id="order">

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
            <?php printf( __('Browsing all stores <span>(%1s of %2s)</span>', 'buddyboss-marketplace' ), $of , $total ); ?>
        </p>

        <?php if ( $members_query ) : ?>

            <?php do_action( 'bm_before_stores_loop' ); ?>


                <?php while ( bp_members() ) : bp_the_member(); ?>

                    <?php
                    $vendor_id 			= bp_get_member_user_id();
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
                    ?>
                    <article class="table store-item">
                        <div class="table-cell store-desc">
                            <a href="<?php echo $shop_url; ?>" class="about-store">
                                <?php echo $store_icon; ?>
                                <h3><?php echo $shop_name; ?></h3>
                                    <span class="icon-wrap">
                                        <span class="bb-side-icon"></span>
                                    </span>
                            </a>

                            <div class="about-owner">
                                <div class="table">
                                    <div class="table-cell owner-avatar">
                                        <a href="<?php bp_member_permalink(); ?>"><?php echo get_avatar( $vendor_id, 40 ); ?></a>
                                    </div>
                                    <div class="table-cell owner-name">
                                        <span><?php _e('Shop Owner', 'buddyboss-marketplace');?></span>
                                        <a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-cell store-products">
                            <?php // Products Loop
                            $product_args = array(
                                'post_type'       => 'product',
                                'author'          => $vendor_id,
                                'posts_per_page'  => -1,
                                'meta_query' => array(
                                    array(
                                     'key' => '_private_listing',
                                     'compare' => 'NOT EXISTS'
                                    ),
                                )
                            );

                            //Set number of page.
                           if ( isset( $product_paged ) ) $product_args['paged'] = $product_paged;

                            $product_posts = new WP_Query( $product_args );

                            $total_count = $product_posts->post_count;
                            $count = 0;
                            ?>

                            <?php if ( $product_posts->have_posts() ) : ?>

                                <?php while ( $product_posts->have_posts() && $count < 13 ) : $product_posts->the_post(); ?>

                                    <a href="<?php echo get_the_permalink($product->id) ?>">
                                        <?php
                                        global $product;

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

                            <?php for ( $i = $count; $i < 13; $i++ ) { ?>
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
                <p><?php _e( "Sorry, no stores were found.", 'buddyboss-marketplace' ); ?></p>
            </div>

        <?php endif; ?>

        </div>

    </div>

<?php get_footer();