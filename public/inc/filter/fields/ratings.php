<?php
defined('ABSPATH') || die();

$swpf_sc_id = $this->filter_shortcode_id;
$swpf_sc_title = get_the_title($swpf_sc_id);
?>

<div class="swpf-filter-rating-wrap">
    <?php
    if (isset($settings['title_label']['ratings']) && !empty($settings['title_label']['ratings'])) {
        ?>
        <div class="swpf-filter-title">
            <h4 class="swpf-filter-title-heading">
                <?php echo esc_html(apply_filters('swpf_translate_string', $settings['title_label']['ratings'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Taxonomy Name ratings')); ?>
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
        $swpf_selected_val = [];
        if (isset($current_filter_option['rating-from']) && !empty($current_filter_option['rating-from'])) {
            $swpf_selected_val = $current_filter_option['rating-from'];
        }
        ?>
        <div class="swpf-filter-item-list swpf-list-rating">
            <?php
            for ($swpf_star = 5; $swpf_star >= 1; $swpf_star--) {
                $swpf_class_item = in_array($swpf_star, $swpf_selected_val) ? ' selected' : null;
                ?>
                <div class="swpf-filter-item swpf-rating-item <?php echo esc_attr($swpf_class_item); ?>">
                    <label class="swpf-filter-label">
                        <input type="radio" name="rating-from[]" value="<?php echo esc_attr($swpf_star); ?>" class="swpf-rating-input" <?php isset($swpf_selected_val) ? checked(in_array($swpf_star, $swpf_selected_val), true) : ''; ?>>
                        <div class="swpf-rating-star swpf-<?php echo esc_attr($swpf_star); ?>-star" data-rating-star="<?php echo esc_attr($swpf_star); ?>">
                            <i class="swpf-each-star"></i>
                        </div>
                    </label>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>