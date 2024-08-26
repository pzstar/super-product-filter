<?php
defined('ABSPATH') || die();

$sc_id = $this->filter_shortcode_id;
$sc_title = get_the_title($sc_id);

if (isset($settings['title_label']['on_sale']) && ! empty($settings['title_label']['on_sale'])) {
    ?>
    <div class="swpf-filter-title">
        <h4 class="swpf-filter-title-heading">
            <?php echo esc_html(apply_filters('swpf_translate_string', $settings['title_label']['on_sale'], 'Super Product Filter', esc_html($sc_title) . ' - Taxonomy Name on_sale')); ?>
        </h4>
        <?php if ($settings['config']['show_filter_list_toggle'] == 'on') { ?>
            <i class="swpf-filter-title-toggle swpf-minus-icon"></i>
        <?php } ?>
    </div>
<?php } ?>

<div class="swpf-filter-content">
    <div class="swpf-tax-list-wrapper">
        <div class="swpf-filter-item-list swpf-checkbox-type">
            <div class="swpf-filter-item <?php isset($current_filter_option['on-sale']) ? selected($current_filter_option['on-sale'], '1') : ''; ?>">
                <label class="swpf-filter-label">
                    <input type="checkbox" value="1" name="on-sale" <?php isset($current_filter_option['on-sale']) ? checked($current_filter_option['on-sale'], '1') : ''; ?>/>
                    <span class="swpf-title">
                        <span class="swpf-term"><?php esc_html_e('Show On Sale only', 'super-product-filter'); ?></span>
                    </span>
                </label>
            </div>
        </div>
    </div>
</div>