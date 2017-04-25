<?php

/**
 * @package WordPress
 * @subpackage MarketPlace
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'BuddyBoss_BM_BP_Component' ) ):

	/**
	 *
	 * MarketPlace BuddyPress Component
	 * ***********************************
	 */
	class BuddyBoss_BM_BP_Component extends BP_Component {


		public $id = 'orders';

		/**
		 * INITIALIZE CLASS
		 *
		 * @since MarketPlace 1.0.0
		 */
		public function __construct() {
			parent::start(
				'bm', __( 'BM', 'buddyboss-marketplace' ), dirname( __FILE__ )
			);
		}

		/**
		 * SETUP ACTIONS
		 *
		 * @since  MarketPlace 1.0.0
		 */
		public function setup_actions() {
			// Add body class
			add_filter( 'body_class', array( $this, 'body_class' ) );

			// Front End Assets
			if ( ! is_admin() && ! is_network_admin() ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'assets' ), 11 );
			}

			// Back End Assets
			//add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );

			parent::setup_actions();
		}

		/**
		 * Add active BM class
		 *
		 * @since MarketPlace (0.1.1)
		 */
		public function body_class( $classes ) {
			$classes[] = apply_filters( 'buddyboss_bm_body_class', 'buddyboss-marketplace' );
			return $classes;
		}

		/**
		 * Load CSS/JS
		 * @return void
		 */
		public function assets() {
			global $wp_query;

			// FontAwesome icon fonts. If browsing on a secure connection, use HTTPS.
			//wp_register_style( 'buddyboss-bm-fontawesome', "//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css", false, null );
			//wp_enqueue_style( 'buddyboss-bm-fontawesome' );

			// BM stylesheet.

			$css_file = ( is_rtl() ) ? '/css/buddyboss-marketplace-rtl.min.css' : '/css/buddyboss-marketplace.min.css';
			wp_enqueue_style( 'buddyboss-bm-main-css', buddyboss_bm()->assets_url . $css_file, array(), BUDDYBOSS_BM_PLUGIN_VERSION, 'all' );

			// Scripts
			// wp_enqueue_script( 'bm-zoom-js', buddyboss_bm()->assets_url . '/js/vendors/jquery.elevateZoom.min.js', array( 'jquery' ), BUDDYBOSS_BM_PLUGIN_VERSION, true );

			// wp_enqueue_script( 'buddyboss-bm-main-js', buddyboss_bm()->assets_url . '/js/buddyboss-marketplace.js', array( 'jquery' ), BUDDYBOSS_BM_PLUGIN_VERSION, true );
			wp_enqueue_script( 'buddyboss-bm-main-js', buddyboss_bm()->assets_url . '/js/buddyboss-marketplace.min.js', array( 'jquery' ), BUDDYBOSS_BM_PLUGIN_VERSION, true );
			wp_localize_script( 'buddyboss-bm-main-js', 'bmVars', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'add_to_favorites' => __('Add to Favorites', 'buddyboss-marketplace' ),
				'added_to_favorites' => __('Remove from Favorites', 'buddyboss-marketplace' ),
				'currency_symbol' => get_woocommerce_currency_symbol(),
			) );

		}

	} //End of class BuddyBoss_BM_BP_Component

endif;
