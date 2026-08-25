<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Keeps filtered product views out of search engine indexes.
 *
 * A filtered view rearranges products that already have their own canonical
 * category and product pages. Letting crawlers index every combination of
 * filters produces near duplicate results and spends crawl budget that the
 * real pages need, so filtered views are marked noindex while staying
 * followable.
 */
class Super_Product_Filter_SEO {

    public function __construct() {
        add_action('admin_init', array($this, 'set_initial_default'));
        add_filter('wp_robots', array($this, 'filtered_view_robots'));
    }

    /**
     * Decide the starting value once, per site.
     *
     * New sites opt in. Sites that already had the plugin keep filtered views
     * indexable, so updating never pulls pages out of an index without the
     * site owner choosing it.
     */
    public function set_initial_default() {
        if (get_option('swpf_seo_defaults_set')) {
            return;
        }

        $existing = get_option('swpf_general_settings');
        $settings = is_array($existing) ? $existing : array();

        if (!isset($settings['noindex_filtered'])) {
            $settings['noindex_filtered'] = $existing ? 'off' : 'on';
            update_option('swpf_general_settings', $settings);
        }

        update_option('swpf_seo_defaults_set', 1);
    }

    /**
     * Whether the site owner has the option switched on.
     *
     * @return bool
     */
    public static function is_enabled() {
        $settings = get_option('swpf_general_settings');

        return is_array($settings) && isset($settings['noindex_filtered']) && 'on' === $settings['noindex_filtered'];
    }

    /**
     * Whether the current request carries at least one filter parameter.
     *
     * @return bool
     */
    public static function is_filtered_view() {
        if (is_admin() || wp_doing_ajax() || is_feed()) {
            return false;
        }

        foreach (swpf_get_filter_query_keys() as $swpf_key) {
            // WordPress already noindexes search results, so 's' is left alone.
            if ('s' === $swpf_key) {
                continue;
            }

            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read only check on a public page, no state is changed.
            if (isset($_GET[$swpf_key]) && '' !== $_GET[$swpf_key]) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $robots Directives keyed by name.
     * @return array
     */
    public function filtered_view_robots($robots) {
        if (!self::is_enabled() || !self::is_filtered_view()) {
            return $robots;
        }

        unset($robots['index']);

        $robots['noindex'] = true;
        $robots['follow'] = true;

        return $robots;
    }
}

new Super_Product_Filter_SEO();
