<?php

namespace PAC\Includes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * PAC_Plugin class
 *
 * Main plugin initialization class.
 *
 * @package PAC\Includes
 */
class PAC_Plugin {

    /**
     * Initialize the plugin.
     *
     * Checks for WooCommerce dependency and initializes admin and frontend classes.
     *
     * @return void
     */
    public static function init(): void {
        // Secondary WooCommerce check (safety measure).
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        if ( is_admin() ) {
            new Admin( PAC_PLUGIN_TEMPLATE_PATH . 'admin/', 10 );
        }

        new Front();
    }
}