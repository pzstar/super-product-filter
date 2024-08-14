<?php
defined('ABSPATH') || die();

global $wp_query, $swpf_product_columns;
$GLOBALS['swpf_data']['need_reset_paging'] = 0;

$post_data = swpf_get_post_data('swpf_form_data');

if (empty($post_data)) {
    wp_send_json_error(esc_html__('Invalid request data!', 'super-product-filter'));
}

$posid = swpf_get_post('posid');
$settings = get_post_meta($posid, 'swpf_settings', true);
$settings = Super_Product_Filter_Admin::recursive_parse_args($settings, Super_Product_Filter_Admin::default_settings_values());
$post_per_page = get_option('posts_per_page');

if (isset($settings['config']['product_columns']) && !empty($settings['config']['product_columns'])) {
    $swpf_product_columns = absint($settings['config']['product_columns']);
}

if (isset($settings['config']['product_rows']) && !empty($settings['config']['product_rows'])) {
    $product_rows = absint($settings['config']['product_rows']);
}

if (isset($swpf_product_columns) && isset($product_rows)) {
    $post_per_page = $swpf_product_columns * $product_rows;
}

remove_action('woocommerce_product_query', array($this, 'filter_posts'), 11);

$qry = $this->filter_posts(new WP_Query(), $post_data);
wp_reset_postdata();
$args = [];
$args['post_type'] = 'product';
$args['paged'] = !empty($post_data['paged']) ? intval($post_data['paged']) : 1;
$args['posts_per_page'] = $post_per_page;
$args['meta_query'] = $qry->get('meta_query');
$args['tax_query'] = $qry->get('tax_query');
$args['wc_query'] = 'product_query';
$args['post__in'] = $qry->get('post__in');
$args['orderby'] = $qry->get('orderby');
$args['order'] = $qry->get('order');
if ($qry->get('meta_key')) {
    $args['meta_key'] = $qry->get('meta_key');
}

$wp_query = new WP_Query($args);
$total_posts_found = $wp_query->found_posts;
wp_reset_postdata();

$filtered_data = '';

if (isset($swpf_product_columns) && $swpf_product_columns > 1) {
    add_filter('loop_shop_columns', function() {
        global $swpf_product_columns;
        return absint($swpf_product_columns);
    }, 999);
}

ob_start();
require SWPF_PATH . 'public/inc/ajax-product-list.php';
$html_ul_products_content = ob_get_clean();

ob_start();
wc_get_template('loop/result-count.php', array(
    'total' => $total_posts_found,
    'per_page' => $post_per_page,
    'current' => wc_get_loop_prop('current_page'),
));
$html_result_count_content = ob_get_clean();

ob_start();
wc_get_template('loop/pagination.php');
$html_pagination_content = ob_get_clean();

$min_max_price = $this->get_filtered_price($qry->get('tax_query'));

ob_start();
self::render_html(SWPF_PATH . 'public/inc/render-filter.php', array(
    'post_data' => $post_data,
    'settings' => $settings,
    'min_price' => floor($min_max_price->min_price ?: 0),
    'max_price' => ceil($min_max_price->max_price ?: 0)
));
$filter_panel = ob_get_clean();

$response = [];
$response['html_ul_products_content'] = $html_ul_products_content;
$response['html_result_count_content'] = $html_result_count_content;
$response['html_pagination_content'] = preg_replace('/<\/*nav[^>]*>/', '', $html_pagination_content);
$response['html_filter_panel'] = $filter_panel;
$response['html_filtered_data'] = $filtered_data;
$response['html_post_count'] = $total_posts_found . ' ' . _n('Item Found', 'Items Found', $total_posts_found, 'super-product-filter');
$response['html_columns'] = $swpf_product_columns;
$response['qry'] = $wp_query;
$response['posid'] = $posid;
wp_send_json($response);
wp_die();