<?php

class Super_Product_Filter_General_Settings {
    public function __construct() {
        // Create a Setting Page
        add_action('admin_menu', array($this, 'register_submenu_page'));

        // Get posts by query
        add_action('wp_ajax_swpf_general_settings_save', array($this, 'handle_generalsettingsform'));
    }

    public function register_submenu_page() {
        add_submenu_page('edit.php?post_type=swpf-product-filter', esc_html__('Settings', 'super-product-filter'), esc_html__('Settings', 'super-product-filter'), 'manage_options', 'swpf-general-settings', array($this, 'generalsettingsconfiguration'));
    }

    public function generalsettingsconfiguration() {
        include SWPF_PATH . 'admin/inc/general/settings.php';
    }

    public function handle_generalsettingsform() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('You are not allowed to perform this action.');
        }

        if (wp_verify_nonce(swpf_get_post('swpf_nonce'), 'swpf_nonce_update_general_settings')) {
            $general_settings = swpf_get_post_data_arr('swpf_general_settings');
            $general_settings = Super_Product_Filter_Admin::recursive_parse_args($general_settings, self::checkbox_general_settings());
            $general_settings = Super_Product_Filter_Admin::sanitize_array($general_settings, self::sanitize_general_setting_rules());

            update_option('swpf_general_settings', $general_settings);
            wp_send_json_success(array('message' => esc_html__('Settings Saved!', 'super-product-filter')));
        }
    }

    public static function checkbox_general_settings() {
        return array(
            'load_fonts_locally' => 'off',
        );
    }

    public static function sanitize_general_setting_rules() {
        return array(
            'load_fonts_locally' => 'swpf_sanitize_checkbox',
        );
    }

    public static function default_general_settings_values() {
        return array(
            'load_fonts_locally' => 'off',
        );
    }
}

new Super_Product_Filter_General_Settings();