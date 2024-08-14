<?php

defined('ABSPATH') || die();

class SWPFBlock {

    public function __construct() {
        add_action('init', array($this, 'register_block'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
    }

    public function register_block() {
        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('swpf/filter-selector', array(
            'attributes' => array(
                'filterId' => array(
                    'type' => 'string',
                )
            ),
            'editor_style' => 'swpf-block-editor',
            'editor_script' => 'swpf-block-editor',
            'render_callback' => array($this, 'get_filter_html'),
        ));
    }

    public function enqueue_block_editor_assets() {
        wp_register_style('swpf-block-editor', SWPF_URL . 'admin/css/filter-block.css', array('wp-edit-blocks'), SWPF_VERSION);
        wp_register_script('swpf-block-editor', SWPF_URL . 'admin/js/filter-block.min.js', array('wp-blocks', 'wp-element', 'wp-i18n', 'wp-components'), SWPF_VERSION, true);

        $all_filters = swpf_get_all_filters();

        $filter_block_data = array(
            'filters' => $all_filters,
            'i18n' => array(
                'title' => esc_html__('Super Product Filter', 'super-product-filter'),
                'description' => esc_html__('Select and display one of your filters.', 'super-product-filter'),
                'filter_keywords' => array(
                    esc_html__('filter', 'super-product-filter'),
                    esc_html__('woocommerce', 'super-product-filter'),
                ),
                'filter_select' => esc_html__('Select a Filter', 'super-product-filter'),
                'filter_settings' => esc_html__('Filter Settings', 'super-product-filter'),
                'filter_selected' => esc_html__('Filter', 'super-product-filter'),
            ),
        );
        wp_localize_script('swpf-block-editor', 'swpf_block_data', $filter_block_data);
    }

    public function get_filter_html($attr) {
        $filter_id = !empty($attr['filterId']) ? absint($attr['filterId']) : 0;
        if (empty($filter_id)) {
            return '';
        }

        return do_shortcode('[swpf_shortcode id="' . absint($filter_id) . '"]');
    }

}

new SWPFBlock();
