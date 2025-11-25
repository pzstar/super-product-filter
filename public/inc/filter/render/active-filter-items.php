<?php
defined('ABSPATH') || die();

if ($swpf_current_filter_option) {
    foreach ($swpf_current_filter_option as $swpf_key => $swpf_value) {
        if ($swpf_key == 'price') {
            $swpf_min_max_price = Super_Product_Filter_General::get_filtered_price();
            $swpf_from = isset($swpf_current_filter_option['price']['min_price']) && $swpf_current_filter_option['price']['min_price'] ? $swpf_current_filter_option['price']['min_price'] : floor($swpf_min_max_price->min_price ?: 0);
            $swpf_to = isset($swpf_current_filter_option['price']['max_price']) && $swpf_current_filter_option['price']['max_price'] ? $swpf_current_filter_option['price']['max_price'] : ceil($swpf_min_max_price->max_price ?: 0);
            ?>

            <div class="swpf-group-activated-filter swpf-activated-price">
                <span class="swpf-active-filter-title">
                    <i class="icofont-tick-boxed"></i>
                    <?php esc_html_e('Price', 'super-product-filter'); ?>
                </span>

                <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[price]" value="1">
                    <?php
                    $this->render_price($swpf_from);
                    echo (" - ");
                    $this->render_price($swpf_to)
                        ?>
                    <span class="swpf-remove-filter-icon"></span>
                </button>
            </div>
            <?php
        } elseif ($swpf_key === 'range') {
            if ($swpf_value) {
                foreach ($swpf_value as $swpf_active_filter_tax => $swpf_active_filter_range) {
                    $swpf_range_taxonomy = get_taxonomy($swpf_active_filter_tax);
                    if (!$swpf_range_taxonomy) {
                        continue;
                    }
                    ?>
                    <div class="swpf-group-activated-filter swpf-activated-range">
                        <span class="swpf-active-filter-title">
                            <i class="icofont-tick-boxed"></i>
                            <?php echo esc_html($swpf_range_taxonomy->labels->singular_name); ?>
                        </span>

                        <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[range_<?php echo esc_attr($swpf_active_filter_tax) ?>]" value="1">
                            <?php echo esc_attr($swpf_active_filter_range['min']) . ' - ' . esc_attr($swpf_active_filter_range['max']) ?>
                            <span class="swpf-remove-filter-icon"></span>
                        </button>
                    </div>
                    <?php
                }
            }
        } elseif ($swpf_key == 'attribute') {
            $swpf_attributes = $swpf_value;
            if ($swpf_attributes) {
                foreach ($swpf_attributes as $swpf_attribute_slug => $swpf_option_slugs) {
                    ?>
                    <div class="swpf-group-activated-filter swpf-activated-attribute">
                        <span class="swpf-active-filter-title">
                            <i class="icofont-tick-boxed"></i>
                            <?php echo esc_html(wc_attribute_label($swpf_attribute_slug)); ?>
                        </span>
                        <?php
                        if ($swpf_option_slugs) {
                            foreach ($swpf_option_slugs as $swpf_slug) {
                                $swpf_str = '';
                                $term = get_term_by('slug', $swpf_slug, $swpf_attribute_slug);
                                $swpf_str .= $term->name;
                                ?>
                                <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[attribute][<?php echo esc_attr($swpf_attribute_slug); ?>]" value="<?php echo esc_attr($swpf_slug); ?>">
                                    <?php echo esc_html($swpf_str); ?><span class="swpf-remove-filter-icon"></span>
                                </button>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <?php
                }
            }
        } elseif ($swpf_key == 'tags') {
            $swpf_tags = $swpf_value;
            ?>
            <div class="swpf-group-activated-filter swpf-activated-attribute">
                <span class="swpf-active-filter-title">
                    <i class="icofont-tick-boxed"></i>
                    <?php esc_html_e('Tags', 'super-product-filter'); ?>
                </span>
                <?php
                if ($swpf_tags) {
                    foreach ($swpf_tags as $swpf_tag_slug) {
                        $term = get_term_by('slug', $swpf_tag_slug, 'product_tag');
                        ?>
                        <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[tags][<?php echo esc_attr($swpf_tag_slug); ?>]" value="<?php echo esc_attr($swpf_tag_slug); ?>">
                            <?php echo esc_html(ucwords($term->name)); ?><span class="swpf-remove-filter-icon"></span>
                        </button>
                        <?php
                    }
                }
                ?>

            </div>
            <?php
        } elseif ($swpf_key == 'brands') {
            $swpf_brands = $swpf_value;
            ?>
            <div class="swpf-group-activated-filter swpf-activated-attribute">
                <span class="swpf-active-filter-title">
                    <i class="icofont-tick-boxed"></i>
                    <?php esc_html_e('Brands', 'super-product-filter'); ?>
                </span>
                <?php
                if ($swpf_brands) {
                    foreach ($swpf_brands as $swpf_brand_slug) {
                        $term = get_term_by('slug', $swpf_brand_slug, 'product_brand');
                        ?>
                        <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[brands][<?php echo esc_attr($swpf_brand_slug); ?>]" value="<?php echo esc_attr($swpf_brand_slug); ?>">
                            <?php echo esc_html(ucwords(esc_html(isset($term->name) ? $term->name : $swpf_brand_slug))); ?><span class="swpf-remove-filter-icon"></span>
                        </button>
                        <?php
                    }
                }
                ?>

            </div>
            <?php
        } elseif ($swpf_key == 'visibility') {
            $swpf_visibility = $swpf_value;
            ?>
            <div class="swpf-group-activated-filter swpf-activated-attribute">
                <span class="swpf-active-filter-title">
                    <i class="icofont-tick-boxed"></i>
                    <?php esc_html_e('Visibility', 'super-product-filter'); ?>
                </span>
                <?php
                if ($swpf_visibility) {
                    foreach ($swpf_visibility as $swpf_visibility_slug) {
                        $term = get_term_by('slug', $swpf_visibility_slug, 'product_visibility');
                        ?>
                        <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[visibility][<?php echo esc_attr($swpf_visibility_slug); ?>]" value="<?php echo esc_attr($swpf_visibility_slug); ?>">
                            <?php echo esc_html(ucwords($term->name)); ?><span class="swpf-remove-filter-icon"></span>
                        </button>
                        <?php
                    }
                }
                ?>
            </div>
            <?php
        } elseif ($swpf_key == 'categories') {
            $swpf_category_slugs = $swpf_value;
            ?>
            <div class="swpf-group-activated-filter  swpf-activated-categories">
                <span class="swpf-active-filter-title">
                    <i class="icofont-tick-boxed"></i>
                    <?php
                    if (count($swpf_category_slugs) > 1) {
                        esc_html_e('Categories', 'super-product-filter');
                    } else {
                        esc_html_e('Category', 'super-product-filter');
                    }
                    ?>
                </span>
                <?php
                if ($swpf_category_slugs) {
                    foreach ($swpf_category_slugs as $swpf_category_slug) {
                        if ($swpf_category_slug) {
                            $cat = get_term_by('slug', $swpf_category_slug, 'product_cat');
                            ?>
                            <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[categories]" value="<?php echo esc_attr($swpf_category_slug); ?>">
                                <?php echo esc_html($cat->name) ?><span class="swpf-remove-filter-icon"></span>
                            </button>
                            <?php
                        }
                    }
                }
                ?>
            </div>
            <?php
        } elseif ($swpf_key == 'on-sale' && $swpf_value == '1') {
            ?>
            <div class="swpf-group-activated-filter swpf-activated-on-sale">
                <span class="swpf-active-filter-title">
                    <i class="icofont-tick-boxed"></i>
                    <?php esc_html_e('On Sale Products', 'super-product-filter'); ?>
                </span>
                ?>
                <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[on-sale]" value="1">
                    <?php esc_html_e('On Sale', 'super-product-filter'); ?>
                    <span class="swpf-remove-filter-icon"></span>
                </button>
            </div>
            <?php
        } elseif ($swpf_key == 'in-stock' && $swpf_value == '1') {
            ?>
            <div class="swpf-group-activated-filter swpf-activated-in-stock">
                <span class="swpf-active-filter-title">
                    <i class="icofont-tick-boxed"></i>
                    <?php esc_html_e('In Stock Product Only', 'super-product-filter'); ?>
                </span>

                <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[in-stock]" value="1">
                    <?php esc_html_e('In Stock', 'super-product-filter'); ?>
                    <span class="swpf-remove-filter-icon"></span>
                </button>
            </div>
            <?php
        } elseif ($swpf_key == 'review') {
            ?>
            <div class="swpf-group-activated-filter swpf-activated-review">
                <span class="swpf-active-filter-title">
                    <i class="icofont-tick-boxed"></i>
                    <?php esc_html_e('Review From', 'super-product-filter'); ?>
                </span>

                <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[review-from]" value="1">
                    <?php echo esc_attr($swpf_value['review_from']); ?>
                    <?php $swpf_value['review_from'] > 1 ? esc_html_e(' reviews', 'super-product-filter') : esc_html_e(' review', 'super-product-filter'); ?>
                    <span class="swpf-remove-filter-icon"></span>
                </button>
            </div>
            <?php
        } elseif ($swpf_key == 'rating-from') {
            ?>
            <div class="swpf-group-activated-filter swpf-activated-rating">
                <span class="swpf-active-filter-title">
                    <i class="icofont-tick-boxed"></i>
                    <?php esc_html_e('Rating By', 'super-product-filter'); ?>
                </span>

                <?php
                if ($swpf_value) {
                    foreach ($swpf_value as $swpf_rating) {
                        ?>
                        <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[rating-from]" value="1">
                            <?php echo esc_attr($swpf_rating); ?>
                            <?php $swpf_rating > 1 ? esc_html_e(' stars', 'super-product-filter') : esc_html_e(' star', 'super-product-filter'); ?>
                            <span class="swpf-remove-filter-icon"></span>
                        </button>
                        <?php
                    }
                }
                ?>

            </div>
            <?php
        }
    }
}