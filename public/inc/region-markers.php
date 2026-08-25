<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Tags the shop loop regions in the rendered page.
 *
 * The filter has to know which elements to replace when results come back. Asking
 * the shop owner for CSS selectors puts the burden of knowing their theme's markup
 * on them, so instead the regions are tagged as WooCommerce renders them and the
 * script looks for those tags. The selector settings remain available as an
 * override for themes this cannot reach.
 */
class Super_Product_Filter_Region_Markers {

    public function __construct() {
        add_filter('woocommerce_product_loop_start', array($this, 'mark_loop_start'));
        add_action('wp', array($this, 'wrap_woocommerce_output'));
    }

    /**
     * Add the region attribute to the first tag in a fragment.
     *
     * @param string $html     Markup to tag.
     * @param string $region   Region name.
     * @param string $source   Which query rendered the loop: main or shortcode.
     * @param int    $per_page How many products this loop shows per page.
     * @return string
     */
    public static function mark($html, $region, $source = '', $per_page = 0) {
        if (!is_string($html) || '' === trim($html)) {
            return $html;
        }

        // Never tag the same fragment twice.
        if (false !== strpos($html, 'data-swpf-region')) {
            return $html;
        }

        $attributes = 'data-swpf-region="' . esc_attr($region) . '"';

        if ($source) {
            $attributes .= ' data-swpf-source="' . esc_attr($source) . '"';
        }

        if ($per_page > 0) {
            $attributes .= ' data-swpf-per-page="' . esc_attr($per_page) . '"';
        }

        // A callback keeps the attribute string clear of backreference syntax.
        return preg_replace_callback(
            '/<([a-zA-Z][a-zA-Z0-9-]*)/',
            function ($matches) use ($attributes) {
                return '<' . $matches[1] . ' ' . $attributes;
            },
            $html,
            1
        );
    }

    /**
     * Which query produced the loop currently rendering.
     *
     * Shortcodes and the page builder widgets built on them run their own query,
     * which the filter does not touch, so a loop from one of those cannot be
     * refreshed by replaying a page render.
     *
     * @return string
     */
    public static function loop_source() {
        if (function_exists('wc_get_loop_prop') && wc_get_loop_prop('is_shortcode')) {
            return 'shortcode';
        }

        return 'main';
    }

    /**
     * @param string $html Opening markup of the product loop.
     * @return string
     */
    public function mark_loop_start($html) {
        /*
         * Record the page size alongside the loop. A shortcode carries its own
         * limit, which the filter has no other way of knowing, so without this
         * the number of products changes the moment someone filters.
         */
        $per_page = function_exists('wc_get_loop_prop') ? absint(wc_get_loop_prop('per_page')) : 0;

        return self::mark($html, 'products', self::loop_source(), $per_page);
    }

    /**
     * Re-register WooCommerce's result count and pagination so their output can be
     * tagged. The original priority is reused, and nothing happens when a theme has
     * already unhooked them, in which case the script falls back to its own lookup.
     */
    public function wrap_woocommerce_output() {
        $count_priority = has_action('woocommerce_before_shop_loop', 'woocommerce_result_count');
        if (false !== $count_priority) {
            remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', $count_priority);
            add_action('woocommerce_before_shop_loop', array($this, 'render_result_count'), $count_priority);
        }

        $pagination_priority = has_action('woocommerce_after_shop_loop', 'woocommerce_pagination');
        if (false !== $pagination_priority) {
            remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', $pagination_priority);
            add_action('woocommerce_after_shop_loop', array($this, 'render_pagination'), $pagination_priority);
        }
    }

    public function render_result_count() {
        ob_start();
        woocommerce_result_count();
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce template output, already escaped by the template.
        echo self::mark(ob_get_clean(), 'result-count', self::loop_source());
    }

    public function render_pagination() {
        ob_start();
        woocommerce_pagination();
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce template output, already escaped by the template.
        echo self::mark(ob_get_clean(), 'pagination', self::loop_source());
    }
}

new Super_Product_Filter_Region_Markers();
