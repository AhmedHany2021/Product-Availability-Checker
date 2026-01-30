<?php

namespace PAC\Includes;

if ( ! defined( 'ABSPATH' ) ) exit;

use PAC\Includes\Zip_Data;

class Admin {

    private string $template_path;
    private int $per_page;

    public function __construct(string $template_path, int $per_page = 10) {
        $this->template_path = $template_path;
        $this->per_page = $per_page;

        $this->init_hooks();
    }

    /**
     * Initialize all admin hooks
     */
    private function init_hooks(): void {
        // WooCommerce settings tab
        add_filter('woocommerce_settings_tabs_array', [$this, 'add_wc_settings_tab'], 50);
        add_action('woocommerce_settings_tabs_pac_availability', [$this, 'render_wc_settings_tab']);

        // Admin assets
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);

        // AJAX actions
        add_action('wp_ajax_pac_add_zip', [$this, 'ajax_add_zip']);
        add_action('wp_ajax_pac_update_zip', [$this, 'ajax_update_zip']);
        add_action('wp_ajax_pac_delete_zip', [$this, 'ajax_delete_zip']);

    }

    /**
     * Add WooCommerce settings tab
     */
    public function add_wc_settings_tab(array $tabs): array {
        $tabs['pac_availability'] = __('Product Availability', 'product-availability-checker');
        return $tabs;
    }

    /**
     * Render the WooCommerce settings tab
     */
    public function render_wc_settings_tab(): void {
        $zip_codes = Zip_Data::get_all();
        $total = count($zip_codes);

        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $pages = ceil($total / $this->per_page);
        $offset = ($current_page - 1) * $this->per_page;
        $zips_to_show = array_slice($zip_codes, $offset, $this->per_page, true);
        include $this->template_path . 'zip-list.php';
    }

    /**
     * Enqueue admin CSS/JS
     */
    public function enqueue_admin_assets(): void {
        wp_enqueue_style('pac-admin-css', PAC_PLUGIN_URL . 'assets/admin/css/admin.css');
        wp_enqueue_script('pac-admin-js', PAC_PLUGIN_URL . 'assets/admin/js/admin.js', ['jquery'], false, true);

        wp_localize_script('pac-admin-js', 'pac_admin', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('pac_admin_nonce')
        ]);
    }

    /**
     * AJAX: add new zip code
     */
    public function ajax_add_zip(): void {
        check_ajax_referer('pac_admin_nonce', 'nonce');

        $zip = sanitize_text_field($_POST['zip'] ?? '');
        $status = !empty($_POST['status']);

        if (!$zip) wp_send_json_error(['message' => 'Zip required']);

        Zip_Data::save($zip, $status);

        wp_send_json_success(['zip' => $zip, 'status' => $status]);
    }

    /**
     * AJAX: edit zip code
     */
    public function ajax_update_zip(): void {
        check_ajax_referer('pac_admin_nonce', 'nonce');

        $zip = sanitize_text_field($_POST['zip'] ?? '');
        $status = isset($_POST['status']) ? (bool) $_POST['status'] : false;

        if (!$zip) wp_send_json_error(['message' => 'Zip required']);

        Zip_Data::save($zip, $status);

        wp_send_json_success(['zip' => $zip, 'status' => $status]);
    }

    /**
     * AJAX: delete zip code
     */
    public function ajax_delete_zip(): void {
        check_ajax_referer('pac_admin_nonce', 'nonce');

        $zip = sanitize_text_field($_POST['zip'] ?? '');
        if (!$zip) wp_send_json_error(['message' => 'Zip required']);

        Zip_Data::delete($zip);

        wp_send_json_success(['zip' => $zip]);
    }
}
