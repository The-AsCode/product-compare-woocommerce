<?php
/**
 * Plugin Name: Product Compare
 * Plugin URI: https://osmanhaideradnan.wordpress.com/
 * Description: A simple product comparison plugin for WooCommerce.
 * Version: 1.0.0
 * Author: Shop ManagerX
 * Author URI: https://osmanhaideradnan.wordpress.com/
 * License: GPL2
 * requires at least: 3.5
 * Tested up to: 4.0
 * Text Domain: product-compare
 * Domain Path: /languages/
 * Requires Plugins: woocommerce
 **/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Check if the Composer autoload file exists, and if not, show an error message.
if ( ! file_exists(__DIR__ . '/vendor/autoload.php' ) ) {
    die('Please run `composer install` in the main plugin directory.');
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Plugin main class
 */
final class Product_Compare {
    const product_compare_version = '1.0.0';

    // Private constructor to enforce singleton pattern.
    private function __construct() {
        $this->define_constants();

        // Register activation hook.
        register_activation_hook(__FILE__, [$this, 'activate']);

        // Hook into the upgrader process to handle plugin updates
        add_action('upgrader_process_complete', array($this, 'update'), 10, 2);

        // Hook into the 'plugins_loaded' action to initialize the plugin.
        add_action('plugins_loaded', [$this, 'init_plugin']);
    }

    //public static function init() {
    public static function init() {
        static $instance = false;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    // Define the plugin constants.
    private function define_constants() {
        define('PRODUCT_COMPARE_VERSION', self::product_compare_version);
        define('PRODUCT_COMPARE_FILE', __FILE__);
        define('PRODUCT_COMPARE_PATH', __DIR__);
        define('PRODUCT_COMPARE_URL', plugins_url('', PRODUCT_COMPARE_FILE));
        define('PRODUCT_COMPARE_ASSETS', PRODUCT_COMPARE_URL . '/assets');
    }

    //activate the plugin
    public function activate() {
        $installed = get_option('product_compare_installed');

        if (!$installed) {
            update_option('product_compare_installed', time());
        }

        update_option('product_compare_version', PRODUCT_COMPARE_VERSION);
    }

    //update the plugin
    public function update($upgrader_object, $options) {
        if ($options['action'] == 'update' && $options['type'] == 'plugin') {
            foreach ($options['plugins'] as $plugin) {
                if ($plugin == PRODUCT_COMPARE_FILE) {
                    $this->activate();
                }
            }
        }
    }

    //initialize the plugin
    public function init_plugin() {
        if( is_admin() ) {
            new ProductCompare\Backend\Menu();
        }

        new ProductCompare\Frontend\Enqueue();
    }
}

//Initialize the main plugin

function product_compare() {
    return Product_Compare::init();
}

//kick-off the plugin
product_compare();