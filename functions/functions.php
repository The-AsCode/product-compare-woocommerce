<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Initialize session to store comparison data
function wpc_init_session() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'wpc_init_session');

// Add "Compare" button to product listings
add_action('woocommerce_after_shop_loop_item', 'wpc_add_compare_button');
function wpc_add_compare_button() {
    echo '<a href="#" class="button add-to-compare" data-product-id="' . get_the_ID() . '">Compare</a>';
}

// Display comparison page via shortcode
add_shortcode('wpc_comparison', 'wpc_display_comparison');
function wpc_display_comparison() {
    // Retrieve compared products from session
    $compare_products = isset($_SESSION['compare_products']) ? $_SESSION['compare_products'] : array();

    if (empty($compare_products)) {
        return 'No products in comparison list.';
    }

    ob_start();

    echo '<table class="shop_table compare-table">';
    echo '<tr><th>Product</th><th>Price</th><th>Stock</th></tr>';

    foreach ($compare_products as $product_id) {
        $product = wc_get_product($product_id);
        echo '<tr>';
        echo '<td>' . $product->get_name() . '</td>';
        echo '<td>' . wc_price($product->get_price()) . '</td>';
        echo '<td>' . ($product->is_in_stock() ? 'In Stock' : 'Out of Stock') . '</td>';
        echo '</tr>';
    }

    echo '</table>';

    return ob_get_clean();
}

