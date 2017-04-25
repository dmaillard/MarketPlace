<?php
/**
 * The template for displaying the shop coupon form
 *
 * Override this template by copying it to yourtheme/wc-vendors/dashboard/
 *
 * @package    WCVendors_Pro
 * @version    1.2.3
 */

/**
 *   DO NOT EDIT ANY OF THE LINES BELOW UNLESS YOU KNOW WHAT YOU'RE DOING
 *
 */

$title = 	( is_numeric( $object_id ) ) ? __('Save Changes', 'buddyboss-marketplace') : __('Add Coupon', 'buddyboss-marketplace');
$coupon = 	( is_numeric( $object_id ) ) ? get_post( $object_id ) : null;

// Get basic information for the coupon
$coupon_title     			= ( is_object( $coupon ) ) ? $coupon->post_title    : '';
$coupon_description        	= ( is_object( $coupon ) ) ? $coupon->post_excerpt  : '';
$coupon_meta				= array();

if ( $object_id ) {
    $coupon_meta				= get_post_meta( $object_id );
}

/**
 *  Ok, You can edit the template below but be careful!
 */
?>

<h2><?php echo $title; ?></h2>

<!-- Product Edit Form -->
<form method="post" action="" id="wcv-shop_coupon-edit" class="wcv-form">

    <!-- Coupon Code -->
    <?php WCVendors_Pro_Coupon_Form::coupon_code( $coupon_title ); ?>
    <!-- Coupon description -->
    <?php WCVendors_Pro_Coupon_Form::coupon_description( $coupon_description ); ?>

    <div class="wcv-tabs top" data-prevent-url-change="true">
        <ul class="tabs-nav">
            <li><a class="tabs-tab" href="#general"><?php _e( 'General', 'buddyboss-marketplace'); ?></a></li>
            <li><a class="tabs-tab" href="#usage"><?php _e( 'Usage Restrictions', 'buddyboss-marketplace'); ?></a></li>
            <li><a class="tabs-tab" href="#limits"><?php _e( 'Usage Limits', 'buddyboss-marketplace'); ?></a></li>
        </ul>

        <div class="wcv-coupon-general tabs-content" id="general">
            <div class="bm-field-groups">
                <!-- Discount Type -->
                <?php WCVendors_Pro_Coupon_Form::discount_type( ( array_key_exists( 'discount_type', $coupon_meta ) ) ? $coupon_meta[ 'discount_type' ] : '' ); ?>
                <!-- Apply to all products -->
                <?php WCVendors_Pro_Coupon_Form::apply_to_all_products( ( array_key_exists( 'apply_to_all_products', $coupon_meta ) ) ? $coupon_meta[ 'apply_to_all_products' ] : '' ); ?>
            </div>
            <div class="bm-field-groups">
            <!-- Coupon Amount  -->
            <?php WCVendors_Pro_Coupon_Form::coupon_amount( ( array_key_exists( 'coupon_amount', $coupon_meta ) ) ? $coupon_meta[ 'coupon_amount' ] : '' ); ?>
            <!-- Allow Free Shipping -->
            <?php WCVendors_Pro_Coupon_Form::free_shipping( ( array_key_exists( 'free_shipping', $coupon_meta ) ) ? $coupon_meta[ 'free_shipping' ] : '' ); ?>
            </div>
            <!-- Coupon Expiry -->
            <?php WCVendors_Pro_Coupon_Form::expiry_date( ( array_key_exists( 'expiry_date', $coupon_meta ) ) ? $coupon_meta[ 'expiry_date' ] : '' ); ?>
        </div>


        <div class="wcv-coupon-usage-restrictions tabs-content" id="usage">
            <div class="bm-field-groups">
            <!-- Min spend -->
                <?php WCVendors_Pro_Coupon_Form::minimum_amount( ( array_key_exists( 'minimum_amount', $coupon_meta ) ) ? $coupon_meta[ 'minimum_amount' ] : '' ); ?>
                <!-- individual use -->
                <?php WCVendors_Pro_Coupon_Form::individual_use( ( array_key_exists( 'individual_use', $coupon_meta ) ) ? $coupon_meta[ 'individual_use' ] : '' ); ?>
            </div>
            <div class="bm-field-groups">
            <!-- Max spend  -->
                <?php WCVendors_Pro_Coupon_Form::maximum_amount( ( array_key_exists( 'maximum_amount', $coupon_meta ) ) ? $coupon_meta[ 'maximum_amount' ] : '' ); ?>
                <!-- exclude sale -->
            <?php WCVendors_Pro_Coupon_Form::exclude_sale_items( ( array_key_exists( 'exclude_sale_items', $coupon_meta ) ) ? $coupon_meta[ 'exclude_sale_items' ] : '' ); ?>
            <!-- Products  -->
            </div>
            <div class="bm-field-groups search">
            <label>
                <?php WCVendors_Pro_Coupon_Form::products( ( array_key_exists( 'product_ids', $coupon_meta ) ) ? $coupon_meta[ 'product_ids' ] : '' ); ?>
            </label>
            <!-- exclude Products -->
            </div>
            <div class="bm-field-groups search">
            <label>
                <?php WCVendors_Pro_Coupon_Form::exclude_products( ( array_key_exists( 'exclude_product_ids', $coupon_meta ) ) ? $coupon_meta[ 'exclude_product_ids' ] : '' ); ?>
            </label>
            </div>

            <div class="bm-field-groups">
            <!-- Need to sort out shop categories  -->
            <!-- Product Categories  -->
            <!-- Exclude categories -->
            <!-- Email restrictions -->
            <?php WCVendors_Pro_Coupon_Form::email_addresses( ( array_key_exists( 'email_addresses', $coupon_meta ) ) ? $coupon_meta[ 'email_addresses' ] : '' ); ?>
            </div>
        </div>

        <div class="wcv-coupon-usage-limits tabs-content" id="limits">
            <div class="bm-field-groups">
            <!-- Usage limit per coupon -->
            <?php WCVendors_Pro_Coupon_Form::usage_limit( ( array_key_exists( 'usage_limit', $coupon_meta ) ) ? $coupon_meta[ 'usage_limit' ] : '' ); ?>
            <!-- Limit usage to X items -->
            </div>

            <div class="bm-field-groups">
            <!-- Usage limit per user -->
            <?php WCVendors_Pro_Coupon_Form::usage_limit_per_user( ( array_key_exists( 'usage_limit_per_user', $coupon_meta ) ) ? $coupon_meta[ 'usage_limit_per_user' ] : '' ); ?>
            </div>
        </div>

        <!-- Form data -->
        <?php WCVendors_Pro_Coupon_Form::form_data( $title, $object_id ); ?>

</form>
