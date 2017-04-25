<?php
/**
 * The template for displaying the Pro Dashboard permission denied
 *
 * Override this template by copying it to yourtheme/wc-vendors/dashboard/
 *
 * @var 	   $page_url  		The permalink to the page
 * @var 	   $page_label 		The page label for the menu item
 * @package    WCVendors_Pro
 * @version    1.0.3
 */
?>

<h3><?php _e( 'No Permission.', 'wcvendors-pro'); ?></h3>

<a href="<?php echo $return_url; ?>" class="button"><?php _e( 'Return to dashboard', 'wcvendors-pro'); ?></a>
