<?php
defined('ABSPATH') || die();

$swpf_items_order = array();
$swpf_taxonomies = swpf_get_taxonomies(); // get all taxonomies object
$swpf_taxonomies_keys = array_keys($swpf_taxonomies); // get only the taxo name array

if (isset($swpf_settings['list_order']) && !empty($swpf_settings['list_order'])) {
    $swpf_items_order = $swpf_settings['list_order'];
    $swpf_meta_items = ['price_range', 'reviews', 'ratings', 'on_sale', 'in_stock'];
    $swpf_tax_items = array_diff($swpf_items_order, $swpf_meta_items); // only tax_items from db
    $swpf_new_tax_items = array_diff($swpf_taxonomies_keys, $swpf_tax_items); // new added tax_items not available in db
    // check if new_tax_items are registered then append it to the current database items list
    if (isset($swpf_new_tax_items) && !empty($swpf_new_tax_items)) {
        $swpf_items_order = array_merge($swpf_items_order, $swpf_new_tax_items);
    }

    foreach ($swpf_meta_items as $swpf_m_item) {
        if (!in_array($swpf_m_item, $swpf_items_order)) {
            $swpf_items_order[] = $swpf_m_item;
        }
    }
} else {
    $swpf_meta_items = ['price_range', 'reviews', 'ratings', 'on_sale', 'in_stock'];
    $swpf_items_order = array_merge($swpf_meta_items, $swpf_taxonomies_keys); // add other metas to the existing taxonomy array
}

$swpf_index = 1;
?>
<div class="swpf-options-fields-wrap tab-content" id="swpf-filters">
    <div class="swpf-filter-list swpf-active">
        <div class="swpf-filters-title">
            <h3><?php esc_html_e('Filters to Display', 'super-product-filter') ?></h3>
            <i class="swpf-filters-title-toggle icofont-minus"></i>
        </div>

        <div class="swpf-filters-listing">
            <p><?php esc_html_e('Enable/Disable the filters. Click on the title to highlight the filter.', 'super-product-filter') ?></p>
            <div class="swpf-filters-wrap">
                <div class="swpf-filter-show-hide">
                    <label><a class="swpf-filter-scroll-to-section" href="#price_range"><?php esc_html_e('Price Range', 'super-product-filter'); ?></a></label>
                    <div class="swpf-settings-input-field">
                        <div class="swpf-toggle-wrap">
                            <label class="swpf-toggle">
                                <input type="checkbox" name="swpf_settings[enable][price_range]" <?php isset($swpf_settings['enable']['price_range']) ? checked($swpf_settings['enable']['price_range'], 'on') : ''; ?> class="swpf-filter-enable" id="swpf-filters-show-hide-price" data-condition="toggle">
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="swpf-filter-show-hide">
                    <label><a class="swpf-filter-scroll-to-section" href="#reviews"><?php esc_html_e('Reviews', 'super-product-filter'); ?></a></label>
                    <div class="swpf-settings-input-field">
                        <div class="swpf-toggle-wrap">
                            <label class="swpf-toggle">
                                <input type="checkbox" name="swpf_settings[enable][reviews]" <?php isset($swpf_settings['enable']['reviews']) ? checked($swpf_settings['enable']['reviews'], 'on') : ''; ?> class="swpf-filter-enable" id="swpf-filters-show-hide-reviews" data-condition="toggle">
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="swpf-filter-show-hide">
                    <label><a class="swpf-filter-scroll-to-section" href="#ratings"><?php esc_html_e('Ratings', 'super-product-filter'); ?></a></label>
                    <div class="swpf-settings-input-field">
                        <div class="swpf-toggle-wrap">
                            <label class="swpf-toggle">
                                <input type="checkbox" name="swpf_settings[enable][ratings]" <?php isset($swpf_settings['enable']['ratings']) ? checked($swpf_settings['enable']['ratings'], 'on') : ''; ?> class="swpf-filter-enable" id="swpf-filters-show-hide-ratings" data-condition="toggle">
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="swpf-filter-show-hide">
                    <label><a class="swpf-filter-scroll-to-section" href="#on_sale"><?php esc_html_e('On Sale', 'super-product-filter'); ?></a></label>
                    <div class="swpf-settings-input-field">
                        <div class="swpf-toggle-wrap">
                            <label class="swpf-toggle">
                                <input type="checkbox" name="swpf_settings[enable][on_sale]" <?php isset($swpf_settings['enable']['on_sale']) ? checked($swpf_settings['enable']['on_sale'], 'on') : ''; ?> class="swpf-filter-enable" id="swpf-filters-show-hide-on-sale" data-condition="toggle">
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="swpf-filter-show-hide">
                    <label><a class="swpf-filter-scroll-to-section" href="#in_stock"><?php esc_html_e('In Stock', 'super-product-filter'); ?></a></label>
                    <div class="swpf-settings-input-field">
                        <div class="swpf-toggle-wrap">
                            <label class="swpf-toggle">
                                <input type="checkbox" name="swpf_settings[enable][in_stock]" <?php isset($swpf_settings['enable']['in_stock']) ? checked($swpf_settings['enable']['in_stock'], 'on') : ''; ?> class="swpf-filter-enable" id="swpf-filters-show-hide-in-stock" data-condition="toggle">
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>

                <?php
                foreach ($swpf_items_order as $swpf_item_key) {
                    if (isset($swpf_taxonomies[$swpf_item_key])) {
                        ?>
                        <div class="swpf-filter-show-hide">
                            <label><a class="swpf-filter-scroll-to-section" href="#<?php echo esc_attr($swpf_taxonomies[$swpf_item_key]->name); ?>"><?php echo esc_html(str_replace('Product ', 'Product - ', ucwords($swpf_taxonomies[$swpf_item_key]->label))); ?></a></label>
                            <div class="swpf-settings-input-field">
                                <div class="swpf-toggle-wrap">
                                    <label class="swpf-toggle">
                                        <input type="checkbox" name="swpf_settings[enable][<?php echo esc_attr($swpf_item_key); ?>]" <?php isset($swpf_settings['enable'][$swpf_item_key]) ? checked($swpf_settings['enable'][$swpf_item_key], 'on') : ''; ?> class="swpf-filter-enable" id="swpf-filters-show-hide-<?php echo esc_attr($swpf_item_key); ?>" data-condition="toggle">
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <div class="swpf-field-sortable">

        <?php
        foreach ($swpf_items_order as $swpf_item_key) {
            if ($swpf_item_key == 'price_range') {
                ?>
                <div class="swpf-pricerange-options-fields swpf-each-items-wrap" data-condition-toggle="swpf-filters-show-hide-price" id="price_range">
                    <div class="swpf-tax-heading-wrap">
                        <h4><?php esc_html_e('Price Range', 'super-product-filter'); ?></h4>
                        <div class="swpf-tab-action">
                            <span class="swpf-each-actions swpf-sortable-box icofont-drag"></span>
                            <span class="swpf-each-actions swpf-toggle-box icofont-caret-down"></span>
                        </div>
                    </div>

                    <div class="swpf-option-fields-inner-wrap">
                        <input type="hidden" name="swpf_settings[list_order][price_range]" value="<?php echo esc_attr($swpf_item_key); ?>">
                        <div class="swpf-row">
                            <div class="swpf-field-wrap">
                                <label><?php esc_html_e('Title', 'super-product-filter'); ?></label>
                                <div class="swpf-settings-input-field">
                                    <input type="text" name="swpf_settings[title_label][<?php echo esc_attr($swpf_item_key); ?>]" value="<?php echo esc_attr($swpf_settings['title_label'][$swpf_item_key]); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            } elseif ($swpf_item_key == 'reviews') {
                ?>
                <div class="swpf-reviews-options-fields swpf-each-items-wrap" data-condition-toggle="swpf-filters-show-hide-reviews" id="reviews">
                    <div class="swpf-tax-heading-wrap">
                        <h4><?php esc_html_e('Reviews', 'super-product-filter'); ?></h4>
                        <div class="swpf-tab-action">
                            <span class="swpf-each-actions swpf-sortable-box icofont-drag"></span>
                            <span class="swpf-each-actions swpf-toggle-box icofont-caret-down"></span>
                        </div>
                    </div>

                    <div class="swpf-option-fields-inner-wrap">
                        <input type="hidden" name="swpf_settings[list_order][reviews]" value="<?php echo esc_attr($swpf_item_key); ?>">
                        <div class="swpf-row">
                            <div class="swpf-field-wrap">
                                <label><?php esc_html_e('Title', 'super-product-filter'); ?></label>
                                <div class="swpf-settings-input-field">
                                    <input type="text" name="swpf_settings[title_label][<?php echo esc_attr($swpf_item_key); ?>]" value="<?php echo esc_attr($swpf_settings['title_label'][$swpf_item_key]); ?>" min="1" step="1">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            } elseif ($swpf_item_key == 'ratings') {
                ?>
                <div class="swpf-ratings-options-fields swpf-each-items-wrap" data-condition-toggle="swpf-filters-show-hide-ratings" id="ratings">
                    <div class="swpf-tax-heading-wrap">
                        <h4><?php esc_html_e('Ratings', 'super-product-filter'); ?></h4>
                        <div class="swpf-tab-action">
                            <span class="swpf-each-actions swpf-sortable-box icofont-drag"></span>
                            <span class="swpf-each-actions swpf-toggle-box icofont-caret-down"></span>
                        </div>
                    </div>

                    <div class="swpf-option-fields-inner-wrap">
                        <input type="hidden" name="swpf_settings[list_order][ratings]" value="<?php echo esc_attr($swpf_item_key); ?>">
                        <div class="swpf-row">
                            <div class="swpf-field-wrap">
                                <label><?php esc_html_e('Title', 'super-product-filter'); ?></label>
                                <div class="swpf-settings-input-field">
                                    <input type="text" name="swpf_settings[title_label][<?php echo esc_attr($swpf_item_key); ?>]" value="<?php echo esc_attr($swpf_settings['title_label'][$swpf_item_key]); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            } elseif ($swpf_item_key == 'on_sale') {
                ?>
                <div class="swpf-onsale-options-fields swpf-each-items-wrap" data-condition-toggle="swpf-filters-show-hide-on-sale" id="on_sale">
                    <div class="swpf-tax-heading-wrap">
                        <h4><?php esc_html_e('On Sale Products', 'super-product-filter'); ?></h4>
                        <div class="swpf-tab-action">
                            <span class="swpf-each-actions swpf-sortable-box icofont-drag"></span>
                            <span class="swpf-each-actions swpf-toggle-box icofont-caret-down"></span>
                        </div>
                    </div>

                    <div class="swpf-option-fields-inner-wrap">
                        <input type="hidden" name="swpf_settings[list_order][on_sale]" value="<?php echo esc_attr($swpf_item_key); ?>">
                        <div class="swpf-row">
                            <div class="swpf-field-wrap">
                                <label><?php esc_html_e('Title', 'super-product-filter'); ?></label>
                                <div class="swpf-settings-input-field">
                                    <input type="text" name="swpf_settings[title_label][<?php echo esc_attr($swpf_item_key) ?>]" value="<?php echo esc_attr($swpf_settings['title_label'][$swpf_item_key]); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            } elseif ($swpf_item_key == 'in_stock') {
                ?>
                <div class="swpf-instock-options-fields swpf-each-items-wrap" data-condition-toggle="swpf-filters-show-hide-in-stock" id="in_stock">
                    <div class="swpf-tax-heading-wrap">
                        <h4><?php esc_html_e('In Stock Products', 'super-product-filter'); ?></h4>
                        <div class="swpf-tab-action">
                            <span class="swpf-each-actions swpf-sortable-box icofont-drag"></span>
                            <span class="swpf-each-actions swpf-toggle-box icofont-caret-down"></span>
                        </div>
                    </div>

                    <div class="swpf-option-fields-inner-wrap">
                        <input type="hidden" name="swpf_settings[list_order][in_stock]" value="<?php echo esc_attr($swpf_item_key); ?>">
                        <div class="swpf-row">
                            <div class="swpf-field-wrap">
                                <label><?php esc_html_e('Title', 'super-product-filter'); ?></label>
                                <div class="swpf-settings-input-field">
                                    <input type="text" name="swpf_settings[title_label][<?php echo esc_attr($swpf_item_key) ?>]" value="<?php echo esc_attr($swpf_settings['title_label'][$swpf_item_key]); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            } else {
                if (isset($swpf_taxonomies[$swpf_item_key])) {
                    ?>
                    <div class="swpf-<?php echo esc_attr($swpf_item_key); ?>-options-fields swpf-each-items-wrap swpf-settings-content" data-condition-toggle="swpf-filters-show-hide-<?php echo esc_attr($swpf_item_key); ?>" id="<?php echo esc_attr($swpf_taxonomies[$swpf_item_key]->name); ?>">
                        <div class="swpf-tax-heading-wrap">
                            <h4><?php echo esc_html(str_replace('Product ', 'Product - ', ucwords($swpf_taxonomies[$swpf_item_key]->label))); ?></h4>
                            <div class="swpf-tab-action">
                                <span class="swpf-each-actions swpf-sortable-box icofont-drag"></span>
                                <span class="swpf-each-actions swpf-toggle-box icofont-caret-down"></span>
                            </div>
                        </div>

                        <div class="swpf-option-fields-inner-wrap swpf-settings-content">
                            <input type="hidden" name="swpf_settings[list_order][<?php echo esc_attr($swpf_item_key); ?>]" value="<?php echo esc_attr($swpf_item_key); ?>">

                            <div class="swpf-row">
                                <div class="swpf-field-wrap">
                                    <label><?php esc_html_e('Title', 'super-product-filter'); ?></label>
                                    <div class="swpf-settings-input-field">
                                        <input type="text" name="swpf_settings[title_label][<?php echo esc_attr($swpf_item_key); ?>]" value="<?php echo esc_attr($swpf_settings['title_label'][$swpf_item_key]); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="swpf-row">
                                <div class="swpf-field-wrap">
                                    <label><?php esc_html_e('Show Product Count', 'super-product-filter'); ?></label>
                                    <div class="swpf-settings-input-field">
                                        <div class="swpf-toggle-wrap">
                                            <label class="swpf-toggle">
                                                <input type="checkbox" name="swpf_settings[show_count][<?php echo esc_attr($swpf_item_key); ?>]" <?php checked($swpf_settings['show_count'][$swpf_item_key], 'on'); ?> class="swpf-filter-show_count">
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="swpf-field-wrap swpf-hide-term" data-condition-toggle="swpf-field-settings-display-type-<?php echo esc_attr($swpf_item_key); ?>" data-condition-val="image,color">
                                    <label><?php esc_html_e('Hide Term Name', 'super-product-filter'); ?></label>
                                    <div class="swpf-toggle-wrap">
                                        <label class="swpf-toggle">
                                            <input type="checkbox" name="swpf_settings[hide_term_name][<?php echo esc_attr($swpf_item_key); ?>]" <?php checked($swpf_settings['hide_term_name'][$swpf_item_key], 'on'); ?>>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="swpf-row">
                                <div class="swpf-field-wrap">
                                    <label><?php esc_html_e('Display Type', 'super-product-filter'); ?></label>
                                    <div class="swpf-settings-input-field">
                                        <select name="swpf_settings[display_type][<?php echo esc_attr($swpf_item_key); ?>]" class="swpf-filter-display-type" data-condition="toggle" id="swpf-field-settings-display-type-<?php echo esc_attr($swpf_item_key); ?>">
                                            <option value="radio" <?php selected($swpf_settings['display_type'][$swpf_item_key], 'radio'); ?>><?php esc_html_e('Radio', 'super-product-filter') ?></option>
                                            <option value="checkbox" <?php selected($swpf_settings['display_type'][$swpf_item_key], 'checkbox'); ?>><?php esc_html_e('Checkbox', 'super-product-filter') ?></option>
                                            <option value="dropdown" <?php selected($swpf_settings['display_type'][$swpf_item_key], 'dropdown'); ?>><?php esc_html_e('Dropdown', 'super-product-filter') ?></option>
                                            <option value="multi_select" <?php selected($swpf_settings['display_type'][$swpf_item_key], 'multi_select'); ?>><?php esc_html_e('Multi Select', 'super-product-filter') ?></option>
                                            <option value="button" <?php selected($swpf_settings['display_type'][$swpf_item_key], 'button'); ?>><?php esc_html_e('Button', 'super-product-filter') ?></option>
                                            <option value="toggle" <?php selected($swpf_settings['display_type'][$swpf_item_key], 'toggle'); ?>><?php esc_html_e('Toggle', 'super-product-filter') ?></option>
                                            <option value="color" <?php selected($swpf_settings['display_type'][$swpf_item_key], 'color'); ?>><?php esc_html_e('Color', 'super-product-filter') ?></option>
                                            <option value="image" <?php selected($swpf_settings['display_type'][$swpf_item_key], 'image'); ?>><?php esc_html_e('Image', 'super-product-filter') ?></option>
                                        </select>
                                    </div>
                                    <p class="swpf-desc" data-condition-toggle="swpf-field-settings-display-type-<?php echo esc_attr($swpf_item_key); ?>" data-condition-val="color">
                                        <?php esc_html_e('Click on the "Configure Term Options" Button to configure Color.', 'super-product-filter') ?>
                                    </p>
                                    <p class="swpf-desc" data-condition-toggle="swpf-field-settings-display-type-<?php echo esc_attr($swpf_item_key); ?>" data-condition-val="image">
                                        <?php esc_html_e('Click on the "Configure Term Options" Button to configure Image.', 'super-product-filter') ?>
                                    </p>
                                </div>

                                <div class="swpf-field-wrap" data-condition-toggle="swpf-field-settings-display-type-<?php echo esc_attr($swpf_item_key); ?>" data-condition-val="multi_select">
                                    <label><?php esc_html_e('Placeholder Text', 'super-product-filter'); ?></label>
                                    <div class="swpf-settings-input-field">
                                        <input type="text" name="swpf_settings[placeholder_txt][<?php echo esc_attr($swpf_item_key); ?>]" value="<?php echo isset($swpf_settings['placeholder_txt'][$swpf_item_key]) ? esc_attr($swpf_settings['placeholder_txt'][$swpf_item_key]) : null; ?>">
                                    </div>
                                </div>

                                <?php
                                $swpf_args = array(
                                    'taxonomy' => $swpf_item_key,
                                    'orderby' => 'name',
                                    'order' => 'ASC',
                                    'hierarchical' => 0,
                                    'hide_empty' => 0,
                                );
                                $swpf_all_categories = get_terms($swpf_args);
                                $swpf_cat_ids = [];

                                if (!empty($swpf_all_categories)) {
                                    foreach ($swpf_all_categories as $swpf_cat) {
                                        $swpf_cat_ids[] = $swpf_cat->term_id;
                                    }
                                }
                                ?>

                                <div class="swpf-field-wrap swpf-field-logic-operator">
                                    <label><?php esc_html_e('Logic Operator', 'super-product-filter'); ?></label>
                                    <div class="swpf-settings-input-field">
                                        <select name="swpf_settings[multiselect_logic_operator][<?php echo esc_attr($swpf_item_key); ?>]">
                                            <option value="AND" <?php selected($swpf_settings['multiselect_logic_operator'][$swpf_item_key], 'AND'); ?>><?php esc_html_e('AND', 'super-product-filter'); ?></option>
                                            <option value="IN" <?php selected($swpf_settings['multiselect_logic_operator'][$swpf_item_key], 'IN'); ?>><?php esc_html_e('OR', 'super-product-filter'); ?></option>
                                        </select>
                                    </div>
                                </div>

                                <?php
                                if (!($swpf_item_key == 'product_visibility')) {
                                    ?>
                                    <div class="swpf-field-wrap">
                                        <label><?php esc_html_e('Order By', 'super-product-filter'); ?></label>
                                        <div class="swpf-settings-input-field">
                                            <select name="swpf_settings[orderby][<?php echo esc_attr($swpf_item_key); ?>]">
                                                <option value="term_id" <?php selected($swpf_settings['orderby'][$swpf_item_key], 'term_id'); ?>><?php esc_html_e('ID', 'super-product-filter'); ?></option>
                                                <option value="name" <?php selected($swpf_settings['orderby'][$swpf_item_key], 'name'); ?>><?php esc_html_e('Name', 'super-product-filter'); ?></option>
                                                <option value="count" <?php selected($swpf_settings['orderby'][$swpf_item_key], 'count'); ?>><?php esc_html_e('Count', 'super-product-filter'); ?></option>
                                                <option value="number" <?php selected($swpf_settings['orderby'][$swpf_item_key], 'number'); ?>><?php esc_html_e('Name (Numeric)', 'super-product-filter'); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="swpf-field-wrap">
                                        <label><?php esc_html_e('Order Type', 'super-product-filter'); ?></label>
                                        <div class="swpf-settings-input-field">
                                            <select name="swpf_settings[order_type][<?php echo esc_attr($swpf_item_key); ?>]">
                                                <option value="ASC" <?php selected($swpf_settings['order_type'][$swpf_item_key], 'ASC'); ?>><?php esc_html_e('Ascending', 'super-product-filter'); ?></option>
                                                <option value="DESC" <?php selected($swpf_settings['order_type'][$swpf_item_key], 'DESC'); ?>><?php esc_html_e('Descending', 'super-product-filter'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <?php
                            if (!($swpf_item_key == 'product_visibility')) {
                                ?>
                                <div class="swpf-row">
                                    <div class="swpf-field-wrap">
                                        <label><?php esc_html_e('Display Option', 'super-product-filter'); ?></label>
                                        <div class="swpf-settings-input-field">
                                            <select name="swpf_settings[include_exclude_filter][<?php echo esc_attr($swpf_item_key); ?>]" id="swpf-include-exclude-filter-display-<?php echo esc_attr($swpf_item_key); ?>" data-condition="toggle">
                                                <option value="all" <?php selected($swpf_settings['include_exclude_filter'][$swpf_item_key], 'all'); ?>><?php esc_html_e('Display All', 'super-product-filter'); ?></option>
                                                <option value="include-terms" <?php selected($swpf_settings['include_exclude_filter'][$swpf_item_key], 'include-terms'); ?>><?php esc_html_e('Include', 'super-product-filter'); ?></option>
                                                <option value="exclude-terms" <?php selected($swpf_settings['include_exclude_filter'][$swpf_item_key], 'exclude-terms'); ?>><?php esc_html_e('Exclude', 'super-product-filter'); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="swpf-field-wrap swpf-exclude-include-option swpf-include-terms" data-condition-toggle="swpf-include-exclude-filter-display-<?php echo esc_attr($swpf_item_key); ?>" data-condition-val="include-terms">
                                        <label><?php esc_html_e('Include Terms', 'super-product-filter'); ?></label>
                                        <div class="swpf-settings-input-field">
                                            <select name="swpf_settings[include_terms][<?php echo esc_attr($swpf_item_key); ?>][]" class="swpf-selectize" multiple="multiple">
                                                <?php
                                                echo wp_kses(Super_Product_Filter_Admin::get_dropdown_indent(0, $swpf_all_categories, $swpf_settings['include_terms'][$swpf_item_key], $swpf_cat_ids), array(
                                                    'option' => array(
                                                        'value' => array(),
                                                        'selected' => array()
                                                    )
                                                ));
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="swpf-field-wrap swpf-exclude-include-option swpf-exclude-terms" data-condition-toggle="swpf-include-exclude-filter-display-<?php echo esc_attr($swpf_item_key); ?>" data-condition-val="exclude-terms">
                                        <label><?php esc_html_e('Exclude Terms', 'super-product-filter'); ?></label>
                                        <div class="swpf-settings-input-field">
                                            <select name="swpf_settings[exclude_terms][<?php echo esc_attr($swpf_item_key); ?>][]" class="swpf-selectize" multiple="multiple">
                                                <?php
                                                echo wp_kses(Super_Product_Filter_Admin::get_dropdown_indent(0, $swpf_all_categories, $swpf_settings['exclude_terms'][$swpf_item_key], $swpf_cat_ids), array(
                                                    'option' => array(
                                                        'value' => array(),
                                                        'selected' => array()
                                                    )
                                                ));
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <?php
                                    if ($swpf_item_key == 'product_cat') {
                                        ?>
                                        <div class="swpf-field-wrap" data-condition-toggle="swpf-vcol-options-select-<?php echo esc_attr($swpf_item_key); ?>" data-condition-val="vertical">
                                            <div class="swpf-field-wrap" data-condition-toggle="swpf-field-settings-display-type-<?php echo esc_attr($swpf_item_key); ?>" data-condition-val="radio,checkbox,dropdown,multi_select,toggle,color,image">
                                                <label><?php esc_html_e('Indent Category', 'super-product-filter'); ?></label>

                                                <div class="swpf-settings-input-field">
                                                    <div class="swpf-toggle-wrap">
                                                        <label class="swpf-toggle">
                                                            <input type="checkbox" name="swpf_settings[config][indent_cat]" <?php checked($swpf_settings['config']['indent_cat'], 'on'); ?> class="swpf-filter-show_count">
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="swpf-row">
                                    <div class="swpf-field-wrap">
                                        <label><?php esc_html_e('Display Option', 'super-product-filter'); ?></label>
                                        <div class="swpf-settings-input-field">
                                            <select name="swpf_settings[include_exclude_filter][<?php echo esc_attr($swpf_item_key); ?>]" id="swpf-include-exclude-filter-display-<?php echo esc_attr($swpf_item_key); ?>" data-condition="toggle">
                                                <option value="all" <?php selected($swpf_settings['include_exclude_filter'][$swpf_item_key], 'all'); ?>><?php esc_html_e('Display All', 'super-product-filter'); ?></option>
                                                <option value="include-terms" <?php selected($swpf_settings['include_exclude_filter'][$swpf_item_key], 'include-terms'); ?>><?php esc_html_e('Include', 'super-product-filter'); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="swpf-field-wrap swpf-exclude-include-option swpf-include-terms" data-condition-toggle="swpf-include-exclude-filter-display-<?php echo esc_attr($swpf_item_key); ?>" data-condition-val="include-terms">
                                        <label><?php esc_html_e('Include Terms', 'super-product-filter'); ?></label>
                                        <div class="swpf-settings-input-field">
                                            <select name="swpf_settings[include_terms][<?php echo esc_attr($swpf_item_key); ?>][]" class="swpf-selectize" multiple="multiple">
                                                <?php
                                                echo wp_kses(Super_Product_Filter_Admin::get_dropdown_indent(0, $swpf_all_categories, $swpf_settings['include_terms'][$swpf_item_key], $swpf_cat_ids), array(
                                                    'option' => array(
                                                        'value' => array(),
                                                        'selected' => array()
                                                    )
                                                ));
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <?php
                            }
                            ?>

                            <div class="swpf-row swpf-last-row" data-condition-toggle="swpf-field-settings-display-type-<?php echo esc_attr($swpf_item_key); ?>" data-condition-val="radio,checkbox,toggle,button,image,color">
                                <div class="swpf-field-wrap swpf-field-orientation">
                                    <label><?php esc_html_e('Field Orientation', 'super-product-filter'); ?></label>
                                    <div class="swpf-settings-input-field">
                                        <select name="swpf_settings[field_orientation][<?php echo esc_attr($swpf_item_key); ?>]" id="swpf-vcol-options-select-<?php echo esc_attr($swpf_item_key); ?>" data-condition="toggle">
                                            <option value="vertical" <?php selected($swpf_settings['field_orientation'][$swpf_item_key], 'vertical'); ?>><?php esc_html_e('Vertical', 'super-product-filter'); ?></option>
                                            <option value="horizontal" <?php selected($swpf_settings['field_orientation'][$swpf_item_key], 'horizontal'); ?>><?php esc_html_e('Horizontal', 'super-product-filter'); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="swpf-custom-term-options-wrap">
                                <div class="swpf-button-wrap">
                                    <button class="swpf-show-custom-term-options" data-tax-key="<?php echo esc_attr($swpf_item_key); ?>" data-tax-id="<?php echo esc_attr($post_id); ?>" data-terms-customize-settings="<?php echo esc_attr(htmlspecialchars(wp_json_encode(isset($swpf_settings['terms_customize'][$swpf_item_key]) ? $swpf_settings['terms_customize'][$swpf_item_key] : []), ENT_QUOTES, 'UTF-8')); ?>">
                                        <i class="icofont-gear"></i> <?php esc_html_e('Configure Term Options', 'super-product-filter'); ?>
                                    </button>
                                </div>

                            </div> <!-- swpf-custom-term-options-wrap -->
                        </div>
                    </div>
                    <?php
                }
            }
            $swpf_index++;
        }
        ?>
    </div>
</div> <!-- swpf-settings-field-wrap -->