<?php

namespace ProductCompare\Backend;

/**
 * Class Menu
 * @package ProductCompare\Backend
 */
class Menu {
    /**
     * Menu constructor.
     */
    public function __construct() {
        // add_action('admin_menu', [$this, 'add_menu']);
    }

    /**
     * Add menu
     * 
     * @return void
     */
    public function add_menu() {
        add_menu_page(
            'Product Compare',
            'Product Compare',
            'manage_options',
            'product-compare',
            [$this, 'product_compare_page'],
            'dashicons-cart',
            10
        );
    }

    public function product_compare_page() {
        include PRODUCT_COMPARE_PATH . '/backend/views/admin-view.php';
    }
}