<?php
defined('ABSPATH') || die();

global $wp_query;
$swpf_display = 'none';
if ($wp_query->is_main_query() && $wp_query->is_tax()) {
    $swpf_cln_queried_tax = $wp_query->get_queried_object();
}

if (!empty($swpf_current_filter_option)) {
    $swpf_display = 'block';
    if (isset($swpf_cln_queried_tax)) {
        if (!empty($swpf_current_filter_option['categories']) && 'product_cat' === $swpf_cln_queried_tax->taxonomy) {
            $swpf_tax_index = array_search($swpf_cln_queried_tax->slug, $swpf_current_filter_option['categories']);
            if (false !== $swpf_tax_index && 1 === count($swpf_current_filter_option['categories']) && 1 === count($swpf_current_filter_option)) {
                $swpf_display = 'none';
            }
        }
        if (!empty($swpf_current_filter_option['tags']) && 'product_tag' === $swpf_cln_queried_tax->taxonomy) {
            $swpf_tax_index = array_search($swpf_cln_queried_tax->slug, $swpf_current_filter_option['tags']);
            if (false !== $swpf_tax_index && 1 === count($swpf_current_filter_option['tags']) && 1 === count($swpf_current_filter_option)) {
                $swpf_display = 'none';
            }
        }
        if (!empty($swpf_current_filter_option['brands']) && 'product_brand' === $swpf_cln_queried_tax->taxonomy) {
            $swpf_tax_index = array_search($swpf_cln_queried_tax->slug, $swpf_current_filter_option['brands']);
            if (false !== $swpf_tax_index && 1 === count($swpf_current_filter_option['brands']) && 1 === count($swpf_current_filter_option)) {
                $swpf_display = 'none';
            }
        }
    }
    $swpf_filters_count = count($swpf_current_filter_option);
    if ($swpf_filters_count === 1) {
        if (isset($swpf_current_filter_option['orderby']) || isset($swpf_current_filter_option['relation'])) {
            $swpf_display = 'none';
        }
        if (isset($has_filter_category)) {
            if (!$has_filter_category) {
                $swpf_display = 'none';
            }
        }
    }
}
?>
<div class="swpf-filter-block swpf-active-filter" style="display: <?php echo esc_attr($swpf_display); ?>">
    <h4 class="swpf-filter-title">
        <?php esc_html_e('Active Filters', 'super-product-filter'); ?>
        <div class="swpf-activated-clear-all">
            <button type="submit" class="swpf-clear-all" name="swpf_remove_all" value="1">
                <?php esc_html_e('Clear All', 'super-product-filter'); ?>
            </button>
        </div>
    </h4>

    <div class="swpf-activated-filter-wrap">
        <?php
        if (isset($swpf_current_filter_option) && isset($swpf_filters_count)) {
            include SWPF_PATH . 'public/inc/filter/render/active-filter-items.php';
        }
        ?>
    </div>
</div>