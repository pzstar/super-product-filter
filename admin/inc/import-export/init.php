<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Super_Product_Filter_Import_Export {
	public function __construct() {

        // Process a settings export that generates a .json file of the cart settings
        add_action('admin_init', array($this, 'process_settings_export'));

        // Process a settings import from a json file
        add_action('admin_init', array($this, 'process_settings_import'));
    }

    public function process_settings_export() {

        if (empty(swpf_get_post('swpf_imex_action')) || 'export_settings' != swpf_get_post('swpf_imex_action') || empty(swpf_get_post('swpf_filter_id'))) {
            return;
        }

        if (!wp_verify_nonce(swpf_get_post('swpf_imex_export_nonce'), 'swpf_imex_export_nonce')) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        $filter_id = swpf_get_post('swpf_filter_id');

        if ('publish' == get_post_status($filter_id) || 'draft' == get_post_status($filter_id)) {
            $settings = get_post_meta($filter_id, 'swpf_settings', true);
            $unset_array = array('enable', 'title_label', 'list_order', 'show_count', 'hide_term_name', 'search_filter', 'placeholder_txt', 'multiselect_logic_operator', 'include_exclude_filter', 'field_orientation', 'terms_customize', 'orderby', 'order_type', 'display_option', 'shortcode', 'display');

            foreach ($unset_array as $key) {
                unset($settings[$key]);
            }
            unset($settings['config']['lo_specific_cat']);


            ignore_user_abort(true);

            nocache_headers();
            header('Content-Type: application/json; charset=utf-8');
            header('Content-Disposition: attachment; filename=swpf-' . $filter_id . '-' . gmdate('m-d-Y') . '.json');
            header("Expires: 0");

            echo wp_json_encode($settings);
            exit;

        } else {
            wp_die(esc_html__('Please update post before you export', 'super-product-filter'));
        }
    }

    public function process_settings_import() {

        if (empty(swpf_get_post('swpf_imex_action')) || 'import_settings' != swpf_get_post('swpf_imex_action') || empty(swpf_get_post('swpf_filter_id'))) {
            return;
        }

        if (!wp_verify_nonce(swpf_get_post('swpf_imex_import_nonce'), 'swpf_imex_import_nonce')) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        $filename = isset($_FILES['swpf_import_file']['name']) ? sanitize_file_name($_FILES['swpf_import_file']['name']) : '';
        $extension = explode('.', $filename);
        $extension = end($extension);

        if ($extension != 'json') {
            wp_die(esc_html__('Please upload a valid .json file', 'super-product-filter'));
        }

        $import_file = isset($_FILES['swpf_import_file']['tmp_name']) ? sanitize_text_field($_FILES['swpf_import_file']['tmp_name']) : '';

        if (empty($import_file)) {
            wp_die(esc_html__('Please upload a file to import', 'super-product-filter'));
        }

        // Retrieve the settings from the file and convert the json object to an array.
        $imdat = json_decode(file_get_contents($import_file), true);

        $filter_id = swpf_get_post('swpf_filter_id');

        if ('publish' == get_post_status($filter_id) || 'draft' == get_post_status($filter_id)) {
            $old_settings = get_post_meta($filter_id, 'swpf_settings', true);
            $settings = self::import_images($imdat);
            $settings = Super_Product_Filter_Admin::recursive_parse_args($settings, $old_settings);
            $settings = Super_Product_Filter_Admin::sanitize_array($settings, Super_Product_Filter_Panels::sanitize_settings_rules());
            update_post_meta($filter_id, 'swpf_settings', $settings);

            $location = isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER'])) : '';
            wp_safe_redirect($location . '&swpfalert=Settings%20Imported%20Successfully');
            exit();
        } else {
            wp_die(esc_html__('Please update post before you import', 'super-product-filter'));
        }
    }

    private static function import_images($mods) {
        if ($mods) {
            foreach ($mods as $key => $value) {
                //For repeater fields
                if (is_array($value)) {
                    foreach ($value as $data_key => $data_value) {
                        if (self::is_image_url($data_value)) {
                            $sub_data = self::media_handle_sideload($data_value);
                            if (!is_wp_error($sub_data))
                                $value[$data_key] = $sub_data->url;
                        } else {
                            $value[$data_key] = $data_value;
                        }
                    }

                    $mods[$key] = $value;
                } elseif (self::is_image_url($value)) {
                    $data = self::media_handle_sideload($value);
                    if (!is_wp_error($data))
                        $mods[$key] = $data->url;
                }
            }
        }
        return $mods;
    }

    private static function is_image_url($url) {
        if (!is_string($url) || '' === $url) {
            return false;
        }

        // Require an absolute http(s) URL whose path ends in an image extension,
        // so arbitrary strings containing ".jpg" are not sideloaded.
        if (!preg_match('#^https?://#i', $url)) {
            return false;
        }

        $path = wp_parse_url($url, PHP_URL_PATH);

        return $path && preg_match('/\.(jpe?g|png|gif)$/i', $path);
    }

    private static function media_handle_sideload($file) {
        $data = new stdClass();

        if (!function_exists('media_handle_sideload')) {
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
        }

        if (!empty($file)) {
            // Set variables for storage, fix file filename for query strings.
            preg_match('/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches);
            $file_array = array();
            $file_array['name'] = basename($matches[0]);

            // Download file to temp location.
            $file_array['tmp_name'] = download_url($file);

            // If error storing temporarily, return the error.
            if (is_wp_error($file_array['tmp_name'])) {
                return $file_array['tmp_name'];
            }

            // Do the validation and storage stuff.
            $id = media_handle_sideload($file_array, 0);

            // If error storing permanently, unlink.
            if (is_wp_error($id)) {
                wp_delete_file($file_array['tmp_name']);
                return $id;
            }

            // Build the object to return.
            $meta = wp_get_attachment_metadata($id);
            $data->attachment_id = $id;
            $data->url = wp_get_attachment_url($id);
            $data->thumbnail_url = wp_get_attachment_thumb_url($id);
            $data->height = $meta['height'];
            $data->width = $meta['width'];
        }

        return $data;
    }
}

new Super_Product_Filter_Import_Export();