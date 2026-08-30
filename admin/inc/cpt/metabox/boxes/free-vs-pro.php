<?php
defined('ABSPATH') || die();

/*
 * Free vs Pro comparison.
 *
 * Every figure here is the number of choices the plugin actually offers, so the
 * table can be checked against the settings screens rather than taken on trust.
 */

$swpf_demo = 'https://demo.hashthemes.com/super-woocommerce-product-filter/';

/**
 * One comparison row.
 *
 * @param string $feature Feature name.
 * @param string $desc    Short line explaining what it does. Optional.
 * @param array  $free    array(text, state) where state is yes|no|plain.
 * @param array  $pro     array(text, state).
 * @param string $demo    Demo page path, relative to the demo site. Optional.
 */
if (!function_exists('swpf_compare_row')) :
function swpf_compare_row($feature, $desc, $free, $pro, $demo = '') {
    $cell = function ($value) {
        list($text, $state) = $value;

        if ('yes' === $state) {
            return '<td class="swpf-yes"><i class="mdi-check"></i>' . esc_html($text) . '</td>';
        }

        if ('no' === $state) {
            return '<td class="swpf-no"><i class="mdi-close"></i>' . esc_html($text) . '</td>';
        }

        return '<td class="swpf-plain">' . esc_html($text) . '</td>';
    };

    $pro_text = $pro[0];
    $pro_cell = ('yes' === $pro[1] || 'no' === $pro[1])
        ? $cell($pro)
        : '<td class="swpf-pro">' . esc_html($pro_text) . '</td>';

    echo '<tr>';
    echo '<td class="swpf-feature"><span class="swpf-feature-name">' . esc_html($feature) . '</span>';

    if ($demo) {
        echo ' <a class="swpf-feature-demo" href="' . esc_url($demo) . '" target="_blank">' . esc_html__('Demo', 'super-product-filter') . '</a>';
    }

    if ($desc) {
        echo '<span class="swpf-feature-desc">' . esc_html($desc) . '</span>';
    }

    echo '</td>';
    echo $cell($free); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above from escaped parts.
    echo $pro_cell; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above from escaped parts.
    echo '</tr>';
}

function swpf_compare_heading($title) {
    echo '<tr class="swpf-compare-section"><td colspan="3">' . esc_html($title) . '</td></tr>';
}
endif;
?>

<div class="swpf-options-fields-wrap tab-content swpf-settings-content" id="free-vs-pro-settings" style="display: none;">

    <table class="swpf-compare-table">
        <tr>
            <th><?php esc_html_e('Feature', 'super-product-filter'); ?></th>
            <th><?php esc_html_e('Free Version', 'super-product-filter'); ?></th>
            <th><?php esc_html_e('Pro Version', 'super-product-filter'); ?></th>
        </tr>

        <?php
        swpf_compare_heading(esc_html__('What you can filter by', 'super-product-filter'));

        swpf_compare_row(
            esc_html__('AJAX Filtering', 'super-product-filter'),
            esc_html__('Results refresh in place, with no page reload.', 'super-product-filter'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes')
        );

        swpf_compare_row(
            esc_html__('Categories, Tags & Brands', 'super-product-filter'),
            '',
            array(esc_html__('Yes', 'super-product-filter'), 'yes'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes')
        );

        swpf_compare_row(
            esc_html__('Product Attributes', 'super-product-filter'),
            esc_html__('Any WooCommerce attribute, shown as checkboxes, radios, buttons, a dropdown, a multi select, toggles, colour or image swatches.', 'super-product-filter'),
            array(esc_html__('All 8 display types', 'super-product-filter'), 'yes'),
            array(esc_html__('All 8 display types', 'super-product-filter'), 'yes')
        );

        swpf_compare_row(
            esc_html__('Price Range Slider', 'super-product-filter'),
            esc_html__('Pro can also move the slider ends to match the products currently shown.', 'super-product-filter'),
            array(esc_html__('Fixed range', 'super-product-filter'), 'yes'),
            array(esc_html__('Range follows the results', 'super-product-filter'), 'plain')
        );

        swpf_compare_row(
            esc_html__('Rating, Stock & On Sale', 'super-product-filter'),
            '',
            array(esc_html__('Yes', 'super-product-filter'), 'yes'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes')
        );

        swpf_compare_row(
            esc_html__('Product Count Beside Each Term', 'super-product-filter'),
            '',
            array(esc_html__('Yes', 'super-product-filter'), 'yes'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes'),
            $swpf_demo . 'filter-with-product-count/'
        );

        swpf_compare_row(
            esc_html__('Search Inside a Filter', 'super-product-filter'),
            esc_html__('A search box that narrows a long list of terms as you type.', 'super-product-filter'),
            array(esc_html__('No', 'super-product-filter'), 'no'),
            array(esc_html__('Yes', 'super-product-filter'), 'plain')
        );

        swpf_compare_heading(esc_html__('Filtering by your own data', 'super-product-filter'));

        swpf_compare_row(
            esc_html__('ACF Field Filters', 'super-product-filter'),
            esc_html__('Turn Advanced Custom Fields values into filters.', 'super-product-filter'),
            array(esc_html__('No', 'super-product-filter'), 'no'),
            array(esc_html__('Yes', 'super-product-filter'), 'plain')
        );

        swpf_compare_row(
            esc_html__('Custom Meta Field Filters', 'super-product-filter'),
            esc_html__('Define your own meta keys and the options shoppers pick from.', 'super-product-filter'),
            array(esc_html__('No', 'super-product-filter'), 'no'),
            array(esc_html__('Yes', 'super-product-filter'), 'plain')
        );

        swpf_compare_row(
            esc_html__('Variation Images In The Loop', 'super-product-filter'),
            esc_html__('Filtering by a colour shows that colour on the product card.', 'super-product-filter'),
            array(esc_html__('No', 'super-product-filter'), 'no'),
            array(esc_html__('Yes', 'super-product-filter'), 'plain')
        );

        swpf_compare_heading(esc_html__('How the filters behave', 'super-product-filter'));

        swpf_compare_row(
            esc_html__('Match Any or Match All', 'super-product-filter'),
            esc_html__('Set AND or OR separately for each filter.', 'super-product-filter'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes')
        );

        swpf_compare_row(
            esc_html__('Hide Terms With No Products', 'super-product-filter'),
            esc_html__('Drop options that would return an empty result.', 'super-product-filter'),
            array(esc_html__('No', 'super-product-filter'), 'no'),
            array(esc_html__('Yes', 'super-product-filter'), 'plain'),
            $swpf_demo . 'hide-filter-attributes-with-no-products/'
        );

        swpf_compare_row(
            esc_html__('Show a Filter Only Where It Applies', 'super-product-filter'),
            esc_html__('Screen size appears for laptops, not for wine.', 'super-product-filter'),
            array(esc_html__('No', 'super-product-filter'), 'no'),
            array(esc_html__('Yes', 'super-product-filter'), 'plain'),
            $swpf_demo . 'selective-display-of-attributes/'
        );

        swpf_compare_row(
            esc_html__('Step-by-Step Filtering', 'super-product-filter'),
            esc_html__('Reveal the next filter only once the one before it is answered.', 'super-product-filter'),
            array(esc_html__('No', 'super-product-filter'), 'no'),
            array(esc_html__('Yes', 'super-product-filter'), 'plain'),
            $swpf_demo . 'step-by-step-filter/'
        );

        swpf_compare_row(
            esc_html__('Filter As You Click', 'super-product-filter'),
            esc_html__('Apply straight away, or wait for an Apply button.', 'super-product-filter'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes'),
            $swpf_demo . 'auto-submit-filter/'
        );

        swpf_compare_row(
            esc_html__('Readable Filter URLs', 'super-product-filter'),
            esc_html__('/shop/filter/categories-laptop/ instead of a query string.', 'super-product-filter'),
            array(esc_html__('No', 'super-product-filter'), 'no'),
            array(esc_html__('Yes', 'super-product-filter'), 'plain')
        );

        swpf_compare_row(
            esc_html__('Send Results To Another Page', 'super-product-filter'),
            esc_html__('Filter from anywhere and land the shopper on your shop.', 'super-product-filter'),
            array(esc_html__('No', 'super-product-filter'), 'no'),
            array(esc_html__('Yes', 'super-product-filter'), 'plain')
        );

        swpf_compare_heading(esc_html__('Where the filters go', 'super-product-filter'));

        swpf_compare_row(
            esc_html__('Shortcode & Block', 'super-product-filter'),
            esc_html__('Drop the filter into any page, sidebar or widget area.', 'super-product-filter'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes')
        );

        swpf_compare_row(
            esc_html__('Attach To The Shop Archive', 'super-product-filter'),
            esc_html__('Place the filter above the shop and category pages without editing a template.', 'super-product-filter'),
            array(esc_html__('No', 'super-product-filter'), 'no'),
            array(esc_html__('Yes', 'super-product-filter'), 'plain'),
            $swpf_demo . 'shop/'
        );

        swpf_compare_row(
            esc_html__('Off-Canvas Panel', 'super-product-filter'),
            esc_html__('A slide-out panel with its own trigger button. Pro adds show, hide and idle animations.', 'super-product-filter'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes'),
            array(esc_html__('Yes, with animations', 'super-product-filter'), 'plain'),
            $swpf_demo . 'off-canvas-filter/'
        );

        swpf_compare_row(
            esc_html__('Collapsible Filter Boxes', 'super-product-filter'),
            esc_html__('Let shoppers fold a filter away, or cap its height and scroll.', 'super-product-filter'),
            array(esc_html__('No', 'super-product-filter'), 'no'),
            array(esc_html__('Yes', 'super-product-filter'), 'plain'),
            $swpf_demo . 'open-close-filter-box/'
        );

        swpf_compare_row(
            esc_html__('Multi-Column Filter Layout', 'super-product-filter'),
            esc_html__('Lay filters, and the terms inside them, across columns.', 'super-product-filter'),
            array(esc_html__('No', 'super-product-filter'), 'no'),
            array(esc_html__('Yes', 'super-product-filter'), 'plain'),
            $swpf_demo . 'filter-with-fixed-height/'
        );

        swpf_compare_row(
            esc_html__('Reorder Filters By Dragging', 'super-product-filter'),
            '',
            array(esc_html__('Yes', 'super-product-filter'), 'yes'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes')
        );

        swpf_compare_row(
            esc_html__('Selected Filter Chips', 'super-product-filter'),
            esc_html__('Pro chooses where they sit and how they are grouped.', 'super-product-filter'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes'),
            array(esc_html__('Yes, positioned', 'super-product-filter'), 'plain')
        );

        swpf_compare_heading(esc_html__('Styling', 'super-product-filter'));

        swpf_compare_row(
            esc_html__('Checkbox & Radio Styles', 'super-product-filter'),
            '',
            array(esc_html__('1 style', 'super-product-filter'), 'plain'),
            array(esc_html__('11 styles', 'super-product-filter'), 'plain')
        );

        swpf_compare_row(
            esc_html__('Price Slider Styles', 'super-product-filter'),
            '',
            array(esc_html__('1 style', 'super-product-filter'), 'plain'),
            array(esc_html__('10 styles', 'super-product-filter'), 'plain')
        );

        swpf_compare_row(
            esc_html__('Button Styles', 'super-product-filter'),
            '',
            array(esc_html__('1 style', 'super-product-filter'), 'plain'),
            array(esc_html__('10 styles', 'super-product-filter'), 'plain')
        );

        swpf_compare_row(
            esc_html__('Toggle Styles', 'super-product-filter'),
            '',
            array(esc_html__('1 style', 'super-product-filter'), 'plain'),
            array(esc_html__('10 styles', 'super-product-filter'), 'plain')
        );

        swpf_compare_row(
            esc_html__('Dropdown Styles', 'super-product-filter'),
            esc_html__('Pro can also replace the browser dropdown with a styled one.', 'super-product-filter'),
            array(esc_html__('1 style', 'super-product-filter'), 'plain'),
            array(esc_html__('6 styles', 'super-product-filter'), 'plain')
        );

        swpf_compare_row(
            esc_html__('Multi Select Styles', 'super-product-filter'),
            '',
            array(esc_html__('1 style', 'super-product-filter'), 'plain'),
            array(esc_html__('6 styles', 'super-product-filter'), 'plain')
        );

        swpf_compare_row(
            esc_html__('Colour Swatch Styles', 'super-product-filter'),
            esc_html__('Square or round, with a tick, ring, notch or offset shadow when chosen.', 'super-product-filter'),
            array(esc_html__('1 style', 'super-product-filter'), 'plain'),
            array(esc_html__('6 styles', 'super-product-filter'), 'plain')
        );

        swpf_compare_row(
            esc_html__('Image Swatch Styles', 'super-product-filter'),
            '',
            array(esc_html__('1 style', 'super-product-filter'), 'plain'),
            array(esc_html__('6 styles', 'super-product-filter'), 'plain')
        );

        swpf_compare_row(
            esc_html__('Colour & Size Controls', 'super-product-filter'),
            esc_html__('Border, background, icon and active colours, plus sizing, for every control.', 'super-product-filter'),
            array(esc_html__('No', 'super-product-filter'), 'no'),
            array(esc_html__('Yes', 'super-product-filter'), 'plain')
        );

        swpf_compare_row(
            esc_html__('Live Design Previews', 'super-product-filter'),
            esc_html__('Watch a control take on each setting as you change it.', 'super-product-filter'),
            array(esc_html__('Typography only', 'super-product-filter'), 'plain'),
            array(esc_html__('Every control', 'super-product-filter'), 'plain')
        );

        swpf_compare_row(
            esc_html__('Typography', 'super-product-filter'),
            esc_html__('Google Fonts for headings and filter text, served locally if you prefer.', 'super-product-filter'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes')
        );

        swpf_compare_row(
            esc_html__('Filter Box Styling', 'super-product-filter'),
            esc_html__('Background, border, radius, shadow, padding and spacing.', 'super-product-filter'),
            array(esc_html__('Colours & spacing', 'super-product-filter'), 'plain'),
            array(esc_html__('Full control', 'super-product-filter'), 'plain')
        );

        swpf_compare_heading(esc_html__('Results & pagination', 'super-product-filter'));

        swpf_compare_row(
            esc_html__('Paged Navigation', 'super-product-filter'),
            esc_html__('Pro loads each page over AJAX instead of reloading.', 'super-product-filter'),
            array(esc_html__('Standard links', 'super-product-filter'), 'yes'),
            array(esc_html__('AJAX paging', 'super-product-filter'), 'plain'),
            $swpf_demo . 'pagination-paged/'
        );

        swpf_compare_row(
            esc_html__('Load More Button', 'super-product-filter'),
            '',
            array(esc_html__('No', 'super-product-filter'), 'no'),
            array(esc_html__('Yes', 'super-product-filter'), 'plain'),
            $swpf_demo . 'pagination-load-more/?paged=1&swpf_filter=1&swpf_filter_sc=8305&pgn_type=more-btn'
        );

        swpf_compare_row(
            esc_html__('Infinite Scroll', 'super-product-filter'),
            '',
            array(esc_html__('No', 'super-product-filter'), 'no'),
            array(esc_html__('Yes', 'super-product-filter'), 'plain'),
            $swpf_demo . 'pagination-infinite-scroll/?paged=1&swpf_filter=1&swpf_filter_sc=8308&pgn_type=infinite'
        );

        swpf_compare_row(
            esc_html__('Scroll To Results', 'super-product-filter'),
            esc_html__('Jump to the products after a filter is applied.', 'super-product-filter'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes')
        );

        swpf_compare_row(
            esc_html__('Loading Animation', 'super-product-filter'),
            esc_html__('Shown over the products while the new results arrive.', 'super-product-filter'),
            array(esc_html__('3 styles', 'super-product-filter'), 'plain'),
            array(esc_html__('16 styles, or your own image', 'super-product-filter'), 'plain')
        );

        swpf_compare_heading(esc_html__('Shop layout', 'super-product-filter'));

        swpf_compare_row(
            esc_html__('Shop Builder', 'super-product-filter'),
            esc_html__('Build the product card itself: image, badge, title, price, rating and buttons.', 'super-product-filter'),
            array(esc_html__('No', 'super-product-filter'), 'no'),
            array(esc_html__('Yes', 'super-product-filter'), 'plain'),
            $swpf_demo . 'shop/shop-builder-style-1/'
        );

        swpf_compare_row(
            esc_html__('Ready-Made Shop Layouts', 'super-product-filter'),
            esc_html__('Eight starting points you can import and edit.', 'super-product-filter'),
            array(esc_html__('No', 'super-product-filter'), 'no'),
            array(esc_html__('8 layouts', 'super-product-filter'), 'plain'),
            $swpf_demo . 'shop/'
        );

        swpf_compare_row(
            esc_html__('Product Quick View', 'super-product-filter'),
            esc_html__('Open a product in a popup without leaving the results.', 'super-product-filter'),
            array(esc_html__('No', 'super-product-filter'), 'no'),
            array(esc_html__('Yes', 'super-product-filter'), 'plain')
        );

        swpf_compare_heading(esc_html__('Fitting your site', 'super-product-filter'));

        swpf_compare_row(
            esc_html__('Works With Your Theme', 'super-product-filter'),
            esc_html__('Pro lets you name the product, count and pagination selectors when a theme differs from the norm.', 'super-product-filter'),
            array(esc_html__('Standard markup', 'super-product-filter'), 'yes'),
            array(esc_html__('Custom selectors', 'super-product-filter'), 'plain')
        );

        swpf_compare_row(
            esc_html__('Page Builders', 'super-product-filter'),
            esc_html__('Elementor widget and Gutenberg block.', 'super-product-filter'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes')
        );

        swpf_compare_row(
            esc_html__('Custom CSS & JS', 'super-product-filter'),
            esc_html__('A CSS box, plus hooks that run before and after each filter.', 'super-product-filter'),
            array(esc_html__('No', 'super-product-filter'), 'no'),
            array(esc_html__('Yes', 'super-product-filter'), 'plain')
        );

        swpf_compare_row(
            esc_html__('Import & Export Presets', 'super-product-filter'),
            esc_html__('Move a configured filter between sites.', 'super-product-filter'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes')
        );

        swpf_compare_row(
            esc_html__('Responsive', 'super-product-filter'),
            esc_html__('Pro sets column counts and panel width per breakpoint.', 'super-product-filter'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes'),
            array(esc_html__('Per breakpoint', 'super-product-filter'), 'plain')
        );

        swpf_compare_heading(esc_html__('Getting help', 'super-product-filter'));

        swpf_compare_row(
            esc_html__('Support', 'super-product-filter'),
            '',
            array(esc_html__('WordPress.org forum', 'super-product-filter'), 'plain'),
            array(esc_html__('Direct support from us', 'super-product-filter'), 'plain')
        );

        swpf_compare_row(
            esc_html__('Updates', 'super-product-filter'),
            esc_html__('Pro updates from your dashboard once the licence is activated.', 'super-product-filter'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes'),
            array(esc_html__('Yes', 'super-product-filter'), 'yes')
        );

        swpf_compare_row(
            esc_html__('Price', 'super-product-filter'),
            '',
            array(esc_html__('Free', 'super-product-filter'), 'plain'),
            array(esc_html__('One-off payment', 'super-product-filter'), 'plain')
        );
        ?>

        <tr>
            <td class="swpf-feature"></td>
            <td class="swpf-compare-button"><a class="button" href="https://demo.hashthemes.com/super-woocommerce-product-filter/comparison-free-vs-pro/" target="_blank"><?php esc_html_e('Detail Comparison', 'super-product-filter'); ?></a></td>
            <td class="swpf-compare-button"><a class="button" href="https://1.envato.market/eK5yrQ" target="_blank"><?php esc_html_e('Buy Now', 'super-product-filter'); ?></a></td>
        </tr>
    </table>
</div>
