<div class="swpf-general-settings">
    <?php
    if (swpf_get_post('updated') === 'true') {
        $this->handle_generalsettingsform();
    }

    $general_settings = get_option('swpf_general_settings');
    if (!$general_settings) {
        $general_settings = self::default_general_settings_values();
    } else {
        $general_settings = self::recursive_parse_args($general_settings, self::default_general_settings_values());
    }
    ?>

    <form method="POST">
        <input type="hidden" name="updated" value="true" />
        <?php wp_nonce_field('swpf_nonce_update_general_settings', 'swpf_nonce'); ?>
        <h2 class="swpf-main-header"><?php esc_html_e('General Settings', 'super-product-filter'); ?></h2>

        <div class="swpf-settings-row">
            <label>
                <input type="checkbox" name="swpf_general_settings[load_fonts_locally]" <?php checked($general_settings['load_fonts_locally'], 'on', true); ?>>
                <strong><?php esc_html_e('Load Google Fonts Locally', 'super-product-filter'); ?></strong>
            </label>
            <p class="swpf-desc"><?php esc_html_e('It is required to load the Google Fonts locally in order to comply with GDPR. However, if your website is not required to comply with GDPR then you can check this field off. Loading the Fonts locally with lots of different Google fonts can decrease the speed of the website slightly.', 'super-product-filter'); ?></p>
        </div>

        <div class="swpf-save-settings">
            <button type="submit" name="submit" class="button button-primary"><?php esc_html_e('Save Settings', 'super-product-filter'); ?></button>
        </div>
    </form>
</div>