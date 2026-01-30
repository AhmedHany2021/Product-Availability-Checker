<?php
/**
 * Plugin Name: Product Availability Checker
 * Plugin URI: https://github.com/AhmedHany2021/Product-Availability-Checker
 * Description: Check product availability by zip code for WooCommerce.
 * Version: 1.0.0
 * Author: Ahmed Hany
 * Author URI: https://github.com/AhmedHany2021
 * Text Domain: product-availability-checker
 */

namespace PAC;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'PAC_PLUGIN_PATH' ) ) {
    define( 'PAC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'PAC_PLUGIN_URL' ) ) {
    define( 'PAC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}


// Include Autoloader class
require_once PAC_PLUGIN_PATH . 'includes/autoloader.php';

use PAC\Includes\Autoloader;

// Initialize autoloader
$autoloader = new Autoloader("PAC\\Includes\\", PAC_PLUGIN_PATH . 'includes');
$autoloader->register();

use PAC\Includes\PAC_Plugin;
PAC_Plugin::init();

