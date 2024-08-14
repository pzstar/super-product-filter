<?php
defined('ABSPATH') || die();

global $wp_query;

$taxonomy = swpf_get_taxonomies();

if (!isset($settings) || empty($settings)) {
    $settings = $this->settings ? $this->settings : array();
}

$shortcode_id = $this->filter_shortcode_id ? $this->filter_shortcode_id : null;
$unique_id = wp_rand();
$current_page_id = get_the_ID();
$current_filter_option = array();

if (defined('DOING_AJAX') && DOING_AJAX) {
    $current_filter_option = $this->get_current_filter_options($post_data);
    $unique_id = swpf_get_post('unique_id');
    $current_page_id = swpf_get_post('current_page_id');
    $prevposid = isset($posid) ? absint($posid) : null;
    $posid = swpf_get_post('posid', 'absint', $prevposid);
    if ($prevposid != $posid) {
        $settings = get_post_meta($posid, 'swpf_settings', true);
        $settings = Super_Product_Filter_Admin::recursive_parse_args($settings, Super_Product_Filter_Admin::default_settings_values());
    }
}

if (empty($current_filter_option)) {
    $current_filter_option = $this->get_current_filter_options_vars();
}
$shop_page_id = get_option('woocommerce_shop_page_id');
$elementor = get_post_meta($shop_page_id, '_elementor_edit_mode', true);

$config = [
    'posid' => absint($posid),
    'unique_id' => esc_attr($unique_id),
    'current_page_id' => absint($current_page_id),
    'swpf_preset' => 'swpf-filter-preset-' . esc_attr($unique_id),
    'product_selector' => isset($settings['config']['product_selector']) && !empty($settings['config']['product_selector']) ? esc_attr($settings['config']['product_selector']) : 'ul.products',
    'product_count_selector' => isset($settings['config']['product_count_selector']) && !empty($settings['config']['product_count_selector']) ? esc_attr($settings['config']['product_count_selector']) : '.woocommerce-result-count',
    'pagination_selector' => isset($settings['config']['pagination_selector']) && !empty($settings['config']['pagination_selector']) ? esc_attr($settings['config']['pagination_selector']) : '.woocommerce-pagination',
    'shop_page' => empty(get_option('permalink_structure')) ? get_post_type_archive_link('product') : get_permalink(wc_get_page_id('shop')),
    'scroll_after_filter' => $settings['config']['scroll_after_filter'] == 'on' ? true : null
];

if (wp_doing_ajax()) {
    $is_product_taxonomy = swpf_get_post('is_prod_taxonomy');
    $config['is_prod_taxonomy'] = $is_product_taxonomy == 'yes' ? 'yes' : 'no';
    $config['page_cat_id'] = $is_product_taxonomy == 'yes' ? swpf_get_post('page_cat_id', 'absint') : null;
    $config['page_tax_name'] = $is_product_taxonomy == 'yes' ? swpf_get_post('page_tax_name') : null;
    $config['page_term_name'] = $is_product_taxonomy == 'yes' ? swpf_get_post('page_term_name') : null;
} else {
    if (isset($cat_id) && !empty($cat_id)) {
        $config['is_prod_taxonomy'] = 'yes';
        $config['page_cat_id'] = $cat_id ? $cat_id : null;
        $current_term = get_term_by('id', $cat_id, 'product_cat', 'ARRAY_A');
        $config['page_tax_name'] = $current_term['taxonomy'] ? $current_term['taxonomy'] : null;
        $config['page_term_name'] = $current_term['slug'] ? $current_term['slug'] : null;
    } else {
        $config['is_prod_taxonomy'] = is_product_taxonomy() ? 'yes' : 'no';
        $config['page_cat_id'] = is_product_taxonomy() ? get_queried_object()->term_id : null;
        $config['page_tax_name'] = is_product_taxonomy() ? get_queried_object()->taxonomy : null;
        $config['page_term_name'] = is_product_taxonomy() ? get_queried_object()->slug : null;
    }
}
$config = wp_json_encode($config);
$auto_submit = $settings['config']['autosubmit'] == 'on' ? true : false;
$form_class = ['apply_ajax'];
if ($auto_submit) {
    array_push($form_class, 'swpf-instant-filtering');
}
$checkbox_skin = isset($settings['checkboxradio']['skin']) ? array_push($form_class, $settings['checkboxradio']['skin']) : array_push($form_class, 'swpf-checkboxradio-skin-1');
$dropdown_skin = isset($settings['dropdown']['skin']) ? array_push($form_class, $settings['dropdown']['skin']) : array_push($form_class, 'swpf-dropdown-skin-1');
$multiselect_skin = isset($settings['multiselect']['skin']) ? array_push($form_class, $settings['multiselect']['skin']) : array_push($form_class, 'swpf-multiselect-skin-1');
$rangeslider_skin = isset($settings['pricerangeslider']['skin']) ? array_push($form_class, $settings['pricerangeslider']['skin']) : array_push($form_class, 'swpf-pricerangeslider-skin-1');
$button_skin = isset($settings['button']['skin']) ? array_push($form_class, $settings['button']['skin']) : array_push($form_class, 'swpf-button-skin-1');
$toggle_skin = isset($settings['toggle']['skin']) ? array_push($form_class, $settings['toggle']['skin']) : array_push($form_class, 'swpf-toggle-skin-1');
$button_size = isset($settings['button']['size']) ? array_push($form_class, $settings['button']['size']) : array_push($form_class, 'swpf-medium');
$enablebottomborder = isset($settings['filterbox']['enablebottomborder']) && $settings['filterbox']['enablebottomborder'] == 'on' ? array_push($form_class, 'swpf-enablebottomborder') : '';

$before_trigger = isset($settings['advanced_settings']['before_filter_js']) && !empty($settings['advanced_settings']['before_filter_js']) ? $settings['advanced_settings']['before_filter_js'] : null;
$after_trigger = isset($settings['advanced_settings']['after_filter_js']) && !empty($settings['advanced_settings']['after_filter_js']) ? $settings['advanced_settings']['after_filter_js'] : null;

if (!empty($before_trigger)) {
    wp_add_inline_script('super-product-filter', ' jQuery(document).bind("swpf_before_filter", function (event, response) {' . wp_kses_post($before_trigger) . '});');
}
if (!empty($after_trigger)) {
    wp_add_inline_script('super-product-filter', 'jQuery(document).bind("swpf_after_filter", function (event, response) {' . wp_kses_post($after_trigger) . '});');
}

$main_wrap_classes = array(
    'swpf-main-wrap',
    'swpf-filter-id-' . esc_attr($posid),
    'swpf-ajax-initial-filter-on'
);
?>

<div class="<?php echo esc_attr(implode(' ', $main_wrap_classes)) ?>" id="swpf-filter-preset-<?php echo esc_attr($unique_id); ?>">
    <form id="swpf-form-<?php echo esc_attr($unique_id); ?>" class="swpf-form <?php echo esc_attr(implode(' ', $form_class)); ?>" action="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" method="post" data-config="<?php echo esc_attr($config); ?>">
        <div class="swpf-filters">
            <?php
            /* Show Active Filters Position at The Start */
            include SWPF_PATH . 'public/inc/shortcodes/active-filters.php';

            if ($settings) {
                $order_lists = isset($settings['list_order']) ? $settings['list_order'] : array();
                $count = 0;
                $hide_empty = false;
                $min_max_price = $this->get_filtered_price();
                $min_price = isset($min_price) ? $min_price : floor($min_max_price->min_price ?: 0);
                $max_price = isset($max_price) ? $max_price : ceil($min_max_price->max_price ?: 0);
                if ($order_lists) {
                    foreach ($order_lists as $tax_name) {
                        if ($tax_name == 'price_range' && $settings['enable']['price_range'] == 'on') {
                            ?>
                            <div class="swpf-filter-item-wrap swpf-<?php echo esc_attr($tax_name) ?>-wrap swpf-tax-count-<?php echo esc_attr($count); ?>">
                                <?php $this->render_pricerange($settings, $current_filter_option, $min_price, $max_price); ?>
                            </div>
                            <?php
                        } else if ($tax_name == 'reviews' && $settings['enable']['reviews'] == 'on') {
                            ?>
                            <div class="swpf-filter-item-wrap swpf-<?php echo esc_attr($tax_name) ?>-wrap swpf-tax-count-<?php echo esc_attr($count); ?>">
                                <?php $this->render_reviews($settings, $current_filter_option); ?>
                            </div>
                            <?php
                        } else if ($tax_name == 'ratings' && $settings['enable']['ratings'] == 'on') {
                            ?>
                            <div class="swpf-filter-item-wrap swpf-<?php echo esc_attr($tax_name) ?>-wrap swpf-tax-count-<?php echo esc_attr($count); ?>">
                                <?php $this->render_ratings($settings, $current_filter_option); ?>
                            </div>
                            <?php
                        } else if ($tax_name == 'on_sale' && $settings['enable']['on_sale'] == 'on') {
                            ?>
                            <div class="swpf-filter-item-wrap swpf-<?php echo esc_attr($tax_name) ?>-wrap swpf-tax-count-<?php echo esc_attr($count); ?>">
                                <?php $this->render_onsale($settings, $current_filter_option); ?>
                            </div>
                            <?php
                        } else if ($tax_name == 'in_stock' && $settings['enable']['in_stock'] == 'on') {
                            ?>
                            <div class="swpf-filter-item-wrap swpf-<?php echo esc_attr($tax_name) ?>-wrap swpf-tax-count-<?php echo esc_attr($count); ?>">
                                <?php $this->render_instock($settings, $current_filter_option); ?>
                            </div>
                            <?php
                        } else if (isset($settings['enable'][$tax_name]) && $settings['enable'][$tax_name] == 'on') {
                            $args = swpf_get_vars_query_args_tax($current_filter_option, $settings, $tax_name);
                            $term_cquery = new WP_Query($args);
                            wp_reset_postdata();
                            $post_count = $term_cquery->post_count;

                            $this->render_fields($settings, $taxonomy, $tax_name, $config, $current_filter_option, $count);
                        }
                        $count++;
                    }
                }
            }

            $hide_empty = false;
            $orderby = isset($current_filter_option['orderby']) && !empty($current_filter_option['orderby']) ? $current_filter_option['orderby'] : (isset($settings['config']['orderby']) ? $settings['config']['orderby'] : 'menu_order');
            ?>
        </div>

        <input type="hidden" name="paged" value="<?php echo esc_attr($wp_query->query_vars['paged']) ?>">
        <input type="hidden" name="posts_per_page" value="<?php echo absint(get_query_var('posts_per_page')); ?>">
        <input type="hidden" name="hide_empty" value="<?php echo esc_attr($hide_empty); ?>">
        <input type="hidden" name="pagination_link" value="<?php echo esc_url_raw(str_replace(999999999, '%#%', remove_query_arg('add-to-cart', get_pagenum_link(999999999, false)))); ?>">
        <input type="hidden" name="filter_list_id" value="<?php echo esc_attr($unique_id); ?>">
        <input type="hidden" name="swpf_filter" value="1">
        <input type="hidden" name="swpf_filter_sc" value="<?php echo esc_attr($shortcode_id); ?>">
        <input type="hidden" name="orderby" value="<?php echo esc_attr($orderby); ?>">

        <?php
        if (!$auto_submit) {
            ?>
            <button class="swpf-form-submit" type="submit">
                <?php echo isset($settings['config']['submit_btn_text']) ? esc_html($settings['config']['submit_btn_text']) : esc_html__('Apply', 'super-product-filter'); ?>
                <i class="far fa-spinner"></i>
            </button>
            <?php
        }
        wp_nonce_field('apply_filter', 'swpf_nonce_setting');
        ?>
    </form>
    <!-- swpf-main-wrap ends -->
</div>