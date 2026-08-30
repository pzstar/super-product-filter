<?php
defined('ABSPATH') || die();

global $post;
$post_id = $post->ID;
$swpf_settings = get_post_meta($post_id, 'swpf_settings', true);

if (!$swpf_settings) {
    $swpf_settings = self::default_settings_values();
} else {
    $swpf_settings = Super_Product_Filter_Admin::recursive_parse_args($swpf_settings, self::default_settings_values());
}

wp_nonce_field('swpf-settings-nonce', 'swpf_settings_nonce');

?>
<div class="swpf-settings-main-wrapper">
    <div class="swpf-settings-inner-wrap">
        <div class="swpf-tab-options-wrap">
            <ul>
                <li class="swpf-tab swpf-tab-active" data-tab="swpf-filters" data-tohide="tab-content"><?php esc_html_e('Filters', 'super-product-filter'); ?></li>
                <li class="swpf-tab" data-tab="appearance-settings" data-tohide="tab-content"><?php esc_html_e('Settings', 'super-product-filter'); ?></li>
                <li class="swpf-tab" data-tab="swpf-display-settings" data-tohide="tab-content"><?php esc_html_e('Display Settings', 'super-product-filter'); ?></li>
                <li class="swpf-tab" data-tab="swpf-design-settings" data-tohide="tab-content"><?php esc_html_e('Designs', 'super-product-filter'); ?></li>
                <li class="swpf-tab" data-tab="import-export-settings" data-tohide="tab-content"><?php esc_html_e('Import/Export', 'super-product-filter'); ?></li>
                <li class="swpf-tab" data-tab="free-vs-pro-settings" data-tohide="tab-content"><?php esc_html_e('Free Vs Pro', 'super-product-filter'); ?></li>
            </ul>
        </div>

        <?php
        include SWPF_PATH . 'admin/inc/cpt/metabox/boxes/swpf-filters.php';
        include SWPF_PATH . 'admin/inc/cpt/metabox/boxes/appearance.php';
        include SWPF_PATH . 'admin/inc/cpt/metabox/boxes/design.php';
        include SWPF_PATH . 'admin/inc/cpt/metabox/boxes/display.php';
        include SWPF_PATH . 'admin/inc/cpt/metabox/boxes/import-export.php';
        include SWPF_PATH . 'admin/inc/cpt/metabox/boxes/free-vs-pro.php';
        ?>
    </div>

    <div class="swpf-settings-footer">
        <a href="https://codecanyon.net/item/super-woocommerce-product-filters/49852702" target="_blank" class="button button-primary swpf-upgrade-to-pro"><?php echo esc_html__('Upgrade to Pro', 'super-product-filter'); ?></a>
        <button type="submit" class="button button-primary"><?php echo esc_html__('Save Settings', 'super-product-filter'); ?></button>
    </div>
</div>