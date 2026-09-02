<?php
defined('ABSPATH') || die();
?>

<div class="swpf-general-settings">
    <?php
    $swpf_general_settings = get_option('swpf_general_settings');
    if (!$swpf_general_settings) {
        $swpf_general_settings = self::default_general_settings_values();
    } else {
        $swpf_general_settings = Super_Product_Filter_Admin::recursive_parse_args($swpf_general_settings, self::default_general_settings_values());
    }
    ?>

    <form method="POST">
        <input type="hidden" name="updated" value="true" />
        <?php wp_nonce_field('swpf_nonce_update_general_settings', 'swpf_nonce'); ?>
        <?php
        /* The same bar the filter builder uses, so moving between the two
           screens does not feel like moving between two plugins. */
        ?>
        <div class="swpf-settings-topbar">
            <span class="swpf-settings-mark" aria-hidden="true">
                <span class="dashicons dashicons-admin-generic"></span>
            </span>
            <h2 class="swpf-main-header"><?php esc_html_e('General Settings', 'super-product-filter'); ?></h2>
        </div>

        <div class="swpf-settings-card">

        <div class="swpf-settings-row swpf-settings-option">
            <div class="swpf-option-text">
                <strong class="swpf-option-title"><?php esc_html_e('Load Google Fonts Locally', 'super-product-filter'); ?></strong>
                <p class="swpf-desc"><?php esc_html_e('Google Fonts must be loaded locally to meet GDPR standards. If your website does not need to comply with GDPR, you can turn this option off. Keep in mind that hosting multiple Google Fonts locally may slightly slow down your website.', 'super-product-filter'); ?></p>
            </div>
            <div class="swpf-toggle-wrap">
                <label class="swpf-toggle">
                    <input type="checkbox" name="swpf_general_settings[load_fonts_locally]" <?php checked($swpf_general_settings['load_fonts_locally'], 'on', true); ?>>
                    <span></span>
                </label>
            </div>
        </div>

        <div class="swpf-settings-row swpf-settings-option">
            <div class="swpf-option-text">
                <strong class="swpf-option-title"><?php esc_html_e('Keep Filtered Results Out of Search Engines', 'super-product-filter'); ?></strong>
                <p class="swpf-desc"><?php esc_html_e('Filtered views show products that already have their own category and product pages. Letting search engines index every combination of filters creates near duplicate pages and uses up the crawl budget your real pages need. With this on, a filtered view is marked "noindex, follow", so it stays out of search results while links on the page are still followed. Recommended for most stores.', 'super-product-filter'); ?></p>
            </div>
            <div class="swpf-toggle-wrap">
                <label class="swpf-toggle">
                    <input type="checkbox" name="swpf_general_settings[noindex_filtered]" <?php checked($swpf_general_settings['noindex_filtered'], 'on', true); ?>>
                    <span></span>
                </label>
            </div>
        </div>

        <div class="swpf-settings-row swpf-settings-option">
            <div class="swpf-option-text">
                <strong class="swpf-option-title"><?php esc_html_e('Theme Compatibility Mode', 'super-product-filter'); ?></strong>
                <p class="swpf-desc"><?php esc_html_e('Turn this on if your products look wrong after filtering. Normally the filter rebuilds the product list using WooCommerce\'s default templates, which does not match themes and page builders that render their own product cards. In this mode the filtered page is requested from your site instead, so products come back in exactly the markup your theme already uses. It is slightly slower because the whole page is rendered, so leave it off unless you need it. This applies to shop and category pages, where the filter drives the product list. A loop added by a shortcode or a page builder widget runs its own query, so those keep using the standard method.', 'super-product-filter'); ?></p>
            </div>
            <div class="swpf-toggle-wrap">
                <label class="swpf-toggle">
                    <input type="checkbox" name="swpf_general_settings[compat_mode]" <?php checked($swpf_general_settings['compat_mode'], 'on', true); ?>>
                    <span></span>
                </label>
            </div>
        </div>

        <div class="swpf-settings-row swpf-settings-option swpf-settings-option-danger">
            <div class="swpf-option-text">
                <strong class="swpf-option-title"><?php esc_html_e('Delete All Data on Uninstall', 'super-product-filter'); ?></strong>
                <p class="swpf-desc"><?php esc_html_e('Removes every filter and setting this plugin created when you delete it from the Plugins screen. This cannot be undone, and deactivating alone never removes anything. Leave this off if you might reinstall later or are only moving to another server, and your filters will still be here.', 'super-product-filter'); ?></p>
            </div>
            <div class="swpf-toggle-wrap">
                <label class="swpf-toggle">
                    <input type="checkbox" name="swpf_general_settings[delete_data_on_uninstall]" <?php checked($swpf_general_settings['delete_data_on_uninstall'], 'on', true); ?>>
                    <span></span>
                </label>
            </div>
        </div>

            <div class="swpf-save-settings swpf-general-settings-btn">
                <button type="submit" name="submit" class="button button-primary"><?php esc_html_e('Save Settings', 'super-product-filter'); ?></button>
            </div>
        </div>
    </form>
</div>