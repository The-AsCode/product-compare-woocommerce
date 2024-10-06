<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! function_exists( 'pcwc_init_session' ) ) {
    function pcwc_init_session() {
        if (!session_id()) {
            session_start();
        }
    }
    add_action('init', 'pcwc_init_session');
}

// Add "Compare" button to product page
if( !function_exists( 'pcwc_add_compare_button')) {
    function pcwc_add_compare_button() {
        echo '<a href="#" class="button add-to-compare" data-product-id="' . esc_attr( get_the_ID() ) . '">Compare</a>';
    }
    add_action('woocommerce_after_shop_loop_item', 'pcwc_add_compare_button');
}



// Display comparison page via shortcode
if( ! function_exists( 'pcwc_display_comparison' ) ){
    add_shortcode('pcwc_comparison', 'pcwc_display_comparison');
    function pcwc_display_comparison() {
        // Retrieve compared products from session
        $compare_products = isset($_SESSION['compare_products']) ? $_SESSION['compare_products'] : array();

        if (empty($compare_products)) {
            return 'No products in comparison list.';
        }

        ob_start();

        echo '<table class="shop_table compare-table">';
        echo '<tr><th>Product</th><th>Price</th><th>Stock</th></tr>';

        foreach ( $compare_products as $product_id ) {
            $product = wc_get_product( intval( $product_id ) );
            if ( ! $product ) {
                continue; // Skip invalid product
            }
            echo '<tr>';
            echo '<td>' . esc_html( $product->get_name() ) . '</td>';
            echo '<td>' . wc_price( $product->get_price() ) . '</td>';
            echo '<td>' . ( $product->is_in_stock() ? 'In Stock' : 'Out of Stock' ) . '</td>';
            echo '</tr>';
        }

        echo '</table>';

        return ob_get_clean();
    }
}

