<div class="pv_paypal_container">
	<p>
		<label for="pv_paypal"><?php _e( 'PayPal Address', 'buddyboss-marketplace' ); ?></label>
		<span><?php _e( 'Your PayPal address is used to send you your commission.', 'buddyboss-marketplace' ); ?></span>

		<input type="email" name="pv_paypal" id="pv_paypal" placeholder="some@email.com"
			   value="<?php echo get_user_meta( $user_id, 'pv_paypal', true ); ?>"/>
	</p>
</div>
