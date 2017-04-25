<?php
/**
 * @package WordPress
 * @subpackage MarketPlace
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'BuddyBoss_BM_Admin' ) ):

	/**
	 *
	 * MarketPlace Admin
	 * ********************
	 *
	 *
	 */
	class BuddyBoss_BM_Admin {
		/* Options/Load
		 * ===================================================================
		 */

		/**
		 * Plugin options
		 *
		 * @var array
		 */
		public	$options = array();
		private $plugin_settings_tabs = array(),
				$network_activated = false,
				$plugin_slug = 'bb-buddyboss-bm',
				$menu_hook = 'admin_menu',
				$settings_page = 'buddyboss-settings',
				$capability = 'manage_options',
				$form_action = 'options.php',
				$plugin_settings_url;

		/**
		 * Empty constructor function to ensure a single instance
		 */
		public function __construct() {
			// ... leave empty, see Singleton below
		}

		/* Singleton
		 * ===================================================================
		 */

		/**
		 * Admin singleton
		 *
		 * @since MarketPlace (1.0.0)
		 *
		 * @param  array  $options [description]
		 *
		 * @uses BuddyBoss_BM_Admin::setup() Init admin class
		 *
		 * @return object Admin class
		 */
		public static function instance() {
			static $instance = null;

			if ( null === $instance ) {
				$instance = new BuddyBoss_BM_Admin;
				$instance->setup();
			}

			return $instance;
		}

		/* Utility functions
		 * ===================================================================
		 */

		/**
		 * Get option
		 *
		 * @since MarketPlace (1.0.0)
		 *
		 * @param  string $key Option key
		 *
		 * @uses BuddyBoss_BM_Plugin::option() Get option
		 *
		 * @return mixed      Option value
		 */
		public function option( $key ) {
			$value = buddyboss_bm()->option( $key );
			return $value;
		}

		/* Actions/Init
		 * ===================================================================
		 */

		/**
		 * Setup admin class
		 *
		 * @since MarketPlace (1.0.0)
		 *
		 * @uses buddyboss_bm() Get options from main BuddyBoss_BM_Plugin class
		 * @uses is_admin() Ensures we're in the admin area
		 * @uses curent_user_can() Checks for permissions
		 * @uses add_action() Add hooks
		 */
		public function setup() {
			if ( ( ! is_admin() && ! is_network_admin() ) || ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$this->plugin_settings_url = admin_url( 'admin.php?page=' . $this->plugin_slug );

			$this->network_activated = $this->is_network_activated();

			//if the plugin is activated network wide in multisite, we need to override few variables
			if ( $this->network_activated ) {
				// Main settings page - menu hook
				$this->menu_hook = 'network_admin_menu';

				// Main settings page - parent page
				$this->settings_page = 'settings.php';

				// Main settings page - Capability
				$this->capability = 'manage_network_options';

				// Settins page - form's action attribute
				$this->form_action = 'edit.php?action=' . $this->plugin_slug;

				// Plugin settings page url
				$this->plugin_settings_url = network_admin_url( 'settings.php?page=' . $this->plugin_slug );
			}

			//if the plugin is activated network wide in multisite, we need to process settings form submit ourselves
			if ( $this->network_activated ) {
				//add_action( 'network_admin_edit_' . $this->plugin_slug, array( $this, 'save_network_settings_page' ) );
			}

			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_init', array($this, 'register_support_settings' ) );

			add_action( $this->menu_hook, array( $this, 'admin_menu' ) );

			//add_filter( 'plugin_action_links', array( $this, 'add_action_links' ), 10, 2 );
			//add_filter( 'network_admin_plugin_action_links', array( $this, 'add_action_links' ), 10, 2 );
		}

		/**
		 * Check if the plugin is activated network wide(in multisite).
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
		 * Register admin settings
		 *
		 * @since MarketPlace (1.0.0)
		 *
		 * @uses register_setting() Register plugin options
		 * @uses add_settings_section() Add settings page option sections
		 * @uses add_settings_field() Add settings page option
		 */
		public function admin_init() {
			$this->plugin_settings_tabs['buddyboss_bm_plugin_options'] = 'General';

			register_setting( 'buddyboss_bm_plugin_options', 'buddyboss_bm_plugin_options', array( $this, 'plugin_options_validate' ) );
			add_settings_section( 'general_section', __( 'General Settings', 'buddyboss-marketplace' ), array( $this, 'section_general' ), __FILE__ );

			add_settings_field( 'store-index-page', __( 'Select Page To Show Stores', 'buddyboss-marketplace' ), array( $this, 'create_new_store_index' ), __FILE__, 'general_section');
			add_settings_field( 'sellers-index-page', __( 'Select Page To Show Sellers', 'buddyboss-marketplace' ), array( $this, 'create_new_sellers_index' ), __FILE__, 'general_section');
			add_settings_field( 'show-sold', __( 'Show Sold Items Count', 'buddyboss-marketplace' ), array( $this, 'show_sold' ), __FILE__, 'general_section');
			add_settings_field( 'show-as-vendor', __( 'Show "Register As Vendor" Checkbox', 'buddyboss-marketplace' ), array( $this, 'show_as_vendor' ), __FILE__, 'general_section');
		}

		function register_support_settings() {
			$this->plugin_settings_tabs[ 'buddyboss_bm_support_options' ] = __('Support','buddyboss-marketplace');

			register_setting( 'buddyboss_bm_support_options', 'buddyboss_bm_support_options' );
			add_settings_section( 'section_support', ' ', array( &$this, 'section_support_desc' ), 'buddyboss_bm_support_options' );
		}

		function section_support_desc() {
			if ( file_exists( dirname( __FILE__ ) . '/help-support.php' ) ) {
				require_once( dirname( __FILE__ ) . '/help-support.php' );
			}
		}

		public function show_sold() {
			$value = $this->option( 'show-sold' );

			$checked = '';

			if ( $value ) {
				$checked = ' checked="checked" ';
			}

			echo '<label for="show-sold">';
			echo "<input " . $checked . " id='show-sold' name='buddyboss_bm_plugin_options[show-sold]' type='checkbox' />  ";
			echo '</label>';
		}

		public function show_as_vendor() {
			$value = $this->option( 'show-as-vendor' );

			$checked = '';

			if ( $value ) {
				$checked = ' checked="checked" ';
			}

			echo '<label for="show-as-vendor">';
			echo "<input " . $checked . " id='show-as-vendor' name='buddyboss_bm_plugin_options[show-as-vendor]' type='checkbox' />  ";
			echo '</label>';
		}

		public function create_new_store_index() {
			$store_index_page = $this->option('store-index');

			echo wp_dropdown_pages(array(
				'name' => 'buddyboss_bm_plugin_options[store-index]',
				'echo' => false,
				'show_option_none' => __('- None -', 'buddyboss-marketplace'),
				'selected' => $store_index_page
			));
			echo '<a href="' . admin_url(esc_url(add_query_arg(array('post_type' => 'page'), 'post-new.php'))) . '" class="button-secondary">' . __('New Page', 'buddyboss-marketplace') . '</a>';
			if (!empty($store_index_page)) {
				echo '<a href="' . get_permalink($store_index_page) . '" class="button-secondary" target="_bp" style="margin-left: 5px;">' . __('View', 'buddyboss-marketplace') . '</a>';
			}
			echo '<p class="description">' . __('You may need to reset your permalinks after changing this setting. Go to Settings > Permalinks.', 'buddyboss-marketplace') . '</p>';
		}

		public function create_new_sellers_index() {
			$sellers_index_page = $this->option('sellers-index');

			echo wp_dropdown_pages(array(
				'name' => 'buddyboss_bm_plugin_options[sellers-index]',
				'echo' => false,
				'show_option_none' => __('- None -', 'buddyboss-marketplace'),
				'selected' => $sellers_index_page
			));
			echo '<a href="' . admin_url(esc_url(add_query_arg(array('post_type' => 'page'), 'post-new.php'))) . '" class="button-secondary">' . __('New Page', 'buddyboss-marketplace') . '</a>';
			if (!empty($sellers_index_page)) {
				echo '<a href="' . get_permalink($sellers_index_page) . '" class="button-secondary" target="_bp" style="margin-left: 5px;">' . __('View', 'buddyboss-marketplace') . '</a>';
			}
			echo '<p class="description">' . __('You may need to reset your permalinks after changing this setting. Go to Settings > Permalinks.', 'buddyboss-marketplace') . '</p>';
		}

		/**
		 * Add plugin settings page
		 *
		 * @since MarketPlace (1.0.0)
		 *
		 * @uses add_options_page() Add plugin settings page
		 */
		public function admin_menu() {
			add_submenu_page(
					$this->settings_page, 'BuddyBoss MarketPlace', 'MarketPlace', $this->capability, $this->plugin_slug, array( $this, 'options_page' )
			);
		}

		/**
		 * Add plugin settings page
		 *
		 * @since MarketPlace (1.0.0)
		 *
		 * @uses BuddyBoss_BM_Admin::admin_menu() Add settings page option sections
		 */
		public function network_admin_menu() {
			return $this->admin_menu();
		}

		/* Settings Page + Sections
		 * ===================================================================
		 */

		/**
		 * Render settings page
		 *
		 * @since MarketPlace (1.0.0)
		 *
		 * @uses do_settings_sections() Render settings sections
		 * @uses settings_fields() Render settings fields
		 * @uses esc_attr_e() Escape and localize text
		 */
		public function options_page() {
			$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : __FILE__;
			?>
			<div class="wrap">
				<h2><?php _e( 'BuddyBoss MarketPlace', 'buddyboss-marketplace' ); ?></h2>
				<?php $this->plugin_options_tabs(); ?>
				<form action="<?php echo $this->form_action; ?>" method="post">

					<?php
					if ( $this->network_activated && isset( $_GET[ 'updated' ] ) ) {
						echo "<div class='updated'><p>" . __( 'Settings updated.', 'buddyboss-marketplace' ) . "</p></div>";
					}
					if ( 'buddyboss_bm_plugin_options' == $tab || empty($_GET['tab']) ) {
						settings_fields( 'buddyboss_bm_plugin_options' );
						do_settings_sections( __FILE__ ); ?>
						<p class="submit">
							<input name="bboss_g_s_settings_submit" type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'buddyboss-marketplace' ); ?>" />
						</p><?php
					} else {
						settings_fields( $tab );
						do_settings_sections( $tab );
					} ?>

				</form>
			</div>

			<?php
		}

		function plugin_options_tabs() {
			$current_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'buddyboss_bm_plugin_options';

			echo '<h2 class="nav-tab-wrapper">';
			foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				echo '<a class="nav-tab ' . $active . '" href="?page=' . 'bb-buddyboss-bm' . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
			}
			echo '</h2>';
		}

		public function add_action_links( $links, $file ) {
			// Return normal links if not this plugin
			if ( plugin_basename( basename( constant( 'BUDDYBOSS_BM_PLUGIN_DIR' ) ) . '/buddyboss-marketplace.php' ) != $file ) {
				return $links;
			}

			$mylinks = array(
				'<a href="' . esc_url( $this->plugin_settings_url ) . '">' . __( "Settings", "buddyboss-marketplace" ) . '</a>',
			);
			return array_merge( $links, $mylinks );
		}

		public function save_network_settings_page() {
			if ( ! check_admin_referer( 'buddyboss_bm_plugin_options-options' ) )
				return;

			if ( ! current_user_can( $this->capability ) )
				die( 'Access denied!' );

			if ( isset( $_POST[ 'bboss_g_s_settings_submit' ] ) ) {
				$submitted = stripslashes_deep( $_POST[ 'buddyboss_bm_plugin_options' ] );
				$submitted = $this->plugin_options_validate( $submitted );

				update_site_option( 'buddyboss_bm_plugin_options', $submitted );
			}

			// Where are we redirecting to?
			$base_url = trailingslashit( network_admin_url() ) . 'settings.php';
			$redirect_url = esc_url_raw( add_query_arg( array( 'page' => $this->plugin_slug, 'updated' => 'true' ), $base_url ) );

			// Redirect
			wp_redirect( $redirect_url );
			die();
		}

		/**
		 * General settings section
		 *
		 * @since BuddyBoss Media (1.0.0)
		 */
		public function section_general() {

		}

		/**
		 * Validate plugin option
		 *
		 * @since MarketPlace (1.0.0)
		 */
		public function plugin_options_validate( $input ) {
			$input[ 'enabled' ] = sanitize_text_field( $input[ 'enabled' ] );

			return $input; // return validated input
		}

	} //End of BuddyBoss_BM_Admin class


endif;

