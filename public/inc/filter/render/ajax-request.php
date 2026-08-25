<?php
defined('ABSPATH') || die();

global $wp_query, $swpf_product_columns;
$GLOBALS['swpf_data']['need_reset_paging'] = 0;

$swpf_post_data = swpf_get_post_data('swpf_form_data');

if (empty($swpf_post_data)) {
    wp_send_json_error(esc_html__('Invalid request data!', 'super-product-filter'));
}

$swpf_posid = swpf_get_preset_id();
$swpf_settings = get_post_meta($swpf_posid, 'swpf_settings', true);
$swpf_settings = Super_Product_Filter_Admin::recursive_parse_args($swpf_settings, Super_Product_Filter_Metabox::default_settings_values());
/*
 * The page tells us the grid it is showing, which covers shortcodes and page
 * builders that set their own columns and limit as well as ordinary shop pages.
 * When it cannot be read, fall back to WooCommerce's own catalog settings
 * rather than the generic posts per page option, so filtered results match what
 * the page would have rendered on its own.
 */
$swpf_post_per_page = swpf_get_post('current_per_page', 'absint');

if (!$swpf_post_per_page) {
    $swpf_post_per_page = function_exists('wc_get_default_products_per_row')
        ? wc_get_default_products_per_row() * wc_get_default_product_rows_per_page()
        : absint(get_option('posts_per_page'));
}

$swpf_loop_columns = swpf_get_post('current_columns', 'absint');

if (!$swpf_loop_columns && function_exists('wc_get_default_products_per_row')) {
    $swpf_loop_columns = wc_get_default_products_per_row();
}

remove_action('woocommerce_product_query', array($this, 'filter_posts'), 11);

$swpf_qry = $this->filter_posts(new WP_Query(), $swpf_post_data);
wp_reset_postdata();
$swpf_args = [];
$swpf_args['post_type'] = 'product';
$swpf_args['paged'] = !empty($swpf_post_data['paged']) ? intval($swpf_post_data['paged']) : 1;
$swpf_args['posts_per_page'] = $swpf_post_per_page;
$swpf_args['meta_query'] = $swpf_qry->get('meta_query');
$swpf_args['tax_query'] = $swpf_qry->get('tax_query');
$swpf_args['wc_query'] = 'product_query';
$swpf_args['post__in'] = $swpf_qry->get('post__in');
$swpf_args['orderby'] = $swpf_qry->get('orderby');
$swpf_args['order'] = $swpf_qry->get('order');
if ($swpf_qry->get('meta_key')) {
    $swpf_args['meta_key'] = $swpf_qry->get('meta_key');
}

$wp_query = new WP_Query($swpf_args);
$swpf_total_posts_found = $wp_query->found_posts;
wp_reset_postdata();

$swpf_filtered_data = '';

if ($swpf_loop_columns > 1) {
    $swpf_product_columns = $swpf_loop_columns;
    add_filter('loop_shop_columns', function () {
        global $swpf_product_columns;
        return absint($swpf_product_columns);
    }, 999);
}

ob_start();
require SWPF_PATH . 'public/inc/filter/render/ajax-product-list.php';
$swpf_html_ul_products_content = ob_get_clean();

ob_start();
wc_get_template('loop/result-count.php', array(
    'total' => $swpf_total_posts_found,
    'per_page' => $swpf_post_per_page,
    'current' => wc_get_loop_prop('current_page'),
    'orderedby' => $swpf_args['orderby']
));
$swpf_html_result_count_content = Super_Product_Filter_Region_Markers::mark(ob_get_clean(), 'result-count');

ob_start();
wc_get_template('loop/pagination.php');
$swpf_html_pagination_content = ob_get_clean();

ob_start();
$swpf_min_max_price = Super_Product_Filter_General::get_filtered_price($swpf_qry->get('tax_query'));
$swpf_min_price = floor($swpf_min_max_price->min_price ?: 0);
$swpf_max_price = ceil($swpf_min_max_price->max_price ?: 0);
include SWPF_PATH . 'public/inc/filter/render/filter.php';
$swpf_filter_panel = ob_get_clean();

$swpf_response = [];
$swpf_response['html_ul_products_content'] = $swpf_html_ul_products_content;
$swpf_response['html_result_count_content'] = $swpf_html_result_count_content;
$swpf_response['html_pagination_content'] = preg_replace('/<\/*nav[^>]*>/', '', $swpf_html_pagination_content);
$swpf_response['html_filter_panel'] = $swpf_filter_panel;
$swpf_response['html_filtered_data'] = $swpf_filtered_data;
$swpf_response['html_post_count'] = $swpf_total_posts_found . ' ' . _n('Item Found', 'Items Found', $swpf_total_posts_found, 'super-product-filter');
$swpf_response['html_columns'] = $swpf_product_columns;
$swpf_response['posid'] = $swpf_posid;
wp_send_json($swpf_response);
wp_die();