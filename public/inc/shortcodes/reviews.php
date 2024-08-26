<?php
defined('ABSPATH') || die();

$sc_id = $this->filter_shortcode_id;
$sc_title = get_the_title($sc_id);
?>

<div class="swpf-filter-reviews-wrap">
    <?php if (isset($settings['title_label']['reviews']) && !empty($settings['title_label']['reviews'])) { ?>
        <div class="swpf-filter-title">
            <h4 class="swpf-filter-title-heading">
                <?php echo esc_html(apply_filters('swpf_translate_string', $settings['title_label']['reviews'], 'Super Product Filter', esc_html($sc_title) . ' - Taxonomy Name reviews')); ?>
            </h4>
            <?php if ($settings['config']['show_filter_list_toggle'] == 'on') { ?>
                <i class="swpf-filter-title-toggle swpf-minus-icon"></i>
            <?php } ?>
        </div>
    <?php } ?>

    <div class="swpf-filter-content">
        <div class="swpf-filter-item-list swpf-list-review">
            <div class="swpf-filter-item swpf-review-item">
                <?php
                $review_from = null;
                if (isset($current_filter_option['review']['review_from'])) {
                    $review_from = intval($current_filter_option['review']['review_from']);
                }
                if (isset($current_filter_option['review']['review_to'])) {
                    $review_to = intval($current_filter_option['review']['review_to']);
                }
                ?>
                <label><?php esc_html_e('From', 'super-product-filter') ?></label>
                <input name="review-from" value="<?php echo esc_attr($review_from); ?>" type="number">
            </div>
        </div>
    </div>
</div>