<?php

/**
 * @package WordPress
 * @subpackage MarketPlace
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
    exit;

if ( ! class_exists( 'BuddyBoss_BM_Vendors' ) ):

    class BuddyBoss_BM_Vendors {

        /**
         * Array of all vendor
         * @var array
         */
        public $all_vendors = array();

        /**
         * Constructor
         */
        private function __construct() {
            $user_query = new WP_User_Query( array( 'role' => 'vendor', 'fields' => 'ID' ) );
            foreach ( $user_query->get_results() as $id )
            if($this->bm_vendor_has_products($id, array('product'))) {
                $this->all_vendors[] = $id;
            }
        }

        private function bm_vendor_has_products( $post_author, $post_type ) {
            global $wp_post_types; // If nonexistent post type found return
            if ( array_intersect((array)$post_type, array_keys($wp_post_types))
                != (array)$post_type ) return apply_filters( 'bm_vendor_has_products', false );

            static $posts = NULL; // Cache the query internally
            if ( !$posts ) {
                global $wpdb;

                $sql = "SELECT `post_type`, `post_author`, COUNT(*) AS `post_count`".
                    " FROM {$wpdb->posts}".
                    " WHERE `post_type` NOT IN ('revision', 'nav_menu_item')".
                    " AND `post_status`='publish'".
                    " GROUP BY `post_type`, `post_author`";

                $posts = $wpdb->get_results( $sql );
            }

            foreach( $posts as $post ) {
                if ( $post->post_author == $post_author
                    and in_array( $post->post_type, (array)$post_type )
                    and $post->post_count ) return apply_filters( 'bm_vendor_has_products', true ) ;
            }
            return apply_filters( 'bm_vendor_has_products', false );
        }

        /**
         * Vendors ids as string
         * @return string
         */
        public function bm_get_vendor_users_string() {
            return $theExcludeString = implode(",", $this->all_vendors);
        }

        /**
         * Object Instance.
         */
        public static function instance() {
            static $instance = null;

            if ( null === $instance ) {
                $instance = new BuddyBoss_BM_Vendors();
                $instance->setup_actions();
            }

            return $instance;
        }

        /**
         * Main actions
         */
        private function setup_actions() {
            // Shop Index Filter
			add_filter( 'bp_ajax_querystring', array( $this, 'bm_index_search_results' ), 20, 2 );
        }

        /**
         * Get Search Results
         * @param $request
         * @return array
         */
        public function bm_stores_search ($request, $subject) {

            $results = array();

            foreach ( $this->all_vendors as $vendor_id ) {
                if($subject == 'shops') {
                    $shop_name = WCV_Vendors::get_vendor_shop_name($vendor_id);
                    if (strpos(strtolower($shop_name), strtolower($request)) !== false) {
                        $results[] = $vendor_id;
                    }
                } else {
                    $vendor_name = bp_core_get_user_displayname($vendor_id);
                    if (strpos(strtolower($vendor_name), strtolower($request)) !== false) {
                        $results[] = $vendor_id;
                    }
                }
            }

            return $results;
        }

        /**
         * Filter after search
         * @param bool|false $qs
         * @param bool|false $object
         * @return bool
         */
        public function bm_index_search_results($qs=false, $object=false) {
            if ($object != 'members')  return $qs;

            if (isset ($_GET['bm_index_search']))
                $request = $_GET['bm_index_search'];

            if (isset ($_GET['subject'])) {
                $subject = $_GET['subject'];
            } else {
                $subject = 'shops';
            }

            if (empty ($request))  return $qs;

            $users = $this->bm_stores_search ($request, $subject);

                $args = wp_parse_args ($qs);

                if (isset ($args['include']))
                {
                    $included = explode (',', $args['include']);
                    $users = array_intersect ($users, $included);
                    if (count ($users) == 0)  $users = array (0);
                }

                $users = apply_filters ('bps_filter_members', $users);
                $args['include'] = implode (',', $users);
                $qs = build_query ($args);


            return $qs;
        }
    }

endif;