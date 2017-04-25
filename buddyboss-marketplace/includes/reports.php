<?php

class BM_Reports_Controller extends WCVendors_Pro_Reports_Controller
{
    public function __construct() {
        // call Grandpa's constructor
        parent::__construct('wcvendors-pro', WCV_PRO_VERSION, false);
    }

    public function get_product_chart_data()
    {

        $grouped_products = array();
        $wcv_orders = $this->get_filtered_orders();

        // Group the orders by date and get total orders for that date
        foreach ($wcv_orders as $order) {

        // Make sure the order exists before attempting to loop over it.
        if (is_object($order->order) && is_array( $order->order_items ) ) {

        foreach ($order->order_items as $item) {

        if (!array_key_exists($item['name'], $grouped_products)) {
        $grouped_products[$item['name']] = array();
        }

        if (is_array($grouped_products[$item['name']]) && !array_key_exists('total', $grouped_products[$item['name']])) {
        $grouped_products[$item['name']] = array('total' => 0);
        }

        $grouped_products[$item['name']]['total'] += $item['line_total'];

        }
        }
        }

        $chart_data = array();

        // create the pie chart data, color and highlight are currently randomly generated
        foreach ($grouped_products as $label => $total) {

        $chart_data[] = array(
        'value' => reset($total),
        'color' => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
        'label' => $label
        );
        }

        if (empty($chart_data)) return false;

        return json_encode($chart_data);

    } // get_product_chart_data()
}