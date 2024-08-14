<?php
defined('ABSPATH') || die();

function swpf_get_shop_page_url() {
    $shop_page_id = (int) wc_get_page_id('shop');
    if ($shop_page_id <= 0) {
        $shop_page = get_post_type_archive_link('product');
    } else {
        $shop_page = urldecode(get_permalink($shop_page_id));
    }
    return $shop_page;
}

function swpf_pagination($products, $left_icon, $right_icon) {
    $total_pages = $products->max_num_pages;
    $big = 999999999;
    if ($total_pages > 1) {
        $paged = get_query_var('paged');
        if (is_front_page()) {
            global $wp_query;
            if (is_array($wp_query->query) && count($wp_query->query) > 0 && isset($wp_query->query['paged'])) {
                $paged = $wp_query->query['paged'];
            }
        }
        $current_page = max(1, $paged);
        echo wp_kses_post(paginate_links(array(
            'base' => str_replace($big, '%#%', get_pagenum_link($big, false)),
            'format' => '?paged=%#%',
            'current' => $current_page,
            'total' => $total_pages,
            'prev_text' => '<i class="' . esc_attr($left_icon['value']) . '"></i>',
            'next_text' => '<i class="' . esc_attr($right_icon['value']) . '"></i>',
        )));
    }
}

function swpf_hide_show_pages($specific_page, $hide_show, $cpt, $specific_archive, $specific_product_categories, $specific_product_tags) {
    wp_reset_postdata();
    global $post;
    $pageid = is_front_page() ? null : ($post ? $post->ID : null);
    if (class_exists('WooCommerce') && is_shop()) {
        $pageid = wc_get_page_id('shop');
    } else if (is_single() || is_front_page()) {
        $pageid = isset($post->ID) ? $post->ID : null;
    } else if (is_archive()) {
        $pageid = get_queried_object_id();
    } else if (is_home()) {
        $pageid = get_queried_object_id();
    }
    $current_archive = $post ? $post->post_type : get_queried_object()->name;
    $current_post_type = is_archive() ? null : $current_archive;

    $show = true;

    switch ($hide_show) {
        case 'show_all':
            $show = true;
            break;

        case 'hide_all':
            $show = false;
            break;

        case 'show_selected':
            $show = false;
            if (in_array($pageid, $specific_page)) {
                $show = true;
            }
            if (is_singular() && !is_archive() && in_array($current_post_type, $cpt)) {
                $show = true;
            }
            if (!is_singular() && in_array($current_archive, $specific_archive) && !is_product_category() && !is_product_tag()) {
                $show = true;
            }
            if (!empty($specific_product_categories) && function_exists('is_product_category') && is_product_category($specific_product_categories)) {
                $show = true;
            }
            if (!empty($specific_product_tags) && function_exists('is_product_tag') && is_product_tag($specific_product_tags)) {
                $show = true;
            }
            break;

        case 'hide_selected':
            $show = true;
            if (is_singular() && !is_archive() && in_array($current_post_type, $cpt)) {
                $show = false;
            }
            if (in_array($pageid, $specific_page)) {
                $show = false;
            }
            if (!is_singular() && in_array($current_archive, $specific_archive) && !is_product_category() && !is_product_tag()) {
                $show = false;
            }
            if (!empty($specific_product_categories) && function_exists('is_product_category') && is_product_category($specific_product_categories)) {
                $show = false;
            }
            if (!empty($specific_product_tags) && function_exists('is_product_tag') && is_product_tag($specific_product_tags)) {
                $show = false;
            }
            break;
    }
    return $show;
}

function swpf_get_image_alt_by_url($url = '') {
    $image_id = attachment_url_to_postid($url);
    $image_alt = '';
    if ($image_id) {
        $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', TRUE);
    }
    return $image_alt;
}

function swpf_is_standard_font($font) {
    $standard_fonts = array('Helvetica', 'Verdana', 'Arial', 'Times', 'Georgia', 'Courier', 'Trebuchet', 'Tahoma', 'Palatino');
    if (in_array($font, $standard_fonts)) {
        $flag = 'standard';
    } else {
        $flag = 'google';
    }
    return $flag;
}
