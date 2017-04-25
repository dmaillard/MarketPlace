<?php
/**
 * The template for showing the vendor store product pagination 
 *
 * Override this template by copying it to yourtheme/wc-vendors/store
 *
 * @package    WCVendors_Pro
 * @version    1.0.3
 */

// Products Pagination 
$product_paged_args = array(
    'format'  => '?product_paged=%#%',
    'current' => $current,
    'total'   => $total,
    'prev_text'    => '&larr;',
    'next_text'    => '&rarr;',
    'type'         => 'list',
    'end_size'     => 3,
    'mid_size'     => 3, 
);
?>

<nav class="woocommerce-pagination">
	<?php echo paginate_links( $product_paged_args ); ?>
</nav>