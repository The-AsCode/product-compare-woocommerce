<?php

namespace ProductCompare\Frontend;

class Enqueue {
    public function __construct() {

        add_action('wp_ajax_add_to_compare', [$this, 'add_to_compare']); // For logged-in users
        add_action('wp_ajax_nopriv_add_to_compare', [$this, 'add_to_compare']); // For logged-out users

        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_scripts']); // Enqueue scripts
    }

    public function enqueue_frontend_scripts() {
        // Enqueue the frontend JavaScript file.
        if ( is_shop() ) {
			wp_enqueue_script(
				'product-compare-frontend',
				PRODUCT_COMPARE_ASSETS . '/js/frontend/compare-product-script.js', 
				['jquery'], 
				1.0, 
				true
			);

			// Localize the JavaScript script with necessary data.
			wp_localize_script(
				'product-compare-frontend',
				'product_compare_ajax_object',
				[
					'ajax_url'   => esc_url(admin_url('admin-ajax.php')), // Properly escaped URL
					'ajax_nonce' => wp_create_nonce('product-compare-show-compare') // Nonce for security
				]
			);
		}
	}

    public function add_to_compare() {

        // Verify and sanitize the nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'product-compare-show-compare')) {
            wp_send_json_error('Invalid nonce');
            wp_die();
        }

        // Sanitize the product_id
        $product_id = isset($_POST['product_id']) ? intval( wp_unslash( $_POST['product_id'] ) ) : 0;

        if ( $product_id === 0 ) {
            wp_send_json_error('Invalid product ID');
            wp_die();
        }

        // Initialize comparison array in session if it doesn't exist
        if (!isset($_SESSION['compare_products'])) {
            $_SESSION['compare_products'] = array();
        }

        // Add product to comparison session if not already there
        if (!in_array($product_id, $_SESSION['compare_products'])) {
            $_SESSION['compare_products'][] = $product_id;
            wp_send_json_success('Product added to comparison');
        } else {
            wp_send_json_error('Product is already in comparison');
        }

        wp_die();
    }

}
