<div class="swpf-options-fields-wrap tab-content swpf-settings-content" id="swpf-custom-code" style="display: none;">
    <div class="swpf-field-inline-wrap">
        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Before Filter JavaScript', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <textarea class="swpf-codemirror-js-textarea" name="swpf_settings[advanced_settings][before_filter_js]"><?php echo stripslashes($settings['advanced_settings']['before_filter_js']); ?></textarea>
                <p class="swpf-desc"><?php esc_html_e('This code runs just before the filter is executed.', 'super-product-filter'); ?></p>
            </div>
        </div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('After Filter JavaScript', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <textarea class="swpf-codemirror-js-textarea" name="swpf_settings[advanced_settings][after_filter_js]"><?php echo stripslashes($settings['advanced_settings']['after_filter_js']); ?></textarea>
                <p class="swpf-desc"><?php esc_html_e('This code runs after the filter is completed.', 'super-product-filter'); ?></p>
            </div>
        </div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Custom CSS', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <textarea class="swpf-codemirror-css-textarea" name="swpf_settings[advanced_settings][custom_css]"><?php echo stripslashes($settings['advanced_settings']['custom_css']); ?></textarea>
            </div>
        </div>
    </div>
</div>