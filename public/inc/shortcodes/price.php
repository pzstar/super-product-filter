<?php
defined('ABSPATH') || die();
if (wp_is_mobile()) {
    wp_enqueue_script('touch-punch');
}
$sc_id = $this->shortcode_id;
$sc_title = get_the_title($sc_id);
?>
<div class="swpf-pricerange swpf-filter-pricerange-wrap swpf-filter-byprice slider-price">
    <?php
    if (isset($settings['title_label']['price_range']) && !empty($settings['title_label']['price_range'])) {
        ?>
        <div class="swpf-filter-title">
            <h4 class="swpf-filter-title-heading">
                <?php
                echo esc_html(apply_filters('swpf_translate_string', $settings['title_label']['price_range'], 'Super Product Filter', esc_html($sc_title) . ' - Taxonomy Name price_range'));
                ?>
            </h4>
            <?php
            if ($settings['config']['show_filter_list_toggle'] == 'on') {
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
        $price_min = $min_price;
        $price_max = $max_price;
        $price_from = isset($current_filter_option['price']['min_price']) ? $current_filter_option['price']['min_price'] : '';
        $price_to = isset($current_filter_option['price']['max_price']) ? $current_filter_option['price']['max_price'] : '';
        if (isset($settings['pricerangeslider']['skin']) && $settings['pricerangeslider']['skin'] == 'swpf-pricerangeslider-skin-1') {
            ?>
            <div class="swpf-range-slider"></div>
            <?php
        } else {
            $ionSkin = 'flat';
            if (isset($settings['pricerangeslider']['skin'])) {
                if ($settings['pricerangeslider']['skin'] == 'swpf-pricerangeslider-skin-2') {
                    $ionSkin = 'flat';
                } elseif ($settings['pricerangeslider']['skin'] == 'swpf-pricerangeslider-skin-3') {
                    $ionSkin = 'big';
                } elseif ($settings['pricerangeslider']['skin'] == 'swpf-pricerangeslider-skin-4') {
                    $ionSkin = 'modern';
                } elseif ($settings['pricerangeslider']['skin'] == 'swpf-pricerangeslider-skin-5') {
                    $ionSkin = 'sharp';
                } elseif ($settings['pricerangeslider']['skin'] == 'swpf-pricerangeslider-skin-6') {
                    $ionSkin = 'round';
                } elseif ($settings['pricerangeslider']['skin'] == 'swpf-pricerangeslider-skin-7') {
                    $ionSkin = 'square';
                }
            }
            ?>
            <div class="swpf-range-irs-slider">
                <input type="text" class="swpf-irs-slider" value="" data-type="double" data-skin="<?php echo esc_attr($ionSkin); ?>" data-min="<?php echo esc_attr($price_min); ?>" data-max="<?php echo esc_attr($price_max); ?>" data-from="<?php echo esc_attr($price_from); ?>" data-to="<?php echo esc_attr($price_to); ?>" data-grid="true" />
            </div>
            <?php
        }
        ?>

        <input type="hidden" class="price-from" name="price[min_price]" value="<?php echo esc_attr($price_from); ?>">
        <input type="hidden" class="price-to" name="price[max_price]" value="<?php echo esc_attr($price_to); ?>">

        <div class="swpf-price-amount-slider">
            <?php echo esc_html__('Price', 'super-product-filter') ?>
            <span class="swpf-price-from">
                <?php
                if (empty($price_from)) {
                    $price_from = $price_min;
                }
                $this->render_price($price_from);
                ?>
            </span>-
            <span class="swpf-price-to">
                <?php
                if (empty($price_to)) {
                    $price_to = $price_max;
                }
                $this->render_price($price_to);
                ?>
            </span>
        </div>
        <?php
        ?>
        <input type="hidden" class="amount" value="">
        <input type="hidden" class="price-min" value="<?php echo esc_attr($price_min); ?>">
        <input type="hidden" class="price-max" value="<?php echo esc_attr($price_max); ?>">
    </div>
</div>