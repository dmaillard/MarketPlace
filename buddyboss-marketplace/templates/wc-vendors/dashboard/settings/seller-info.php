<div id="pv_seller_info_container">
	<p>
		<label><?php echo apply_filters( 'wcvendors_seller_info_label', __( 'Seller info', 'buddyboss-marketplace' ) ); ?></label>
		<span><?php _e( 'This is displayed on each of your products.', 'buddyboss-marketplace' ); ?></span>
	</p>

	<p>
		<?php

		if ( $global_html || $has_html ) {
			$old_post          = $GLOBALS[ 'post' ];
			$GLOBALS[ 'post' ] = 0;
			wp_editor( $seller_info, 'pv_seller_info' );
			$GLOBALS[ 'post' ] = $old_post;
		} else {
			?><textarea class="large-text" rows="10" id="pv_seller_info_unhtml" style="width:95%"
						name="pv_seller_info"><?php echo $seller_info; ?></textarea><?php
		}

		?>
	</p>
</div>
