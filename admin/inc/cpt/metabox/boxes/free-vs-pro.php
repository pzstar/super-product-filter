<?php
defined('ABSPATH') || die();
?>

<div class="swpf-options-fields-wrap tab-content swpf-settings-content" id="free-vs-pro-settings" style="display: none;">
    
    <table class="swpf-compare-table">
        <tr>
            <th><?php esc_html_e('Feature', 'super-product-filter'); ?></th>
            <th><?php esc_html_e('Free Version', 'super-product-filter'); ?></th>
            <th><?php esc_html_e('Pro Version', 'super-product-filter'); ?></th>
        </tr>

        <!-- CORE FEATURES -->
        <tr>
            <td class="swpf-feature"><?php esc_html_e('AJAX Filtering', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Enhanced & Faster', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Category Filter', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Tag Filter', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Attribute Filters', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Basic', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Advanced Styles', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Price Range Slider', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Basic', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('7 Slider Styles', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Rating Filter', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Stock Status Filter', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Sale Filter', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature">
                <?php
                /* translators: 1: Link open, 2: Link close */
                echo sprintf(esc_html__('Show Product Count %1$sDemo%2$s', 'super-product-filter'), '<a href="https://demo.hashthemes.com/super-woocommerce-product-filter/filter-with-product-count/" target="_blank">', '</a>');
                ?>
            </td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>

        <!-- LOGIC -->
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Filter Logic (AND/OR)', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Advanced Control', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature">Show/Hide Empty Terms <a href="https://demo.hashthemes.com/super-woocommerce-product-filter/hide-filter-attributes-with-no-products/" target="_blank">Demo</a></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Basic', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Advanced Options', 'super-product-filter'); ?></td>
        </tr>

        <!-- ADVANCED FILTERING -->
        <tr>
            <td class="swpf-feature">Conditional Filters <a href="https://demo.hashthemes.com/super-woocommerce-product-filter/selective-display-of-attributes/" target="_blank">Demo</a></td>
            <td class="swpf-no"><i class="mdi-close"></i><?php esc_html_e('No', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature">Step-by-Step Filtering <a href="https://demo.hashthemes.com/super-woocommerce-product-filter/step-by-step-filter/" target="_blank">Demo</a></td>
            <td class="swpf-no"><i class="mdi-close"></i><?php esc_html_e('No', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('ACF / Custom Field Support', 'super-product-filter'); ?></td>
            <td class="swpf-no"><i class="mdi-close"></i><?php esc_html_e('No', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Variation Image Support', 'super-product-filter'); ?></td>
            <td class="swpf-no"><i class="mdi-close"></i><?php esc_html_e('No', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>

        <!-- FILTER TYPES & STYLES -->
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Checkbox Styles', 'super-product-filter'); ?></td>
            <td><?php esc_html_e('Basic', 'super-product-filter'); ?></td>
            <td class="swpf-pro"><?php esc_html_e('11 Styles', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Radio Styles', 'super-product-filter'); ?></td>
            <td>Basic</td>
            <td class="swpf-pro"><?php esc_html_e('11 Styles', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Price Slider Styles', 'super-product-filter'); ?></td>
            <td><?php esc_html_e('Basic', 'super-product-filter'); ?></td>
            <td class="swpf-pro"><?php esc_html_e('7 Styles', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Button Filter Skins', 'super-product-filter'); ?></td>
            <td>Basic</td>
            <td class="swpf-pro"><?php esc_html_e('5 Styles', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Toggle Styles', 'super-product-filter'); ?></td>
            <td>Basic</td>
            <td class="swpf-pro"><?php esc_html_e('5 Styles', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Dropdown Styles', 'super-product-filter'); ?></td>
            <td>Basic</td>
            <td class="swpf-pro"><?php esc_html_e('Multiple Advanced Styles', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Multi Select Skins', 'super-product-filter'); ?></td>
            <td>Basic</td>
            <td class="swpf-pro"><?php esc_html_e('Multiple Advanced Styles', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Color Swatches', 'super-product-filter'); ?></td>
            <td>Basic</td>
            <td class="swpf-pro"><?php esc_html_e('Enhanced Layout Options', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Image Swatches', 'super-product-filter'); ?></td>
            <td>Basic</td>
            <td class="swpf-pro"><?php esc_html_e('More Layout Options', 'super-product-filter'); ?></td>
        </tr>

        <!-- DISPLAY -->
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Shortcode Support', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Widget Support', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature">Filter Placement <a href="https://demo.hashthemes.com/super-woocommerce-product-filter/shop/" target="_blank">Demo</a></td>
            <td>Sidebar / Inline</td>
            <td class="swpf-pro"><?php esc_html_e('Off-Canvas, Header, Advanced Positions', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature">Off-Canvas Filters <a href="https://demo.hashthemes.com/super-woocommerce-product-filter/off-canvas-filter/" target="_blank">Demo</a></td>
            <td class="swpf-no"><i class="mdi-close"></i><?php esc_html_e('No', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature">Auto Apply Filters <a href="https://demo.hashthemes.com/super-woocommerce-product-filter/auto-submit-filter/" target="_blank">Demo</a></td>
            <td>Basic</td>
            <td class="swpf-pro"><?php esc_html_e('Advanced Auto-Apply', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Multi-column Layout', 'super-product-filter'); ?></td>
            <td class="swpf-no"><i class="mdi-close"></i><?php esc_html_e('No', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature">Open/Close Filters <a href="https://demo.hashthemes.com/super-woocommerce-product-filter/open-close-filter-box/" target="_blank">Demo</a></td>
            <td class="swpf-no"><i class="mdi-close"></i><?php esc_html_e('No', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature">Fixed Height Filters <a href="https://demo.hashthemes.com/super-woocommerce-product-filter/filter-with-fixed-height/" target="_blank">Demo</a></td>
            <td class="swpf-no"><i class="mdi-close"></i><?php esc_html_e('No', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>

        <!-- CONTROL -->
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Drag & Drop Filter Ordering', 'super-product-filter'); ?></td>
            <td class="swpf-no"><i class="mdi-close"></i><?php esc_html_e('No', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Custom Product Selector', 'super-product-filter'); ?></td>
            <td>Basic</td>
            <td class="swpf-pro"><?php esc_html_e('Fully Customizable', 'super-product-filter'); ?></td>
        </tr>

        <!-- PAGINATION -->
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Basic Pagination', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature">AJAX Pagination <a href="https://demo.hashthemes.com/super-woocommerce-product-filter/pagination-paged/" target="_blank">Demo</a></td>
            <td class="swpf-no"><i class="mdi-close"></i><?php esc_html_e('No', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature">Load More Button <a href="https://demo.hashthemes.com/super-woocommerce-product-filter/pagination-load-more/?paged=1&swpf_filter=1&swpf_filter_sc=8305&pgn_type=more-btn" target="_blank">Demo</a></td>
            <td class="swpf-no"><i class="mdi-close"></i><?php esc_html_e('No', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature">Infinite Scroll <a href="https://demo.hashthemes.com/super-woocommerce-product-filter/pagination-infinite-scroll/?paged=1&swpf_filter=1&swpf_filter_sc=8308&pgn_type=infinite" target="_blank">Demo</a></td>
            <td class="swpf-no"><i class="mdi-close"></i><?php esc_html_e('No', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Scroll to Top After Filter', 'super-product-filter'); ?></td>
            <td class="swpf-no"><i class="mdi-close"></i><?php esc_html_e('No', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Preloader Styles', 'super-product-filter'); ?></td>
            <td><?php esc_html_e('Limited', 'super-product-filter'); ?></td>
            <td class="swpf-pro"><?php esc_html_e('16 Styles', 'super-product-filter'); ?></td>
        </tr>

        <!-- SHOP BUILDER -->
        <tr>
            <td class="swpf-feature">Shop Builder <a href="https://demo.hashthemes.com/super-woocommerce-product-filter/shop/shop-builder-style-1/" target="_blank">Demo</a></td>
            <td class="swpf-no"><i class="mdi-close"></i><?php esc_html_e('No', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature">Custom Shop Layout <a href="https://demo.hashthemes.com/super-woocommerce-product-filter/shop/" target="_blank">Demo</a></td>
            <td class="swpf-no"><i class="mdi-close"></i><?php esc_html_e('No', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Product Grid Control', 'super-product-filter'); ?></td>
            <td class="swpf-no"><i class="mdi-close"></i><?php esc_html_e('No', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Full Control', 'super-product-filter'); ?></td>
        </tr>

        <!-- MOBILE -->
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Responsive Layout', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Better Mobile UX', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Mobile Off-Canvas', 'super-product-filter'); ?></td>
            <td class="swpf-no"><i class="mdi-close"></i><?php esc_html_e('No', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>

        <!-- STYLING -->
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Styling Options', 'super-product-filter'); ?></td>
            <td>Basic</td>
            <td class="swpf-pro"><?php esc_html_e('Advanced', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Custom CSS Field', 'super-product-filter'); ?></td>
            <td>Limited</td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Google Fonts Optimization', 'super-product-filter'); ?></td>
            <td class="swpf-no"><i class="mdi-close"></i><?php esc_html_e('No', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
        </tr>

        <!-- IMPORT & EXPORT -->
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Import / Export', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Advanced Export', 'super-product-filter'); ?></td>
        </tr>

        <!-- COMPATIBILITY -->
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Page Builder Support', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Yes', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Enhanced', 'super-product-filter'); ?></td>
        </tr>
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Theme Compatibility', 'super-product-filter'); ?></td>
            <td><?php esc_html_e('Good', 'super-product-filter'); ?></td>
            <td class="swpf-pro"><?php esc_html_e('Enhanced With Custom Selectors', 'super-product-filter'); ?></td>
        </tr>

        <!-- PERFORMANCE -->
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Performance', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Lightweight', 'super-product-filter'); ?></td>
            <td class="swpf-yes"><i class="mdi-check"></i><?php esc_html_e('Fully Optimized UX', 'super-product-filter'); ?></td>
        </tr>

        <!-- SUPPORT -->
        <tr>
            <td class="swpf-feature"><?php esc_html_e('Support', 'super-product-filter'); ?></td>
            <td><?php esc_html_e('Community Support', 'super-product-filter'); ?></td>
            <td class="swpf-pro"><?php esc_html_e('Premium Support', 'super-product-filter'); ?></td>
        </tr>

        <tr>
            <td class="swpf-feature"><?php esc_html_e('Price', 'super-product-filter'); ?></td>
            <td><?php esc_html_e('FREE', 'super-product-filter'); ?></td>
            <td class="swpf-pro"><?php esc_html_e('Paid', 'super-product-filter'); ?></td>
        </tr>

        <tr>
            <td class="swpf-feature"></td>
            <td class="swpf-compare-button"><a class="button" href="https://demo.hashthemes.com/super-woocommerce-product-filter/comparison-free-vs-pro/" target="_blank"><?php esc_html_e('Detail Comparison', 'super-product-filter'); ?></a></td>
            <td class="swpf-compare-button"><a class="button" href="https://demo.hashthemes.com/super-woocommerce-product-filter/" target="_blank"><?php esc_html_e('Buy Now', 'super-product-filter'); ?></a></td>
        </tr>
    </table>
</div>