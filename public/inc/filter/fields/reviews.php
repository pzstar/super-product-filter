<?php
defined('ABSPATH') || die();

$swpf_sc_id = $this->filter_shortcode_id;
$swpf_sc_title = get_the_title($swpf_sc_id);
?>

<div class="swpf-filter-reviews-wrap">
    <?php
    if (isset($swpf_settings['title_label']['reviews']) && !empty($swpf_settings['title_label']['reviews'])) {
        ?>
        <div class="swpf-filter-title">
            <h4 class="swpf-filter-title-heading">
                <?php echo esc_html(apply_filters('swpf_translate_string', $swpf_settings['title_label']['reviews'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Taxonomy Name reviews')); ?>
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
        <div class="swpf-filter-item-list swpf-list-review">
            <div class="swpf-filter-item swpf-review-item">
                <?php
                $swpf_review_from = null;

                if (isset($swpf_current_filter_option['review']['review_from'])) {
                    $swpf_review_from = intval($swpf_current_filter_option['review']['review_from']);
                }

                if (isset($swpf_current_filter_option['review']['review_to'])) {
                    $swpf_review_to = intval($swpf_current_filter_option['review']['review_to']);
                }
                ?>
                <label><?php esc_html_e('From', 'super-product-filter') ?></label>
                <input name="review-from" value="<?php echo esc_attr($swpf_review_from); ?>" type="number">
            </div>
        </div>
    </div>
</div>