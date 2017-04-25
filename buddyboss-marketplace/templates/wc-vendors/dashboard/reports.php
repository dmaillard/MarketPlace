<h2><?php _e( 'Sales Report', 'buddyboss-marketplace' ); ?></h2>

<?php

if ( $datepicker !== 'false' ) {
	wc_get_template( 'date-picker.php', array(
													  'start_date' => $start_date,
													  'end_date'   => $end_date,
												 ), 'wc-vendors/dashboard/', wcv_plugin_dir . 'templates/dashboard/' );
}

?>

<table class="table table-condensed table-vendor-sales-report report">
	<thead>
	<tr>
	<th class="product-header"><?php _e( 'Product', 'buddyboss-marketplace' ); ?></th>
	<th class="quantity-header"><?php _e( 'Quantity', 'buddyboss-marketplace' ) ?></th>
	<th class="commission-header"><?php _e( 'Commission', 'buddyboss-marketplace' ) ?></th>
	<th class="rate-header"><?php _e( 'Rate', 'buddyboss-marketplace' ) ?></th>
	<th></th>
	</thead>
	<tbody>

	<?php if ( !empty( $vendor_summary ) ) : ?>


		<?php if ( !empty( $vendor_summary[ 'products' ] ) ) : ?>

			<?php foreach ( $vendor_summary[ 'products' ] as $product ) :
				$_product = get_product( $product[ 'id' ] ); ?>

				<tr>

					<td class="product" data-th="<?php _e( 'Product', 'buddyboss-marketplace' ); ?>"><strong><a
								href="<?php echo esc_url( get_permalink( $_product->id ) ) ?>"><?php echo $product[ 'title' ] ?></a></strong>
						<?php if ( !empty( $_product->variation_id ) ) {
							echo woocommerce_get_formatted_variation( $_product->variation_data );
						} ?>
					</td>
					<td class="qty" data-th="<?php _e( 'Quantity', 'buddyboss-marketplace' ) ?>"><?php echo $product[ 'qty' ]; ?></td>
					<td class="commission" data-th="<?php _e( 'Commission', 'buddyboss-marketplace' ) ?>"><?php echo woocommerce_price( $product[ 'cost' ] ); ?></td>
					<td class="rate" data-th="<?php _e( 'Rate', 'buddyboss-marketplace' ) ?>"><?php echo sprintf( '%.2f%%', $product[ 'commission_rate' ] ); ?></td>

					<?php if ( $can_view_orders ) : ?>
						<td>
							<a href="<?php echo $product[ 'view_orders_url' ]; ?>"><?php _e( 'Show Orders', 'buddyboss-marketplace' ); ?></a>
						</td>
					<?php endif; ?>

				</tr>

			<?php endforeach; ?>

			<tr>
				<td><strong><?php _e( 'Totals', 'buddyboss-marketplace' ); ?></strong></td>
				<td data-th="<?php _e( 'Quantity', 'buddyboss-marketplace' ) ?>"><?php echo $vendor_summary[ 'total_qty' ]; ?></td>
				<td data-th="<?php _e( 'Commission', 'buddyboss-marketplace' ) ?>"><?php echo woocommerce_price( $vendor_summary[ 'total_cost' ] ); ?></td>
				<td></td>

				<?php if ( $can_view_orders ) : ?>
					<td></td>
				<?php endif; ?>

			</tr>

		<?php else : ?>

			<tr>
				<td colspan="4" class="empty"><?php _e( 'You have no sales during this period.', 'buddyboss-marketplace' ); ?></td>
			</tr>

		<?php endif; ?>



	<?php else : ?>

		<tr>
			<td colspan="4" class="empty"><?php _e( 'You haven\'t made any sales yet.', 'buddyboss-marketplace' ); ?></td>
		</tr>

	<?php endif; ?>

	</tbody>
</table>
