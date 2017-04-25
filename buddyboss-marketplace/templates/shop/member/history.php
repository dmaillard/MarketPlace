<?php
/**
 * @package		WordPress
 * @subpackage	BuddyBoss MarketPlace
 */
?>

<div id="item-body" role="main">
	<?php do_action( 'woocommerce_before_my_account' ); ?>

	<div class="woocommerce">
	<?php do_action( 'bm_before_history_body' ); ?>

	<?php do_shortcode('[bm_my_recent_orders]') ?>

    <?php do_action( 'woocommerce_after_my_account' ); ?>
	</div>

	<?php do_action( 'bm_after_history_body' ); ?>

</div><!-- #item-body -->
