<?php

namespace PAC\Includes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use PAC\Includes\Zip_Data;

/**
 * Admin class
 *
 * Handles all admin-related functionality including:
 * - WooCommerce settings tab integration
 * - Admin UI rendering
 * - AJAX handlers for ZIP code management
 *
 * @package PAC\Includes
 */
class Admin {

    /**
     * Template path for admin templates.
     *
     * @var string
     */
    private string $template_path;

    /**
     * Number of items per page for pagination.
     *
     * @var int
     */
    private int $per_page;

    /**
     * Constructor.
     *
     * @param string $template_path Path to admin template directory.
     * @param int    $per_page     Number of items per page. Default 10.
     */
    public function __construct( string $template_path, int $per_page = 10 ) {
        $this->template_path = $template_path;
        $this->per_page      = $per_page;

        $this->init_hooks();
    }

    /**
     * Initialize all admin hooks.
     *
     * @return void
     */
    private function init_hooks(): void {
        // WooCommerce settings tab.
        add_filter( 'woocommerce_settings_tabs_array', [ $this, 'add_wc_settings_tab' ], 50 );
        add_action( 'woocommerce_settings_tabs_pac_availability', [ $this, 'render_wc_settings_tab' ] );

        // Admin assets.
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );

        // AJAX actions.
        add_action( 'wp_ajax_pac_add_zip', [ $this, 'ajax_add_zip' ] );
        add_action( 'wp_ajax_pac_update_zip', [ $this, 'ajax_update_zip' ] );
        add_action( 'wp_ajax_pac_delete_zip', [ $this, 'ajax_delete_zip' ] );
    }

    /**
     * Add WooCommerce settings tab.
     *
     * @param array $tabs Existing WooCommerce settings tabs.
     * @return array Modified tabs array.
     */
    public function add_wc_settings_tab( array $tabs ): array {
        $tabs['pac_availability'] = __( 'Product Availability', 'product-availability-checker' );
        return $tabs;
    }

    /**
     * Render the WooCommerce settings tab.
     *
     * @return void
     */
    public function render_wc_settings_tab(): void {
        $zip_codes = Zip_Data::get_all();
        $total     = count( $zip_codes );

        // Sanitize and validate pagination parameter.
        $current_page = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
        $pages        = $total > 0 ? ceil( $total / $this->per_page ) : 1;
        $offset       = ( $current_page - 1 ) * $this->per_page;
        $zips_to_show = array_slice( $zip_codes, $offset, $this->per_page, true );

        include $this->template_path . 'zip-list.php';
    }

    /**
     * Enqueue admin CSS/JS assets.
     *
     * Only loads assets on WooCommerce settings page.
     *
     * @return void
     */
    public function enqueue_admin_assets(): void {
        // Only load on WooCommerce settings page.
        $screen = get_current_screen();
        if ( ! $screen || 'woocommerce_page_wc-settings' !== $screen->id ) {
            return;
        }

        // Check if we're on the correct tab.
        if ( ! isset( $_GET['tab'] ) || 'pac_availability' !== $_GET['tab'] ) {
            return;
        }

        wp_enqueue_style(
            'pac-admin-css',
            PAC_PLUGIN_URL . 'assets/admin/css/admin.css',
            [],
            PAC_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'pac-admin-js',
            PAC_PLUGIN_URL . 'assets/admin/js/admin.js',
            [ 'jquery' ],
            PAC_PLUGIN_VERSION,
            true
        );

        wp_localize_script(
            'pac-admin-js',
            'pac_admin',
            [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'pac_admin_nonce' ),
                'i18n'     => [
                    'zip_required'     => __( 'ZIP code is required.', 'product-availability-checker' ),
                    'zip_invalid'      => __( 'Invalid ZIP code format.', 'product-availability-checker' ),
                    'delete_confirm'   => __( 'Are you sure you want to delete this ZIP code?', 'product-availability-checker' ),
                    'update_success'   => __( 'Updated successfully!', 'product-availability-checker' ),
                    'add_success'      => __( 'ZIP code added successfully!', 'product-availability-checker' ),
                    'delete_success'   => __( 'ZIP code deleted successfully!', 'product-availability-checker' ),
                    'error_occurred'   => __( 'An error occurred. Please try again.', 'product-availability-checker' ),
                    'unauthorized'     => __( 'You do not have permission to perform this action.', 'product-availability-checker' ),
                ],
            ]
        );
    }

    /**
     * AJAX handler: Add new ZIP code.
     *
     * @return void
     */
    public function ajax_add_zip(): void {
        // Security checks.
        check_ajax_referer( 'pac_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'You do not have permission to perform this action.', 'product-availability-checker' ),
                ]
            );
        }

        // Sanitize and validate input.
        $zip = isset( $_POST['zip'] ) ? sanitize_text_field( wp_unslash( $_POST['zip'] ) ) : '';

        if ( empty( $zip ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'ZIP code is required.', 'product-availability-checker' ),
                ]
            );
        }

        // Validate ZIP code format.
        if ( ! $this->validate_zip_code( $zip ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'Invalid ZIP code format. Please enter a valid ZIP code (5-10 alphanumeric characters).', 'product-availability-checker' ),
                ]
            );
        }

        $status  = isset( $_POST['status'] ) && ! empty( $_POST['status'] );
        $message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

        // Limit message length.
        if ( strlen( $message ) > 500 ) {
            $message = substr( $message, 0, 500 );
        }

        $success = Zip_Data::save( $zip, $status, $message );

        if ( $success ) {
            wp_send_json_success(
                [
                    'zip'     => $zip,
                    'status'  => $status,
                    'message' => $message,
                ]
            );
        } else {
            wp_send_json_error(
                [
                    'message' => __( 'Failed to save ZIP code. Please try again.', 'product-availability-checker' ),
                ]
            );
        }
    }

    /**
     * AJAX handler: Update existing ZIP code.
     *
     * @return void
     */
    public function ajax_update_zip(): void {
        // Security checks.
        check_ajax_referer( 'pac_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'You do not have permission to perform this action.', 'product-availability-checker' ),
                ]
            );
        }

        // Sanitize and validate input.
        $zip = isset( $_POST['zip'] ) ? sanitize_text_field( wp_unslash( $_POST['zip'] ) ) : '';

        if ( empty( $zip ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'ZIP code is required.', 'product-availability-checker' ),
                ]
            );
        }

        $status  = isset( $_POST['status'] ) ? (bool) $_POST['status'] : false;
        $message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

        // Limit message length.
        if ( strlen( $message ) > 500 ) {
            $message = substr( $message, 0, 500 );
        }

        $success = Zip_Data::save( $zip, $status, $message );

        if ( $success ) {
            wp_send_json_success(
                [
                    'zip'     => $zip,
                    'status'  => $status,
                    'message' => $message,
                ]
            );
        } else {
            wp_send_json_error(
                [
                    'message' => __( 'Failed to update ZIP code. Please try again.', 'product-availability-checker' ),
                ]
            );
        }
    }

    /**
     * AJAX handler: Delete ZIP code.
     *
     * @return void
     */
    public function ajax_delete_zip(): void {
        // Security checks.
        check_ajax_referer( 'pac_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'You do not have permission to perform this action.', 'product-availability-checker' ),
                ]
            );
        }

        // Sanitize and validate input.
        $zip = isset( $_POST['zip'] ) ? sanitize_text_field( wp_unslash( $_POST['zip'] ) ) : '';

        if ( empty( $zip ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'ZIP code is required.', 'product-availability-checker' ),
                ]
            );
        }

        $success = Zip_Data::delete( $zip );

        if ( $success ) {
            wp_send_json_success(
                [
                    'zip' => $zip,
                ]
            );
        } else {
            wp_send_json_error(
                [
                    'message' => __( 'Failed to delete ZIP code. It may not exist.', 'product-availability-checker' ),
                ]
            );
        }
    }

    /**
     * Validate ZIP code format.
     *
     * Accepts 5-10 alphanumeric characters (supports US and international formats).
     *
     * @param string $zip ZIP code to validate.
     * @return bool True if valid, false otherwise.
     */
    private function validate_zip_code( string $zip ): bool {
        // Remove spaces and dashes for validation.
        $zip = preg_replace( '/[\s\-]/', '', $zip );

        // Validate: 5-10 alphanumeric characters.
        return (bool) preg_match( '/^[0-9A-Za-z]{5,10}$/', $zip );
    }
}
