<?php
defined('ABSPATH') || die();
?>

<div class="swpf-options-fields-wrap tab-content swpf-settings-content" id="shortcode-settings" style="display: none;">
    <h3><?php esc_html_e('Get Shortcode', 'super-product-filter') ?></h3>

    <div class="swpf-option-fields-inner-wrap">
        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Shortcode', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <input type="text" readonly name="swpf_settings[shortcode]" id="swpf-shortcode-field" value="<?php echo esc_attr('[swpf_shortcode id="' . absint($post_id) . '"]'); ?>">
                <button id="swpf-copy-shortcode"><?php echo esc_html('Copy Shortcode', 'super-product-filter'); ?></button>
            </div>
        </div>
        <div id="swpf-copied-shortcode" style="display: none;"></div>
    </div>
</div>