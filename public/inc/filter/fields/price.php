<?php
defined('ABSPATH') || die();

if (wp_is_mobile()) {
    wp_enqueue_script('touch-punch');
}

$swpf_sc_title = get_the_title($swpf_shortcode_id);
?>

<div class="swpf-pricerange swpf-filter-pricerange-wrap swpf-filter-byprice slider-price">
    <?php
    if (isset($swpf_settings['title_label']['price_range']) && !empty($swpf_settings['title_label']['price_range'])) {
        ?>
        <div class="swpf-filter-title">
            <h4 class="swpf-filter-title-heading">
                <?php
                echo esc_html(apply_filters('swpf_translate_string', $swpf_settings['title_label']['price_range'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Taxonomy Name price_range'));
                ?>
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
        <?php
        $swpf_price_min = $swpf_min_price;
        $swpf_price_max = $swpf_max_price;
        $swpf_price_from = isset($swpf_current_filter_option['price']['min_price']) ? $swpf_current_filter_option['price']['min_price'] : '';
        $swpf_price_to = isset($swpf_current_filter_option['price']['max_price']) ? $swpf_current_filter_option['price']['max_price'] : '';

        ?>
        <div class="swpf-range-slider"></div>

        <input type="hidden" class="price-from" name="price[min_price]" value="<?php echo esc_attr($swpf_price_from); ?>">
        <input type="hidden" class="price-to" name="price[max_price]" value="<?php echo esc_attr($swpf_price_to); ?>">

        <div class="swpf-price-amount-slider">
            <?php echo esc_html__('Price', 'super-product-filter') ?>
            <span class="swpf-price-from">
                <?php
                if (empty($swpf_price_from)) {
                    $swpf_price_from = $swpf_price_min;
                }
                $this->render_price($swpf_price_from);
                ?>
            </span>-
            <span class="swpf-price-to">
                <?php
                if (empty($swpf_price_to)) {
                    $swpf_price_to = $swpf_price_max;
                }
                $this->render_price($swpf_price_to);
                ?>
            </span>
        </div>
        <input type="hidden" class="amount" value="">
        <input type="hidden" class="price-min" value="<?php echo esc_attr($swpf_price_min); ?>">
        <input type="hidden" class="price-max" value="<?php echo esc_attr($swpf_price_max); ?>">
    </div>
</div>