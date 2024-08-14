<?php
defined('ABSPATH') || die();

$sc_id = $this->filter_shortcode_id;
$sc_title = get_the_title($sc_id);

if (isset($settings['title_label']['in_stock']) and ! empty($settings['title_label']['in_stock'])) {
    ?>
    <div class="swpf-filter-title">
        <h4 class="swpf-filter-title-heading">
            <?php echo esc_html(apply_filters('swpf_translate_string', $settings['title_label']['in_stock'], 'Super Product Filter', esc_html($sc_title) . ' - Taxonomy Name in_stock')); ?>
        </h4>
        <?php if ($settings['config']['show_filter_list_toggle'] == 'on') { ?>
            <i class="swpf-filter-title-toggle swpf-minus-icon"></i>
        <?php } ?>
    </div>
<?php } ?>

<div class="swpf-filter-content">
    <div class="swpf-tax-list-wrapper">
        <div class="swpf-filter-item-list swpf-checkbox-type">
            <div class="swpf-filter-item <?php isset($current_filter_option['in-stock']) ? selected($current_filter_option['in-stock'], '1') : ''; ?>">
                <label class="swpf-filter-label">
                    <input type="checkbox" value="1" name="in-stock" <?php isset($current_filter_option['in-stock']) ? checked($current_filter_option['in-stock'], '1') : ''; ?>/>
                    <span class="swpf-title">
                        <span class="swpf-term"><?php esc_html_e('Show In Stock Only', 'super-product-filter') ?></span>
                    </span>
                </label>
            </div>
        </div>
    </div>
</div>