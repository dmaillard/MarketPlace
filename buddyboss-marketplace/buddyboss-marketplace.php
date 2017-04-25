<?php
/**
 * Plugin Name: MarketPlace
 * Plugin URI:  https://www.buddyboss.com/products/social-marketplace
 * Description: Integrates OneSocial theme and BuddyPress with WC Vendors.
 * Author:      BuddyBoss
 * Author URI:  http://buddyboss.com
 * Version:     1.4.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ========================================================================
 * CONSTANTS
 * ========================================================================
 */
// Codebase version
if ( ! defined( 'BUDDYBOSS_BM_PLUGIN_VERSION' ) ) {
	define( 'BUDDYBOSS_BM_PLUGIN_VERSION', '1.4.0' );
}

// Database version
if ( ! defined( 'BUDDYBOSS_BM_PLUGIN_DB_VERSION' ) ) {
	define( 'BUDDYBOSS_BM_PLUGIN_DB_VERSION', 1 );
}

// Directory
if ( ! defined( 'BUDDYBOSS_BM_PLUGIN_DIR' ) ) {
	define( 'BUDDYBOSS_BM_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

// Url
if ( ! defined( 'BUDDYBOSS_BM_PLUGIN_URL' ) ) {
	$plugin_url = plugin_dir_url( __FILE__ );

	// If we're using https, update the protocol. Workaround for WP13941, WP15928, WP19037.
	if ( is_ssl() )
		$plugin_url = str_replace( 'http://', 'https://', $plugin_url );

	define( 'BUDDYBOSS_BM_PLUGIN_URL', $plugin_url );
}

// File
if ( ! defined( 'BUDDYBOSS_BM_PLUGIN_FILE' ) ) {
	define( 'BUDDYBOSS_BM_PLUGIN_FILE', __FILE__ );
}

/**
 * ========================================================================
 * MAIN FUNCTIONS
 * ========================================================================
 */

/**
 * Main
 *
 * @return void
 */
add_action( 'plugins_loaded', 'buddyboss_bm_init' );

function buddyboss_bm_init() {

	global $bp, $BUDDYBOSS_BM;

	if ( !$bp ) {
		add_action('admin_notices','bm_bp_admin_notice');
		return;
	}

	if ( !class_exists('WC_Vendors') ) {
		add_action('admin_notices','bm_vendors_admin_notice');
		return;
	}

	$main_include = BUDDYBOSS_BM_PLUGIN_DIR . 'includes/main-class.php';

	try {
		if ( file_exists( $main_include ) ) {
			require( $main_include );
		} else {
			$msg = sprintf( __( "Couldn't load main class at:<br/>%s", 'buddyboss-marketplace' ), $main_include );
			throw new Exception( $msg, 404 );
		}
	} catch ( Exception $e ) {
		$msg = sprintf( __( "<h1>Fatal error:</h1><hr/><pre>%s</pre>", 'buddyboss-marketplace' ), $e->getMessage() );
		echo $msg;
	}

	$BUDDYBOSS_BM = BuddyBoss_BM_Plugin::instance();
}

/**
 * Check whether
 * it meets all requirements
 * @return void
 */
function bm_requirements()
{

    global $Plugin_Requirements_Check;

    $requirements_Check_include  = BUDDYBOSS_BM_PLUGIN_DIR  . 'includes/requirements-class.php';

    try
    {
        if ( file_exists( $requirements_Check_include ) )
        {
            require( $requirements_Check_include );
        }
        else{
            $msg = sprintf( __( "Couldn't load BMT_Plugin_Check class at:<br/>%s", 'buddyboss-marketplace' ), $requirements_Check_include );
            throw new Exception( $msg, 404 );
        }
    }
    catch( Exception $e )
    {
        $msg = sprintf( __( "<h1>Fatal error:</h1><hr/><pre>%s</pre>", 'buddyboss-marketplace' ), $e->getMessage() );
        echo $msg;
    }

    $Plugin_Requirements_Check = new BM_Plugin_Requirements_Check();
    $Plugin_Requirements_Check->activation_check();

}
register_activation_hook( __FILE__, 'bm_requirements' );

/**
 * Must be called after hook 'plugins_loaded'
 * @return BuddyBoss_BM_Plugin main controller object
 */
function buddyboss_bm() {

	global $BUDDYBOSS_BM;
	return $BUDDYBOSS_BM;

}

function bm_bp_admin_notice() {
	echo "<div class='error'><p>MarketPlace needs BuddyPress activated</p></div>";
}

function bm_vendors_admin_notice() {
	echo "<div class='error'><p>MarketPlace needs WC Vendor activated</p></div>";
}


/**
 * Allow automatic updates via the WordPress dashboard
 */
require_once('includes/buddyboss-plugin-updater.php');
new buddyboss_updater_plugin( 'http://update.buddyboss.com/plugin', plugin_basename(__FILE__), 195);
