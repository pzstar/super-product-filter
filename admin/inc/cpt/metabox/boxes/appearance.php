<?php
defined('ABSPATH') || die();
?>

<div class="swpf-options-fields-wrap tab-content swpf-settings-content" id="appearance-settings" style="display: none;">
    <div class="swpf-field-inline-wrap">
        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Show Only (Categories)', 'super-product-filter'); ?></label>
            <?php
            $args = array(
                'taxonomy' => 'product_cat',
                'orderby' => 'name',
                'order' => 'ASC',
                'hierarchical' => 0,
                'hide_empty' => 0,
            );
            $all_categories = get_terms($args);
            $cat_ids = [];
            if (!empty($all_categories)) {
                foreach ($all_categories as $cat) {
                    $cat_ids[] = $cat->term_id;
                }
            }
            ?>

            <div class="swpf-settings-input-field">
                <select name="swpf_settings[config][lo_specific_cat][]" class="swpf-selectize" multiple="multiple">
                    <?php
                    echo wp_kses(Super_Product_Filter_Admin::get_dropdown_indent(0, $all_categories, $settings['config']['lo_specific_cat'], $cat_ids), array(
                        'option' => array(
                            'value' => array(),
                            'selected' => array()
                        )
                    ));
                    ?>
                </select>
                <p class="swpf-desc">
                    <?php esc_html_e('Displays only the products that belong to the selected category when filtering. Leave blank to include all categories.', 'super-product-filter'); ?><br>
                    <?php echo sprintf(esc_html__('Find in detail %shere%s', 'super-product-filter'), '<a href="https://hashthemes.com/documentation/super-woocommerce-product-filter-documentation/#ShowOnly(Categories)" target="_blank">', '</a>'); ?>
                </p>
            </div>
        </div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Auto Submit', 'super-product-filter'); ?></label>

            <div class="swpf-settings-input-field">
                <select name="swpf_settings[config][autosubmit]" id="swpf-autosubmit" data-condition="toggle">
                    <option value="off" <?php selected($settings['config']['autosubmit'], 'off'); ?>><?php esc_html_e('No', 'super-product-filter'); ?></option>
                    <option value="on" <?php selected($settings['config']['autosubmit'], 'on'); ?>><?php esc_html_e('Yes', 'super-product-filter'); ?></option>
                </select>
                <p class="swpf-desc">
                    <?php esc_html_e('Instant filtering on selection, no submit button required.', 'super-product-filter'); ?>
                    <?php echo sprintf(esc_html__('Find in detail %shere%s', 'super-product-filter'), '<a href="https://hashthemes.com/documentation/super-woocommerce-product-filter-documentation/#AutoSubmit" target="_blank">', '</a>'); ?>
                </p>
            </div>
        </div>

        <div class="swpf-field-wrap" data-condition-toggle="swpf-autosubmit" data-condition-val="off">
            <label><?php esc_html_e('Submit Button Text', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <input type="text" name="swpf_settings[config][submit_btn_text]" value="<?php echo esc_attr($settings['config']['submit_btn_text']); ?>">
            </div>
        </div>

        <div class="swpf-separator"></div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Logic Operator', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <select name="swpf_settings[config][logic_operator]">
                    <option value="AND" <?php selected($settings['config']['logic_operator'], 'AND'); ?>><?php esc_html_e('AND', 'super-product-filter'); ?></option>
                    <option value="OR" <?php selected($settings['config']['logic_operator'], 'OR'); ?>><?php esc_html_e('OR', 'super-product-filter'); ?></option>
                </select>
                <p class="swpf-desc">
                    <?php esc_html_e('AND refers all the fiters should be matched.', 'super-product-filter'); ?><br>
                    <?php esc_html_e('OR refers if any one of the fiter is matched.', 'super-product-filter'); ?><br>
                    <?php echo sprintf(esc_html__('Find in detail %shere%s', 'super-product-filter'), '<a href="https://hashthemes.com/documentation/super-woocommerce-product-filter-documentation/#LogicOperator" target="_blank">', '</a>'); ?>
                </p>
            </div>
        </div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Order By', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <select name="swpf_settings[config][orderby]">
                    <?php
                    $order_by_items = Super_Product_Filter_Admin::get_order_by_options();
                    foreach ($order_by_items as $key => $val) {
                        ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($settings['config']['orderby'], $key); ?>><?php echo esc_html($val); ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="swpf-separator"></div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Products Wrapper Selector Class', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <input type="text" name="swpf_settings[config][product_selector]" value="<?php echo esc_attr($settings['config']['product_selector']); ?>">
                <p class="swpf-desc">
                    <?php
                    echo esc_html__('Enter the selector that wraps the entire product list on the page where this filter is used.', 'super-product-filter');
                    ?>
                </p>
            </div>
        </div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Products Count Div Selector Class', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <input type="text" name="swpf_settings[config][product_count_selector]" value="<?php echo esc_attr($settings['config']['product_count_selector']); ?>">
                <p class="swpf-desc">
                    <?php
                    echo esc_html__('Enter the selector that wraps the product counter on the page where this filter is used.', 'super-product-filter');
                    ?>
                </p>
            </div>
        </div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Products Pagination Div Selector Class', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <input type="text" name="swpf_settings[config][pagination_selector]" value="<?php echo esc_attr($settings['config']['pagination_selector']); ?>">
                <p class="swpf-desc">
                    <?php
                    echo esc_html__('Enter the selector that wraps the pagination on the page where this filter is used.', 'super-product-filter');
                    ?>
                </p>
            </div>
        </div>

        <div class="swpf-separator"></div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Product Columns', 'super-product-filter'); ?></label>

            <div class="swpf-settings-input-field">
                <div class="swpf-range-slider-field">
                    <div class="swpf-range-slider"></div>
                    <input type="number" name="swpf_settings[config][product_columns]" value="<?php echo esc_attr($settings['config']['product_columns']); ?>" class="swpf-range-input" min="1" max="6" step="1">
                </div>
            </div>
        </div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Product Rows', 'super-product-filter'); ?></label>

            <div class="swpf-settings-input-field">
                <div class="swpf-range-slider-field">
                    <div class="swpf-range-slider"></div>
                    <input type="number" name="swpf_settings[config][product_rows]" value="<?php echo esc_attr($settings['config']['product_rows']); ?>" class="swpf-range-input" min="1" max="10" step="1">
                </div>
            </div>
        </div>

        <div class="swpf-separator"></div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Scroll to Top After Filtering', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <div class="swpf-toggle-wrap">
                    <label class="swpf-toggle">
                        <input type="checkbox" name="swpf_settings[config][scroll_after_filter]" <?php checked($settings['config']['scroll_after_filter'], 'on'); ?> class="swpf-filter-enable">
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
                        <input type="checkbox" name="swpf_settings[config][show_filter_list_toggle]" <?php checked($settings['config']['show_filter_list_toggle'], 'on'); ?> class="swpf-filter-enable">
                        <span></span>
                    </label>
                </div>
                <p class="swpf-desc">
                    <?php esc_html_e('Open/Close each filter by clicking on button.', 'super-product-filter'); ?>
                    <?php echo sprintf(esc_html__('Find in detail %shere%s', 'super-product-filter'), '<a href="https://hashthemes.com/documentation/super-woocommerce-product-filter-documentation/#Show/HideEachFilter" target="_blank">', '</a>'); ?>
                </p>
            </div>
        </div>

        <div class="swpf-separator"></div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Preloaders', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <select name="swpf_settings[config][preloaders]">
                    <option value="none" <?php selected($settings['config']['preloaders'], 'none'); ?>><?php esc_html_e('None', 'super-product-filter'); ?></option>
                    <option value="preloader1" <?php selected($settings['config']['preloaders'], 'preloader1'); ?>><?php esc_html_e('Preloader 1', 'super-product-filter'); ?></option>
                    <option value="preloader2" <?php selected($settings['config']['preloaders'], 'preloader2'); ?>><?php esc_html_e('Preloader 2', 'super-product-filter'); ?></option>
                    <option value="preloader3" <?php selected($settings['config']['preloaders'], 'preloader3'); ?>><?php esc_html_e('Preloader 3', 'super-product-filter'); ?></option>
                </select>
            </div>
        </div>
    </div>
</div>