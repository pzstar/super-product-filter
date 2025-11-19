<?php
defined('ABSPATH') || die();
?>

<div class="swpf-general-settings">
    <?php
    $general_settings = get_option('swpf_general_settings');
    if (!$general_settings) {
        $general_settings = self::default_general_settings_values();
    } else {
        $general_settings = Super_Product_Filter_Admin::recursive_parse_args($general_settings, self::default_general_settings_values());
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
            <p class="swpf-desc"><?php esc_html_e('Google Fonts must be loaded locally to meet GDPR standards. If your website does not need to comply with GDPR, you can turn this option off. Keep in mind that hosting multiple Google Fonts locally may slightly slow down your website.', 'super-product-filter'); ?></p>
        </div>

        <div class="swpf-save-settings swpf-general-settings-btn">
            <button type="submit" name="submit" class="button button-primary"><?php esc_html_e('Save Settings', 'super-product-filter'); ?></button>
        </div>
    </form>
</div>