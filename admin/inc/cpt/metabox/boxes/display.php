<?php
defined('ABSPATH') || die();
?>

<div class="swpf-options-fields-wrap tab-content swpf-settings-content" id="swpf-display-settings" style="display: none;">
    <div class="swpf-field-inline-wrap">
        <div class="swpf-field-inline-wrap swpf-display-with-shortcode">
            <div class="swpf-field-wrap">
                <label><?php esc_html_e('Shortcode', 'super-product-filter'); ?></label>
                <div class="swpf-settings-input-field swpf-settings-shortcode-field">
                    <input type="text" readonly name="swpf_settings[shortcode]" id="swpf-shortcode-field" value="<?php echo esc_attr('[swpf_shortcode id="' . absint($post_id) . '"]', 'super-product-filter'); ?>">
                    <button id="swpf-copy-shortcode"><?php echo esc_html('Copy Shortcode', 'super-product-filter'); ?></button>
                    <div id="swpf-copied-shortcode" style="display: none;"></div>
                </div>
            </div>
        </div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Display as OffCanvas Menu after', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <div class="swpf-range-slider-field">
                    <div class="swpf-range-slider"></div>
                    <input class="swpf-range-input" type="number" min="320" max="1400" step="1" value="<?php echo esc_attr($settings['responsive_width']); ?>" name="swpf_settings[responsive_width]" /> px
                </div>
                <p class="swpf-desc"><?php esc_html_e('The product filters will be hidden and will display on clicking a Toggle Button as OffCanvase Menu.', 'super-product-filter'); ?></p>
            </div>
        </div>
    </div>

</div>