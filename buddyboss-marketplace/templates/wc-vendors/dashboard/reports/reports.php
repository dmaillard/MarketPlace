<?php
/**
 * The template for displaying the vendor store graphs, recent products and recent orders
 *
 * Override this template by copying it to yourtheme/wc-vendors/dashboard/report
 *
 * @package    WCVendors_Pro
 * @version    1.2.3
 */
?>

<div class="wcv_reports wcv-cols-group wcv-horizontal-gutters">

    <div class="all-50 small-100 tiny-100">
        <br />
        <h3><?php _e( 'Orders Totals', 'buddyboss-marketplace'); ?> ( <?php echo $store_report->total_orders; ?> )</h3>
        <hr />
        <?php $order_chart_data = $store_report->get_order_chart_data(); ?>

        <?php if ( !$order_chart_data ) : ?>
            <p><?php _e( 'No orders for this period. Adjust your dates above and click Update, or list new products for customers to buy.', 'buddyboss-marketplace'); ?></p>
        <?php else : ?>
            <canvas id="orders_chart" width="350" height="200"></canvas>
            <script type="text/javascript">
                var orders_chart_labelBM = <?php echo $order_chart_data['labels']; ?>;
                var orders_chart_data = <?php echo $order_chart_data['data']; ?>;
            </script>

        <?php endif; ?>
    </div>

    <div class="all-50 small-100 tiny-100">
        <br />
        <h3><?php _e( 'Product Totals', 'buddyboss-marketplace'); ?> ( <?php echo $store_report->total_products_sold; ?> )</h3>
        <hr />
        <div>
            <?php //$product_chart_data = $store_report->get_product_chart_data(); ?>
            <?php
            $bm_store_report = new BM_Reports_Controller();
            $product_chart_data = $bm_store_report->get_product_chart_data();
            ?>
            <div class="cart-holder <?php echo (!$product_chart_data)?'without-cart':'with-cart'; ?>">

            <?php if ( !$product_chart_data ) : ?>
                <p><?php _e( 'No sales for this period. Adjust your dates above and click Update, or list new products for customers to buy.', 'buddyboss-marketplace'); ?></p>
            <?php else : ?>

                <canvas id="products_chart" width="250" height="250"></canvas>
                <script type="text/javascript">var pieDataBM = <?php echo $product_chart_data; ?></script>
            <?php endif; ?>
            </div>
            <div id="chartjs-tooltip"></div>
        </div>
    </div>

</div>

<div class="wcv_recent wcv_recent_orders wcv-cols-group wcv-horizontal-gutters">
    <div class="xlarge-50 large-50 medium-100 small-100 tiny-100">
        <h3><?php _e( 'Recent Orders', 'buddyboss-marketplace'); ?></h3>
        <?php $recent_orders = $store_report->recent_orders_table(); ?>

        <?php if ( ! $orders_disabled ) : ?>
            <?php if ( !empty( $recent_orders )  ) : ?>
                <a href="<?php echo WCVendors_Pro_Dashboard::get_dashboard_page_url('order'); ?>" class="wcv-button button"><?php _e( 'View All', 'buddyboss-marketplace'); ?></a>
            <?php endif; ?>
        <?php endif; ?>
    </div>


    <div class="xlarge-50 large-50 medium-100 small-100 tiny-100">
        <h3><?php _e( 'Recent Products', 'buddyboss-marketplace'); ?></h3>
        <?php $recent_products = $store_report->recent_products_table(); ?>
        <?php if ( ! $products_disabled ) : ?>
            <?php if ( !empty( $recent_products ) ) : ?>
                <a href="<?php echo WCVendors_Pro_Dashboard::get_dashboard_page_url('product'); ?>" class="wcv-button button"><?php _e( 'View All', 'buddyboss-marketplace'); ?></a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>