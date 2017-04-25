<?php

/**
 * @package WordPress
 * @subpackage MarketPlace
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
    exit;

if ( ! class_exists( 'BuddyBoss_BM_VC_Elements' ) ):

    class BuddyBoss_BM_VC_Elements {
        /**
         * Constructor
         */
        public function __construct() {
            add_action('init', array( $this, 'bm_requireVcExtend'), 11 );
//            $this->bm_requireVcExtend();
            add_shortcode('collections', array( $this, 'collections') );
            add_shortcode('newest_products', array( $this, 'newest_products') );
            add_shortcode('newsletters', array( $this, 'newsletters') );
            add_shortcode('featured_seller', array( $this, 'featured_seller') );
            add_shortcode('product_feed', array( $this, 'product_feed') );
            add_shortcode('featured_sellers', array( $this, 'featured_sellers') );
        }

        /**
         * Extend VC
         */
        public function bm_requireVcExtend(){

            /*** Collections ***/
            vc_map( array(
                "name" => "Collections",
                "base" => "collections",
                "category" => 'MarketPlace',
                "icon" => "icon-buddyboss",
                "allowed_container_element" => 'vc_row',
                "params" => array(
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "class" => "",
                        "heading" => __("Title", 'buddyboss-marketplace'),
                        "param_name" => "title",
                        "value" => "",
                        "description" => ""
                    ),
//                    array(
//                        "type" => "textfield",
//                        "holder" => "div",
//                        "class" => "",
//                        "heading" => __("Product Category IDs", 'buddyboss-marketplace'),
//                        "param_name" => "ids",
//                        "value" => "",
//                        "description" => __("Product Category IDs separated by \",\", max number of products to show is 5.", 'buddyboss-marketplace')
//                    ),
                    array(
                        'type' => 'autocomplete',
                        'heading' => __( 'Categories', 'buddyboss-marketplace' ),
                        'param_name' => 'ids',
                        'settings' => array(
                            'multiple' => true,
                            'sortable' => true,
                        ),
                        'save_always' => true,
                        'description' => __( 'List of product categories', 'buddyboss-marketplace' ),
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => __( 'Style', 'buddyboss-marketplace' ),
                        'param_name' => 'style',
                        'value' => array(
                            'Style 1' => 'style1',
                            'Style 2' => 'style2'
                        ),
                        'save_always' => true,
                    ),
                )
            ) );

            vc_map( array(
                'name' => __( 'Newest Products', 'buddyboss-marketplace' ),
                'base' => 'newest_products',
                'icon' => 'icon-buddyboss',
                'category' => __( 'MarketPlace', 'buddyboss-marketplace' ),
                'description' => __( 'Show multiple products by ID.', 'buddyboss-marketplace' ),
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'heading' => __( 'Title', 'buddyboss-marketplace' ),
                        'param_name' => 'title',
                        'save_always' => true,
                    ),
                    array(
                        "type" => "vc_link",
                        "holder" => "div",
                        "class" => "",
                        "heading" => "Button",
                        "param_name" => "button"
                    ),
//                    array(
//                        "type" => "textfield",
//                        "holder" => "div",
//                        "class" => "",
//                        "heading" => __("Product IDs", 'buddyboss-marketplace'),
//                        "param_name" => "ids",
//                        "value" => "",
//                        "description" => __("Product IDs separated by \",\"", 'buddyboss-marketplace')
//                    ),
                    array(
                        'type' => 'autocomplete',
                        'heading' => __( 'Products', 'buddyboss-marketplace' ),
                        'param_name' => 'ids',
                        'settings' => array(
                            'multiple' => true,
                            'sortable' => true,
                            'unique_values' => true,
                            // In UI show results except selected. NB! You should manually check values in backend
                        ),
                        'save_always' => true,
                        'description' => __( 'Enter List of Products', 'buddyboss-marketplace' ),
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => __( 'Style', 'buddyboss-marketplace' ),
                        'param_name' => 'style',
                        'value' => array(
                            'Style 1' => 'style1',
                            'Style 2' => 'style2'
                        ),
                        'save_always' => true,
                    ),
                    array(
                        'type' => 'hidden',
                        'param_name' => 'skus',
                    )
                )
            ) );

            vc_map( array(
                'name' => __( 'Subscription', 'buddyboss-marketplace' ),
                'base' => 'newsletters',
                'icon' => 'icon-buddyboss',
                'category' => __( 'MarketPlace', 'buddyboss-marketplace' ),
                'description' => __( 'Show subscription form.', 'buddyboss-marketplace' ),
                'params' => array(
                    array(
                        'type' => 'textarea',
                        'heading' => __( 'Description', 'buddyboss-marketplace' ),
                        'param_name' => 'description',
                        'save_always' => true,
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => __( 'Which WP plugin do you use:', 'buddyboss-marketplace' ),
                        'param_name' => 'which',
                        'value' => array(
                            'Newsletter' => 'newsletter',
                            'MailChimp'  => 'mailchimp'
                        ),
                        'save_always' => true,
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => __( 'MailChimp Form ID', 'buddyboss-marketplace' ),
                        'description' => __( 'If you are using MailChimp enter form ID here', 'buddyboss-marketplace' ),
                        'param_name' => 'shortcode',
                        'save_always' => true,
                    )
                )
            ) );

            vc_map( array(
                'name' => __( 'Featured Seller', 'buddyboss-marketplace' ),
                'base' => 'featured_seller',
                'icon' => 'icon-buddyboss',
                'category' => __( 'MarketPlace', 'buddyboss-marketplace' ),
                'description' => __( 'Show featured seller\'s products.', 'buddyboss-marketplace' ),
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'heading' => __( 'Title', 'buddyboss-marketplace' ),
                        'param_name' => 'title',
                        'save_always' => true,
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => __( 'User ID', 'buddyboss-marketplace' ),
                        'description' => __( 'If ID is not set featured seller will be chosen by popularity', 'buddyboss-marketplace' ),
                        'param_name' => 'id',
                        'save_always' => true,
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => __( 'Style', 'buddyboss-marketplace' ),
                        'param_name' => 'style',
                        'value' => array(
                            'Style 1' => 'style1',
                            'Style 2' => 'style2'
                        ),
                        'save_always' => true,
                    ),
                )
            ) );

            vc_map( array(
                'name' => __( 'Product Feed', 'buddyboss-marketplace' ),
                'base' => 'product_feed',
                'icon' => 'icon-buddyboss',
                'category' => __( 'MarketPlace', 'buddyboss-marketplace' ),
                'description' => __( 'Show products/shops with most feedback.', 'buddyboss-marketplace' ),
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'heading' => __( 'Title', 'buddyboss-marketplace' ),
                        'param_name' => 'title',
                        'save_always' => true,
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => __( 'Style', 'buddyboss-marketplace' ),
                        'param_name' => 'style',
                        'value' => array(
                            'Style 1' => 'style1',
                            'Style 2' => 'style2'
                        ),
                        'save_always' => true,
                    ),
                )
            ) );

            vc_map( array(
                'name' => __( 'Featured Sellers', 'buddyboss-marketplace' ),
                'base' => 'featured_sellers',
                'icon' => 'icon-buddyboss',
                'category' => __( 'MarketPlace', 'buddyboss-marketplace' ),
                'description' => __( 'Show featured sellers', 'buddyboss-marketplace' ),
                "allowed_container_element" => 'vc_row',
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'heading' => __( 'Title', 'buddyboss-marketplace' ),
                        'param_name' => 'title',
                        'save_always' => true,
                    ),
                    array(
                        "type" => "vc_link",
                        "holder" => "div",
                        "class" => "",
                        "heading" => "Button",
                        "param_name" => "button"
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => __( 'Style', 'buddyboss-marketplace' ),
                        'param_name' => 'style',
                        'value' => array(
                            'Style 1' => 'style1',
                            'Style 2' => 'style2'
                        ),
                        'save_always' => true,
                    ),
                )
            ) );
        }

        /**
         * VC shortcodes
         */
         public function featured_sellers($atts, $content = null) {
			global $wpdb;

            $args = array(
                "title"   => "",
                "style"   => "",
                "button"  => ""
            );

            extract(shortcode_atts($args, $atts));

            $button = vc_build_link( $button );

            $shops_favorited_count = get_option('shops_favorited_count');

            if(is_array($shops_favorited_count)):

                $highgest_stores = array(); //Hold 3 most favoured store in marketplace
                arsort( $shops_favorited_count );

                foreach( $shops_favorited_count as $user_id => $s_favorite_count ) {

                    //Check user in loop still has a Vendor role
                    if ( 'Vendor' == wcv_get_user_role( $user_id ) ) $highgest_stores[$user_id] = $s_favorite_count;

					//We need 3 stores
                    if ( sizeof( $highgest_stores) > 2 ) break;
                }

                ob_start();
                ?>
                <div class="bm-feat_seller-el <?php echo $style; ?>">
                    <div class="bm-vc-header">
                        <h3><?php echo $title; ?></h3>
                        <?php
                        $class = ( 'style1' == $style )?'button ':'';
                        if($button['url']) {
                            echo '<a href="'. $button['url'] .'" class="'.$class.'more" target="' . $button['target'] . '">' . trim($button['title']) .'<i class="fa fa-chevron-right"></i></a>';
                        }
                         ?>
                    </div>
                    <div class="bm-feed-boxes">
                    <?php
                    foreach($highgest_stores as $key => $value) {
                        $vendor_id 			= $key;
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
                        ?>
                        <div class="bm-feat_seller">
                            <?php
                            $product_args = array(
                                'post_type'       => 'product',
                                'author'          => $vendor_id,
                                'posts_per_page'  => -1,
                                'meta_query' => array(
                                    array(
                                     'key' => '_private_listing',
                                     'compare' => 'NOT EXISTS'
                                    ),
                                ),
                               'suppress_filters' => true
                            );

                            $product_posts = new WP_Query( $product_args );

                            $total_count = $product_posts->post_count;

                            $count = $sum = 0;

                            if(class_exists('WCVendors_Pro_Ratings_Controller')) {
                                if ($product_posts->have_posts()):

                                    while ($product_posts->have_posts()) :
                                        $product_posts->the_post();
                                        global $product;
                                        $ratings_average = WCVendors_Pro_Ratings_Controller::get_ratings_average($product->post->post_author);
                                        $percent = $ratings_average/5;
                                        $sum += $percent;
                                    endwhile;

                                endif;
                                wp_reset_postdata();
                            }
                            ?>
                            <div class="inner-wrap">
                                <div class="count"><i class="bb-icon-heart"></i><?php echo $value; ?></div>
                                <?php $user_link = bp_core_get_user_domain( $vendor_id ); ?>
                                <div class="avatar">
                                    <a href="<?php echo $user_link; ?>"><?php echo get_avatar( $vendor_id, 56 ); ?></a>
                                </div>
                                <div class="shop-details">
                                    <div class="name">
                                        <a href="<?php echo $user_link; ?>"><?php echo bp_core_get_user_displayname( $vendor_id ); ?></a>
                                    </div>
                                    <div class="details">
                                        <?php if($state != '') echo '<div>'.$state.'</div>'; ?>
                                        <?php
                                        if(class_exists('WCVendors_Pro_Vendor_Controller') && buddyboss_bm()->option('show-sold')) {
                                        $orders = WCVendors_Pro_Vendor_Controller::get_orders2( $vendor_id );
                                        ?>
                                        <div><?php echo __('Sold: ', 'buddyboss-marketplace').count($orders); ?></div>
                                        <?php } ?>
                                        <?php if(class_exists('WCVendors_Pro_Ratings_Controller') && $total_count > 0): ?>
                                        <div><?php echo __('Feedback: ', 'buddyboss-marketplace').round(100*$sum/$total_count, 2).__('%', 'buddyboss-marketplace'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <?php BuddyBoss_BM_Templates::bm_user_social_links($vendor_id); ?>

                                    <div class="table seller-shop-desc">
                                        <?php if($store_icon): ?>
                                        <div class="table-cell owner-avatar">
                                            <a href="<?php echo $shop_url; ?>"><?php echo $store_icon; ?></a>
                                        </div>
                                        <?php endif; ?>
                                        <div class="table-cell owner-name">
                                            <span><?php _e('Shop', 'buddyboss-marketplace');?></span>
                                            <a href="<?php echo $shop_url; ?>"><?php echo $shop_name; ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="seller-shop-products">

                                <?php if ( $product_posts->have_posts() ) :

									while ( $product_posts->have_posts() && $count < 3 ) : $product_posts->the_post();
                                    global $product;
								    $tr_product_id = apply_filters( 'translate_object_id', $product->id, 'product', true ); ?>

                                        <a href="<?php echo get_the_permalink($tr_product_id) ?>">
                                            <?php

                                            if ( has_post_thumbnail() ) {
                                                echo get_the_post_thumbnail( $tr_product_id, 'bm-store-archive' );
                                            } elseif ( wc_placeholder_img_src() ) {
                                                echo wc_placeholder_img( array(135, 150, 1) );
                                            }
                                            ?>
                                        </a>

                                        <?php $count++; ?>

                                    <?php endwhile; // end of the loop. ?>

                                <?php endif; ?>

                                <?php
                                woocommerce_reset_loop();
                                wp_reset_postdata();
                                ?>

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
                    <?php
                    }
                    ?>
                    </div>
                </div>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                return $html;
            endif;
         }

        public function product_feed($atts, $content = null) {
            global $wpdb;

            $args = array(
                "title"   => "",
                "style"   => ""
            );

            extract(shortcode_atts($args, $atts));

           /** store item data ************************************************************/
            $shops_favorited_count = get_option('shops_favorited_count');

            if( is_array( $shops_favorited_count ) ) {

              arsort( $shops_favorited_count );

				// Loop over all favorite users until we found at least 1 user with Vendor role.
				// Why do we need do it over simply select first from arsort array of $shops_favorited_count?
				// Because admin user can change user role later from Vendor to any other role so in that case
				// we need to check user is vendor to avoid an empty block in Product Feed
               foreach ( $shops_favorited_count as $user_id => $s_favorite_count ) {

                     //Check user in loop still has a Vendor role
                     if ( 'Vendor' == wcv_get_user_role( $user_id ) ) {
                        $shop_author = $user_id;
                        $shop_url = WCV_Vendors::get_vendor_shop_page($user_id);
                        $shop_favs_count = $shops_favorited_count[$user_id];
                        break;
                     }
                 }
            }

            /** products data ************************************************************/
            $products_favorited_count = get_option('products_favorited_count');

            if( is_array( $products_favorited_count ) ) {

                $highgest_products = array();//Hold 3 most favoured products in marketplace
                arsort( $products_favorited_count );

                foreach( $products_favorited_count as $product_id => $p_favorite_count ) {

                    //Check product in loop has not beed deleted from db
                    if ( $wpdb->get_var("SELECT id FROM {$wpdb->posts} WHERE id = {$product_id} AND post_status = 'publish'") ) {
                        $highgest_products[$product_id] = $p_favorite_count;
                    }

					//We need 3 product items
                    if ( sizeof( $highgest_products) > 2 ) break;
                }
            }

            ob_start();
            ?>

            <div class="bm-product-feed <?php echo $style; ?>">
                <div class="bm-vc-header">
                    <h3><?php echo $title; ?></h3>
                </div>

                <div class="bm-feed-boxes">
                    <?php
                    $buffer = '';
                    if(!empty($highgest_products)) {
                        $i = 0;
                        $_pf = new WC_Product_Factory();
                        foreach($highgest_products as $key => $value) {
                            $tr_product_id = apply_filters( 'translate_object_id', $key, 'product', true );

                            if($i == 1 ) ob_start();
                            $_product = $_pf->get_product($tr_product_id);
                    ?>
                    <div class="bm-feed-box product-item">
                        <div class="table">
                            <div class="table-cell">
                                <div class="count"><i class="bb-icon-heart"></i><?php echo $value; ?></div>
                            </div>
                            <div class="table-cell">
                                <div class="desc"><?php _e('One of our most popular products', 'buddyboss-marketplace'); ?></div>
                            </div>
                        </div>


                        <a href="<?php echo get_the_permalink($tr_product_id); ?>">
                            <?php
                            if(!$_product->is_in_stock()) {
                                echo '<span class="outofstock-label">' . __('Out of Stock', 'buddyboss-marketplace') . '</span>';
                            }
                            if ( has_post_thumbnail($tr_product_id) ) {
                                echo  get_the_post_thumbnail(  $tr_product_id, 'bm-product-archive' );
                            } elseif ( wc_placeholder_img_src() ) {
                                echo wc_placeholder_img( 'bm-product-archive' );
                            }
                           ?>

                        </a>
                        <h3><a href="<?php echo get_the_permalink($tr_product_id); ?>"><?php echo get_the_title($tr_product_id); ?></a></h3>

                        <?php $user_link = function_exists( 'bp_core_get_user_domain' ) ? bp_core_get_user_domain( $_product->post->post_author ) : '#'; ?>
                        <div class="author">
                            <a href="<?php echo $user_link; ?>"><?php echo get_avatar( $_product->post->post_author, 40 ); ?></a>
                            <a href="<?php echo $user_link; ?>"><?php echo bp_core_get_user_displayname( $_product->post->post_author ); ?></a>
                        </div>
                    </div>
                <?php
                        $i++;
                    }
                    $buffer = ob_get_contents();
                    ob_end_clean();

                }
                ?>
                    <?php if($shop_author): ?>
                    <div class="bm-feed-box store-item">
                        <div class="table">
                            <div class="table-cell">
                                <div class="count"><i class="bb-icon-heart"></i><?php echo $shop_favs_count; ?></div>
                            </div>
                            <div class="table-cell">
                                <div class="desc"><?php _e('One of our most popular shops', 'buddyboss-marketplace'); ?></div>
                            </div>
                        </div>
                        <a href="<?php echo $shop_url; ?>" class="store-products">
                        <?php
                            $query_args = array(
                                'post_type'           => 'product',
                                'author'              => $shop_author,
                                'post_status'         => 'publish',
                                'ignore_sticky_posts' => 1,
                                'orderby'             => 'title',
                                'order'               => 'asc',
                                'posts_per_page'      => -1
                            );

                            global $woocommerce_loop;

                            $products = new WP_Query( $query_args );

                            $total_count = $products->post_count;
                            $count = 0;

                             if ( $products->have_posts() ) : ?>

                                <?php while ( $products->have_posts() && $count < 3 ) : $products->the_post(); ?>
                                    <div>
                                        <?php
                                        global $product;

                                        if ( has_post_thumbnail() ) {
                                            echo get_the_post_thumbnail( $product->id, 'bm-store-archive' );
                                        } elseif ( wc_placeholder_img_src() ) {
                                            echo wc_placeholder_img( array(135, 150, 1) );
                                        }
                                        ?>
                                    </div>

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
                                <div class="overlay">
                                    <div class="table">
                                        <div class="table-cell">
                                            <span class="number"><?php echo $total_count; ?></span>
                                            <span class="text"><?php printf( _n( 'item', 'items', $total_count, 'buddyboss-marketplace' ), $total_count ); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                            woocommerce_reset_loop();
                            wp_reset_postdata();
                            ?>
                        </a>
                        <?php $user_link = function_exists( 'bp_core_get_user_domain' ) ? bp_core_get_user_domain( $shop_author ) : '#'; ?>
                        <div class="about-owner">
                            <div class="table">
                                <div class="table-cell owner-avatar">
                                    <a href="<?php echo $user_link; ?>"><?php echo get_avatar( $shop_author, 40 ); ?></a>
                                </div>
                                <div class="table-cell owner-name">
                                    <span><?php _e('Shop Owner', 'buddyboss-marketplace');?></span>
                                    <a href="<?php echo $user_link; ?>"><?php echo bp_core_get_user_displayname( $shop_author ); ?></a>
                                </div>
                            </div>
                        </div>
                        <div class="rating">
                        <?php if ( class_exists('WCVendors_Pro') && ! WCVendors_Pro::get_option( 'ratings_management_cap' ) ) echo WCVendors_Pro_Ratings_Controller::ratings_link( $shop_author, true ); ?>
                        </div>
                    </div>

                    <?php endif; ?>

                    <?php echo $buffer; ?>
                </div>
            </div>

            <?php
            $html = ob_get_contents();
            ob_end_clean();
            return $html;

        }

        /**
         * VC shortcodes
         */
        public function featured_seller($atts, $content = null) {

            $args = array(
                "title"   => "",
                "id" => "",
                "style"   => ""
            );

            extract(shortcode_atts($args, $atts));

            $shops_favorited_count = get_option('shops_favorited_count');

                $author = 0;
                if(is_array($shops_favorited_count) && !empty( $shops_favorited_count ) ) {
                    $author = array_keys($shops_favorited_count, max($shops_favorited_count));
                    $author = $author[0];
                }
                if( $id && is_int(abs($id))) {
                    $author = $id;
                }
                ob_start();
                ?>
                <div class="bm-favorited-shop-products <?php echo $style; ?>">
                    <div class="bm-vc-header">
                        <div>
                            <h3><?php echo $title; ?></h3>

                            <div>
                            <?php if($author){ ?>
                            <a class="user-link" href="<?php echo bp_core_get_user_domain( $author ); ?>">
                                <?php echo get_avatar( $author, 50 ); ?>
                                <?php echo bp_core_get_user_displayname($author); ?>
                            </a>
                            <?php
                            }
                            $sellers_index_page = buddyboss_bm()->option('sellers-index');
                            $class = ( 'style1' == $style )?'button ':'';
                            if(!$author) { $class = $class . 'no-wrap '; }
                            if(!empty( $sellers_index_page )) {
                                echo '<a href="'. get_the_permalink($sellers_index_page) .'" class="'.$class.'more" target="_blank">' . __('see all sellers', 'buddyboss-marketplace') .'<i class="fa fa-chevron-right"></i></a>';
                            }
                            ?>
                            </div>
                    </div>
                </div>
                <?php

                $atts = shortcode_atts( array(
                    'columns' => '4',
                    'orderby' => 'title',
                    'order'   => 'asc',
                    'ids'     => '',
                    'skus'    => ''
                ), $atts );

                $query_args = array(
                    'post_type'           => 'product',
                    'author'              => $author,
                    'post_status'         => 'publish',
                    'ignore_sticky_posts' => 1,
                    'orderby'             => $atts['orderby'],
                    'order'               => $atts['order'],
                    'posts_per_page'      => 4,
                    'suppress_filters'    => true,
                    'meta_query'          => WC()->query->get_meta_query()
                );

                if ( ! empty( $atts['skus'] ) ) {
                    $query_args['meta_query'][] = array(
                        'key'     => '_sku',
                        'value'   => array_map( 'trim', explode( ',', $atts['skus'] ) ),
                        'compare' => 'IN'
                    );
                }

                if ( ! empty( $atts['ids'] ) ) {
                    $query_args['post__in'] = array_map( 'trim', explode( ',', $atts['ids'] ) );
                }

                global $woocommerce_loop;

                $loop_name = 'products';
                $products                    = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $query_args, $atts, $loop_name ) );
                $columns                     = absint( $atts['columns'] );
                $woocommerce_loop['columns'] = $columns;

                ob_start();

                if ( $products->have_posts() ) : ?>

                    <?php do_action( "woocommerce_shortcode_before_{$loop_name}_loop" ); ?>

                    <?php woocommerce_product_loop_start(); ?>

                    <?php while ( $products->have_posts() ) : $products->the_post(); ?>

                        <?php
                         global $post;
                         $tr_product_id = apply_filters( 'translate_object_id', get_the_ID(), 'product', true );
                         $post = get_post( $tr_product_id );
                         setup_postdata( $post );

                        if( 'style2' == $style ) {
                            wc_get_template_part( 'content', 'product_different' );
                        } else {
                            wc_get_template_part( 'content', 'product' );
                        }
                        ?>

                    <?php endwhile; // end of the loop. ?>

                    <?php woocommerce_product_loop_end(); ?>

                    <?php do_action( "woocommerce_shortcode_after_{$loop_name}_loop" ); ?>

                <?php endif;

                woocommerce_reset_loop();
                wp_reset_postdata();

                echo  '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';

                //echo do_shortcode('[products author="'.$author[0].'"]');
            echo '</div>';

            $html = ob_get_contents();
            ob_end_clean();
            return $html;
        }


        public function newsletters($atts, $content = null) {
            $args = array(
                "description"   => "",
                "which"   => "",
                "shortcode"   => ""
            );

            extract(shortcode_atts($args, $atts));

            ob_start();

            global $newsletter;
            ?>
            <div class="bm-newsletters">
                <div class="desc"><?php echo $description; ?></div>

                <?php if($which == 'mailchimp') { ?>
                    <?php echo do_shortcode('[mc4wp_form id="'.$shortcode.'"]'); ?>
                <?php } elseif($newsletter) { ?>
                <script type="text/javascript">
                    //<![CDATA[
                    if (typeof newsletter_check !== "function") {
                        window.newsletter_check = function (f) {
                            var re = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-]{1,})+\.)+([a-zA-Z0-9]{2,})+$/;
                            if (!re.test(f.elements["ne"].value)) {
                                alert("The email is not correct");
                                return false;
                            }
                            for (var i=1; i<20; i++) {
                                if (f.elements["np" + i] && f.elements["np" + i].required && f.elements["np" + i].value == "") {
                                    alert("");
                                    return false;
                                }
                            }
                            if (f.elements["ny"] && !f.elements["ny"].checked) {
                                alert("You must accept the privacy statement");
                                return false;
                            }
                            return true;
                        }
                    }
                    //]]>
                </script>

                <div class="newsletter newsletter-subscription">
                    <form method="post" action="<?php echo get_bloginfo( "url" ); ?>/?na=s" onsubmit="return newsletter_check(this)">

                        <table border="0">

                            <!-- email -->
                            <tr>
                                <td><input class="newsletter-email" type="email" name="ne" size="30" required placeholder="<?php _e('Your email address', 'buddyboss-marketplace'); ?>"></td>
                                <td class="newsletter-td-submit">
                                    <input class="newsletter-submit" type="submit" value="<?php _e( 'Subscribe', 'buddyboss-marketplace' )  ?>"/>
                                </td>
                            </tr>

                        </table>
                    </form>
                </div>
                <?php } ?>
            </div>
            <?php
            $html = ob_get_contents();
            ob_end_clean();
            return $html;
        }

        /**
         * VC shortcodes
         */
        public function newest_products($atts, $content = null) {
            $args = array(
                "title"   => "",
                "button"  => "",
                "ids"     => "",
                "style"   => "",
            );

            extract(shortcode_atts($args, $atts));

            $button = vc_build_link( $button );

            $html = '';

            $html .= '<div class="bm-newest-products '.$style.'">';
                $html .= '<div class="bm-vc-header">';
                    $html .= '<div>';
                        $html .= '<h3>'.$title.'</h3>';
                        $class = ( 'style1' == $style )?'button ':'';
                        if($button['url']) {
                            $html .= '<a href="'. $button['url'] .'" class="'.$class.'more" target="' . $button['target'] . '">' . trim($button['title']) .'<i class="fa fa-chevron-right"></i></a>';
                        }
                    $html .= '</div>';
                $html .= '</div>';

                $atts = array(
                    'columns' => '4',
                    'orderby' => 'post__in',
                    'order'   => 'asc',
                    'ids'     => $ids,
                    'skus'    => ''
                );

                $query_args = array(
                    'post_type'           => 'product',
                    'post_status'         => 'publish',
                    'ignore_sticky_posts' => 1,
                    'orderby'             => $atts['orderby'],
                    'order'               => $atts['order'],
                    'posts_per_page'      => 4,
                    'meta_query'          => WC()->query->get_meta_query(),
                    'suppress_filters'    => true
                );

                if ( ! empty( $atts['skus'] ) ) {
                    $query_args['meta_query'][] = array(
                        'key'     => '_sku',
                        'value'   => array_map( 'trim', explode( ',', $atts['skus'] ) ),
                        'compare' => 'IN'
                    );
                }

                if ( ! empty( $atts['ids'] ) ) {
                    $query_args['post__in'] = array_map( 'trim', explode( ',', $atts['ids'] ) );
                }

                global $woocommerce_loop;

                $products                    = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $query_args, $atts, 'products' ) );
                $columns                     = absint( $atts['columns'] );
                $woocommerce_loop['columns'] = $columns;

                ob_start();

                if ( $products->have_posts() ) : ?>

                    <?php do_action( "woocommerce_shortcode_before_products_loop" ); ?>

                    <?php woocommerce_product_loop_start(); ?>

                        <?php while ( $products->have_posts() ) : $products->the_post(); ?>

                            <?php
                            global $post;
                            $tr_product_id = apply_filters( 'translate_object_id', get_the_ID(), 'product', true );
                            $post = get_post( $tr_product_id );
                            setup_postdata( $post );

                            if( 'style2' == $style ) {
                                wc_get_template_part( 'content', 'product_different' );
                            } else {
                                wc_get_template_part( 'content', 'product' );
                            }
                            ?>

                        <?php endwhile; // end of the loop. ?>

                    <?php woocommerce_product_loop_end(); ?>

                    <?php do_action( "woocommerce_shortcode_after_products_loop" ); ?>

                <?php endif;

                woocommerce_reset_loop();
                wp_reset_postdata();

                $html .= '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';

//                $html .= do_shortcode('[products ids="'.$ids.'" orderby="include"]');
            $html .= '</div>';

            return $html;
        }

        public function collections($atts, $content = null) {
            $args = array(
                "title"   => "",
                "style"   => "",
                "ids"     => ""
            );

            extract(shortcode_atts($args, $atts));

            global $_wp_additional_image_sizes, $sitepress;

            ob_start();

            $cat_ids = explode(',', $ids );

            $args = array(
                'taxonomy'     => 'product_cat',
            	'orderby'       => 'include',
                'order'         => 'ASC',
                'include'      => $cat_ids,
                'orderby'      => 'include',
                'show_count'   => 1,
                'hierarchical' => 1,
                'hide_empty'   => 0,
            );

            // remove WPML term filters
            if ( isset($sitepress) ) {
            remove_filter('get_terms_args', array($sitepress, 'get_terms_args_filter'));
            remove_filter('get_term', array($sitepress,'get_term_adjust_id'));
            remove_filter('terms_clauses', array($sitepress,'terms_clauses'));
            }


            $featured = get_categories( $args );

            // restore WPML term filters
            if( isset( $sitepress) ) {
            add_filter('get_terms_args', array($sitepress, 'get_terms_args_filter') );
            add_filter('get_term', array($sitepress,'get_term_adjust_id'));
            add_filter('terms_clauses', array($sitepress,'terms_clauses'));
            }

            $total = count($featured);

            $i = 0;
            if($total) {
                $image_sizes = array();
                if($total == 1) {
                    $image_sizes[0] = array('large', 'large');
                } else if ($total == 2) {
                    $image_sizes[0] = array('cat-half', 'cat-half');
                    $image_sizes[1] = array('cat-half', 'cat-half');
                } else if ($total == 3) {
                    $image_sizes[0] = array('cat-half', 'cat-half');
                    $image_sizes[1] = array('cat-fourth', 'cat-fourth');
                    $image_sizes[2] = array('cat-fourth', 'cat-fourth');
                } else if ($total == 4) {
                    $image_sizes[0] = array('cat-half', 'cat-half');
                    $image_sizes[1] = array('cat-eighth', 'cat-eighth');
                    $image_sizes[2] = array('cat-eighth', 'cat-eighth');
                    $image_sizes[3] = array('cat-fourth', 'cat-fourth');
                } else if ($total == 5) {
                    $image_sizes[0] = array('vc-cat-1', 'cat-first-one');
                    $image_sizes[1] = array('vc-cat-2', 'cat-half');
                    $image_sizes[2] = array('vc-cat-3', 'cat-eighth');
                    $image_sizes[3] = array('vc-cat-3', 'cat-eighth');
                    $image_sizes[4] = array('vc-cat-4', 'cat-fourth');
                }
                echo '<div class="bm-collections '.$style.'">';
                    echo '<h3>'. $title .'</h3>';
                    echo '<div class="featured-cats">';
                    foreach ($featured as $index => $cat) {

//                        if ($cat->category_parent == 0) {
                            $category_id = $cat_ids[$index];

                            $thumbnail_id = get_woocommerce_term_meta($cat->term_id, 'thumbnail_id', true);
                            $count = $cat->count;

                            ?>
                            <a href="<?php echo get_term_link($cat->slug, 'product_cat'); ?>" class="bm-f-category block-<?php echo $image_sizes[$i][0]; ?>">
                                <?php
                                if($thumbnail_id) {
                                    echo wp_get_attachment_image($thumbnail_id, $image_sizes[$i][1]);
                                } else {

                                     if ( isset($sitepress) ) {
                                        $cur_lang   = $sitepress->get_current_language();
                                        $lang       = $sitepress->get_default_language();
                                        $sitepress->switch_lang($lang);
                                     }

                                    $query_args = array(
                                        'post_type'           => 'product',
                                        'post_status'         => 'publish',
                                        'ignore_sticky_posts' => 1,
                                        'orderby'             => 'date',
                                        'order'               => 'desc',
                                        'posts_per_page'      => 1,
                                        'suppress_filters'    => true,
                                        'tax_query' => array(
                                            array(
                                                'taxonomy' => 'product_cat',
                                                'terms'    => $category_id,
                                                'field'    => 'term_id',
                                            )
                                        )
                                    );

                                    //@todo: Need to fix product thumbnails not showing in WPML different language
                                    $products = new WP_Query( $query_args );

                                    if(has_post_thumbnail($products->post->ID)){
                                        echo get_the_post_thumbnail($products->post->ID, $image_sizes[$i][1]);
                                    } else {
                                        echo '<img src="' . buddyboss_bm()->assets_url . '/images/'.$_wp_additional_image_sizes[$image_sizes[$i][1]]['width'].'x'.$_wp_additional_image_sizes[$image_sizes[$i][1]]['height'].'.png">';
                                    }

                                    if ( isset($sitepress) ) {
                                        $sitepress->switch_lang($cur_lang);
                                    }
                                }
                                ?>
                                <div class="f-cat-des table">
                                    <div class="table-cell">
                                        <?php if( 'style2' == $style ){ ?>
                                            <p><?php echo apply_filters('bm_collections_editor_text', __('editor\'s picked', 'buddyboss-marketplace')); ?></p>
                                        <?php } ?>
                                        <h5>
                                            <?php echo $cat->name; ?>
                                        </h5>
                                        <?php if( 'style2' != $style ){ ?>
                                        <?php printf(_n('%s Item', '%s Items', $count, 'buddyboss-marketplace'), $count); ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </a>
                            <?php
//                        }
                        $i++;
                        if($i == 5) $i = 0;
                    }
                    echo '</div>';
                echo '</div>';
            }

            $html = ob_get_contents();
            ob_end_clean();
            return $html;
        }

    }

endif;
