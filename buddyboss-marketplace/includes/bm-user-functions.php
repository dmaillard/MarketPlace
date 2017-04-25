<?php

/**
 * @package WordPress
 * @subpackage BuddyBoss MarketPlace
 */
if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly


if ( ! function_exists( 'bm_user_products' ) ) {
    function bm_user_products() {
        add_action( 'bp_template_content', 'bm_template_products' );
        bp_core_load_template( apply_filters( 'bm_user_products', 'members/single/plugins' ) );
    }
}

if ( ! function_exists( 'bm_template_products' ) ) {
    function bm_template_products() {
        bm_load_template('bm-products-page');
    }
}

if ( ! function_exists( 'bm_user_favorites' ) ) {
    function bm_user_favorites() {
        add_action( 'bp_template_content', 'bm_template_favorites' );
        bp_core_load_template( apply_filters( 'bm_user_favorites', 'members/single/plugins' ) );
    }
}

if ( ! function_exists( 'bm_template_favorites' ) ) {
    function bm_template_favorites() {
        bm_load_template('bm-favorites-page');
    }
}

if ( ! function_exists( 'bm_user_shops' ) ) {
    function bm_user_shops() {
        add_action( 'bp_template_content', 'bm_template_shops' );
        bp_core_load_template( apply_filters( 'bm_user_shops', 'members/single/plugins' ) );
    }
}

if ( ! function_exists( 'bm_template_shops' ) ) {
    function bm_template_shops() {
        bm_load_template('bm-favorite-shops-page');
    }
}

/**
 * Screen function to display the purchase history
 *
 * Template can be changed via the <code> bm_template_member_history</code>
 * filter hook. Note that template files can also be copied to the current theme.
 *
 * @since 	1.0
 * @uses	bp_core_load_template()
 * @uses	apply_filters()
 */
if ( ! function_exists( 'bm_screen_history' ) ) {
    function bm_screen_history()
    {
        add_action('bp_template_content', 'bm_template_history');
        bp_core_load_template(apply_filters('bm_template_member_history', 'members/single/plugins'));
    }
}

if ( ! function_exists( 'bm_template_history' ) ) {
    function bm_template_history() {
        bm_load_template('shop/member/history');
    }
}

/**
 * Screen function for tracking an order
 *
 * Template can be changed via the <code> bm_template_member_track_order</code>
 * filter hook. Note that template files can also be copied to the current theme.
 *
 * @since 	1.0
 * @uses	bp_core_load_template()
 * @uses	apply_filters()
 */

if ( ! function_exists( 'bm_screen_track_order' ) ) {
    function bm_screen_track_order()
    {
        add_action('bp_template_content', 'bm_template_track_order');
        bp_core_load_template(apply_filters('bm_template_member_track_order', 'members/single/plugins'));
    }
}

if ( ! function_exists( 'bm_template_track_order' ) ) {
    function bm_template_track_order() {
        bm_load_template('shop/member/track');
    }
}

/**
 * Register BuddyBoss Menu Page
 */
if ( !function_exists( 'register_buddyboss_menu_page' ) ) {

    function register_buddyboss_menu_page() {
        // Set position with odd number to avoid confict with other plugin/theme.
        add_menu_page( 'BuddyBoss', 'BuddyBoss', 'manage_options', 'buddyboss-settings', '', buddyboss_bm()->assets_url . '/images/logo.svg', 61.000129 );

        // To remove empty parent menu item.
        add_submenu_page( 'buddyboss-settings', 'BuddyBoss', 'BuddyBoss', 'manage_options', 'buddyboss-settings' );
        remove_submenu_page( 'buddyboss-settings', 'buddyboss-settings' );
    }

    add_action( 'admin_menu', 'register_buddyboss_menu_page' );
}

if ( ! function_exists( 'bm_load_template' ) ) {
    function bm_load_template($template)
    {
        $template .= '.php';
        if (file_exists(STYLESHEETPATH . '/bb-marketplace/' . $template))
            include_once(STYLESHEETPATH . '/bb-marketplace/' . $template);
        else if (file_exists(TEMPLATEPATH . '/bb-marketplace/' . $template))
            include_once(TEMPLATEPATH . '/bb-marketplace/' . $template);
        else {
            $template_dir = apply_filters('bm_load_template', buddyboss_bm()->templates_dir);
            include_once trailingslashit($template_dir) . $template;
        }
    }
}

if ( ! function_exists( 'bm_check_template' ) ) {
    function bm_check_template($template)
    {
        if ( strpos( $template, '.php' ) == false) {
            $template .= '.php';
        }

        if (file_exists(STYLESHEETPATH . '/bb-marketplace/' . $template))
            $path = STYLESHEETPATH . '/bb-marketplace/' . $template;
        else if (file_exists(TEMPLATEPATH . '/bb-marketplace/' . $template))
            $path = TEMPLATEPATH . '/bb-marketplace/' . $template;
        else {
            $template_dir = apply_filters('bm_check_template', buddyboss_bm()->templates_dir);
            $path = trailingslashit($template_dir) . $template;
        }
        return $path;
    }
}

/**
 * Output the tracked order
 *
 * @since 	1.0.8
 */
function  bm_output_tracking_order() {
    global $current_order;

    if( $current_order instanceof WC_Order ) :
        do_action( 'woocommerce_track_order', $current_order->id );
        echo '<h3>'. __( 'Your Order', 'buddyboss-marketplace' ) .'<h3>';

        wc_get_template( 'order/tracking.php', array(
            'order' => $current_order
        ) );
    endif;
}
add_action( 'bm_after_track_body', 'bm_output_tracking_order' );

function bm_my_recent_orders_shortcode( $atts ){

    global $bp;

    if( !isset($bp->action_variables[1])) :

        return wc_get_template( 'myaccount/my-orders.php', array( 'order_count' =>  0 ));
    else:
        return do_action( 'woocommerce_view_order', $bp->action_variables[1] );

    endif;

}
add_shortcode( 'bm_my_recent_orders', 'bm_my_recent_orders_shortcode' );

/**
 * Product filter by shop location
 * @param $query
 */
function bm_shop_location_filter( $query ) {
    global $wpdb;

    if( isset( $_REQUEST['location_search'] )
        && 'product' === $query->query_vars['post_type'] ) {

        $author_in = array(0);
        $users = array();

        //Formatted address e.x Area, City, Admin area level 1
        // Admin area level 2, State, Country
        $formatted_address = array();

        if ( isset( $_REQUEST['formatted_address'] ) ) {
            //Make address reverse 3. Country, 2. State, 1. City
            $formatted_address = array_reverse( explode( ', ', $_REQUEST['formatted_address'] ) );
        }

        if ( ! empty( $_REQUEST['sref'] ) &&  'user_country' == $_REQUEST['sref'] ) {

           $user_query = "SELECT user_id FROM {$wpdb->usermeta} WHERE  meta_key = '_wcv_store_country'
                          AND ( meta_value = '". $_REQUEST['user_country_short'] ."'
                          OR  meta_value = '". $_REQUEST['user_country_long'] ."' )";

           $users = $wpdb->get_col( $user_query );

       } else if ( ! empty( $_REQUEST['country'][0] ) //Country Shorted name
                     && ! empty( $_REQUEST['country'][1] ) //Country long name
                ) {


            $user_query = "SELECT user_id FROM {$wpdb->usermeta} WHERE  meta_key = '_wcv_store_country' AND
                            ( meta_value = '". $_REQUEST['country'][0] ."'
                             OR  meta_value = '". $_REQUEST['country'][1] ."'";

            //Country conventional name
            if ( ! empty( $formatted_address[0] ) ) {
                $user_query .= " OR  meta_value = '". $formatted_address[0] ."'";
            }

            $user_query .= ")";



            $country_users = $users = $wpdb->get_col( $user_query );

            //0 users fallback
            if ( empty( $users ) ) {
                $country_users = $users = array( 0 );
            }

            if ( ! empty( $_REQUEST['state'][0] ) //State short name
                 && ! empty( $_REQUEST['state'][1] ) //State long name
                )
            {
                $user_query = "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = '_wcv_store_state' AND
                              ( LOWER( meta_value ) = LOWER( '". $_REQUEST['state'][0] ."')
                               OR LOWER( meta_value ) = LOWER( '". $_REQUEST['state'][1] ."')";

                //Include users from country
                if ( ! empty( $formatted_address[1] ) ) {
                    $user_query .= " OR LOWER( meta_value ) = LOWER( '". $formatted_address[1] ."')";
                }

                $user_query .= ")";

                $user_str = implode( ',', $users );
                $user_query .= " AND user_id IN ({$user_str}) ";

                $state_users = $users = $wpdb->get_col( $user_query );

            }

            //0 users fallback
            if ( empty( $users ) ) {
                $state_users = $users = array( 0 );
            }

            if ( ! empty( $_REQUEST['city'][0] ) //City short name
                 && ! empty( $_REQUEST['city'][1] ) //City long name
            )
            {
                $user_query = "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = '_wcv_store_city' AND
                              ( LOWER( meta_value ) = LOWER( '". $_REQUEST['city'][0] ."' )
                               OR LOWER( meta_value ) = LOWER( '". $_REQUEST['city'][1] ."' )";

                //City conventional name
                if ( ! empty( $formatted_address[2] ) ) {
                    $user_query .= " OR LOWER( meta_value ) = LOWER( '". $formatted_address[2] ."')";
                }

                $user_query .= ")";

                //merge users from country and state
                $users = array_merge( $country_users, $state_users );

                //Include users from state
                    $user_str = implode( ',', $users );
                    $user_query .= " AND user_id IN ({$user_str}) ";

                $users = $wpdb->get_col( $user_query );
            }

            //0 users fallback
            if ( empty( $users ) ) {
                $users = array( 0 );
            }

        }

        //All user ids found in location search
        if ( ! empty( $users ) ) {
            $author_in = $users;
        }

        $query->set('author__in', $author_in );

    }
} // $where_arr[] = "( meta_key = '_wcv_store_city' AND LOWER( meta_value ) = LOWER( '". $_REQUEST['city'] ."' ) )";

add_action( 'pre_get_posts', 'bm_shop_location_filter' );

/**
 * Add a new column, for favorite count, in the product list table.
 * @param array $columns
 * @return array
 */
function bm_wcv_product_table_columns( $columns ){
    if( !empty( $columns ) ){
        $new_columns = array();
        
        $new_column_number = 5;
        $i = 1;
        
        foreach( $columns as $k=>$l ){
            if( $i == $new_column_number ){
                $new_columns['favorites'] = '<i class="fa fa-heart"></i>';
            }
            
            $new_columns[$k] = $l;
            $i++;
        }
        
        $columns = $new_columns;
    }
    return $columns;
}
add_filter( 'wcv_product_table_columns', 'bm_wcv_product_table_columns' );

/**
 * Show the data for the newly added column above.
 * 
 * @param array $rows
 * @return array
 */
function bm_wcv_product_table_rows( $rows ){
    if( !empty( $rows ) ){
        $products_favorited_count = get_option('products_favorited_count');
        
        foreach( $rows as $row ){
            $row->favorites = isset( $products_favorited_count[$row->ID] ) ? $products_favorited_count[$row->ID] : 0;
        }
    }
    return $rows;
}
add_filter( 'wcv_product_table_rows', 'bm_wcv_product_table_rows' );