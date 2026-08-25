<?php
defined('ABSPATH') || die();

$swpf_sc_id = $this->filter_shortcode_id;
$swpf_sc_title = get_the_title($swpf_sc_id);

if (isset($swpf_settings['title_label']['order_by']) && !empty($swpf_settings['title_label']['order_by'])) {
    ?>
    <div class="swpf-filter-title">
        <h4 class="swpf-filter-title-heading">
            <?php echo esc_html(apply_filters('swpf_translate_string', $swpf_settings['title_label']['order_by'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Taxonomy Name order_by')); ?>
        </h4>
        <?php
        if ($swpf_settings['config']['show_filter_list_toggle'] == 'on') {
            ?>
            <i class="swpf-filter-title-toggle swpf-minus-icon"></i>
            <?php
        }
        ?>
    </div>
    <?php
}
?>

<div class="swpf-filter-content">
    <div class="swpf-tax-list-wrapper">
        <div class="swpf-filter-select">
            <select class="swpf-filter-type-dropdown" name="orderby">
                <option value=""><?php esc_html_e('None', 'super-product-filter'); ?></option>
                <?php
                $swpf_order_by_items = Super_Product_Filter_Admin::get_order_by_options();
                $swpf_selected_orderby = isset($swpf_current_filter_option['orderby']) && !empty($swpf_current_filter_option['orderby'])
                    ? $swpf_current_filter_option['orderby']
                    : (isset($swpf_settings['config']['orderby']) ? $swpf_settings['config']['orderby'] : 'menu_order');

                $swpf_ie_filter = isset($swpf_settings['include_exclude_filter']['order_by']) ? $swpf_settings['include_exclude_filter']['order_by'] : 'all';
                $swpf_included = isset($swpf_settings['include_terms']['order_by']) && is_array($swpf_settings['include_terms']['order_by'])
                    ? $swpf_settings['include_terms']['order_by']
                    : array();

                foreach ($swpf_order_by_items as $swpf_obi_key => $swpf_obi_label) {
                    if ('all' !== $swpf_ie_filter && !in_array($swpf_obi_key, $swpf_included, true)) {
                        continue;
                    }
                    ?>
                    <option value="<?php echo esc_attr($swpf_obi_key); ?>" <?php selected($swpf_selected_orderby, $swpf_obi_key); ?>>
                        <?php echo esc_html($swpf_obi_label); ?>
                    </option>
                    <?php
                }
                ?>
            </select>
        </div>
    </div>
</div>
