<?php
/**
 * BuddyBoss MarketPlace - Favorite Shops
 *
 * @package WordPress
 * @subpackage BuddyBoss MarketPlace
 */

$favorites = get_user_meta(bp_displayed_user_id(), "favorite_shops", true);
if($favorites):
    $favorites = implode(",", $favorites);
    ?>

    <?php $members_query = bp_has_members( 'include='.$favorites .'&'.bp_ajax_querystring( 'members' ) ); ?>

    <?php if ( $members_query ) : ?>

        <?php do_action( 'bm_before_favorite_shops_loop' ); ?>

        <?php while ( bp_members() ) : bp_the_member(); ?>

            <?php
            $vendor_id 			= bp_get_member_user_id();
            $shop_name = WCV_Vendors::is_vendor( $vendor_id )
                ? WCV_Vendors::get_vendor_shop_name( $vendor_id )
                : get_bloginfo( 'name' );
            $store_icon_src 	= wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_icon_id', true ), array( 70, 70 ) );
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
                        'posts_per_page'  => -1
                    );

                    $product_posts = new WP_Query( $product_args );

                    $total_count = $product_posts->post_count;
                    $count = 0;
                    ?>

                    <?php if ( $product_posts->have_posts() ) : ?>

                        <?php while ( $product_posts->have_posts() && $count < 9 ) : $product_posts->the_post(); ?>

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

                    <?php for ( $i = $count; $i < 9; $i++ ) { ?>
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

        <?php do_action( 'bm_after_favorite_shops_loop' ); ?>

        <div id="pag-bottom" class="pagination">

            <ul class="pagination-links" id="member-dir-pag-bottom">

                <?php bp_members_pagination_links(); ?>

            </ul>

        </div>

    <?php else: ?>

        <div id="message" class="info">
            <p><?php _e( "Sorry, no favorite stores were found.", 'buddyboss-marketplace' ); ?></p>
        </div>

    <?php endif; ?>

<?php else: ?>

    <div id="message" class="info">
        <p><?php _e( "Sorry, no favorite stores were found.", 'buddyboss-marketplace' ); ?></p>
    </div>

<?php endif; ?>