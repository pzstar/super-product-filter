<?php
defined('ABSPATH') || die();
?>

<div class="swpf-options-fields-wrap tab-content swpf-settings-content" id="appearance-settings" style="display: none;">
    <div class="swpf-field-inline-wrap">
        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Show Only (Categories)', 'super-product-filter'); ?></label>
            <?php
            $swpf_args = array(
                'taxonomy' => 'product_cat',
                'orderby' => 'name',
                'order' => 'ASC',
                'hierarchical' => 0,
                'hide_empty' => 0,
            );
            $swpf_all_categories = get_terms($swpf_args);
            $swpf_cat_ids = [];
            if (!empty($swpf_all_categories)) {
                foreach ($swpf_all_categories as $cat) {
                    $swpf_cat_ids[] = $cat->term_id;
                }
            }
            ?>

            <div class="swpf-settings-input-field">
                <select name="swpf_settings[config][lo_specific_cat][]" class="swpf-selectize" multiple="multiple">
                    <?php
                    echo wp_kses(Super_Product_Filter_Admin::get_dropdown_indent(0, $swpf_all_categories, $swpf_settings['config']['lo_specific_cat'], $swpf_cat_ids), array(
                        'option' => array(
                            'value' => array(),
                            'selected' => array()
                        )
                    ));
                    ?>
                </select>
                <p class="swpf-desc">
                    <?php esc_html_e('Displays only the products that belong to the selected category when filtering. Leave blank to include all categories.', 'super-product-filter'); ?><br>
                    <?php
                    /* translators: 1: Link open, 2: Link close */
                    echo sprintf(esc_html__('Find in detail %1$shere%2$s', 'super-product-filter'), '<a href="https://hashthemes.com/documentation/super-woocommerce-product-filter-documentation/#ShowOnly(Categories)" target="_blank">', '</a>');
                    ?>
                </p>
            </div>
        </div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Auto Submit', 'super-product-filter'); ?></label>

            <div class="swpf-settings-input-field">
                <select name="swpf_settings[config][autosubmit]" id="swpf-autosubmit" data-condition="toggle">
                    <option value="off" <?php selected($swpf_settings['config']['autosubmit'], 'off'); ?>><?php esc_html_e('No', 'super-product-filter'); ?></option>
                    <option value="on" <?php selected($swpf_settings['config']['autosubmit'], 'on'); ?>><?php esc_html_e('Yes', 'super-product-filter'); ?></option>
                </select>
                <p class="swpf-desc">
                    <?php
                    esc_html_e('Instant filtering on selection, no submit button required.', 'super-product-filter');
                    /* translators: 1: Link open, 2: Link close */
                    echo sprintf(esc_html__('Find in detail %1$shere%2$s', 'super-product-filter'), '<a href="https://hashthemes.com/documentation/super-woocommerce-product-filter-documentation/#AutoSubmit" target="_blank">', '</a>');
                    ?>
                </p>
            </div>
        </div>

        <div class="swpf-field-wrap" data-condition-toggle="swpf-autosubmit" data-condition-val="off">
            <label><?php esc_html_e('Submit Button Text', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <input type="text" name="swpf_settings[config][submit_btn_text]" value="<?php echo esc_attr($swpf_settings['config']['submit_btn_text']); ?>">
            </div>
        </div>

        <div class="swpf-separator"></div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Logic Operator', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <select name="swpf_settings[config][logic_operator]">
                    <option value="AND" <?php selected($swpf_settings['config']['logic_operator'], 'AND'); ?>><?php esc_html_e('AND', 'super-product-filter'); ?></option>
                    <option value="OR" <?php selected($swpf_settings['config']['logic_operator'], 'OR'); ?>><?php esc_html_e('OR', 'super-product-filter'); ?></option>
                </select>
                <p class="swpf-desc">
                    <?php esc_html_e('AND refers all the fiters should be matched.', 'super-product-filter'); ?><br>
                    <?php esc_html_e('OR refers if any one of the fiter is matched.', 'super-product-filter'); ?><br>
                    <?php
                    /* translators: 1: Link open, 2: Link close */
                    echo sprintf(esc_html__('Find in detail %1$shere%2$s', 'super-product-filter'), '<a href="https://hashthemes.com/documentation/super-woocommerce-product-filter-documentation/#LogicOperator" target="_blank">', '</a>');
                    ?>
                </p>
            </div>
        </div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Order By', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <select name="swpf_settings[config][orderby]">
                    <?php
                    $swpf_order_by_items = Super_Product_Filter_Admin::get_order_by_options();
                    foreach ($swpf_order_by_items as $swpf_key => $swpf_val) {
                        ?>
                        <option value="<?php echo esc_attr($swpf_key); ?>" <?php selected($swpf_settings['config']['orderby'], $swpf_key); ?>><?php echo esc_html($swpf_val); ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
        </div>

        <details class="swpf-advanced-settings">
            <summary><?php esc_html_e('Advanced', 'super-product-filter'); ?></summary>

            <p class="swpf-desc"><?php esc_html_e('These are only needed when the filter cannot work out where your theme puts the product list. Leave them empty unless filtering is not updating part of the page.', 'super-product-filter'); ?></p>

            <div class="swpf-field-wrap">
                <label><?php esc_html_e('Products Wrapper Selector Class', 'super-product-filter'); ?></label>
                <div class="swpf-settings-input-field">
                    <input type="text" name="swpf_settings[config][product_selector]" value="<?php echo esc_attr(swpf_selector_override_value($swpf_settings['config']['product_selector'], array('ul.products', '.woocommerce .products'))); ?>" placeholder="<?php esc_attr_e('Detected automatically', 'super-product-filter'); ?>">
                    <p class="swpf-desc"><?php esc_html_e('The selector wrapping the product list. Leave empty to detect it automatically.', 'super-product-filter'); ?></p>
                </div>
            </div>

            <div class="swpf-field-wrap">
                <label><?php esc_html_e('Products Count Div Selector Class', 'super-product-filter'); ?></label>
                <div class="swpf-settings-input-field">
                    <input type="text" name="swpf_settings[config][product_count_selector]" value="<?php echo esc_attr(swpf_selector_override_value($swpf_settings['config']['product_count_selector'], '.woocommerce-result-count')); ?>" placeholder="<?php esc_attr_e('Detected automatically', 'super-product-filter'); ?>">
                    <p class="swpf-desc"><?php esc_html_e('The selector wrapping the result count. Leave empty to detect it automatically.', 'super-product-filter'); ?></p>
                </div>
            </div>

            <div class="swpf-field-wrap">
                <label><?php esc_html_e('Products Pagination Div Selector Class', 'super-product-filter'); ?></label>
                <div class="swpf-settings-input-field">
                    <input type="text" name="swpf_settings[config][pagination_selector]" value="<?php echo esc_attr(swpf_selector_override_value($swpf_settings['config']['pagination_selector'], '.woocommerce-pagination')); ?>" placeholder="<?php esc_attr_e('Detected automatically', 'super-product-filter'); ?>">
                    <p class="swpf-desc"><?php esc_html_e('The selector wrapping the pagination. Leave empty to detect it automatically.', 'super-product-filter'); ?></p>
                </div>
            </div>

        </details>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Product Columns and Rows', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <p class="swpf-desc">
                    <?php
                    /* translators: 1: Link open, 2: Link close */
                    echo sprintf(esc_html__('Set these under %1$sAppearance > Customize > WooCommerce > Product Catalog%2$s. A product list added by a shortcode or a page builder uses its own columns and limit instead.', 'super-product-filter'), '<a href="' . esc_url(admin_url('customize.php?autofocus[section]=woocommerce_product_catalog')) . '" target="_blank">', '</a>');
                    ?>
                </p>
            </div>
        </div>
        <div class="swpf-separator"></div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Scroll to Top After Filtering', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <div class="swpf-toggle-wrap">
                    <label class="swpf-toggle">
                        <input type="checkbox" name="swpf_settings[config][scroll_after_filter]" <?php checked($swpf_settings['config']['scroll_after_filter'], 'on'); ?> class="swpf-filter-enable">
                        <span></span>
                    </label>
                </div>
                <p class="swpf-desc"><?php esc_html_e('Automatically scrolls the page to the top after filters are applied.', 'super-product-filter'); ?></p>
            </div>
        </div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Show/Hide Each Filter', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <div class="swpf-toggle-wrap">
                    <label class="swpf-toggle">
                        <input type="checkbox" name="swpf_settings[config][show_filter_list_toggle]" <?php checked($swpf_settings['config']['show_filter_list_toggle'], 'on'); ?> class="swpf-filter-enable">
                        <span></span>
                    </label>
                </div>
                <p class="swpf-desc">
                    <?php
                    esc_html_e('Open/Close each filter by clicking on button.', 'super-product-filter');
                    /* translators: 1: Link open, 2: Link close */
                    echo sprintf(esc_html__('Find in detail %1$shere%2$s', 'super-product-filter'), '<a href="https://hashthemes.com/documentation/super-woocommerce-product-filter-documentation/#Show/HideEachFilter" target="_blank">', '</a>');
                    ?>
                </p>
            </div>
        </div>

        <div class="swpf-separator"></div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Preloaders', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <select name="swpf_settings[config][preloaders]">
                    <option value="none" <?php selected($swpf_settings['config']['preloaders'], 'none'); ?>><?php esc_html_e('None', 'super-product-filter'); ?></option>
                    <option value="preloader1" <?php selected($swpf_settings['config']['preloaders'], 'preloader1'); ?>><?php esc_html_e('Preloader 1', 'super-product-filter'); ?></option>
                    <option value="preloader2" <?php selected($swpf_settings['config']['preloaders'], 'preloader2'); ?>><?php esc_html_e('Preloader 2', 'super-product-filter'); ?></option>
                    <option value="preloader3" <?php selected($swpf_settings['config']['preloaders'], 'preloader3'); ?>><?php esc_html_e('Preloader 3', 'super-product-filter'); ?></option>
                </select>
            </div>
        </div>
    </div>
</div>