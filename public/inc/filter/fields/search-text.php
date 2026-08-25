<?php
defined('ABSPATH') || die();

global $wp_query;

$swpf_sc_id = $this->filter_shortcode_id;
$swpf_sc_title = get_the_title($swpf_sc_id);

$swpf_search_term = '';
if (isset($swpf_current_filter_option['s']) && '' !== $swpf_current_filter_option['s']) {
    $swpf_search_term = $swpf_current_filter_option['s'];
} elseif (isset($wp_query->query_vars['s'])) {
    $swpf_search_term = $wp_query->query_vars['s'];
}

if (isset($swpf_settings['title_label']['search_text']) && !empty($swpf_settings['title_label']['search_text'])) {
    ?>
    <div class="swpf-filter-title">
        <h4 class="swpf-filter-title-heading">
            <?php echo esc_html(apply_filters('swpf_translate_string', $swpf_settings['title_label']['search_text'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Taxonomy Name search_text')); ?>
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
        <div class="swpf-search-by-title">
            <input type="text" name="s" value="<?php echo esc_attr($swpf_search_term); ?>" placeholder="<?php echo esc_attr(apply_filters('swpf_search_text_placeholder', esc_html__('Search products&hellip;', 'super-product-filter'))); ?>">
        </div>
    </div>
</div>
