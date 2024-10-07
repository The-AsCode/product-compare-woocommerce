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
        // Retrieve compared products from session and sanitize them
        $compare_products = isset($_SESSION['compare_products']) ? array_map('intval', $_SESSION['compare_products']) : array();

        if (empty($compare_products)) {
            return esc_html__('No products in comparison list.', 'product-comparison-for-woocommerce');
        }

        ob_start();

        echo '<table class="shop_table compare-table">';
        echo '<tr><th>' . esc_html__('Product', 'product-comparison-for-woocommerce') . '</th><th>' . esc_html__('Price', 'product-comparison-for-woocommerce') . '</th><th>' . esc_html__('Stock', 'product-comparison-for-woocommerce') . '</th></tr>';

        foreach ( $compare_products as $product_id ) {
            $product = wc_get_product( $product_id );
            if ( ! $product ) {
                continue; // Skip invalid product
            }
            echo '<tr>';
            echo '<td>' . esc_html( $product->get_name() ) . '</td>';
            echo '<td>' . wp_kses_post( wc_price( $product->get_price() ) ) . '</td>';
            echo '<td>' . esc_html( $product->is_in_stock() ? __('In Stock', 'product-comparison-for-woocommerce') : __('Out of Stock', 'product-comparison-for-woocommerce') ) . '</td>';
            echo '</tr>';
        }

        echo '</table>';

        return ob_get_clean();
    }
}