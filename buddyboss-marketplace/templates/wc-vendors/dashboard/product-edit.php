<?php
/**
 * The template for displaying the Product edit form
 *
 * Override this template by copying it to yourtheme/wc-vendors/dashboard/
 *
 * @package    WCVendors_Pro
 * @version    1.3.0
 */
/**
 *   DO NOT EDIT ANY OF THE LINES BELOW UNLESS YOU KNOW WHAT YOU'RE DOING
 *
*/

$title   = 	( is_numeric( $object_id ) ) ? __('Save Changes', 'buddyboss-marketplace') : __('Add Product', 'buddyboss-marketplace');
$product = 	( is_numeric( $object_id ) ) ? wc_get_product( $object_id ) : null;
$post 	 =  ( is_numeric( $object_id ) ) ? get_post( $object_id ) : null;

// Get basic information for the product
$product_title     			= ( isset($product) && null !== $product ) ? $product->get_title()    : '';
$product_description        = ( isset($product) && null !== $product ) ? $post->post_content  : '';
$product_short_description  = ( isset($product) && null !== $product ) ? $post->post_excerpt  : '';
$post_status				= ( isset($product) && null !== $product ) ? $post->post_status   : '';

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
				<!-- Product Short Description -->
				<?php WCVendors_Pro_Product_Form::short_description( $object_id, $product_short_description );  ?>
				<!-- Product Categories -->
				<?php WCVendors_Pro_Product_Form::categories( $object_id, true ); ?>
				<!-- Colors -->
				<?php
				if(class_exists('NM_Color_Filters')) {
					$basic_options 		= (array) WC_Vendors::$pv_options->get_option( 'hide_product_basic' );
					if( ! isset($basic_options[ 'colors' ] ) || ! $basic_options[ 'colors' ] ) {
						BuddyBoss_BM_Templates::colors($object_id, true);
					}
				}
				?>
				<!-- Product Tags -->
				<?php WCVendors_Pro_Product_Form::tags( $object_id, true ); ?>
	</div>

	<div class="all-100">
    	<!-- Media uploader -->
		<div class="wcv-product-media">
			<?php do_action( 'wcv_before_media', $object_id ); ?>
			<?php BuddyBoss_BM_Templates::product_media_uploader( $object_id ); ?>
			<?php do_action( 'wcv_after_media', $object_id ); ?>
		</div>
	</div>

	<div class="all-100">
		<?php do_action( 'wcv_before_product_type', $object_id ); ?>
		<!-- Product Type -->
		<div class="wcv-product-type">
			<?php WCVendors_Pro_Product_Form::product_type( $object_id ); ?>
		</div>
	</div>

	<div class="all-100">
		<div class="wcv-tabs top" data-prevent-url-change="true">

			<?php WCVendors_Pro_Product_Form::product_meta_tabs( ); ?>

			<?php do_action( 'wcv_before_general_tab', $object_id ); ?>

			<!-- General Product Options -->
			<div class="wcv-product-general tabs-content" id="general">

				<div class="hide_if_grouped">
					<!-- SKU  -->
					<?php WCVendors_Pro_Product_Form::sku( $object_id ); ?>
                    <!-- Private listing  -->
					<?php WCVendors_Pro_Product_Form::private_listing( $object_id ); ?>
				</div>

				<div class="options_group show_if_external">
					<?php WCVendors_Pro_Product_Form::external_url( $object_id ); ?>
					<?php WCVendors_Pro_Product_Form::button_text( $object_id ); ?>
				</div>

				<div class="show_if_simple show_if_external no-tip">
					<!-- Price and Sale Price -->
					<?php WCVendors_Pro_Product_Form::prices( $object_id ); ?>
				</div>

				<div class="show_if_simple show_if_external show_if_variable">
					<!-- Tax -->
					<?php WCVendors_Pro_Product_Form::tax( $object_id ); ?>
				</div>

				<div class="show_if_downloadable" id="files_download">
					<!-- Downloadable files -->
					<?php WCVendors_Pro_Product_Form::download_files( $object_id ); ?>
					<!-- Download Limit -->
					<?php WCVendors_Pro_Product_Form::download_limit( $object_id ); ?>
					<!-- Download Expiry -->
					<?php WCVendors_Pro_Product_Form::download_expiry( $object_id ); ?>
					<!-- Download Type -->
					<?php WCVendors_Pro_Product_Form::download_type( $object_id ); ?>
				</div>

				<?php do_action( 'wcv_product_options_general_product_data', $object_id ); ?>

			</div>

			<?php do_action( 'wcv_after_general_tab', $object_id ); ?>

			<?php do_action( 'wcv_before_inventory_tab', $object_id ); ?>

			<!-- Inventory -->
			<div class="wcv-product-inventory inventory_product_data tabs-content" id="inventory">

				<?php WCVendors_Pro_Product_Form::manage_stock( $object_id ); ?>

				<?php do_action( 'wcv_product_options_stock' ); ?>

				<div class="stock_fields show_if_simple show_if_variable">
					<?php WCVendors_Pro_Product_Form::stock_qty( $object_id ); ?>
					<?php WCVendors_Pro_Product_Form::backorders( $object_id ); ?>
				</div>

				<?php WCVendors_Pro_Product_Form::stock_status( $object_id ); ?>
				<div class="options_group show_if_simple show_if_variable">
					<?php WCVendors_Pro_Product_Form::sold_individually( $object_id ); ?>
				</div>

				<?php do_action( 'wcv_product_options_sold_individually', $object_id ); ?>

				<?php do_action( 'wcv_product_options_inventory_product_data', $object_id ); ?>

			</div>

			<?php do_action( 'wcv_after_inventory_tab', $object_id ); ?>

			<?php do_action( 'wcv_before_shipping_tab', $object_id ); ?>

			<!-- Shipping  -->
			<div class="wcv-product-shipping shipping_product_data tabs-content" id="shipping">

				<div class="hide_if_grouped hide_if_external">

					<!-- Shipping rates  -->
					<?php WCVendors_Pro_Product_Form::shipping_rates( $object_id ); ?>
					<!-- weight  -->
					<?php WCVendors_Pro_Product_Form::weight( $object_id ); ?>
					<!-- Dimensions -->
					<?php WCVendors_Pro_Product_Form::dimensions( $object_id ); ?>
					<?php do_action( 'wcv_product_options_dimensions' ); ?>
					<!-- shipping class -->
					<?php WCVendors_Pro_Product_Form::shipping_class( $object_id ); ?>
					<?php do_action( 'wcv_product_options_shipping', $object_id ); ?>

					<?php do_action( 'wcv_product_options_shipping_data_panel', $object_id ); ?>
				</div>

			</div>

			<?php do_action( 'wcv_after_shipping_tab', $object_id ); ?>

			<?php do_action( 'wcv_before_linked_tab', $object_id ); ?>

			<!-- Upsells and grouping -->
			<div class="wcv-product-upsells tabs-content" id="linked_product">

				<?php WCVendors_Pro_Product_Form::up_sells( $object_id ); ?>

				<?php WCVendors_Pro_Product_Form::crosssells( $object_id ); ?>

				<div class="hide_if_grouped hide_if_external">

					<?php WCVendors_Pro_Product_Form::grouped_products( $object_id, $product ); ?>

				</div>

				<?php do_action( 'wcv_product_options_upsells_product_data' ); ?>
			</div>

			<?php do_action( 'wcv_after_linked_tab', $object_id ); ?>

			<!-- Attributes -->

			<?php do_action( 'wcv_before_attributes_tab', $object_id ); ?>

			<div class="wcv_product_attributes tabs-content" id="attributes">

				<?php WCVendors_Pro_Product_Form::product_attributes( $object_id ); ?>

				<?php do_action( 'wcv_product_options_attributes_product_data' ); ?>

			</div>

			<?php do_action( 'wcv_after_attributes_tab', $object_id ); ?>

			<!-- Variations -->

	        <?php do_action( 'wcv_before_variations_tab', $object_id ); ?>

          	<div class="wcv_product_variations tabs-content" id="variations">

              <?php WCVendors_Pro_Product_Form::product_variations( $object_id ); ?>

			  <?php do_action( 'wcv_product_options_variations_product_data' ); ?>

			</div>

			<?php do_action( 'wcv_after_variations_tab', $object_id ); ?>

			<?php WCVendors_Pro_Product_Form::form_data( $object_id, $post_status ); ?>
			<?php WCVendors_Pro_Product_Form::save_button( $title ); ?>
			<?php WCVendors_Pro_Product_Form::draft_button( __('Save Draft', 'buddyboss-marketplace') ); ?>

			</div>
		</div>
</form>
