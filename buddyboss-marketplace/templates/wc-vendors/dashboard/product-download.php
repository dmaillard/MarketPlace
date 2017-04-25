<?php
/**
 * The template for displaying the Downloadable Product Edit form  
 *
 * Override this template by copying it to yourtheme/wc-vendors/dashboard/
 *
 * @package    WCVendors_Pro
 * @version    1.4.0
 */
/**
 *   DO NOT EDIT ANY OF THE LINES BELOW UNLESS YOU KNOW WHAT YOU'RE DOING 
 *   
*/

$title 		= ( is_numeric( $object_id ) ) ? __('Save Changes', 'wcvendors-pro') : __( 'Add Downloadable Product', 'buddyboss-marketplace');
$product 	= ( is_numeric( $object_id ) ) ? wc_get_product( $object_id ) : null;
$post 		= ( is_numeric( $object_id ) ) ? get_post( $object_id ) : null;

// Get basic information for the product 
$product_title     			= ( isset($product) && null !== $product ) ? $product->get_title()  : ''; 
$product_description        = ( isset($product) && null !== $product ) ? $post->post_content  	: ''; 
$product_short_description  = ( isset($product) && null !== $product ) ? $post->post_excerpt  	: ''; 
$post_status				= ( isset($product) && null !== $product ) ? $post->post_status   	: ''; 

/**
 *  Ok, You can edit the template below but be careful!
*/
?>

<h2><?php echo $title; ?></h2>

<!-- Product Edit Form -->
<form method="post" action="" id="wcv-product-edit" class="wcv-form wcv-formvalidator"> 

	<!-- Basic Product Details -->
	<div class="wcv-product-basic wcv-product"> 
		<!-- Product Title -->
		<?php WCVendors_Pro_Product_Form::title( $object_id, $product_title ); ?>
		<!-- Product Description -->
		<?php WCVendors_Pro_Product_Form::description( $object_id, $product_description );  ?>
		<!-- Product Categories -->
	    <?php WCVendors_Pro_Product_Form::categories( $object_id, true ); ?>
	</div>

	<div class="all-100">
		<!-- Media uploader -->
		<div class="wcv-product-media">
			<?php do_action( 'wcv_before_media', $object_id ); ?>
			<?php BuddyBoss_BM_Templates::product_media_uploader( $object_id ); ?>
			<?php do_action( 'wcv_after_media', $object_id ); ?>
		</div>
	</div>

	<hr />
	
	<!-- Price and Sale Price -->
	<?php WCVendors_Pro_Product_Form::prices( $object_id ); ?>

	<!-- SKU  -->
	<?php WCVendors_Pro_Product_Form::sku( $object_id ); ?>
	<!-- Private listing  -->
	<?php WCVendors_Pro_Product_Form::private_listing( $object_id ); ?>

	<div id="files_download">
		<!-- Downloadable files -->
		<?php WCVendors_Pro_Product_Form::download_files( $object_id ); ?>
		<!-- Download Limit -->
		<?php WCVendors_Pro_Product_Form::download_limit( $object_id ); ?>
		<!-- Download Expiry -->
		<?php WCVendors_Pro_Product_Form::download_expiry( $object_id ); ?>
		<!-- Download Type -->
		<?php WCVendors_Pro_Product_Form::download_type( $object_id ); ?>
	</div>


	<!-- Product Type  -->
	<?php WCVendors_Pro_Product_Form::product_type_hidden( $object_id, 'simple' ); ?>
	<!-- Virtual Product -->
	<?php WCVendors_Pro_Product_Form::virtual_product_hidden( $object_id ); ?>
	<!-- Downloadable Product -->
	<?php WCVendors_Pro_Product_Form::downloadable_product_hidden( $object_id ); ?>

	<?php WCVendors_Pro_Product_Form::form_data( $object_id, $post_status, $template ); ?>
	<?php WCVendors_Pro_Product_Form::save_button( $title ); ?>
	<?php WCVendors_Pro_Product_Form::draft_button( __('Save Draft','buddyboss-marketplace') ); ?>

			</div>
		</div>
</form>