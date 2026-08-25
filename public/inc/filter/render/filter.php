<?php
defined('ABSPATH') || die();

global $wp_query;

$taxonomy = swpf_get_taxonomies();

if (!isset($swpf_settings) || empty($swpf_settings)) {
    $swpf_settings = $this->settings ? $this->settings : array();
}

$swpf_shortcode_id = $this->filter_shortcode_id ? $this->filter_shortcode_id : null;
$swpf_unique_id = wp_rand();
$swpf_current_page_id = get_the_ID();
$swpf_current_filter_option = array();
$swpf_shop_page_id = get_option('woocommerce_shop_page_id');
$swpf_elementor_page = get_post_meta($swpf_shop_page_id, '_elementor_edit_mode', true);

if (defined('DOING_AJAX') && DOING_AJAX) {
    $swpf_current_filter_option = self::get_current_filter_options($swpf_post_data);
    $swpf_unique_id = swpf_get_post('unique_id');
    $swpf_current_page_id = swpf_get_post('current_page_id');
    $swpf_prevposid = isset($swpf_posid) ? absint($swpf_posid) : null;
    $swpf_posid = swpf_get_post('posid', 'absint', $swpf_prevposid);
    if ($swpf_prevposid != $swpf_posid) {
        $swpf_settings = get_post_meta($swpf_posid, 'swpf_settings', true);
        $swpf_settings = Super_Product_Filter_Admin::recursive_parse_args($swpf_settings, Super_Product_Filter_Metabox::default_settings_values());
    }
}

if (empty($swpf_current_filter_option)) {
    $swpf_current_filter_option = self::get_current_filter_options_vars();
}
$swpf_shop_page_id = get_option('woocommerce_shop_page_id');
$swpf_elementor = get_post_meta($swpf_shop_page_id, '_elementor_edit_mode', true);

$swpf_config = [
    'posid' => absint($swpf_posid),
    'unique_id' => esc_attr($swpf_unique_id),
    'current_page_id' => absint($swpf_current_page_id),
    'swpf_preset' => 'swpf-filter-preset-' . esc_attr($swpf_unique_id),
    'product_selector' => isset($swpf_settings['config']['product_selector']) && !empty($swpf_settings['config']['product_selector']) ? esc_attr($swpf_settings['config']['product_selector']) : 'ul.products',
    'product_count_selector' => isset($swpf_settings['config']['product_count_selector']) && !empty($swpf_settings['config']['product_count_selector']) ? esc_attr($swpf_settings['config']['product_count_selector']) : '.woocommerce-result-count',
    'pagination_selector' => isset($swpf_settings['config']['pagination_selector']) && !empty($swpf_settings['config']['pagination_selector']) ? esc_attr($swpf_settings['config']['pagination_selector']) : '.woocommerce-pagination',
    'shop_page' => empty(get_option('permalink_structure')) ? get_post_type_archive_link('product') : get_permalink(wc_get_page_id('shop')),
    'scroll_after_filter' => $swpf_settings['config']['scroll_after_filter'] == 'on' ? true : null
];

if (wp_doing_ajax()) {
    $swpf_is_product_taxonomy = swpf_get_post('is_prod_taxonomy');
    $swpf_is_shop = swpf_get_post('is_shop');
    $swpf_config['is_shop'] = $swpf_is_shop == 'yes' ? 'yes' : 'no';
    $swpf_config['is_prod_taxonomy'] = $swpf_is_product_taxonomy == 'yes' ? 'yes' : 'no';
    $swpf_config['page_cat_id'] = $swpf_is_product_taxonomy == 'yes' ? swpf_get_post('page_cat_id', 'absint') : null;
    $swpf_config['page_tax_name'] = $swpf_is_product_taxonomy == 'yes' ? swpf_get_post('page_tax_name') : null;
    $swpf_config['page_term_name'] = $swpf_is_product_taxonomy == 'yes' ? swpf_get_post('page_term_name') : null;
} else {
    if (isset($cat_id) && !empty($cat_id)) {
        $swpf_config['is_prod_taxonomy'] = 'yes';
        $swpf_config['page_cat_id'] = $cat_id ? $cat_id : null;
        $swpf_current_term = get_term_by('id', $cat_id, 'product_cat', 'ARRAY_A');
        $swpf_config['page_tax_name'] = $swpf_current_term['taxonomy'] ? $swpf_current_term['taxonomy'] : null;
        $swpf_config['page_term_name'] = $swpf_current_term['slug'] ? $swpf_current_term['slug'] : null;
    } else {
        $swpf_config['is_prod_taxonomy'] = is_product_taxonomy() ? 'yes' : 'no';
        $swpf_config['page_cat_id'] = is_product_taxonomy() ? get_queried_object()->term_id : null;
        $swpf_config['page_tax_name'] = is_product_taxonomy() ? get_queried_object()->taxonomy : null;
        $swpf_config['page_term_name'] = is_product_taxonomy() ? get_queried_object()->slug : null;
    }
    $swpf_config['is_shop'] = is_shop() ? 'yes' : 'no';
}
$swpf_is_prod_taxonomy = $swpf_config['is_prod_taxonomy'];
$swpf_config = wp_json_encode($swpf_config);
$swpf_auto_submit = $swpf_settings['config']['autosubmit'] == 'on' ? true : false;
$swpf_form_class = ['apply_ajax'];
if ($swpf_auto_submit) {
    array_push($swpf_form_class, 'swpf-instant-filtering');
}

isset($swpf_settings['checkboxradio']['skin']) ? array_push($swpf_form_class, $swpf_settings['checkboxradio']['skin']) : array_push($swpf_form_class, 'swpf-checkboxradio-skin-1');
isset($swpf_settings['dropdown']['skin']) ? array_push($swpf_form_class, $swpf_settings['dropdown']['skin']) : array_push($swpf_form_class, 'swpf-dropdown-skin-1');
isset($swpf_settings['multiselect']['skin']) ? array_push($swpf_form_class, $swpf_settings['multiselect']['skin']) : array_push($swpf_form_class, 'swpf-multiselect-skin-1');
isset($swpf_settings['pricerangeslider']['skin']) ? array_push($swpf_form_class, $swpf_settings['pricerangeslider']['skin']) : array_push($swpf_form_class, 'swpf-pricerangeslider-skin-1');
isset($swpf_settings['button']['skin']) ? array_push($swpf_form_class, $swpf_settings['button']['skin']) : array_push($swpf_form_class, 'swpf-button-skin-1');
isset($swpf_settings['toggle']['skin']) ? array_push($swpf_form_class, $swpf_settings['toggle']['skin']) : array_push($swpf_form_class, 'swpf-toggle-skin-1');
isset($swpf_settings['button']['size']) ? array_push($swpf_form_class, $swpf_settings['button']['size']) : array_push($swpf_form_class, 'swpf-medium');

$swpf_enablebottomborder = isset($swpf_settings['filterbox']['enablebottomborder']) && $swpf_settings['filterbox']['enablebottomborder'] == 'on' ? array_push($swpf_form_class, 'swpf-enablebottomborder') : '';
$swpf_ajax_load = apply_filters('swpf_ajax_initial_filter', (!(is_shop() || is_product_category() || is_product_taxonomy())) || $swpf_elementor_page);

$swpf_main_wrap_classes = array(
    'swpf-main-wrap',
    'swpf-filter-id-' . esc_attr($swpf_posid),
    'swpf-ajax-initial-filter-' . ($swpf_ajax_load ? 'on' : 'off')
);
?>

<div class="<?php echo esc_attr(implode(' ', $swpf_main_wrap_classes)) ?>" id="swpf-filter-preset-<?php echo esc_attr($swpf_unique_id); ?>">
    <form id="swpf-form-<?php echo esc_attr($swpf_unique_id); ?>" class="swpf-form <?php echo esc_attr(implode(' ', $swpf_form_class)); ?>" action="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" method="post" data-config="<?php echo esc_attr($swpf_config); ?>">
        <div class="swpf-filters">
            <?php
            /* Show Active Filters Position at The Start */
            include SWPF_PATH . 'public/inc/filter/render/active-filters.php';

            if ($swpf_settings) {
                $swpf_order_lists = isset($swpf_settings['list_order']) ? $swpf_settings['list_order'] : array();
                $swpf_count = 0;
                $swpf_hide_empty = false;
                $swpf_min_max_price = Super_Product_Filter_General::get_filtered_price();
                $swpf_min_price = isset($swpf_min_price) ? $swpf_min_price : floor($swpf_min_max_price->min_price ?: 0);
                $swpf_max_price = isset($swpf_max_price) ? $swpf_max_price : ceil($swpf_min_max_price->max_price ?: 0);
                if ($swpf_order_lists) {
                    foreach ($swpf_order_lists as $swpf_tax_name) {
                        if ($swpf_tax_name == 'price_range' && $swpf_settings['enable']['price_range'] == 'on') {
                            ?>
                            <div class="swpf-filter-item-wrap swpf-<?php echo esc_attr($swpf_tax_name) ?>-wrap swpf-tax-count-<?php echo esc_attr($swpf_count); ?>">
                                <?php
                                include SWPF_PATH . 'public/inc/filter/fields/price.php';
                                ?>
                            </div>
                            <?php
                        } elseif ($swpf_tax_name == 'order_by' && $swpf_settings['enable']['order_by'] == 'on') {
                            $swpf_has_orderby_field = true;
                            ?>
                            <div class="swpf-filter-item-wrap swpf-<?php echo esc_attr($swpf_tax_name) ?>-wrap swpf-tax-count-<?php echo esc_attr($swpf_count); ?>">
                                <?php
                                include SWPF_PATH . 'public/inc/filter/fields/order-by.php';
                                ?>
                            </div>
                            <?php
                        } elseif ($swpf_tax_name == 'search_text' && $swpf_settings['enable']['search_text'] == 'on') {
                            ?>
                            <div class="swpf-filter-item-wrap swpf-<?php echo esc_attr($swpf_tax_name) ?>-wrap swpf-tax-count-<?php echo esc_attr($swpf_count); ?>">
                                <?php
                                include SWPF_PATH . 'public/inc/filter/fields/search-text.php';
                                ?>
                            </div>
                            <?php
                        } elseif ($swpf_tax_name == 'reviews' && $swpf_settings['enable']['reviews'] == 'on') {
                            ?>
                            <div class="swpf-filter-item-wrap swpf-<?php echo esc_attr($swpf_tax_name) ?>-wrap swpf-tax-count-<?php echo esc_attr($swpf_count); ?>">
                                <?php
                                include SWPF_PATH . 'public/inc/filter/fields/reviews.php';
                                ?>
                            </div>
                            <?php
                        } elseif ($swpf_tax_name == 'ratings' && $swpf_settings['enable']['ratings'] == 'on') {
                            ?>
                            <div class="swpf-filter-item-wrap swpf-<?php echo esc_attr($swpf_tax_name) ?>-wrap swpf-tax-count-<?php echo esc_attr($swpf_count); ?>">
                                <?php
                                include SWPF_PATH . 'public/inc/filter/fields/ratings.php';
                                ?>
                            </div>
                            <?php
                        } elseif ($swpf_tax_name == 'on_sale' && $swpf_settings['enable']['on_sale'] == 'on') {
                            ?>
                            <div class="swpf-filter-item-wrap swpf-<?php echo esc_attr($swpf_tax_name) ?>-wrap swpf-tax-count-<?php echo esc_attr($swpf_count); ?>">
                                <?php
                                include SWPF_PATH . 'public/inc/filter/fields/on-sale.php';
                                ?>
                            </div>
                            <?php
                        } elseif ($swpf_tax_name == 'in_stock' && $swpf_settings['enable']['in_stock'] == 'on') {
                            ?>
                            <div class="swpf-filter-item-wrap swpf-<?php echo esc_attr($swpf_tax_name) ?>-wrap swpf-tax-count-<?php echo esc_attr($swpf_count); ?>">
                                <?php
                                include SWPF_PATH . 'public/inc/filter/fields/in-stock.php';
                                ?>
                            </div>
                            <?php
                        } elseif (isset($swpf_settings['enable'][$swpf_tax_name]) && $swpf_settings['enable'][$swpf_tax_name] == 'on') {
                            $swpf_args = swpf_get_vars_query_args_tax($swpf_current_filter_option, $swpf_settings, $swpf_tax_name);
                            $swpf_term_cquery = new WP_Query($swpf_args);
                            wp_reset_postdata();
                            $swpf_post_count = $swpf_term_cquery->post_count;

                            if ($swpf_tax_name != 'product_cat' || ($swpf_tax_name == 'product_cat' && $swpf_is_prod_taxonomy != 'yes')) {
                                $this->render_fields($swpf_settings, $taxonomy, $swpf_tax_name, $swpf_config, $swpf_current_filter_option, $swpf_count);
                            }
                        }
                        $swpf_count++;
                    }
                }
            }

            $swpf_hide_empty = false;
            $orderby = isset($swpf_current_filter_option['orderby']) && !empty($swpf_current_filter_option['orderby']) ? $swpf_current_filter_option['orderby'] : (isset($swpf_settings['config']['orderby']) ? $swpf_settings['config']['orderby'] : 'menu_order');
            ?>
        </div>

        <input type="hidden" name="paged" value="<?php echo isset($wp_query->query_vars['paged']) ? esc_attr($wp_query->query_vars['paged']) : ''; ?>">
        <input type="hidden" name="posts_per_page" value="<?php echo absint(get_query_var('posts_per_page')); ?>">
        <input type="hidden" name="hide_empty" value="<?php echo esc_attr($swpf_hide_empty); ?>">
        <input type="hidden" name="pagination_link" value="<?php echo esc_url(str_replace(999999999, '%#%', remove_query_arg('add-to-cart', get_pagenum_link(999999999, false)))); ?>">
        <input type="hidden" name="filter_list_id" value="<?php echo esc_attr($swpf_unique_id); ?>">
        <input type="hidden" name="swpf_filter" value="1">
        <input type="hidden" name="swpf_filter_sc" value="<?php echo esc_attr($swpf_shortcode_id); ?>">
        <?php
        /*
         * The Sorting filter renders its own orderby control. Emitting this
         * hidden field as well would put two inputs of the same name in the
         * form, and the later one wins when the payload is parsed, so the
         * shopper's choice would be silently replaced by the default.
         */
        if (empty($swpf_has_orderby_field)) {
            ?>
            <input type="hidden" name="orderby" value="<?php echo esc_attr($orderby); ?>">
            <?php
        }
        ?>

        <?php
        if (!$swpf_auto_submit) {
            ?>
            <button class="swpf-form-submit" type="submit">
                <?php echo isset($swpf_settings['config']['submit_btn_text']) ? esc_html($swpf_settings['config']['submit_btn_text']) : esc_html__('Apply', 'super-product-filter'); ?>
                <i class="swpf-icon swpf-icon-spinner"></i>
            </button>
            <?php
        }
        wp_nonce_field('apply_filter', 'swpf_nonce_setting');
        ?>
    </form>
    <!-- swpf-main-wrap ends -->
</div>