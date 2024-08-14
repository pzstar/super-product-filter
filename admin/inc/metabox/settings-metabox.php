<?php
defined('ABSPATH') || die();

global $post;
$post_id = $post->ID;
$settings = get_post_meta($post_id, 'swpf_settings', true);

if (!$settings) {
    $settings = self::default_settings_values();
} else {
    $settings = self::recursive_parse_args($settings, self::default_settings_values());
}

wp_nonce_field('swpf-settings-nonce', 'swpf_settings_nonce');

// Custom Typography settings
$custom = isset($settings['custom']) ? $settings['custom'] : null;
$standard_fonts = swpf_get_standard_font_families();
$google_fonts = swpf_get_google_font_families();
$text_transforms = swpf_get_text_transform_choices();
$text_decorations = swpf_get_text_decoration_choices();
?>
<div class="swpf-settings-main-wrapper">
    <div class="swpf-settings-inner-wrap">
        <div class="swpf-tab-options-wrap">
            <ul>
                <li class="swpf-tab swpf-tab-active" data-tab="swpf-filters" data-tohide="tab-content"><?php esc_html_e('Filters', 'super-product-filter'); ?></li>
                <li class="swpf-tab" data-tab="appearance-settings" data-tohide="tab-content"><?php esc_html_e('Settings', 'super-product-filter'); ?></li>
                <li class="swpf-tab" data-tab="swpf-display-settings" data-tohide="tab-content"><?php esc_html_e('Display Settings', 'super-product-filter'); ?></li>
                <li class="swpf-tab" data-tab="swpf-design-settings" data-tohide="tab-content"><?php esc_html_e('Designs', 'super-product-filter'); ?></li>
                <li class="swpf-tab" data-tab="swpf-custom-code" data-tohide="tab-content"><?php esc_html_e('Custom Code', 'super-product-filter'); ?></li>
                <li class="swpf-tab" data-tab="import-export-settings" data-tohide="tab-content"><?php esc_html_e('Import/Export', 'super-product-filter'); ?></li>
            </ul>
        </div>

        <?php
        include SWPF_PATH . 'admin/inc/metabox/boxes/swpf-filters.php';
        include SWPF_PATH . 'admin/inc/metabox/boxes/appearance-settings.php';
        include SWPF_PATH . 'admin/inc/metabox/boxes/design-settings.php';
        include SWPF_PATH . 'admin/inc/metabox/boxes/custom-code.php';
        include SWPF_PATH . 'admin/inc/metabox/boxes/display-settings.php';
        include SWPF_PATH . 'admin/inc/metabox/boxes/import-export-settings.php';
        ?>
    </div>

    <div class="swpf-settings-footer">
        <button type="submit" class="button button-primary"><?php echo esc_html__('Save Settings', 'super-product-filter'); ?></button>
    </div>
</div>