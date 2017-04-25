<?php

/**
 * @package WordPress
 * @subpackage MarketPlace
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'BuddyBoss_BM_Plugin' ) ):

	/**
	 *
	 * MarketPlace Main Plugin Controller
	 * *************************************
	 *
	 *
	 */
	class BuddyBoss_BM_Plugin {
		/* Includes
		 * ===================================================================
		 */

		/**
		 * Most WordPress/BuddyPress plugin have the includes in the function
		 * method that loads them, we like to keep them up here for easier
		 * access.
		 * @var array
		 */
		private $main_includes = array(
			'marketplace-class',
	        'templates-class',
			'vendors-class',
			'vc-elements',
            'class-BMProductQuestionsHelper',
            'bm-img-zoom',
		);

		/**
		 * Template functions instance
		 *
		 * @var object
		 */
		public $template_functions;

		/**
		 * Vendor controller instance
		 *
		 * @var object
		 */
		public $vendors_controller;

		/**
		 * Admin includes
		 * @var array
		 */
		private $admin_includes = array(
			//Uncomment this to load admin options
			'admin'
		);

		public static $store_slug = 'vendor_store';

		/* Plugin Options
		 * ===================================================================
		 */

		/**
		 * Default options for the plugin, the strings are
		 * run through localization functions during instantiation,
		 * and after the user saves options the first time they
		 * are loaded from the DB.
		 *
		 * @var array
		 */
		private $default_options = array(
			'enabled' => true,
		);

		/**
		 * This options array is setup during class instantiation, holds
		 * default and saved options for the plugin.
		 *
		 * @var array
		 */
		public $options = array();

		/**
		 * Whether the plugin is activated network wide.
		 *
		 * @var boolean
		 */
		public $network_activated = false;

		/**
		 * Is BuddyPress installed and activated?
		 * @var boolean
		 */
		public $bp_enabled = false;

		/* Version
		 * ===================================================================
		 */

		/**
		 * Plugin codebase version
		 * @var string
		 */
		public $version = '1.0.0';

		/**
		 * Plugin database version
		 * @var string
		 */
		public $db_version = '0.0.0';

		/* Paths
		 * ===================================================================
		 */
		public $file = '';
		public $basename = '';
		public $plugin_dir = '';
		public $plugin_url = '';
		// public $includes_dir        = '';
		// public $includes_url        = '';
		public $lang_dir = '';
		public $assets_dir = '';
		public $assets_url = '';

		/* Component State
		 * ===================================================================
		 */
		public $current_type = '';
		public $current_item = '';
		public $current_action = '';
		public $is_single_item = false;

		/* Magic
		 * ===================================================================
		 */

		/**
		 * MarketPlace uses many variables, most of which can be filtered to
		 * customize the way that it works. To prevent unauthorized access,
		 * these variables are stored in a private array that is magically
		 * updated using PHP 5.2+ methods. This is to prevent third party
		 * plugins from tampering with essential information indirectly, which
		 * would cause issues later.
		 *
		 * @see BuddyBoss_BM_Plugin::setup_globals()
		 * @var array
		 */
		private $data;

		/* Singleton
		 * ===================================================================
		 */

		/**
		 * Main MarketPlace Instance.
		 *
		 * MarketPlace is great
		 * Please load it only one time
		 * For this, we thank you
		 *
		 * Insures that only one instance of MarketPlace exists in memory at any
		 * one time. Also prevents needing to define globals all over the place.
		 *
		 * @since MarketPlace (1.0.0)
		 *
		 * @static object $instance
		 * @uses BuddyBoss_BM_Plugin::setup_globals() Setup the globals needed.
		 * @uses BuddyBoss_BM_Plugin::setup_actions() Setup the hooks and actions.
		 * @uses BuddyBoss_BM_Plugin::setup_textdomain() Setup the plugin's language file.
		 * @see buddyboss_bm()
		 *
		 * @return MarketPlace The one true BuddyBoss.
		 */
		public static function instance() {
			// Store the instance locally to avoid private static replication
			static $instance = null;

			// Only run these methods if they haven't been run previously
			if ( null === $instance ) {
				$instance = new BuddyBoss_BM_Plugin();
				$instance->setup_globals();
				$instance->setup_actions();
				$instance->setup_textdomain();
			}

			// Always return the instance
			return $instance;
		}

		/* Magic Methods
		 * ===================================================================
		 */

		/**
		 * A dummy constructor to prevent MarketPlace from being loaded more than once.
		 *
		 * @since MarketPlace (1.0.0)
		 * @see BuddyBoss_BM_Plugin::instance()
		 * @see buddypress()
		 */
		private function __construct() { /* nothing here */
		}

		/**
		 * A dummy magic method to prevent MarketPlace from being cloned.
		 *
		 * @since MarketPlace (1.0.0)
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'buddyboss-marketplace' ), '1.0.0' );
		}

		/**
		 * A dummy magic method to prevent MarketPlace from being unserialized.
		 *
		 * @since MarketPlace (1.0.0)
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'buddyboss-marketplace' ), '1.0.0' );
		}

		/**
		 * Magic method for checking the existence of a certain custom field.
		 *
		 * @since MarketPlace (1.0.0)
		 */
		public function __isset( $key ) {
			return isset( $this->data[ $key ] );
		}

		/**
		 * Magic method for getting MarketPlace varibles.
		 *
		 * @since MarketPlace (1.0.0)
		 */
		public function __get( $key ) {
			return isset( $this->data[ $key ] ) ? $this->data[ $key ] : null;
		}

		/**
		 * Magic method for setting MarketPlace varibles.
		 *
		 * @since MarketPlace (1.0.0)
		 */
		public function __set( $key, $value ) {
			$this->data[ $key ] = $value;
		}

		/**
		 * Magic method for unsetting MarketPlace variables.
		 *
		 * @since MarketPlace (1.0.0)
		 */
		public function __unset( $key ) {
			if ( isset( $this->data[ $key ] ) )
				unset( $this->data[ $key ] );
		}

		/**
		 * Magic method to prevent notices and errors from invalid method calls.
		 *
		 * @since MarketPlace (1.0.0)
		 */
		public function __call( $name = '', $args = array() ) {
			unset( $name, $args );
			return null;
		}

		/* Plugin Specific, Setup Globals, Actions, Includes
		 * ===================================================================
		 */

		/**
		 * Setup MarketPlace plugin global variables.
		 *
		 * @since MarketPlace (1.0.0)
		 * @access private
		 *
		 * @uses plugin_dir_path() To generate MarketPlace plugin path.
		 * @uses plugin_dir_url() To generate MarketPlace plugin url.
		 * @uses apply_filters() Calls various filters.
		 */
		private function setup_globals( $args = array() ) {
			$this->network_activated = $this->is_network_activated();

			global $BUDDYBOSS_BM;

			$saved_options = $this->network_activated ? get_site_option( 'buddyboss_bm_plugin_options' ) : get_option( 'buddyboss_bm_plugin_options' );
			$saved_options = maybe_unserialize( $saved_options );

			$this->options = wp_parse_args( $saved_options, $this->default_options );

			// Normalize legacy uppercase keys
			foreach ( $this->options as $key => $option ) {
				// Delete old entry
				unset( $this->options[ $key ] );

				// Override w/ lowercase key
				$this->options[ strtolower( $key ) ] = $option;
			}

			/** Versions ************************************************* */
			$this->version = BUDDYBOSS_BM_PLUGIN_VERSION;
			$this->db_version = BUDDYBOSS_BM_PLUGIN_DB_VERSION;

			/** Paths ***************************************************** */
			// MarketPlace root directory
			$this->file = BUDDYBOSS_BM_PLUGIN_FILE;
			$this->basename = plugin_basename( $this->file );
			$this->plugin_dir = BUDDYBOSS_BM_PLUGIN_DIR;
			$this->plugin_url = BUDDYBOSS_BM_PLUGIN_URL;

			// Languages
			$this->lang_dir = dirname( $this->basename ) . '/languages/';

			// Includes
			$this->includes_dir = $this->plugin_dir . 'includes';
			$this->includes_url = $this->plugin_url . 'includes';

			// Templates
			$this->templates_dir = $this->plugin_dir . 'templates';
			$this->templates_url = $this->plugin_url . 'templates';

			// Assets
			$this->assets_dir = $this->plugin_dir . 'assets';
			$this->assets_url = $this->plugin_url . 'assets';

			/** Image Size ***************************************************** */

			add_image_size( 'bm-shop_single', 547, 607, true );
			add_image_size( 'bm-store-icon', 50, 50, true );
			add_image_size( 'bm-store-archive', 135, 150, true );
			add_image_size( 'bm-product-archive', 297, 330, true );
			add_image_size( 'cat-first-one', 261, 300, true );
			add_image_size( 'cat-half', 406, 300, true );
			add_image_size( 'cat-eighth', 376, 270, true );
			add_image_size( 'cat-fourth', 406, 135, true );
		}

		/**
		 * @return string
		 */
		public static function get_path(){
			return plugin_dir_path( dirname( __FILE__ ) );
		}

		/**
		 * Check if the plugin is activated network wide(in multisite)
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @return boolean
		 */
		private function is_network_activated() {
			$network_activated = false;
			if ( is_multisite() ) {
				if ( ! function_exists( 'is_plugin_active_for_network' ) )
					require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

				if ( is_plugin_active_for_network( basename( constant( 'BUDDYBOSS_BM_PLUGIN_DIR' ) ) . '/buddyboss-marketplace.php' ) ) {
					$network_activated = true;
				}
			}
			return $network_activated;
		}

		/**
		 * Setup MarketPlace main actions
		 *
		 * @since  MarketPlace 1.0.0
		 */
		private function setup_actions() {
			// Admin
			add_action( 'init', array( $this, 'setup_admin_settings' ) );

			// Hook into BuddyPress init
			add_action( 'bp_init', array( $this, 'bp_loaded' ) );
//			add_action( 'bp_loaded', array( $this, 'load_component' ) );

			add_action( 'init', array( $this, 'load_init' ), 9 );


			add_action( 'wp_enqueue_scripts', array( $this, 'bm_change_to_rtl' ) );

			// Add body classes
			add_filter( 'body_class', array( $this, 'bm_body_class' ) );

			// Use templates from this plugin
			add_filter( 'wc_get_template', array( $this, 'bm_filter_template' ), 10, 5 );
			add_filter( 'wc_get_template_part', array( $this, 'bm_filter_template_part' ), 10, 3 );

			// Single Posts templates
//			add_filter('single_template', array( $this, 'bm_load_singles_templates'));
//			add_filter('archive_template', array( $this, 'bm_load_archive_templates'));
			add_filter('template_include', array( $this, 'bm_load_archive_templates'), 11);

			// Filter templates
			add_filter( 'template_include',	array( $this, 'bm_load_template' ) );

			// Store Index Search
//			add_filter('bp_before_core_get_users_parse_args', array( $this, 'bm_index_search_results' ) );

			// Shop Single Category Search
//			add_action( 'pre_get_posts', array( $this, 'bm_load_search_results' ) );

			// Shop Single Category Filter
			add_action( 'pre_get_posts', array( $this, 'bm_load_cat_results' ) );

			// Change WooCommerce pagination
			add_filter( 'woocommerce_pagination_args', array( $this, 'bm_woocommerce_pagination' ) );

			// Show header
			add_filter( 'onesocial_single_header', array( $this, 'bm_onesocial_single_header' ) );

			// Show footer
			add_filter( 'onesocial_show_footer', array( $this, 'bm_onesocial_show_footer' ) );

			// Show woo sidebar
			add_filter( 'onesocial_show_woo_sidebar', array( $this, 'bm_onesocial_show_woo_sidebar' ) );

			// Show page sidebar
			add_filter( 'onesocial_show_page_sidebar', array( $this, 'bm_onesocial_show_page_sidebar' ) );

			// Filter "OneSocial" font options
			add_filter( 'onesocial_font_options', array( $this, 'bm_font_options' ) );

			// Filter "OneSocial" footer options
			add_filter( 'onesocial_color_element_options', array( $this, 'bm_color_element_options' ) );

			// Filter "OneSocial" color schemes
			add_filter( 'onesocial_color_schemes', array( $this, 'bm_color_schemes' ) );

			// Remove breadcrumbs
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

			//Remove prettyPhoto lightbox
//			add_action( 'wp_enqueue_scripts', array( $this, 'bm_remove_woo_lightbox' ), 99 );

			//Remove prettyPhoto lightbox
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 30 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 20 );

			//remove_action( 'woocommerce_product_meta_start', $wcvendors_pro->wcvendors_pro_store_controller, 'product_sold_by', 8 );
			//remove_action( 'woocommerce_product_meta_start', $wcvendors_pro->wcvendors_pro_store_controller, 'product_ships_from', 9 );

			// Move Up-Sell
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
			add_action( 'bm_after_single_product_summary', 'woocommerce_upsell_display', 15 );


			add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'bm_loop_cart_button_text' ), 10, 2 );

//			remove_action( 'woocommerce_before_single_product', array( $wcvendors_pro->wcvendors_pro_store_controller, 'store_single_header') );

			// Store banner
			add_action('buddyboss_inside_wrapper', array( $this, 'bm_show_store_banner' ), 10000 );

			// Remove "Sold by"
			add_action('init', array( $this, 'bm_remove_sold_by' ) );

			// Store excerpt
			add_filter('excerpt_length', array( $this, 'bm_store_excerpt_length' ) );

			// Remove store header from single product
			add_action('init', array( $this, 'bm_remove_store_header' ) );

			// Change order at archive product page
			remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
			add_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 30 );

			// Modify archive products filter function
			remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
			add_action( 'woocommerce_before_shop_loop', array( $this, 'bm_woocommerce_catalog_ordering' ), 20 );
			add_action('pre_get_posts', array( $this,'bm_alter_shop_query'));

            // Css to fix the width of shop items, if number of products displayed per row is not 4( our theme default )
            add_action( 'woocommerce_before_shop_loop', array( $this, 'bm_shop_loop_counter_css' ) );

			// Load store index template
			add_filter( 'template_include', array( $this, 'bm_store_index_template' ) );

			// Load sellers index template
			add_filter( 'template_include', array( $this, 'bm_sellers_index_template' ) );

			// Use custom product placholder image
			add_action( 'init', array( $this, 'bm_custom_fix_thumbnail' ) );

			// Product to Favorites
//			add_action('bm_menu_product_actions', array( $this, 'bm_product_to_favorites' ));
			add_action('woocommerce_after_shop_loop_item', array( $this, 'bm_product_to_favorites' ), 9 );
			add_action('woocommerce_after_add_to_cart_form', array( $this, 'bm_product_to_favorites' ) );

			// Change BP Pagination
			add_action('bp_get_members_pagination_links', array( $this, 'bm_change_bp_pagination' ) );

			// Add term field
			add_action( 'product_cat_add_form_fields', array( $this, 'bm_product_cat_add_new_meta_field' ), 10, 2 );

			// Edit term page
			add_action( 'product_cat_edit_form_fields', array( $this, 'bm_product_cat_edit_meta_field' ), 10, 2 );

			// Save extra taxonomy fields callback function.
			add_action( 'created_term', array( $this, 'bm_product_cat_save_custom_meta' ), 10, 2 );
			add_action( 'edit_term', array( $this, 'bm_product_cat_save_custom_meta' ), 10, 2 );

			// Product to favorites
			add_action( 'wp_ajax_nopriv_product_to_favorites', array( $this, 'bm_add_product_to_favorites' ) );
			add_action( 'wp_ajax_product_to_favorites', array( $this, 'bm_add_product_to_favorites' ) );

			// Shop to favorites
			add_action( 'wp_ajax_nopriv_shop_to_favorites', array( $this, 'bm_add_shop_to_favorites' ) );
			add_action( 'wp_ajax_shop_to_favorites', array( $this, 'bm_add_shop_to_favorites' ) );

			// Profile shop info
			add_action( 'bp_after_member_header', array( $this, 'bm_user_shop_info' ) );

			// Remove woo title
			add_filter('woocommerce_show_page_title', array( $this, 'bm_remove_title' ) );

			// Add cart to header
			add_action('onesocial_notification_buttons', array( $this, 'bm_onesocial_notification_buttons' ) );

			// Add one more header
			add_action('buddyboss_after_header', array( $this, 'bm_onesocial_after_header' ) );

			// Add widet area
			add_action( 'widgets_init', array( $this, 'bm_widgets_init') );
			// Add menu location
			add_action( 'after_setup_theme', array( $this, 'bm_menu_init') );

			add_filter( 'wp_setup_nav_menu_item', array( $this, 'bm_add_custom_nav_fields' ) );

			// save menu custom fields
			add_action( 'wp_update_nav_menu_item', array( $this, 'bm_update_custom_nav_fields'), 10, 3 );

			// color scheme
			add_action('wp_head', array($this, 'bm_generate_option_css'), 100);

			// Product reviews
			add_filter( 'comments_template', array( $this, 'bm_comments_template_loader' ), 10 );

			// Ajax cart
			add_filter('add_to_cart_fragments', array( $this, 'header_add_to_cart_fragment') );

			// Html title
			add_filter ( 'document_title_parts', array( $this, 'bm_shop_html_title' ) );

			if(function_exists('vc_path_dir')) {
				require_once vc_path_dir('VENDORS_DIR', 'plugins/class-vc-vendor-woocommerce.php');

				$vendor = new Vc_Vendor_Woocommerce();

				//Filters For autocomplete param:
				//For suggestion: vc_autocomplete_[shortcode_name]_[param_name]_callback
				add_filter('vc_autocomplete_collections_ids_callback', array(
					$vendor,
					'productCategoryCategoryAutocompleteSuggester',
				), 10, 1); // Get suggestion(find). Must return an array
				add_filter('vc_autocomplete_collections_ids_render', array(
					$vendor,
					'productCategoryCategoryRenderByIdExact',
				), 10, 1); // Render exact category by id. Must return an array (label,value)

				//Filters For autocomplete param:
				//For suggestion: vc_autocomplete_[shortcode_name]_[param_name]_callback
				add_filter('vc_autocomplete_newest_products_ids_callback', array(
					$vendor,
					'productIdAutocompleteSuggester',
				), 10, 1); // Get suggestion(find). Must return an array
				add_filter('vc_autocomplete_newest_products_ids_render', array(
					$vendor,
					'productIdAutocompleteRender',
				), 10, 1); // Render exact product. Must return an array (label,value)
			}
			// Order by meta
			add_action( 'bp_pre_user_query', array( $this, 'bm_sort_members_by_shops'), 10, 1 );

			// Woocommerce Catalog Image size
			add_filter('single_product_small_thumbnail_size', array( $this, 'bm_catalog_image'));


			/**
			 *  BuddyPress Global Search will take care of the "Products" label, we don't need to do this
			 *  Don't uncomment it, otherwise "Product" label won't get translated
			 *  Search Page Label
			 */
			//add_filter( 'bboss_global_search_label_search_type', array( $this, 'bm_search_products_label') );

			// Search Page Classes
			add_filter('bboss_global_search_class_search_list', array($this, 'bm_search_products_list'), 10, 2);
			add_filter('bboss_global_search_class_search_wrap', array($this, 'bm_search_products_wrap'), 10, 2);

			// Save colors
			if(class_exists('NM_Color_Filters')) {
				add_action('wcv_save_product', array($this, 'bm_save_product'), 10, 1);
				add_filter('wc_prd_vendor_options', array($this, 'bm_load_settings'), 10, 1);
			}

            // Redirect after login, after "Ask a question" button click or Register as vendor checkbox
            add_filter('login_redirect', array($this, 'bm_login_redirect'), 10, 3 );
            add_filter('woocommerce_login_redirect', array($this, 'bm_wc_login_redirect'), 10, 2 );

            // Add wrapper around the dashboard
            add_action('wcv_pro_after_dashboard', array($this, 'bm_after_dashboard'));
            add_action('wcvendors_after_dashboard', array($this, 'bm_after_dashboard'));
            add_action('wcv_pro_before_dashboard', array($this, 'bm_before_dashboard'));
            add_action('wcvendors_before_dashboard', array($this, 'bm_before_dashboard'));


            add_action( 'before_delete_post', array($this, 'bm_remove_product_from_favorites'));
            add_action( 'delete_user', array($this, 'bm_remove_shop_from_favorites'), 10, 2 );

            // Register as vendor
            add_action( 'bp_account_details_fields', array($this, 'bm_registration_fields'));
            add_action( 'wp_ajax_nopriv_os_ajax_register',  array( $this, 'setup_register_as_vendor' ), 9 );
			add_action( 'set_user_role', array( $this, 'set_user_as_vendor' ), 10, 3 );
			add_action( 'bp_signup_validate', array( $this, 'validate_as_vendor_field' ) );
			add_action( 'bp_core_signup_user', array( $this, 'vendor_on_registration' ), 10, 5 );
			add_action( 'bp_complete_signup', array( $this, 'vendor_redirect_after_signup' ), 9 );


			add_action( 'woocommerce_before_shop_loop_item_title', array($this, 'bm_on_stock_label'));

			// Adjust WPML pages IDs
			add_filter( 'buddyboss_bm_option_sellers-index', array( $this, 'wpml_filter_page_id' ) );
			add_filter( 'buddyboss_bm_option_store-index', array( $this, 'wpml_filter_page_id' ) );

			//SEO
			add_action( 'wp_head', array( $this, 'social_sharing_meta_tags' ), 10, 1 );
		}

		public function bm_change_to_rtl($string){

            if(is_rtl()) {
                // add_filter( 'wcv_pro_ink_style', array($this, 'bm_change_ink_css_file') );

                if(wp_style_is('wcv-ink', 'registered')) {
                    wp_deregister_style('wcv-ink');
                    wp_enqueue_style( 'wcv-ink', buddyboss_bm()->assets_url . '/css/ink-rtl.min.css', array(), '3.1.0' , all );
                }
            }

        }

		public function bm_on_stock_label(){
			global $product;
			if(!$product->is_in_stock()) {
				echo '<span class="outofstock-label">' . __('Out of Stock', 'buddyboss-marketplace') . '</span>';
			}
		}

		public function bm_registration_fields(){
			if(buddyboss_bm()->option('show-as-vendor')) :
				/**
				 * Fires and displays Member Type registration validation errors.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_field_bm_vendor_errors' );
				?>
				<p class="vendor-wrap field_bm_vendor">
                    <label for="as_vendor">
			            <input type="checkbox" id="as_vendor" name="as_vendor" class="input" value="yes"/>
                        <strong>
				        <?php _e('Registering as a vendor.', 'buddyboss-marketplace'); ?>
				        </strong>
				    </label>
				</p>

				<?php if ( $term_page = WC_Vendors::$pv_options->get_option( 'terms_to_apply_page') ): ?>

					<!-- accept terms and conditions field -->
					<?php do_action( 'wcvendors_login_agree_to_terms_before' ); ?>

					<p class="forgetmenot agree-to-terms-container term-wrap" style="display:none;">
						<label for="agree_to_terms">
							<input class="input-checkbox" id="agree_to_terms" <?php checked( isset( $_POST[ 'agree_to_terms' ] ), true ); ?> type="checkbox" name="agree_to_terms" value="1"/>
							<?php apply_filters( 'wcvendors_vendor_registration_terms', printf(  __( 'I have read and accepted the <a target="top" href="%s">terms and conditions</a>', 'buddyboss-marketplace' ), get_permalink( $term_page ) ) ); ?>
						</label>
					</p>

					<script type="text/javascript">
						jQuery(function () {
							if (jQuery('#as_vendor').is(':checked')) {
								jQuery('.agree-to-terms-container').show();
							}

							jQuery('#as_vendor').on('click', function (e) {
								
								var $elmasVendorCheck = jQuery(this),
									$elmTermCheck	  = jQuery('.agree-to-terms-container');

								if ($elmasVendorCheck.is(':checked')) {
									$elmTermCheck.slideDown();
								} else {
									$elmTermCheck.slideUp();
								}
							});
						})
					</script>

					<?php do_action( 'wcvendors_login_agree_to_terms_after' ); ?>


				<?php endif; ?>

			<?php endif;
		}
        
        public function setup_register_as_vendor(){
            if( !class_exists( 'WC_Vendors' ) )
                return;
            
            if( !isset( $_POST['as_vendor'] ) || 'yes' != $_POST['as_vendor'] ) return;

			//Accept term and conditions check
			if ( $term_page = WC_Vendors::$pv_options->get_option( 'terms_to_apply_page') && ( !isset( $_POST['agree_to_terms'] ) || '1' != $_POST['agree_to_terms'] ) ) {

				$response = array(
					'success'   => false,
					'message'   => __( 'You must accept the terms and conditions to become a vendor.', 'buddyboss-marketplace' ),
					'js'        => 'jQuery(\"#agree_to_terms\").focus()',
				);

				die( json_encode( $response ) );
			}

			add_action( 'bp_core_signup_user', array( $this, 'process_register_as_vendor' ), 99 );
			$_POST['apply_for_vendor'] = '1';
        }
        
        public function process_register_as_vendor( $user_id ){
            if( ! $user_id || is_wp_error( $user_id ) )
                return;
            
            $response = array(
                'success'   => true,
                'message'   => '',
                'js'        => '',
            );
            
            $redirect_to = '';
			$manual = WC_Vendors::$pv_options->get_option( 'manual_vendor_registration' );
			$role   = apply_filters( 'wcvendors_pending_role', ( $manual ? 'pending_vendor' : 'vendor' ) );
            
            if ( ! class_exists( 'WCVendors_Pro' ) ) { 
                //set user role as pending and redirect to vendor dashboard

				$wp_user_object = new WP_User( $user_id );
				$wp_user_object->set_role( $role );
                
                do_action( 'wcvendors_application_submited', $user_id );
                
                $vendor_dashboard_page = WC_Vendors::$pv_options->get_option( 'vendor_dashboard_page' );
                $redirect_to = apply_filters( 'wcvendors_signup_redirect', get_permalink( $vendor_dashboard_page ) );
            } else {
                //dont need to change user roles, just redirect to pro dashboard
                $redirect_to = WCVendors_Pro_Dashboard::get_dashboard_page_url(); 
            }

			update_user_meta( $user_id, '_bm_role',  $role );
            wp_set_auth_cookie( $user_id );//necessary to log them in, so that they can proceed with vendor application form
            
            $response['js'] = "window.location.href = '$redirect_to'";
            
            die( json_encode( $response ) );
        }
		
		/**
		 * Validation of vendor field.
		 */
		public function validate_as_vendor_field() {
			global $bp;

			if( !class_exists( 'WC_Vendors' ) )
				return;

			if( !isset( $_POST['as_vendor'] ) || 'yes' != $_POST['as_vendor'] ) return;

			//Accept term and conditions check
			if ( $term_page = WC_Vendors::$pv_options->get_option( 'terms_to_apply_page') && ( !isset( $_POST['agree_to_terms'] ) || '1' != $_POST['agree_to_terms'] ) ) {
				$bp->signup->errors['field_bm_vendor'] = __( 'You must accept the terms and conditions to become a vendor.', 'buddyboss-marketplace' );
			}

		}

		/**
		 * Update vendor role on single site
		 *
		 * @param type $user_id
		 * @param type $user_login
		 * @param type $user_password
		 * @param type $user_email
		 * @param type $usermeta
		 */
		public function vendor_on_registration( $user_id, $user_login, $user_password, $user_email, $usermeta ) {

			if( !class_exists( 'WC_Vendors' ) )
				return;

			if( !isset( $_POST['as_vendor'] ) || 'yes' != $_POST['as_vendor'] ) return;

			$manual = WC_Vendors::$pv_options->get_option( 'manual_vendor_registration' );
			$role   = apply_filters( 'wcvendors_pending_role', ( $manual ? 'pending_vendor' : 'vendor' ) );

			if ( ! class_exists( 'WCVendors_Pro' ) ) {
				//set user role as pending and redirect to vendor dashboard

				$wp_user_object = new WP_User( $user_id );
				$wp_user_object->set_role( $role );

				do_action( 'wcvendors_application_submited', $user_id );
			}

			update_user_meta( $user_id, '_bm_role',  $role );
			wp_set_auth_cookie( $user_id ); //necessary to log them in, so that they can proceed with vendor application form
			wp_set_current_user( $user_id );
		}

		/**
		 * Redirect vendor to vendor registration form on signup
		 */
		function vendor_redirect_after_signup() {

			if ( is_user_logged_in() ) {

				if ( ! class_exists( 'WCVendors_Pro' ) ) {
					$vendor_dashboard_page = WC_Vendors::$pv_options->get_option( 'vendor_dashboard_page' );
					$redirect_to = apply_filters( 'wcvendors_signup_redirect', get_permalink( $vendor_dashboard_page ) );
				} else {
					//dont need to change user roles, just redirect to pro dashboard
					$redirect_to = WCVendors_Pro_Dashboard::get_dashboard_page_url();
				}

				bp_core_redirect( $redirect_to );
			}
		}

		/**
		 * Set user as vendor after BuddyPress email account activation
		 * @param $user_id
		 * @param $role
		 * @param $old_role
		 */
		public function set_user_as_vendor( $user_id, $role, $old_role ) {

			if ( bp_is_activation_page() &&
				 $role == 'subscriber' &&
				 in_array( $old_role[0], array( 'pending_vendor', 'vendor' ) ) ) {

					remove_action( 'set_user_role', array( $this, 'set_user_as_vendor' ), 10, 3 );
					$wp_user_object = get_userdata( $user_id );
					$wp_user_object->set_role( $old_role[0] );
			}
		}

        function bm_before_dashboard(){
            echo '<div class="wcvendors-dashboard-wrapper">';
        }

        function bm_after_dashboard(){
            echo '</div>';
        }

        /**
         * Redirect after login
         * @param  string $redirect_to
         * @param  object $user
         * @return string
         */
        function bm_login_redirect( $redirect_to, $request, $user ) {
            if(isset($_COOKIE['login_redirect']) && $_COOKIE['login_redirect'] != '') {
            	if($_COOKIE['login_redirect'] == 'vendor'){
			        if(function_exists("activate_wcvendors_pro")){
			        	$id = WCVendors_Pro::get_option( 'dashboard_page_id' );
			        	if($id) {
			            	$dashboard_page = get_permalink($id);
			            	$redirect = $dashboard_page;
			        	}
			        }
			        else {
			        	$id = WC_Vendors::$pv_options->get_option( 'vendor_dashboard_page' );
			        	if($id) {
			        		$vendor_dashboard_page = get_permalink($id);
			        		$redirect = $vendor_dashboard_page;
			        	}
			        }
	            } else {
	                $redirect_to = wp_nonce_url( bp_core_get_user_domain($user->ID) . bp_get_messages_slug() . $_COOKIE['login_redirect']);
	            }
                unset($_COOKIE['login_redirect']);
                setcookie('login_redirect', null, -1, '/');
            }
            return $redirect_to;
        }

        /**
         * Redirect after login
         * @param  string $redirect_to
         * @param  object $user
         * @return string
         */
        function bm_wc_login_redirect( $redirect_to, $user ) {
            if(isset($_COOKIE['login_redirect']) && $_COOKIE['login_redirect'] != '') {
            	if($_COOKIE['login_redirect'] == 'vendor'){
			        if(function_exists("activate_wcvendors_pro")){
			        	$id = WCVendors_Pro::get_option( 'dashboard_page_id' );
			        	if($id) {
			            	$dashboard_page = get_permalink($id);
			            	$redirect = $dashboard_page;
			        	}
			        }
			        else {
			        	$id = WC_Vendors::$pv_options->get_option( 'vendor_dashboard_page' );
			        	if($id) {
			        		$vendor_dashboard_page = get_permalink($id);
			        		$redirect = $vendor_dashboard_page;
			        	}
			        }
	            } else {
	                $redirect_to = wp_nonce_url( bp_core_get_user_domain($user->ID) . bp_get_messages_slug() . $_COOKIE['login_redirect']);
	            }
                unset($_COOKIE['login_redirect']);
                setcookie('login_redirect', null, -1, '/');
            }
            return $redirect_to;
        }

		/**
		 * Change WC Vendors settings option
		 * @param $options
		 * @return mixed
		 */
		public function bm_load_settings($options){
			$options[89] = array(
				'name'     => __( 'Basic', 'buddyboss-marketplace' ),
				'id'       => 'hide_product_basic',
				'options'  => array(
					'description' 		=> __( 'Description', 'buddyboss-marketplace' ),
					'short_description' => __( 'Short Description', 'buddyboss-marketplace' ),
					'categories'		=> __( 'Categories', 'buddyboss-marketplace' ),
					'colors'			=> __( 'Colors', 'buddyboss-marketplace' ),
					'tags'				=> __( 'Tags', 'buddyboss-marketplace' ),
					'attributes'		=> __( 'Attributes', 'buddyboss-marketplace' ),
				),
				'type'     => 'checkbox',
				'multiple' => true,
			);

			return $options;
		}

		/**
		 * Sava colors when you save product
		 * @param $product_id
		 */
		public function bm_save_product($product_id){
			// Categories
			if ( isset( $_POST["product_color"] ) && is_array( $_POST["product_color"] ) ) {
				$categories = array_map( 'intval', $_POST["product_color"] );
				$categories = array_unique( $categories );

				wp_set_post_terms( $product_id, $categories, 'product_color' );
			} else {
				// No categories selected so reset them
				wp_set_post_terms( $product_id, null, 'product_color' );
			}
		}

		/**
		 * @param $class
		 * @return string
		 */
		public function bm_search_products_wrap($class, $label)
		{
			if('cpt-product' == $label) {
				return 'woocommerce';
			}
			return $class;
		}

		/**
		 * @param $class
		 * @return string
		 */
		public function bm_search_products_list($class, $label)
		{
			if('cpt-product' == $label) {
				return 'products';
			}
			return $class;
		}

		/**
		 * Search Page Label
		 * @param $all_label
		 * @return mixed
		 */
		public function bm_search_products_label($all_label)
		{
			if('cpt-product' == $all_label) {
				$all_label = 'Products';
			}

			return $all_label;
		}

		/**
		 * Woocommerce Catalog Image size
		 * @return string
		 */
		public function bm_catalog_image()
		{
			return 'bm-product-archive';
		}

		/**
		 * Sort members by shop name - alphabetical
		 * @param $BP_User_Query
		 */
		public function bm_sort_members_by_shops( $BP_User_Query ) {

			$order = isset ( $_REQUEST['storeorder'] ) ? $_REQUEST['storeorder'] :'active' ;

			// Only run this if one of our custom options is selected
			if($order == 'alphabetical') {
				global $wpdb;

				$m_ids_sql = "
SELECT DISTINCT u.{$BP_User_Query->uid_name} as id
FROM {$wpdb->users} u
INNER JOIN {$wpdb->usermeta} um
	ON ( u.{$BP_User_Query->uid_name} = um.user_id ) WHERE um.meta_key = 'pv_shop_name'
	ORDER BY um.meta_value ";

				$m_ids = $wpdb->get_col( $m_ids_sql );

				// The first param in the FIELD() clause is the sort column id.
				$m_ids = array_merge( array( 'u.id' ), wp_parse_id_list( $m_ids ) );
				$str_m_ids_sql = implode( ',', $m_ids );

				// Adjust ORDER BY
				$BP_User_Query->uid_clauses['orderby'] = "ORDER BY FIELD( " . $str_m_ids_sql . ")";

				// Adjust ORDER
				$BP_User_Query->uid_clauses['order'] = 'ASC';
			}
		}

		/**
		 * Widget Style
		 *
		 * @param $page
		 */
		function widget_style( $page ) {
			if ( $page == 'widgets.php' ) {
				echo '<style>.category_lists ul { margin: 5px 0 5px 10px; }</style>';
			}
		}


		/**
		 * Html title
		 *
		 * @param $title
		 * @return mixed
		 */
		public function bm_shop_html_title($title){
			$vendor_shop = urldecode( get_query_var( 'vendor_shop' ) );
			if($vendor_shop) {
				$vendor_id = $this->get_vendor_id($vendor_shop);
				$shop_name = $vendor_id ? WCV_Vendors::get_vendor_sold_by( $vendor_id ) : get_bloginfo( 'name' );
				$title['title'] = $shop_name;
			}
			return $title;
		}

		/**
		 * Cart Ajax
		 *
		 * @param $fragments
		 * @return mixed
		 */
		function header_add_to_cart_fragment( $fragments ) {
			global $woocommerce;

			ob_start();

			$this->bm_onesocial_notification_buttons();

			$fragments['div.header-cart'] = ob_get_clean();

			return $fragments;

		}

		/**
		 * Product reviews
		 *
		 * @param $template
		 *
		 * @return string
		 */
		public function bm_comments_template_loader($template){
			if ( get_post_type() !== 'product' ) {
				return $template;
			}

			$check_dirs = array(
				trailingslashit( get_stylesheet_directory() ) . WC()->template_path(),
				trailingslashit( get_template_directory() ) . WC()->template_path(),
				trailingslashit( $this->templates_dir ) . 'woocommerce',
				trailingslashit( get_stylesheet_directory() ),
				trailingslashit( get_template_directory() ),
				trailingslashit( WC()->plugin_path() ) . 'templates/'
			);

			if ( WC_TEMPLATE_DEBUG_MODE ) {
				$check_dirs = array( array_pop( $check_dirs ) );
			}

			foreach ( $check_dirs as $dir ) {
				if ( file_exists( trailingslashit( $dir ) . 'single-product-reviews.php' ) ) {
					return trailingslashit( $dir ) . 'single-product-reviews.php';
				}
			}
		}

		/**
		 * Color scheme
		 */
		public function bm_generate_option_css(){

			global $onesocial;
			if($onesocial) {
				$accent_color = onesocial_get_option('accent_color');
				?>
				<style>
				.woocommerce div.product .woocommerce-tabs ul.tabs li.active,
				.wcv-navigation ul.menu.horizontal li a:hover,
				.wcv-navigation ul.menu.horizontal li.active a,
				.woocommerce-MyAccount-navigation .is-active a,
				.woocommerce-MyAccount-navigation li a:hover,
				.bm-f-category .f-cat-des,
				body:not(.buddypress).vendor-pro-dashboard #content article,
				body.bb-marketplace,
				body.bb-marketplace #main-wrap,
				.bb-marketplace #primary {
					background-color: <?php echo onesocial_get_option( 'boss_primary_color' ); ?>;
				}

				.woocommerce-MyAccount-navigation .is-active a,
				.woocommerce-MyAccount-navigation li a:hover,
				.woocommerce div.product .woocommerce-tabs ul.tabs li.active {
					border-bottom-color: <?php echo onesocial_get_option( 'boss_primary_color' ); ?>;
				}

                .vendor-dashboard .site-content form .date-pick + input[type="submit"],
				.bp-user.orders.history .button:hover,
				.about-store .bb-side-icon:before,
				.about-store .bb-side-icon:after,
				.about-store .bb-side-icon,
				.store-filters .page-search input[type="submit"],
				.post-type-archive-product .entry-post-thumbnail,
				.woocommerce #review_form #respond #commentform p input[type="submit"]:hover,
				.woocommerce-cart table.cart td.actions input[type="submit"]:hover,
				.widget_mc4wp_form_widget form p button,
				.widget.widget_newsletterwidget form p input[type="submit"],
				.select2-results .select2-highlighted,
				.not-vendor .form-row input[type="submit"]:hover,
				.wcv-form .wcv-button[type=submit]:hover,
				.wcv-search span:after,
				.wcv-modal input[type=submit]:hover,
				.wcv_dashboard_table_header a.button:hover,
				.file-upload-wrap .remove-image:before,
				.wcv-grid #product_images_container ul ul.actions li a,
				.loop-product-image .product-buttons a.added_to_cart:hover, .loop-product-image .product-buttons a.button:hover,
				.woocommerce ul.products li.type-product .product-item-buttons a:hover,
				.bm-product-to-favorites:hover,
				.woocommerce span.onsale,
				.woocommerce #respond input#submit.alt,
				.woocommerce a.button.alt,
				.woocommerce button.button.alt,
				.woocommerce input.button.alt,
				.woocommerce .widget_price_filter .ui-slider .ui-slider-range,
				.woocommerce .widget_price_filter .price_slider_amount .button:hover,
				.bb-marketplace.page-template-homepage #main-wrap .button:not(.more):not(.product_type_simple):not(.product_type_auction),
				.bm-newsletters .table button,
				.bm-newsletters input[type="submit"],
				.bm-feed-boxes .count {
					background-color: <?php echo $accent_color; ?>;
				}

				.onesocial-slider .flex-control-paging li a:hover,
				.onesocial-slider .flex-control-paging li a.flex-active,
				.wcv-calendar-month li a.wcv-calendar-on, .wcv-calendar-month li a.wcv-calendar-on:hover, .wcv-calendar-month-selector li a.wcv-calendar-on, .wcv-calendar-month-selector li a.wcv-calendar-on:hover, .wcv-calendar-year-selector li a.wcv-calendar-on, .wcv-calendar-year-selector li a.wcv-calendar-on:hover {
					background: <?php echo $accent_color; ?>;
				}

				.menu.nav > li > .sub-menu > li i.open:before,
				.product-categories > li > .children li i.open:before,
				.wcv-grid .tip a:hover,
				.wcv-grid #chartjs-tooltip b,
				#mobile-item-nav ul li:not(.current):before,
				#main .btn-group.social a:hover,
				.store-item .about-store h3:hover,
				.bm-feed-box h3 a:hover,
				.style2 .bm-f-category .f-cat-des .table-cell p,
				.seller-desc .follow a.bm-add-to-favs.loading i:before,
				.pagination .pagination-links li a:focus, .pagination .pagination-links li a:hover,
				.pagination .pagination-links li span.current,
				body.bb-marketplace .woocommerce nav.woocommerce-pagination ul li a:focus,
				body.bb-marketplace .woocommerce nav.woocommerce-pagination ul li a:hover,
				body.bb-marketplace .woocommerce nav.woocommerce-pagination ul li span.current,
				body.bb-marketplace.woocommerce nav.woocommerce-pagination ul li a:focus,
				body.bb-marketplace.woocommerce nav.woocommerce-pagination ul li a:hover,
				body.bb-marketplace.woocommerce nav.woocommerce-pagination ul li span.current,
				.woocommerce .widget_layered_nav ul li.chosen a:after,
				.bm-shop #secondary .widget.widget_layered_nav li a:hover + span,
				.bm-shop #secondary .widget.widget_layered_nav li a:hover,
				.wcvendors-table-shop_coupon.wcv-table .row-actions a,
				.show-categories .cat-list ul li.current span,
				.show-categories .cat-list ul li.current a,
				.show-categories .cat-list ul li:hover span,
				.show-categories .cat-list ul li:hover a,
				.is-desktop nav.subheader .menu > li > .sub-menu,
				.store-desc .bm-add-to-favs.favorited,
				.store-desc .bm-add-to-favs:hover,
				.woocommerce #reviews #comments ol.commentlist li .comment-text p.meta strong,
				.woocommerce .star-rating span:before,
				#reviews h2 span,
				.woocommerce-thankyou-order-received,
				.header-notifications a.header-button span > b,
				nav.subheader .nav.menu > li > a:hover,
				nav.subheader .menu > li > b:hover,
				.subheader .sub-menu .children .current-cat > a,
				.subheader .nav .sub-menu .sub-menu a:hover,
				.subheader .nav .sub-menu a:hover,
				.subheader .sub-menu .children a:hover,
				.shop-settings .site-content center a.button:hover,
				.vendor-dashboard .site-content center a.button:hover,
				.wcv-navigation ul.menu.horizontal li a:hover,
				.wcv-navigation ul.menu.horizontal li.active a,
				.wcv-tabs.top > .tabs-nav li.active a,
				.wcv-tabs.top > .tabs-nav li a:hover,
				.wcv-form .control-group .inline input[type=checkbox]:checked + label,
				.wcv_shipping_rates input[type=checkbox]:checked+label,
				.woocommerce ul.products li.type-product .product-item-buttons a.bm-product-to-favorites.favorited i,
				.woocommerce #respond input#submit.loading:after,
				.woocommerce a.button.loading:after,
				.woocommerce button.button.loading:after,
				.woocommerce input.button.loading:after,
				.woocommerce #respond input#submit.added:after,
				.woocommerce a.button.added:after,
				.woocommerce button.button.added:after,
				.woocommerce input.button.added:after,
				.bm-product-to-favorites.favorited i,
				.style1 .bm-f-category:hover h5,
				.bm-shop .widget-area .widget li.current-cat a,
				.bm-shop .widget-area .widget li a:hover,
				.seller-shop-products .product-count .number,
				.store-products .product-count .number,
				.bm-more-shops:hover,
				.seller-desc .follow a.bm-add-to-favs:hover i,
				.seller-desc .follow a.bm-add-to-favs.favorited i,
				.wcv-grid table .row-actions-order a:hover {
					color: <?php echo $accent_color; ?>;
				}

				.pagination .pagination-links li:focus + li a,
				.pagination .pagination-links li:hover + li a,
				.pagination .pagination-links li.current + li a,
				body.bb-marketplace .woocommerce nav.woocommerce-pagination ul li:focus + li a,
				body.bb-marketplace .woocommerce nav.woocommerce-pagination ul li:hover + li a,
				body.bb-marketplace .woocommerce nav.woocommerce-pagination ul li.current + li a,
				body.bb-marketplace.woocommerce nav.woocommerce-pagination ul li:focus + li a,
				body.bb-marketplace.woocommerce nav.woocommerce-pagination ul li:hover + li a,
				body.bb-marketplace.woocommerce nav.woocommerce-pagination ul li.current + li a {
					border-left-color: <?php echo $accent_color; ?>;
				}

				html[dir="rtl"] .pagination .pagination-links li:focus + li a,
				html[dir="rtl"] .pagination .pagination-links li:hover + li a,
				html[dir="rtl"] .pagination .pagination-links li.current + li a,
				html[dir="rtl"] body.bb-marketplace .woocommerce nav.woocommerce-pagination ul li:focus + li a,
				html[dir="rtl"] body.bb-marketplace .woocommerce nav.woocommerce-pagination ul li:hover + li a,
				html[dir="rtl"] body.bb-marketplace .woocommerce nav.woocommerce-pagination ul li.current + li a,
				html[dir="rtl"] body.bb-marketplace.woocommerce nav.woocommerce-pagination ul li:focus + li a,
				html[dir="rtl"] body.bb-marketplace.woocommerce nav.woocommerce-pagination ul li:hover + li a,
				html[dir="rtl"] body.bb-marketplace.woocommerce nav.woocommerce-pagination ul li.current + li a {
					border-right-color: <?php echo $accent_color; ?>;
					border-left-color: #e1e1e1;
				}

				.is-desktop nav.subheader .menu > li > .sub-menu,
				nav.subheader .menu > li > .sub-menu {
					border-top-color: <?php echo $accent_color; ?>;
				}

				.pagination .pagination-links li a:focus, .pagination .pagination-links li a:hover,
				.pagination .pagination-links li span.current, body.bb-marketplace
				.woocommerce nav.woocommerce-pagination ul li a:focus,
				body.bb-marketplace .woocommerce nav.woocommerce-pagination ul li a:focus,
				body.bb-marketplace .woocommerce nav.woocommerce-pagination ul li a:hover,
				body.bb-marketplace .woocommerce nav.woocommerce-pagination ul li span.current,
				body.bb-marketplace.woocommerce nav.woocommerce-pagination ul li a:focus,
				body.bb-marketplace.woocommerce nav.woocommerce-pagination ul li a:hover,
				body.bb-marketplace.woocommerce nav.woocommerce-pagination ul li span.current,
				.wcv-grid table .row-actions-order a:hover {
					border-color: <?php echo $accent_color; ?>;
				}

				.wcv-grid table .row-actions-order a:hover + a {
					border-left-color: <?php echo $accent_color; ?>;
				}

				.header-notifications a#user-messages span:before,
				.header-notifications a.notification-link span:before {
					color: <?php echo onesocial_get_option( 'body_text_color' ); ?> !important;
				}

				.bm-feed-box h3 a,
				.menu-latest-product div.product .price span,
				.is-mobile #sub-trigger,
				.woocommerce .order_details li strong,
				nav.subheader .menu > li > a,
				.menu-latest-product h3,
				nav.subheader .sub-menu .bm-menu-header a,
				nav.subheader .sub-menu .bm-menu-header a:hover,
				.shop-settings .site-content center a.button,
				.vendor-dashboard .site-content center a.button,
				.wcv-navigation ul.menu.horizontal li a,
				.wcv-grid a:hover,
				.wcv-tabs.top > .tabs-nav li a
				.file-upload-wrap .add-image,
				.file-upload-wrap .add-image:before,
				.store-summary .store-desc a.store-name,
				.store-desc .bm-add-to-favs,
				.single-product .upsells.products .store-name,
				.woocommerce ul.products li.type-product .product-item-buttons a i,
				.woocommerce ul.products li.type-product .product-item-buttons a,
				.woocommerce ul.products li.type-product .product-item-buttons a.bm-product-to-favorites:before,
				.woocommerce ul.products li.type-product .product-item-buttons a.added_to_cart:before,
				.woocommerce ul.products li.type-product .product-item-buttons .button.add_to_cart_button:before,
				.single-product #send-private-message a,
				.woocommerce ul.products li.type-product .price,
				.woocommerce.single-product div.product p.price,
				.show-categories .cat-list ul a,
				.show-owner-widget .shop-rating,
				.wcv-store-address-container .store-address address,
				.wcv-store-address-container .store-phone a,
				.show-owner-widget #send-private-message a,
				.bm-shop .widget-area .widget li a,
				.pagination .pagination-links li a,
				.pagination .pagination-links li span,
				body.bb-marketplace .woocommerce nav.woocommerce-pagination ul li a,
				body.bb-marketplace .woocommerce nav.woocommerce-pagination ul li span,
				body.bb-marketplace.woocommerce nav.woocommerce-pagination ul li a,
				body.bb-marketplace.woocommerce nav.woocommerce-pagination ul li span,
				.bm-more-shops,
				.bm-profile-shop-details i,
				.seller-desc .name > a,
				.bm-feat_seller .shop-details .name a {
					color: <?php echo onesocial_get_option( 'body_text_color' ); ?>;
				}

                .vendor-dashboard .site-content form .date-pick + input[type="submit"]:hover,
				.bm-newsletters .table button:hover,
				.bm-newsletters input[type="submit"]:hover,
				.store-filters .page-search input[type="submit"]:hover,
				.woocommerce #review_form #respond #commentform p input[type="submit"],
				.order-again a.button,
				.shop-settings input[type="submit"],
				.not-vendor .form-row input[type="submit"],
				.wcv-form .wcv-button[type=submit],
				.wcv-form button.wcv-button,
				.wcv-form p.tip,
				.wcv-modal input[type=submit],
				.wcv_dashboard_table_header a.button,
				.file-upload-wrap .wcv-file-uploader.full:before,
				.summary .bm-product-to-favorites,
				.woocommerce #respond input#submit.alt:hover,
				.woocommerce a.button.alt:hover,
				.woocommerce button.button.alt:hover,
				.woocommerce input.button.alt:hover,
				.woocommerce .widget_price_filter .price_slider_amount .button {
					background-color: <?php echo onesocial_get_option( 'body_text_color' ); ?>;
				}

				.table.table-vendor-sales-report th {
					border-bottom: <?php echo onesocial_get_option( 'body_text_color' ); ?>;
				}

				.wcv_shipping_rates input[type=checkbox]:checked+label:before,
				.wcv-form .control-group .inline input[type=checkbox]:checked + label:before {
					-webkit-box-shadow: 0px 0px 0px 1px <?php echo $accent_color; ?>;
					-moz-box-shadow: 0px 0px 0px 1px <?php echo $accent_color; ?>;
					box-shadow: 0px 0px 0px 1px <?php echo $accent_color; ?>;
				}

				<?php
				$menu_bg = onesocial_get_option('marketplace_menu_background');
				$menu_text = onesocial_get_option('marketplace_menu_text_color');
				$menu_text_hover = onesocial_get_option('marketplace_menu_text_hover');
				?>

				<?php if( !empty($menu_bg) ) { ?>
					#main-wrap .subheader { background-color: <?php echo $menu_bg; ?>; }
				<?php } ?>

				<?php if( !empty($menu_text) ) { ?>
					#main-wrap .subheader .cat:not(.hovered) a { color: <?php echo $menu_text; ?>; }
				<?php } ?>

				<?php if( !empty($menu_text_hover) ) { ?>
					#main-wrap .subheader a:hover { color: <?php echo $menu_text_hover; ?>; }
				<?php } ?>

				</style><?php
			}
		}

		/**
		 * Save menu custom fields
		 *
		 * @access      public
		 * @since       1.0
		 * @return      void
		 */
		public function bm_update_custom_nav_fields( $menu_id, $menu_item_db_id, $args ) {
			// Check if element is properly sent
			$header_value = false;
			if ( is_array( $_REQUEST['menu-item-header']) ) {
				$header_value = $_REQUEST['menu-item-header'][$menu_item_db_id];
			}
			update_post_meta( $menu_item_db_id, '_menu_item_header', $header_value );

			$footer_value = false;
			if ( is_array( $_REQUEST['menu-item-footer']) ) {
				$footer_value = $_REQUEST['menu-item-footer'][$menu_item_db_id];
			}
			update_post_meta( $menu_item_db_id, '_menu_item_footer', $footer_value );
		}

		/**
		 * Add custom fields to $item nav object
		 * in order to be used in custom Walker
		 *
		 * @access      public
		 * @since       1.0
		 * @return      void
		 */
		public function bm_add_custom_nav_fields( $menu_item ) {

			$menu_item->header = get_post_meta( $menu_item->ID, '_menu_item_header', true );
			$menu_item->footer = get_post_meta( $menu_item->ID, '_menu_item_footer', true );
			return $menu_item;

		}

		/**
		 * Add one more header
		 */
		public function bm_onesocial_after_header(){
			if ( is_active_sidebar('marketplace_panel') || has_nav_menu( 'primary-menu' ) ) {
				?>
				<nav class="subheader">
					<?php
					$menu_locations = (array) get_nav_menu_locations();
					$menu = isset( $menu_locations[ 'marketplace-menu' ] ) ? get_term_by( 'id', (int) $menu_locations[ 'marketplace-menu' ], 'nav_menu', ARRAY_A ) : false;
					if($menu['name']) {
						echo '<div id="sub-trigger">' . $menu['name'] . '<i class="fa fa-chevron-down"></i></div>';
					} else if ( is_active_sidebar( 'marketplace_panel' ) ) {
						echo '<div id="sub-trigger">' . __('Shop by Category', 'buddyboss-marketplace') . '<i class="fa fa-chevron-down"></i></div>';
					}
					global $woocommerce;
					if ($woocommerce) {
						$cart_items = $woocommerce->cart->cart_contents_count;
						?>
						<div class="header-notifications header-cart">
							<a class="cart-button has-count header-button boss-tooltip underlined" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" data-tooltip="<?php _e( 'Cart', 'buddyboss-marketplace' ); ?>">
								<span><b><?php echo $cart_items; ?></b></span>
							</a>
						</div>
						<?php
					}

					?>
					<div class="header-wrapper">
						<?php
						// Widgets
						echo '<ul class="menu">';
						dynamic_sidebar( 'marketplace_panel' );
						echo '</ul>';
						// Menu
						if ( has_nav_menu( 'marketplace-menu' ) ) {
						$args = array(
                            'theme_location' => 'marketplace-menu',
                            'container' => '',
                            'menu_class' => 'menu nav',
                            'fallback_cb' => false
                        );
						wp_nav_menu( $args );
						}
						?>
					</div>
				</nav>
				<?php
			}
		}

		/**
		 * Add widet area
		 */
		public function bm_menu_init()
		{
			register_nav_menus(array(
				'marketplace-menu' => __('Marketplace Menu', 'buddyboss-marketplace')
			));
		}

		/**
		 * Add widget arrea
		 */
		public function bm_widgets_init(){
			register_sidebar( array(
				'name'			 => 'MarketPanel',
				'id'			 => 'marketplace_panel',
				'description'	 => 'The dropdown panel area below the main site navigation. Add "MarketPanel" widgets here for each dropdown panel section.',
				'before_widget'	 => '<li id="%1$s" class="widget %2$s">',
				'after_widget'	 => '</div></li>',
				'before_title'	 => '<b class="widgettitle">',
				'after_title'	 => '</b><div class="sub-menu">',
			) );

			$this->do_includes( array('widgets') );
			register_widget( 'BM_Widget_Product_Categories' );

			$this->do_includes( array('widgets-bm-location-filter') );
			register_widget( 'BM_Location_Filter' );

			add_action('admin_enqueue_scripts', array($this, 'widget_style'));
		}

		/**
		 * Add cart to header
		 */
		public function bm_onesocial_notification_buttons(){
			global $woocommerce;
			if ($woocommerce) {
				$cart_items = $woocommerce->cart->cart_contents_count;
				?>
				<div class="header-notifications header-cart">
					<a class="cart-button has-count header-button boss-tooltip underlined" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" data-tooltip="<?php _e( 'Cart', 'buddyboss-marketplace' ); ?>">
						<span><b><?php echo $cart_items; ?></b></span>
					</a>
				</div>
				<?php
			}
		}

		/**
		 * Profile shop info
		 */
		public function bm_user_shop_info (){
			$vendor_id 			= bp_displayed_user_id();
			if(WCV_Vendors::is_vendor($vendor_id)) {
				$shop_name = WCV_Vendors::is_vendor($vendor_id)
					? WCV_Vendors::get_vendor_shop_name($vendor_id)
					: get_bloginfo('name');
				$store_icon_src = wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_icon_id', true ), array(400, 400));
				$store_icon = '';
				$shop_url = WCV_Vendors::get_vendor_shop_page($vendor_id);
				// see if the array is valid
				if (is_array($store_icon_src)) {
					$store_icon = '<img src="' . $store_icon_src[0] . '" alt="" class="store-icon" style="max-width:100%;" />';
				}
				?>
				<a href="<?php echo $shop_url; ?>" class="bm-profile-shop">
					<?php echo $store_icon; ?>
					<div class="bm-profile-shop-details">
						<i class="bb-icon-cart"></i>
						<h3><?php echo $shop_name; ?></h3>
					</div>
				</a>
				<?php
			}
		}

		/**
		 * Shop to favorites
		 */
		public function bm_add_shop_to_favorites (){

			if(isset($_POST['vendor_id'])) {
				$vendor_id = $_POST['vendor_id'];
				$current_user = get_current_user_id();
				$user_favorites = get_user_meta($current_user, "favorite_shops", true);
				$shops_favorited_count = get_option('shops_favorited_count');

				if(!is_array($user_favorites)) {
					$user_favorites = array();
				}

				if (in_array($vendor_id, $user_favorites)) {
					// remove this shop from user meta
					$user_favorites = array_diff( $user_favorites, array( $vendor_id ) );
					if (update_user_meta($current_user, "favorite_shops", $user_favorites)) {
						echo "Successfully removed";
					}

					// remove this shop from count
					if($shops_favorited_count[$vendor_id] > 0) {
						$shops_favorited_count[$vendor_id] = $shops_favorited_count[$vendor_id] - 1;
					}

					if (update_option('shops_favorited_count', $shops_favorited_count)) {
						echo "Shop has been unfeatured";
					} else {
						echo "Shop couldn't been unfeatured";
					}
				} else {

					// add this shop to user meta
					array_push($user_favorites, intval($vendor_id));
					if (update_user_meta($current_user, "favorite_shops", $user_favorites)) {
						echo "Successfully added";
					} else {
						echo "Failed: Could not update user meta";
					}

					// add this shop to count
					if(!$shops_favorited_count[$vendor_id])
						$shops_favorited_count[$vendor_id] = 0;

					$shops_favorited_count[$vendor_id] = $shops_favorited_count[$vendor_id] + 1;

					if (update_option('shops_favorited_count', $shops_favorited_count)) {
						echo "Shop has been featured";
					} else {
						echo "Shop couldn't been featured";
					}
				}
			}

			exit();
		}

		/**
		 * Product to favorites
		 */
		public function bm_add_product_to_favorites (){
			if(isset($_POST['product_id'])) {
				$product_id = $_POST['product_id'];
				$current_user = get_current_user_id();
				$favorites = get_user_meta($current_user, "favorite_products", true);
				$products_favorited_count = get_option('products_favorited_count');

				if(!is_array($favorites)) {
					$favorites = array();
				}

				if (in_array($product_id, $favorites)) {
					// remove this product from user meta
					$favorites = array_diff($favorites, $product_id);
					if (update_user_meta($current_user, "favorite_products", $favorites)) {
						echo "Successfully removed";
					}

					// remove this product from count
					if($products_favorited_count[$product_id] > 0) {
						$products_favorited_count[$product_id] = $products_favorited_count[$product_id] - 1;
					}

					if (update_option('products_favorited_count', $products_favorited_count)) {
						echo "Product has been unfeatured";
					} else {
						echo "Product couldn't been unfeatured";
					}
				} else {
					// add this product to user meta
					array_push($favorites, intval($product_id));
					if (update_user_meta($current_user, "favorite_products", $favorites)) {
						echo "Successfully added";
					} else {
						echo "Failed: Could not update user meta";
					}

					// add this product to count
					if(!$products_favorited_count[$product_id])
						$products_favorited_count[$product_id] = 0;

					$products_favorited_count[$product_id] = $products_favorited_count[$product_id] + 1;

					if (update_option('products_favorited_count', $products_favorited_count)) {
						echo "Product has been featured";
					} else {
						echo "Product couldn't been featured";
					}
				}
			}

			exit();
		}

		function bm_remove_product_from_favorites($pid){
			$products_favorited_count = get_option('products_favorited_count');
			if ( is_array( $products_favorited_count ) && array_key_exists($pid, $products_favorited_count)) {
				unset($products_favorited_count[$pid]);
				update_option('products_favorited_count', $products_favorited_count);
			}

		}

		function bm_remove_shop_from_favorites( $id, $reassign ) {
			$shops_favorited_count = get_option('shops_favorited_count');
			if ( is_array( $shops_favorited_count ) && array_key_exists($id, $shops_favorited_count)) {
				unset($shops_favorited_count[$id]);
				update_option('shops_favorited_count', $shops_favorited_count );
			}
		}

		// Add term field
		public function bm_product_cat_add_new_meta_field()
		{
			// this will add the custom meta field to the add new term page
			?>
			<div class="form-field">
				<label for="term_meta[show_on_cat]"><?php _e('Featured Category', 'buddyboss-marketplace'); ?></label>
				<input type="checkbox" name="term_meta[show_on_cat]" id="term_meta[show_on_cat]">
				<p class="description"><?php _e('Show this category at the top of any product category page.', 'buddyboss-marketplace'); ?></p>
			</div>
			<?php
		}

		// Edit term page
		public function bm_product_cat_edit_meta_field($term)
		{
			// put the term ID into a variable
			$t_id = $term->term_id;

			// retrieve the existing value(s) for this meta field. This returns an array
			$term_meta = get_option("taxonomy_$t_id");
			?>
			<tr class="form-field">
				<th scope="row" valign="top"><label
						for="term_meta[show_on_cat]"><?php _e('Featured Category', 'buddyboss-marketplace'); ?></label></th>
				<td>
					<input type="hidden" id="term_meta[show_on_cat]" name="term_meta[show_on_cat]" value="off" />
					<input type="checkbox" name="term_meta[show_on_cat]" id="term_meta[show_on_cat]" <?php checked( $term_meta['show_on_cat'], 'on' ); ?>>
					<p class="description"><?php _e('Show this category at the top of any product category page.', 'buddyboss-marketplace'); ?></p>
				</td>
			</tr>
			<?php
		}

		// Save extra taxonomy fields callback function.
		public function bm_product_cat_save_custom_meta( $term_id ) {
			$term_meta = array();
			if ( isset( $_POST['term_meta'] ) ) {
				$t_id = $term_id;
				$term_meta = get_option( "taxonomy_$t_id" );
				$cat_keys = array_keys( $_POST['term_meta'] );
				foreach ( $cat_keys as $key ) {
					if ( isset ( $_POST['term_meta'][$key] ) ) {
						$value = $_POST['term_meta'][$key];
					} else {
						$value = false;
					}
					$term_meta[$key] = $value;
				}
			}

			// Save the option array.
			update_option( "taxonomy_$t_id", $term_meta );
		}

		public function bm_change_bp_pagination($html){
			if($html) {
				$doc = new DOMDocument();
				$doc->loadHTML($html);

				$main = $doc->getElementsByTagName('body')->item(0);

				//Create new wrapper div
				$new_li = $doc->createElement('li');

				$main_children = array();

				foreach ($main->childNodes as $node) {
					$main_children[] = $node;
				}

				foreach ($main_children as $node) {
					if (trim($node->nodeValue)) {
						$new_li_clone = $new_li->cloneNode();
						$node->parentNode->replaceChild($new_li_clone, $node);
						$new_li_clone->appendChild($node);
					} else {
						$node->parentNode->removeChild($node);
					}
				}

				return $doc->saveHTML();
			}
		}

		/**
		 * Product to Favorites
		 */
		public function bm_product_to_favorites() {
			$product_id = get_the_ID();
			$vendor_id 		= WCV_Vendors::get_vendor_from_product( $product_id );
			$current_user = bp_loggedin_user_id();
			if($current_user && $current_user != $vendor_id) {
				$favorites = get_user_meta($current_user, "favorite_products", true);
				$class = (is_array($favorites) && in_array($product_id, $favorites)) ? ' favorited' : '';
				$tooltip = __('Add to Favorites', 'buddyboss-marketplace');
				if($class) {
					$tooltip = __('Remove from Favorites', 'buddyboss-marketplace');
				}
				echo '<a href="#" class="boss-tooltip bm-product-to-favorites ' . $class . '" data-tooltip="'.$tooltip.'" data-id="' . $product_id . '"><i class="fa fa-heart-o"></i></a>';
			}
		}

		/**
		 * Remove Related Products
		 * @param $args
		 * @return array
		 */
		public function bm_remove_related_products( $args ) {
			return array();
		}


		/**
		 * Use custom product placholder image
		 *
		 */
		public function bm_custom_fix_thumbnail() {
			add_filter('woocommerce_placeholder_img_src',  array( $this, 'bm_custom_woocommerce_placeholder_img_src') );
		}

		public function bm_custom_woocommerce_placeholder_img_src( $src ) {
			$src = $this->assets_url . '/images/woo-placeholder.png';
			return $src;
		}

		/**
		 * Load store index template
		 * @param $template
		 * @return string
		 */
		public function bm_store_index_template( $template ) {
			$store_index_page = buddyboss_bm()->option('store-index');

			if(!empty( $store_index_page ) && $store_index_page == get_the_ID()) {
				$template = bm_check_template('store-index');
			}

			return $template;
		}

		/**
		 * Load sellers index template
		 * @param $template
		 * @return string
		 */
		public function bm_sellers_index_template( $template ) {
			$sellers_index_page = buddyboss_bm()->option('sellers-index');

			if(!empty( $sellers_index_page ) && $sellers_index_page == get_the_ID()) {
				$template = bm_check_template('sellers-index');
			}

			return $template;
		}

		/**
		 * Output the product sorting options.
		 *
		 * @subpackage	Loop
		 */
		function bm_woocommerce_catalog_ordering() {
			global $wp_query;

			$searchby = isset( $_GET['bm_store_search'] )?$_GET['bm_store_search']:'';

			if ( ( 1 == $wp_query->found_posts || ! woocommerce_products_will_display() ) && !$searchby ) {
				return;
			}

			$orderby                 = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
			$show_default_orderby    = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
			$catalog_orderby_options = apply_filters( 'woocommerce_catalog_orderby', array(
				'menu_order' => __( 'Default sorting', 'woocommerce' ),
				'popularity' => __( 'Sort by popularity', 'woocommerce' ),
				'rating'     => __( 'Sort by average rating', 'woocommerce' ),
				'date'       => __( 'Sort by newness', 'woocommerce' ),
				'price'      => __( 'Sort by price: low to high', 'woocommerce' ),
				'price-desc' => __( 'Sort by price: high to low', 'woocommerce' )
			) );

			if ( ! $show_default_orderby ) {
				unset( $catalog_orderby_options['menu_order'] );
			}

			if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
				unset( $catalog_orderby_options['rating'] );
			}

			wc_get_template( 'loop/orderby.php', array( 'catalog_orderby_options' => $catalog_orderby_options, 'orderby' => $orderby, 'show_default_orderby' => $show_default_orderby, 'searchby' => $searchby ) );
		}

        /**
         * Print css to fix the width of shop items, if number of products displayed per row is not 4( our theme default )
         */
        public function bm_shop_loop_counter_css(){
            $count = apply_filters( 'loop_shop_columns', 4 );
            if( $count != 4 ){
                $width = floor( 100/$count );
                $width_p = ( $width - 0.2 ) . '%';
                ?>
                <style type="text/css" id="emi-debug">
                    html body.woocommerce ul.products li.product,
                    html body.woocommerce-page ul.products li.product {
                        -webkit-box-flex: 0;
                        -webkit-flex: 0 0 <?php echo $width_p;?>;
                        -ms-flex: 0 0 <?php echo $width_p;?>;
                        flex: 0 0 <?php echo $width_p;?>;
                        max-width: <?php echo $width_p;?>;
                    }
                </style>
                <?php
            }
        }

        /**
         * Vendor's product search
         * @param $query
         */
		public function bm_alter_shop_query($query) {

			$searchby = isset( $_GET['bm_store_search'] )?$_GET['bm_store_search']:'';

            if (!is_admin() && $searchby && is_post_type_archive( 'product' ) && $query->is_main_query()) {
                $query->set('s' ,$searchby);
            }

            return $query;
		}

		/**
		 * Remove store header from single product
		 *
		 * @param $template
		 */
		public function bm_remove_sold_by($template) {
			remove_action('woocommerce_after_shop_loop_item', array('WCV_Vendor_Shop', 'template_loop_sold_by'), 9);
		}

		/**
		 * Store banner
		 */
		public function bm_load_archive_templates($template){

			if( is_shop() ) {

				$file = 'archive-product.php';
				$find[] = $file;
				$find[] = 'woocommerce/' . $file;

				$template = locate_template(array_unique($find));
				if (!$template) {
					$template = $this->templates_dir .'/woocommerce/'. $file;
				}
			}

			return $template;
		}

		/**
		 * Remove store header from single product
		 */
		public function bm_remove_store_header () {
			remove_action('woocommerce_before_single_product', array('WCV_Vendor_Shop', 'vendor_mini_header'));
		}

		/**
		 * Store excerpt
		 *
		 * @param $length
		 * @return int
		 */
		public function bm_store_excerpt_length($length) {
			global $post;
			if( 'vendor_store' == $post->post_type ) {
				return 20;
			}
			return $length;
		}

		/**
		 * Store banner
		 */
		public function bm_show_store_banner(){
			global $product;
			$vendor_shop = urldecode( get_query_var( 'vendor_shop' ) );
			$wc_prd_vendor_options 	= get_option( 'wc_prd_vendor_options' );
			$shop_store_header		= ( isset( $wc_prd_vendor_options[ 'store_shop_headers' ] ) && $wc_prd_vendor_options[ 'store_shop_headers' ] ) ? true : false;
			$single_store_header	= ( isset( $wc_prd_vendor_options[ 'store_single_headers' ] ) && $wc_prd_vendor_options[ 'store_single_headers' ] ) ? true : false;
			$show_on_product = $shop_store_header && $single_store_header && is_product();
			if( $vendor_shop || $show_on_product ) {
				if($vendor_shop) {
					$vendor_id = $this->get_vendor_id($vendor_shop);
				} else {
					$vendor_id = $product->post->post_author;
				}
				$store_banner_src 	= wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_banner_id', true ), 'large');
				$store_icon_src 	= wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_icon_id', true ), array( 188, 188 ) );

				if ( is_array( $store_banner_src ) ) {
					echo '<div class="entry-post-thumbnail">';
					echo '<img src="'. $store_banner_src[0].'" alt="" class="store-banner" />';
					echo '</div>';
					//  Getting default banner
				} else if( class_exists('WCVendors_Pro') ) {
					$default_banner_src = WCVendors_Pro::get_option( 'default_store_banner_src' );
					echo '<div class="entry-post-thumbnail">';
					echo '<img src="'. $default_banner_src .'" alt="" class="store-banner" />';
					echo '</div>';
					//Display empty banner container with solid theme color
				} else {
					if(is_array($store_icon_src)) {
						echo '<div class="entry-post-thumbnail empty">';
						echo '</div>';
					}
				}

			}
		}

		/**
		 * Remove prettyPhoto lightbox
		 *
		 */
		public function bm_loop_cart_button_text($text, $object) {
			return '';
		}

		/**
		 * Remove prettyPhoto lightbox
		 *
		 */
		public function bm_remove_woo_lightbox() {
			if(is_product()) {
				remove_action('wp_head', array($GLOBALS['woocommerce'], 'generator'));
				wp_dequeue_style('woocommerce_prettyPhoto_css');
				wp_dequeue_script('prettyPhoto');
				wp_dequeue_script('prettyPhoto-init');
			}
		}

		/**
		 * Filter "OneSocial" color schemes
		 *
		 * @param $schemes
		 * @return array
		 */
		public function bm_color_schemes($schemes){
			$marketplace = array(
				'marketplace' => array(
					'alt'		 => 'MarketPlace',
					'img'		 => $this->assets_url . '/images/marketplace.png',
					'presets'	 => array(
						'accent_color'				 => '#e7753f',
						'titlebar_bg'		         => '#f9f8f3',
						'boss_primary_color'		 => '#f9f8f3',
						'boss_secondary_color'		 => '#f9f9f9',
						'body_text_color'			 => '#333333',
						'heading_text_color'		 => '#333333',
						'sitetitle_color'		     => '#333333',
						'footer_widget_background'	 => '#f9f9f9',
						'footer_background'			 => '#242424',
						'onesocial_group_cover_bg'	 => '#e7753f',
						'admin_screen_background_color'	=> '#f9f8f3',
						'admin_site_title_color'	=> '#e7753f',
						'admin_screen_text_color'	=> '#333333',
						'admin_screen_button_color'	=> '#e7753f'
					)
				)
			);

			return array_merge($schemes, $marketplace);
		}

		/**
		 * Fileter "OneSocial" font options
		 *
		 * @param $options
		 * @return array
		 */
		public function bm_font_options($options){

			$marketplace_font_options = array(
				array(
					'id'			 => 'boss_marketplace_font_family',
					'type'			 => 'typography',
					'title'			 => __( 'MarketPlace Pages Text', 'buddyboss-marketplace' ),
					'subtitle'		 => __( 'Specify the marketplace pages content font properties.', 'buddyboss-marketplace' ),
					'google'		 => true,
					'line-height'	 => false,
					'text-align'	 => false,
					'subsets'		 => true,
					'color'			 => false,
					'default'		 => array(
						'font-size'		 => '14px',
						'font-family'	 => 'Lato',
						'font-weight'	 => '400',
					),
					'output'		 => array( '.vendor-pro-dashboard article .entry-content, .wcv-grid h2, .wcv-form .select2-container-multi .select2-choices .select2-search-field input' ),
				),
				array(
					'id'			 => 'boss_marketplace_semibold_font_family',
					'type'			 => 'typography',
					'title'			 => __( 'MarketPlace Pages Semibold Text', 'buddyboss-marketplace' ),
					'subtitle'		 => __( 'Specify the marketplace pages content semibold text.', 'buddyboss-marketplace' ),
					'google'		 => true,
					'font-size'		 => false,
					'line-height'	 => false,
					'text-align'	 => false,
					'subsets'		 => false,
					'color'			 => false,
					'default'		 => array(
						'font-family'	 => 'Lato',
						'font-weight'	 => '700',
					),
					'output'		 => array( 'article .wcv-grid h2, .shop-settings .entry-content label, .vendor-dashboard .site-content h2, .shop-settings .site-content center a.button, .vendor-dashboard .site-content center a.button, .table.table-vendor-sales-report th, .is-mobile .wcv-navigation ul.menu.horizontal li a, .is-mobile .wcv-tabs.top > .tabs-nav li a, .wcv-form label, .file-upload-wrap .add-image, .store-summary .store-desc a.store-name, .woocommerce div.product .product_title, .show-categories .cat-list ul span, .bm-featured-title, .bm-shop #secondary .widget h4, .style1 .bm-f-category .f-cat-des h5' ),
				),
				array(
					'id'			 => 'boss_marketplace_bold_font_family',
					'type'			 => 'typography',
					'title'			 => __( 'MarketPlace Pages Bold Text', 'buddyboss-marketplace' ),
					'subtitle'		 => __( 'Specify the marketplace pages content bold text.', 'buddyboss-marketplace' ),
					'google'		 => true,
					'font-size'		 => false,
					'line-height'	 => false,
					'text-align'	 => false,
					'subsets'		 => false,
					'color'			 => false,
					'default'		 => array(
						'font-family'	 => 'Lato',
						'font-weight'	 => '900',
					),
					'output'		 => array( '.wcv-navigation ul.menu.horizontal li a, .wcv-tabs.top>.tabs-nav li a, .order-again a.button, .shop-settings input[type="submit"], .not-vendor .form-row input[type="submit"], .bb-marketplace.page-template-homepage .bm-vc-header h3, .is-mobile .wcv-grid nav.wcv-navigation > div,
						.wcv-form .wcv-button[type=submit], .wcv-form button.wcv-button,.wcv_dashboard_table_header a.button, table.wcv-table tr th, .wcv-modal label, .woocommerce button.button.single_add_to_cart_button, .woocommerce div.product .woocommerce-tabs ul.tabs li, .show-owner-widget .owner-name, .bm-collections h3,
						.woocommerce table.shop_table thead th, .bp-user.orders #buddypress .woocommerce table.shop_table thead th, .woocommerce-order-received .woocommerce table.shop_table.order_details .order_item a, .bp-user.orders #buddypress .woocommerce table.shop_table .order_item a' )
				),
				array(
					'id'			 => 'boss_marketplace_menu_fonts',
					'type'			 => 'typography',
					'title'			 => __( 'MarketPlace Menu Text', 'buddyboss-marketplace' ),
					'subtitle'		 => __( 'Specify the marketplace menu text styles.', 'buddyboss-marketplace' ),
					'google'		 => true,
					'font-size'		 => true,
					'line-height'	 => false,
					'text-align'	 => false,
					'subsets'		 => false,
					'color'			 => false,
					'default'		 => array(
						'font-family'	 => 'Lato',
						'font-size'		 => '12px',
						'font-weight'	 => '900',
					),
					'output'		 => array( '.subheader .sub-menu .product-categories > li > a, nav.subheader .menu.nav > li.menu-item > a, nav.subheader .menu > li > b' )
				)
			);

			return array_merge($options, $marketplace_font_options);
		}

		public function bm_color_element_options($options){
			$marketplace_color_elements = array(
				array( 'slug' => 'marketplace_info', 'desc' => 'Marketplace Menu', 'type' => 'info' ),
				array( 'slug' => 'marketplace_menu_background', 'title' => 'Marketplace Menu Background', 'subtitle' => 'Set marketplace menu background color.', 'desc' => '', 'type' => 'color', 'default' => '' ),
				array( 'slug' => 'marketplace_menu_text_color', 'title' => 'Marketplace Menu Text Color', 'subtitle' => 'Set marketplace menu text color.', 'desc' => '', 'type' => 'color', 'default' => '#333333' ),
				array( 'slug' => 'marketplace_menu_text_hover', 'title' => 'Marketplace Menu Text Hover Color', 'subtitle' => 'Set marketplace menu text hover color.', 'desc' => '', 'type' => 'color', 'default' => '#e7753f' ),
			);

			return array_merge($options, $marketplace_color_elements);
		}

		/**
		 * Show header on single posts
		 *
		 * @param $bool
		 * @return bool
		 */
		public function bm_onesocial_single_header($bool) {
			global $post;
			if( 'vendor_store' == $post->post_type ) {
				return false;
			}
			return $bool;
		}

		/**
		 * Show footer on stores index
		 *
		 * @param $bool
		 * @return bool
		 */
		public function bm_onesocial_show_footer($bool) {

			if(is_post_type_archive( self::$store_slug )) {
				return true;
			}
			if(is_shop()) {
				return true;
			}
			if(is_product()) {
				return true;
			}
			$store_index_page = buddyboss_bm()->option('store-index');
			if(!empty( $store_index_page ) && $store_index_page == get_the_ID()) {
				return true;
			}
			$sellers_index_page = buddyboss_bm()->option('sellers-index');
			if(!empty( $sellers_index_page ) && $sellers_index_page == get_the_ID()) {
				return true;
			}
			if ( is_tax('product_color') ) {
				return true;
			}
			return $bool;
		}

		public function bm_onesocial_show_woo_sidebar($bool) {
			$vendor_shop 		= urldecode( get_query_var( 'vendor_shop' ) );
			if( is_shop() && $vendor_shop || is_product() ) {
				return false;
			}
			return $bool;
		}

		/**
		 * Remove sidebar on cart and checkout
		 * @param $bool
		 * @return bool
		 */
		public function bm_onesocial_show_page_sidebar($bool) {
			if( is_cart() || is_checkout() ) {
				return false;
			}
			if(class_exists('WCVendors_Pro')) {
				$feedback_form_page = WCVendors_Pro::get_option('feedback_page_id');
				if ($feedback_form_page && is_page($feedback_form_page)) {
					return false;
				}
			}
			return $bool;
		}

		/**
		 * Change WooCommerce pagination
		 *
		 * @param $args
		 * @return array
		 */
		public function bm_woocommerce_pagination($args) {
			$args['prev_text'] = '';
			$args['next_text'] = '';

			return $args;
		}

		/**
		 * Category filter
		 *
		 * @param $query
		 */
		public function bm_load_cat_results($query) {

			// don't affect wp-admin screens

			if (is_admin())
				return;

			if(!isset($_GET['cate']))
				return;

			$cat = $_GET['cate'];

			/** append serch var */
			if ($query->is_main_query() && $cat) {
				$taxquery = array(
					array(
						'taxonomy' => 'product_cat',
						'field' => 'id',
						'terms' => array( $cat ),
//						'operator'=> 'NOT IN'
					)
				);
				$query->set('tax_query', $taxquery);

			}
		}

		/**
		 * Search Store Index Page
		 *
		 * @param $query
		 */
		public function bm_load_search_results($query) {

			// don't affect wp-admin screens

			if (is_admin())
				return;

			$search = $_GET['query'];

			/** append serch var */
			if ($query->is_main_query() && $search) {
				$query->set('s', $search);
			}
		}

		public function bm_pre_get_posts( $querystring = '', $object = '' )
		{
			if (is_admin())
				return;

			// vars
			$sort = $_GET['sort'];

			if( $object != 'members' )
				return $querystring;

			$defaults = array(
				'type'            => 'active',
				'action'          => 'active'
			);

			$ch_querystring = wp_parse_args( $querystring, $defaults );

			$ch_querystring['action'] = 'alphabetical';

			return $ch_querystring;
		}

		/**
		 *  Load the new stores template
		 *
		 * @since  MarketPlace 1.0.0
		 */
		public function bm_load_template( $template ) {

			$file = '';

			if ( is_single() && get_post_type() == self::$store_slug ) {

				$file 	= 'single-' . self::$store_slug . '.php';

				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

			} elseif ( is_post_type_archive( self::$store_slug ) ) {

				$file 	= 'archive-' . self::$store_slug  . '.php';
			}

			if ( $file ) {

				// Check in the theme to ensure file is there.
				$template       = locate_template( 'wc-vendors/store/' . $file );

				if ( ! $template ) {
					$template = bm_check_template( "wc-vendors/store/{$file}" );
				}
			}

			return $template;

		}

		public function bm_filter_template_part($template, $slug, $name) {

			if( 'single-product' == $name && 'content' == $slug ) {
				$template = bm_check_template( "woocommerce/{$slug}-{$name}" );
			}
			if( 'product' == $name && 'content' == $slug ) {
				$template = bm_check_template( "woocommerce/{$slug}-{$name}" );
			}
			if( 'product_different' == $name && 'content' == $slug ) {
				$template = bm_check_template( "woocommerce/{$slug}-{$name}" );
			}

			return $template;
		}

		/**
		 * Replace plugin templates
		 *
		 * @param $located
		 * @param $template_name
		 * @param $args
		 * @param $template_path
		 * @param $default_path
		 * @return mixed
		 */
		public function bm_filter_template($located, $template_name, $args, $template_path, $default_path) {
			if( $template_name == 'shop_coupon-edit.php' ){
				$located = bm_check_template( "wc-vendors/dashboard/{$template_name}" );
				//$located = wc_locate_template( $template_name, 'wc-vendors/dashboard/', $this->templates_dir.'/wc-vendors/dashboard/' );
			}

			if( $template_name == 'permission.php' ){
				$located = bm_check_template( "wc-vendors/dashboard/{$template_name}" );
				//$located = wc_locate_template( $template_name, 'wc-vendors/dashboard/', $this->templates_dir.'/wc-vendors/dashboard/' );
			}

			if( $template_name == 'store-settings.php' ){
				$located = bm_check_template( "wc-vendors/dashboard/{$template_name}" );
				//$located = wc_locate_template( $template_name,'wc-vendors/dashboard/', $this->templates_dir.'/wc-vendors/dashboard/' );
			}

			if( $template_name == 'product-edit.php' ){
				$located = bm_check_template( "wc-vendors/dashboard/{$template_name}" );
				//$located = wc_locate_template( $template_name, 'wc-vendors/dashboard/', $this->templates_dir.'/wc-vendors/dashboard/' );
			}

			if( $template_name == 'product-download.php' ){
				$located = bm_check_template( "wc-vendors/dashboard/{$template_name}" );
			}

			if( $template_name == 'product-simple.php' ){
				$located = bm_check_template( "wc-vendors/dashboard/{$template_name}" );
			}

			if( $template_name == 'reports.php' && $template_path == 'wc-vendors/dashboard/'){
				$located = bm_check_template( "wc-vendors/dashboard/{$template_name}" );
				//$located = wc_locate_template( $template_name, 'wc-vendors/dashboard/', $this->templates_dir.'/wc-vendors/dashboard/' );
			}

			if( $template_name == 'reports.php' && $template_path == 'wc-vendors/dashboard/reports/'){
				$located = bm_check_template( "wc-vendors/dashboard/reports/{$template_name}" );
				//$located = wc_locate_template( $template_name, 'wc-vendors/dashboard/reports/', $this->templates_dir.'/wc-vendors/dashboard/reports/' );
			}

			if( $template_name == 'orders.php' ){
				$located = bm_check_template( "wc-vendors/dashboard/{$template_name}" );
				//$located = wc_locate_template( $template_name, 'wc-vendors/dashboard/', $this->templates_dir.'/wc-vendors/dashboard/' );
			}

			if( $template_name == 'store-settings.php' ){
				$located = bm_check_template( "wc-vendors/dashboard/{$template_name}" );
				//$located = wc_locate_template( $template_name, 'wc-vendors/dashboard/', $this->templates_dir.'/wc-vendors/dashboard/' );
			}

			if( $template_name == 'settings.php' ){
				$located = bm_check_template( "wc-vendors/dashboard/settings/{$template_name}" );
				//$located = wc_locate_template( $template_name, 'wc-vendors/dashboard/settings/', $this->templates_dir.'/wc-vendors/dashboard/settings/' );
			}

			if( $template_name == 'paypal-email-form.php' ){
				$located = bm_check_template( "wc-vendors/dashboard/settings/{$template_name}" );
				//$located = wc_locate_template( $template_name, 'wc-vendors/dashboard/settings/', $this->templates_dir.'/wc-vendors/dashboard/settings/' );
			}

			if( $template_name == 'shop-name.php' ){
				$located = bm_check_template( "wc-vendors/dashboard/settings/{$template_name}" );
				//$located = wc_locate_template( $template_name, 'wc-vendors/dashboard/settings/', $this->templates_dir.'/wc-vendors/dashboard/settings/' );
			}

			if( $template_name == 'seller-info.php' ){
				$located = bm_check_template( "wc-vendors/dashboard/settings/{$template_name}" );
				//$located = wc_locate_template( $template_name, 'wc-vendors/dashboard/settings/', $this->templates_dir.'/wc-vendors/dashboard/settings/' );
			}
			if( $template_name == 'shop-description.php' ){
				$located = bm_check_template( "wc-vendors/dashboard/settings/{$template_name}" );
				//$located = wc_locate_template( $template_name, 'wc-vendors/dashboard/settings/', $this->templates_dir.'/wc-vendors/dashboard/settings/' );
			}

			if( $template_name == 'store-header.php' ){
				$located = bm_check_template( "wc-vendors/store/{$template_name}" );
				//$located = wc_locate_template( $template_name, 'wc-vendors/store/', $this->templates_dir.'/wc-vendors/store/' );
			}

			if( $template_name == 'store-ratings.php' ){
				$located = bm_check_template( "wc-vendors/store/{$template_name}" );
				//$located = wc_locate_template( $template_name, 'wc-vendors/store/', $this->templates_dir.'/wc-vendors/store/' );
			}

			if( $template_name == 'vendor-main-header.php' ){
				$located = bm_check_template( "wc-vendors/front/{$template_name}" );
				//$located = wc_locate_template( $template_name, 'wc-vendors/front/', $this->templates_dir.'/wc-vendors/front/' );
			}
//			var_dump($template_name);
			if( $template_name == 'single-product/product-thumbnails.php'
				|| $template_name == 'single-product/meta.php'
				|| $template_name == 'single-product/short-description.php'
				|| $template_name == 'single-product/up-sells.php'
				|| $template_name == 'single-product/product-image.php'
				|| $template_name == 'archive-product.php'
				|| $template_name == 'loop/orderby.php'
				|| $template_name == 'loop/result-count.php'
				|| $template_name == 'cart/cart-empty.php'
				|| $template_name == 'checkout/form-checkout.php'
				|| $template_name == 'checkout/form-billing.php'
				|| $template_name == 'checkout/thankyou.php'
				|| $template_name == 'order/order-details-customer.php'
				|| $template_name == 'myaccount/my-orders.php'
				|| $template_name == 'content-product_cat.php'
			){
				$located = bm_check_template( "woocommerce/{$template_name}" );
				//$located = wc_locate_template( $template_name, $this->templates_dir.'/woocommerce/', $this->templates_dir.'/woocommerce/' );
			}

			return $located;
		}

		public function setup_admin_settings() {
			if ( ( is_admin() || is_network_admin() ) && current_user_can( 'manage_options' ) ) {
				$this->load_admin();
			}
		}

		/**
		 * Add body class
		 *
		 * @param $classes
		 * @return array
		 */
		public function bm_body_class($classes) {
			$classes[] = 'bb-marketplace';

			// not vendor
			if ( !WCV_Vendors::is_vendor( get_current_user_id() ) ) {
				$classes[] = 'not-vendor';
			}
			// orders page
			$orders_page    =  WC_Vendors::$pv_options->get_option( 'orders_page' );
			if ( $orders_page && is_page( $orders_page ) ) {
				$classes[] = 'orders-page';
			}
			// shop settings
			$shop_settings_page    = WC_Vendors::$pv_options->get_option( 'shop_settings_page' );
			if ( $shop_settings_page && is_page( $shop_settings_page ) ) {
				$classes[] = 'shop-settings';
			}
			// free plugin dashboard
			$free_dashboard_page 	= WC_Vendors::$pv_options->get_option( 'vendor_dashboard_page' );
			if ( $free_dashboard_page && is_page( $free_dashboard_page ) ) {
				$classes[] = 'vendor-dashboard';
			}
			// Feedback page
			if(class_exists('WCVendors_Pro')) {
				$feedback_form_page = WCVendors_Pro::get_option('feedback_page_id');
				if ($feedback_form_page && is_page($feedback_form_page)) {
					$classes[] = 'feedback';
				}
			}
			// $feedback_form_page 	= WCVendors_Pro::get_option( 'feedback_page_id' );
			// pro plugin dashboard
			if(class_exists('WCVendors_Pro')) {
				$pro_dashboard_page = WCVendors_Pro::get_option('dashboard_page_id');
				if ( $pro_dashboard_page && is_page($pro_dashboard_page) ) {
					$classes[] = 'vendor-pro-dashboard';
					$classes[] = 'woocommerce';
					if(current_user_can('administrator')) {
						$classes[] = 'administrator';
					}
				}
			}
			$current = get_the_ID();
			$store_index_page = buddyboss_bm()->option('store-index');
			if(!empty( $store_index_page ) && $store_index_page == $current) {
				$classes[] = 'bm-store-index';
			}
			$sellers_index_page = buddyboss_bm()->option('sellers-index');
			if(!empty( $sellers_index_page ) && $sellers_index_page == $current) {
				$classes[] = 'bm-sellers-index';
			}
			if(urldecode( get_query_var( 'vendor_shop' ))) {
				$classes[] = 'bm-vendor-shop';
			}
			if((is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag()) && !urldecode( get_query_var( 'vendor_shop' ))){
				$classes[] = 'bm-shop';
			}
			if(is_shop()){
				$classes[] = 'bm-shop-page';
			}
			if ( is_cart() && WC()->cart->is_empty() ) {
				$classes[] = 'empty-cart';
			}

			return $classes;
		}

		/**
		 * Remove woo title
		 * @param $bool
		 * @return bool
		 */
		public function bm_remove_title($bool) {
			if(is_product_taxonomy() || is_product_category() || is_product_tag())
				return false;

			return $bool;
		}

		/**
		 * Load plugin text domain
		 *
		 * @since MarketPlace (1.0.0)
		 *
		 * @uses sprintf() Format .mo file
		 * @uses get_locale() Get language
		 * @uses file_exists() Check for language file(filename)
		 * @uses load_textdomain() Load language file
		 */
		public function setup_textdomain() {
			$domain = 'buddyboss-marketplace';
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			//first try to load from wp-content/languages/plugins/ directory
			load_textdomain( $domain, WP_LANG_DIR . '/plugins/' . $domain . '-' . $locale . '.mo' );

			//if not found, then load from marketplace/languages/ directory
			load_plugin_textdomain( 'buddyboss-marketplace', false, $this->lang_dir );
		}

		/**
		 * We require BuddyPress to run the main components, so we attach
		 * to the 'bp_loaded' action which BuddyPress calls after it's started
		 * up. This ensures any BuddyPress related code is only loaded
		 * when BuddyPress is active.
		 *
		 * @since MarketPlace (1.0.0)
		 * @access public
		 *
		 * @return void
		 */
		public function bp_loaded() {
			global $bp;

			$this->bp_enabled = true;
			$this->load_main();
		}

		public function load_component() {
			$this->do_includes( array('marketplace-class') );
			$this->component = new BuddyBoss_BM_BP_Component();
		}


		/* Load
		 * ===================================================================
		 */

		/**
		 * Include required admin files.
		 *
		 * @since MarketPlace (1.0.0)
		 * @access private
		 *
		 * @uses $this->do_includes() Loads array of files in the include folder
		 */
		public function load_admin() {
			$this->do_includes( $this->admin_includes );

			$this->admin = BuddyBoss_BM_Admin::instance();
		}

		/**
		 * Include required files.
		 *
		 * @since MarketPlace (1.0.0)
		 * @access private
		 *
		 * @uses BuddyBoss_BM_Plugin::do_includes() Loads array of files in the include folder
		 */
		private function load_main() {
			$this->do_includes( $this->main_includes );

			if (class_exists('WCVendors_Pro_Reports_Controller')) {
				$this->do_includes( array('reports') );
			}

			if (class_exists('WPBakeryVisualComposerAbstract')) {
				$this->vc = new BuddyBoss_BM_VC_Elements();
			}
			$this->component = new BuddyBoss_BM_BP_Component();
			$this->template_functions = BuddyBoss_BM_Templates::instance();
			$this->vendors_controller = BuddyBoss_BM_Vendors::instance();
		}

		/**
		 * Include blog files to added on init.
		 *
		 * @since MarketPlace (1.0.0)
		 * @access private
		 *
		 */
		public function load_init() {
			$this->do_includes( array( 'bm-user-functions', 'bm-user-menus' ) );
		}

		/* Activate/Deactivation/Uninstall callbacks
		 * ===================================================================
		 */

		/**
		 * Fires when plugin is activated
		 *
		 * @since MarketPlace (1.0.0)
		 *
		 * @uses current_user_can() Checks for user permissions
		 * @uses check_admin_referer() Verifies session
		 */
		public function activate() {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			$plugin = isset( $_REQUEST[ 'plugin' ] ) ? $_REQUEST[ 'plugin' ] : '';

			check_admin_referer( "activate-plugin_{$plugin}" );
		}

		/**
		 * Fires when plugin is de-activated
		 *
		 * @since MarketPlace (1.0.0)
		 *
		 * @uses current_user_can() Checks for user permissions
		 * @uses check_admin_referer() Verifies session
		 */
		public function deactivate() {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			$plugin = isset( $_REQUEST[ 'plugin' ] ) ? $_REQUEST[ 'plugin' ] : '';

			check_admin_referer( "deactivate-plugin_{$plugin}" );
		}

		/**
		 * Fires when plugin is uninstalled
		 *
		 * @since MarketPlace (1.0.0)
		 *
		 * @uses current_user_can() Checks for user permissions
		 * @uses check_admin_referer() Verifies session
		 */
		public function uninstall() {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			check_admin_referer( 'bulk-plugins' );

			// Important: Check if the file is the one
			// that was registered during the uninstall hook.
			if ( $this->file != WP_UNINSTALL_PLUGIN ) {
				return;
			}
		}

		/* Utility functions
		 * ===================================================================
		 */

		/**
		 * Include required array of files in the includes directory
		 *
		 * @since MarketPlace (1.0.0)
		 *
		 * @uses require_once() Loads include file
		 */
		public function do_includes( $includes = array() ) {
			foreach ( ( array ) $includes as $include ) {
				require_once($this->includes_dir . '/' . $include . '.php');
			}
		}

		/**
		 * Convenience function to access plugin options, returns false by default
		 *
		 * @since  MarketPlace (1.0.0)
		 *
		 * @param  string $key Option key

		 * @uses apply_filters() Filters option values with 'buddyboss_bm_option' &
		 *                       'buddyboss_bm_option_{$option_name}'
		 * @uses sprintf() Sanitizes option specific filter
		 *
		 * @return mixed Option value (false if none/default)
		 *
		 */
		public function option( $key ) {
			$key = strtolower( $key );
			$option = isset( $this->options[ $key ] ) ? $this->options[ $key ] : null;

			// Apply filters on options as they're called for maximum
			// flexibility. Options are are also run through a filter on
			// class instatiation/load.
			// ------------------------
			// This filter is run for every option
			$option = apply_filters( 'buddyboss_bm_option', $option );

			// Option specific filter name is converted to lowercase
			$filter_name = sprintf( 'buddyboss_bm_option_%s', strtolower( $key ) );
			$option = apply_filters( $filter_name, $option );

			return $option;
		}

        /**
         * A wrapper method for WCV_Vendors::get_vendor_id to cache the result instead of running the same set of queries again and again.
         *
         * @param string $input
         * @return mixed vendor id if a valid record is found, false otherwise.
         */
        public function get_vendor_id( $input ){
            if ( empty( $input ) ) {
                return false;
            }

            if ( !$data = wp_cache_get( $input, 'wcv_get_vendor_id' ) ) {
                $vendor_id = WCV_Vendors::get_vendor_id( $input );
                wp_cache_add( $input, $vendor_id, 'wcv_get_vendor_id' );
                return $vendor_id;
            }

            return $data;
        }

		/**
		 * WPML Compatibility: Returns the translated page ID or original if missing
		 * @param $page_id
		 * @return mixed|void
		 */
		public function wpml_filter_page_id( $page_id ) {
			return apply_filters( 'wpml_object_id', $page_id, 'page', true );
		}

		/**
		 * Social sharing meta tags - og, twitter
		 */
		public function social_sharing_meta_tags() {
			$v_page = WCV_Vendors::is_vendor_page();
			if ($v_page) {

				$vendor_shop = urldecode(get_query_var('vendor_shop'));
				$vendor_id = WCV_Vendors::get_vendor_id($vendor_shop);
				$vendor_name = get_user_meta($vendor_id, 'pv_shop_name', true);
				$vendor_description = strip_tags( get_user_meta($vendor_id, 'pv_shop_description', true) );
				$vendor_url = WCV_Vendors::get_vendor_shop_page($vendor_id);
				$vendor_twitter_username = get_user_meta( $vendor_id, '_wcv_twitter_username', true );
				$image = wp_get_attachment_image_src(get_user_meta($vendor_id, '_wcv_store_banner_id', true), 'full');
				$blog_name = get_bloginfo(strip_tags('name'));
				if (!empty($image)) {
					$img_src = $image[0];
				} else {
					$img_src = apply_filters('woocommerce_placeholder_img_src', WC()->plugin_url() . '/assets/images/placeholder.png');
				}
				?>
				<!--/ Twitter Open Graph for  Vendors /-->
				<meta name="twitter:card" content="summary_large_image" />
				<meta name="twitter:site" content="<?php echo '@' . $vendor_twitter_username; ?>" />
				<meta name="twitter:creator" content="<?php echo '@'. $blog_name; ?>" />
				<meta name="twitter:title" content="<?php echo $vendor_name; ?>" />
				<meta name="twitter:description" content="<?php echo $vendor_description; ?>" />
				<meta name="twitter:image" content="<?php echo esc_attr($img_src); ?>" />
				<meta name="twitter:url" content="<?php echo esc_url($vendor_url); ?>" />
				<!--/ Facebook Open Graph for Vendors /-->
				<meta property="og:title" content="<?php echo $vendor_name; ?><?php echo !empty( $blog_name ) ? ' | '. $blog_name : ''; ?>" />
				<meta property="og:description" content="<?php echo $vendor_description; ?>" />
				<meta property="og:image" content="<?php echo esc_attr($img_src); ?>" />
				<meta property="og:image:width" content="300" />
				<meta property="og:image:height" content="200" />
				<meta property="og:type" content="product" />
				<meta property="og:url" content="<?php echo esc_url($vendor_url); ?>" />
				<meta property="og:site_name" content="<?php echo $blog_name; ?>" />
				<!-- Google Plus Open Graph for Vendors /-->
				<meta property="name" content="<?php echo $vendor_name; ?>" />
				<meta property="description" content="<?php echo $vendor_description; ?>" />
				<meta property="image" content="<?php echo esc_attr($img_src); ?>" />
				<?php
			}
		}

	} // End class BuddyBoss_BM_Plugin

endif;
