<?php
/**
 * Show options for ordering
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="store-filters table">
	<?php
		$vendor_shop 		= urldecode( get_query_var( 'vendor_shop' ) );
		if( is_shop() && $vendor_shop ) {
	?>
	<form id="search-shops" role="search" action="<?php echo site_url( WC_Vendors::$pv_options->get_option( 'vendor_shop_permalink' ) .$vendor_shop ); ?>" method="get" class="table-cell page-search">
		<input type="text" name="bm_store_search" placeholder="<?php _e('Search in this shop', 'buddyboss-marketplace'); ?>" value="<?php echo $searchby; ?>"/>
		<input type="submit" alt="Search" value="<?php _e('Search', 'buddyboss-marketplace'); ?>" />
	</form>
	<?php } ?>
	<form id="filter-shops" class="woocommerce-ordering table-cell filter-dropdown" method="get">
		<label><?php _e('Sort by:', 'buddyboss-marketplace'); ?></label>
		<select name="orderby" class="orderby">
			<?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
				<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
		// Keep query string vars intact
		foreach ( $_GET as $key => $val ) {
			if ( 'orderby' === $key || 'submit' === $key ) {
				continue;
			}
			if ( is_array( $val ) ) {
				foreach( $val as $innerVal ) {
					echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $innerVal ) . '" />';
				}
			} else {
				echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
			}
		}
		?>
	</form>
</div>
