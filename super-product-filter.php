<?php

/**
 * Plugin Name:       Super Product Filter for WooCommerce
 * Plugin URI:        https://demo.hashthemes.com/super-woocommerce-product-filter/
 * Description:       Ajax Filter For WooCommerce Products - Simplify your Search, Save your Time.
 * Version:           1.0.0
 * Author:            hashthemes
 * Author URI:        https://hashthemes.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       super-product-filter
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

function super_product_filter_activate() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-super-product-filter-activator.php';
    Super_Product_Filter_Activator::activate();
}

function super_product_filter_deactivate() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-super-product-filter-deactivator.php';
    Super_Product_Filter_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'super_product_filter_activate');
register_deactivation_hook(__FILE__, 'super_product_filter_deactivate');

function super_product_filter_run() {

    if (!function_exists('is_plugin_active')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    if (is_plugin_active('woocommerce/woocommerce.php')) {
        if (!is_plugin_active('super-woocommerce-product-filter/super-woocommerce-product-filter.php')) {
            define('SWPF_VERSION', '1.0.0');
            define('SWPF_PATH', plugin_dir_path(__FILE__));
            define('SWPF_URL', plugin_dir_url(__FILE__));
            define('SWPF_BASENAME', plugin_basename(__FILE__));
            require plugin_dir_path(__FILE__) . 'includes/class-super-product-filter.php';
            $plugin = new Super_Product_Filter();
            $plugin->run();
        }
    } else {
        add_action('admin_notices', function () {
            $message = sprintf(/* translators: Placeholders: %1$s and %2$s are <strong> tags. %3$s and %4$s are <a> tags */
                esc_html__('%1$sSuper Product Filter for WooCommerce %2$s requires WooCommerce Plugin. Please install and activate %3$sWooCommerce%4$s.', 'super-product-filter'), '<strong>', '</strong>', '<a href="' . esc_url(admin_url('plugin-install.php?s=woocommerce&tab=search&type=term')) . '">', '</a>'
            );

            echo sprintf('<div class="error"><p>%s</p></div>', $message);
        });
    }
}

super_product_filter_run();
