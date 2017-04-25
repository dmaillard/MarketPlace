<?php
/**
 * @package		WordPress
 * @subpackage	BuddyBoss MarketPlace
 */

?>
<div id="item-body" role="main">

	<?php do_action( 'bm_before_track_body' ); ?>

	<h3><?php _e( 'Track your order', 'buddyboss-marketplace' ); ?></h3>

	<?php do_action( 'bm_after_track_heading' ); ?>

    <?php echo do_shortcode( '[woocommerce_order_tracking]' ); ?>

	<?php do_action( 'bm_after_track_body' ); ?>

</div><!-- #item-body -->