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

if ( ! defined( 'PAC_PLUGIN_TEMPLATE_PATH' ) ) {
    define( 'PAC_PLUGIN_TEMPLATE_PATH', PAC_PLUGIN_PATH . 'templates/' );
}

if ( ! defined( 'PAC_PLUGIN_VERSION' ) ) {
    define( 'PAC_PLUGIN_VERSION', '1.0.0' );
}

/**
 * Check if WooCommerce is active.
 *
 * @return bool True if WooCommerce is active, false otherwise.
 */
function pac_is_woocommerce_active(): bool {
    return class_exists( 'WooCommerce' );
}

/**
 * Display admin notice if WooCommerce is not active.
 *
 * @return void
 */
function pac_woocommerce_missing_notice(): void {
    ?>
    <div class="error">
        <p>
            <strong><?php esc_html_e( 'Product Availability Checker', 'product-availability-checker' ); ?></strong>
            <?php
            printf(
                /* translators: %s: WooCommerce plugin name */
                esc_html__( 'requires %s to be installed and active.', 'product-availability-checker' ),
                '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>'
            );
            ?>
        </p>
    </div>
    <?php
}

// Check WooCommerce dependency before initializing plugin.
if ( ! pac_is_woocommerce_active() ) {
    // Show admin notice.
    add_action( 'admin_notices', 'pac_woocommerce_missing_notice' );
    
    // Deactivate plugin if trying to activate without WooCommerce.
    add_action( 'admin_init', function() {
        if ( current_user_can( 'activate_plugins' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            
            // Show deactivation notice.
            add_action( 'admin_notices', function() {
                ?>
                <div class="notice notice-error is-dismissible">
                    <p>
                        <?php
                        esc_html_e(
                            'Product Availability Checker has been deactivated. WooCommerce is required.',
                            'product-availability-checker'
                        );
                        ?>
                    </p>
                </div>
                <?php
            } );
        }
    } );
    
    // Stop plugin initialization.
    return;
}

// Include Autoloader class.
require_once PAC_PLUGIN_PATH . 'includes/autoloader.php';

use PAC\Includes\Autoloader;

// Initialize autoloader.
$autoloader = new Autoloader( "PAC\\Includes\\", PAC_PLUGIN_PATH . 'includes' );
$autoloader->register();

use PAC\Includes\PAC_Plugin;
PAC_Plugin::init();

