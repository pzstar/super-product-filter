<?php
defined('ABSPATH') || die();

global $wp_query;
$display = 'none';
if ($wp_query->is_main_query() && $wp_query->is_tax()) {
    $cln_queried_tax = $wp_query->get_queried_object();
}

if (!empty($current_filter_option)) {
    $display = 'block';
    if (isset($cln_queried_tax)) {
        if (!empty($current_filter_option['categories']) && 'product_cat' === $cln_queried_tax->taxonomy) {
            $tax_index = array_search($cln_queried_tax->slug, $current_filter_option['categories']);
            if (false !== $tax_index && 1 === count($current_filter_option['categories']) && 1 === count($current_filter_option)) {
                $display = 'none';
            }
        }
        if (!empty($current_filter_option['tags']) && 'product_tag' === $cln_queried_tax->taxonomy) {
            $tax_index = array_search($cln_queried_tax->slug, $current_filter_option['tags']);
            if (false !== $tax_index && 1 === count($current_filter_option['tags']) && 1 === count($current_filter_option)) {
                $display = 'none';
            }
        }
    }
    $filters_count = count($current_filter_option);
    if ($filters_count === 1) {
        if (isset($current_filter_option['orderby']) || isset($current_filter_option['relation'])) {
            $display = 'none';
        }
        if (isset($has_filter_category)) {
            if (!$has_filter_category) {
                $display = 'none';
            }
        }
    }
}
?>
<div class="swpf-filter-block swpf-active-filter" style="display: <?php echo esc_attr($display); ?>">
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
        if (isset($current_filter_option) && isset($filters_count)) {
            include SPF_PATH . 'public/inc/shortcodes/active-filter-items.php';
        }
        ?>
    </div>
</div>
