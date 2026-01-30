<?php

namespace PAC\Includes;

use PAC\Includes\Zip_Data;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Front class
 *
 * Handles the frontend ZIP code availability checker:
 * - Enqueues scripts
 * - Renders ZIP checker form
 * - Handles AJAX requests
 */
class Front {

    /**
     * Constructor.
     *
     * Registers hooks for frontend assets, rendering, and AJAX.
     */
    public function __construct() {
        // Enqueue JS for the frontend
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );

        // Display ZIP code checker before the Add to Cart form
        add_action( 'woocommerce_before_add_to_cart_form', [ $this, 'render_zip_checker' ] );

        // AJAX hooks
        add_action( 'wp_ajax_pac_check_zip', [ $this, 'ajax_check_zip' ] );         // Logged-in users
        add_action( 'wp_ajax_nopriv_pac_check_zip', [ $this, 'ajax_check_zip' ] );  // Guests
    }

    /**
     * Enqueue frontend scripts.
     *
     * Localizes the AJAX URL, nonce, and saved ZIP code.
     *
     * @return void
     */
    public function enqueue_assets() {
        wp_enqueue_script(
            'zip-front',
            PAC_PLUGIN_URL . 'assets/front/js/front.js',
            [ 'jquery' ],
            PAC_PLUGIN_VERSION,
            true
        );

        wp_localize_script(
            'zip-front',
            'zipData',
            [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'pac_front_nonce' ),
                'savedZip' => isset( $_COOKIE['zip_code'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['zip_code'] ) ) : '',
            ]
        );
    }

    /**
     * Render the ZIP code checker form.
     *
     * Uses an external template file.
     *
     * @return void
     */
    public function render_zip_checker() {
        include PAC_PLUGIN_TEMPLATE_PATH . 'front/zip-checker.php';
    }

    /**
     * AJAX handler to check ZIP code availability.
     *
     * Expects 'zip' and 'nonce' in POST data.
     *
     * @return void Sends JSON response.
     */
    public function ajax_check_zip() {
        // Security check: verify nonce
        check_ajax_referer( 'pac_front_nonce', 'nonce' );

        // Sanitize ZIP input
        $zip = sanitize_text_field( wp_unslash( $_POST['zip'] ?? '' ) );

        if ( empty( $zip ) ) {
            wp_send_json_error( esc_html__( 'ZIP is required', 'product-availability-checker' ) );
        }

        // Check if ZIP is available
        $available = Zip_Data::is_available( $zip );

        // Save ZIP code in a cookie for future visits
        setcookie(
            'zip_code',
            $zip,
            time() + MONTH_IN_SECONDS,
            COOKIEPATH,
            COOKIE_DOMAIN
        );

        wp_send_json_success( [ 'available' => (bool) $available ] );
    }
}
