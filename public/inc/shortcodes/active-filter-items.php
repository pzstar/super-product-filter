<?php
defined('ABSPATH') || die();

if ($current_filter_option) {
    foreach ($current_filter_option as $key => $value) {
        if ($key == 'price') {
            $min_max_price = $this->get_filtered_price();
            $from = isset($current_filter_option['price']['min_price']) && $current_filter_option['price']['min_price'] ? $current_filter_option['price']['min_price'] : floor($min_max_price->min_price ?: 0);
            $to = isset($current_filter_option['price']['max_price']) && $current_filter_option['price']['max_price'] ? $current_filter_option['price']['max_price'] : ceil($min_max_price->max_price ?: 0);

            echo '<div class="swpf-group-activated-filter swpf-activated-price">';
            echo '<span class="swpf-active-filter-title"><i class="icofont-tick-boxed"></i>' . esc_html__('Price', 'super-product-filter') . '</span>';
            ?>

            <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[price]" value="1">
                <?php
                $this->render_price($from);
                echo (" - ");
                $this->render_price($to)
                ?>
                <span class="swpf-remove-filter-icon"></span>
            </button>

            <?php
            echo '</div>';
        } elseif ($key === 'range') {
            if ($value) {
                foreach ($value as $active_filter_tax => $active_filter_range) {
                    $range_taxonomy = get_taxonomy($active_filter_tax);
                    if (!$range_taxonomy) {
                        continue;
                    }
                    echo '<div class="swpf-group-activated-filter swpf-activated-range">';
                    echo '<span class="swpf-active-filter-title"><i class="icofont-tick-boxed"></i>' . esc_html($range_taxonomy->labels->singular_name) . '</span>';
                    ?>    
                    <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[range_<?php echo esc_attr($active_filter_tax) ?>]" value="1">
                        <?php echo esc_attr($active_filter_range['min']) . ' - ' . esc_attr($active_filter_range['max']) ?><span class="swpf-remove-filter-icon"></span>
                    </button>
                    <?php
                    echo '</div>';
                }
            }
        } else if ($key == 'attribute') {
            $attributes = $value;
            if ($attributes) {
                foreach ($attributes as $attribute_slug => $option_slugs) {
                    echo '<div class="swpf-group-activated-filter swpf-activated-attribute">';
                    echo '<span class="swpf-active-filter-title"><i class="icofont-tick-boxed"></i>' . wc_attribute_label($attribute_slug) . '</span>';
                    if ($option_slugs) {
                        foreach ($option_slugs as $slug) {
                            $str = '';
                            $term = get_term_by('slug', $slug, $attribute_slug);
                            $str .= $term->name;
                            ?>
                            <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[attribute][<?php echo esc_attr($attribute_slug); ?>]" value="<?php echo esc_attr($slug); ?>">
                                <?php echo esc_html($str); ?><span class="swpf-remove-filter-icon"></span>
                            </button>
                            <?php
                        }
                    }
                    echo '</div>';
                }
            }
        } elseif ($key == 'tags') {
            $tags = $value;
            echo '<div class="swpf-group-activated-filter swpf-activated-attribute">';
            echo '<span class="swpf-active-filter-title"><i class="icofont-tick-boxed"></i>' . esc_html__('Tags', 'super-product-filter') . '</span>';
            if ($tags) {
                foreach ($tags as $tag_slug) {
                    $term = get_term_by('slug', $tag_slug, 'product_tag');
                    ?>
                    <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[tags][<?php echo esc_attr($tag_slug); ?>]" value="<?php echo esc_attr($tag_slug); ?>">
                        <?php echo ucwords(esc_html($term->name)); ?><span class="swpf-remove-filter-icon"></span>
                    </button>
                    <?php
                }
            }

            echo '</div>';
        } elseif ($key == 'visibility') {
            $visibility = $value;
            echo '<div class="swpf-group-activated-filter swpf-activated-attribute">';
            echo '<span class="swpf-active-filter-title"><i class="icofont-tick-boxed"></i>' . esc_html__('Visibility', 'super-product-filter') . '</span>';
            if ($visibility) {
                foreach ($visibility as $visibility_slug) {
                    $term = get_term_by('slug', $visibility_slug, 'product_visibility');
                    ?>
                    <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[visibility][<?php echo esc_attr($visibility_slug); ?>]" value="<?php echo esc_attr($visibility_slug); ?>">
                        <?php echo ucwords(esc_html($term->name)); ?><span class="swpf-remove-filter-icon"></span>
                    </button>
                    <?php
                }
            }
            echo '</div>';
        } else if ($key == 'categories') {
            $category_slugs = $value;
            echo '<div class="swpf-group-activated-filter  swpf-activated-categories">';
            echo '<span class="swpf-active-filter-title"><i class="icofont-tick-boxed"></i>';
            if (count($category_slugs) > 1) {
                esc_html_e('Categories', 'super-product-filter');
            } else {
                esc_html_e('Category', 'super-product-filter');
            }
            echo '</span>';
            if ($category_slugs) {
                foreach ($category_slugs as $category_slug) {
                    if ($category_slug) {
                        $cat = get_term_by('slug', $category_slug, 'product_cat');
                        ?>
                        <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[categories]" value="<?php echo esc_attr($category_slug); ?>">
                            <?php echo esc_html($cat->name) ?><span class="swpf-remove-filter-icon"></span>
                        </button>
                        <?php
                    }
                }
            }
            echo '</div>';
        } else if ($key == 'on-sale' && $value == '1') {
            echo '<div class="swpf-group-activated-filter swpf-activated-on-sale">';
            echo '<span class="swpf-active-filter-title"><i class="icofont-tick-boxed"></i>' . esc_html__('On Sale Products', 'super-product-filter') . '</span>';
            ?>
            <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[on-sale]" value="1">
                <?php esc_html_e('On Sale', 'super-product-filter'); ?>
                <span class="swpf-remove-filter-icon"></span></button>
            <?php
            echo '</div>';
        } else if ($key == 'in-stock' && $value == '1') {
            echo '<div class="swpf-group-activated-filter swpf-activated-in-stock">';
            echo '<span class="swpf-active-filter-title"><i class="icofont-tick-boxed"></i>' . esc_html__('In Stock Product Only', 'super-product-filter') . '</span>';
            ?>
            <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[in-stock]" value="1">
                <?php esc_html_e('In Stock', 'super-product-filter'); ?>
                <span class="swpf-remove-filter-icon"></span>
            </button>
            <?php
            echo '</div>';
        } else if ($key == 'review') {
            echo '<div class="swpf-group-activated-filter swpf-activated-review">';
            echo '<span class="swpf-active-filter-title"><i class="icofont-tick-boxed"></i>' . esc_html__('Review From', 'super-product-filter') . '</span>';
            ?>
            <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[review-from]" value="1">
                <?php echo esc_attr($value['review_from']); ?>
                <?php $value['review_from'] > 1 ? esc_html_e(' reviews', 'super-product-filter') : esc_html_e(' review', 'super-product-filter'); ?>
                <span class="swpf-remove-filter-icon"></span>
            </button>
            <?php
            echo '</div>';
        } else if ($key == 'rating-from') {
            echo '<div class="swpf-group-activated-filter swpf-activated-rating">';
            echo '<span class="swpf-active-filter-title"><i class="icofont-tick-boxed"></i>' . esc_html__('Rating By', 'super-product-filter') . '</span>';

            if ($value) {
                foreach ($value as $rating) {
                    ?>
                    <button type="submit" class="swpf-remove-filter-item" name="swpf_remove[rating-from]" value="1">
                        <?php echo esc_attr($rating); ?>
                        <?php $rating > 1 ? esc_html_e(' stars', 'super-product-filter') : esc_html_e(' star', 'super-product-filter'); ?>
                        <span class="swpf-remove-filter-icon"></span>
                    </button>
                    <?php
                }
            }

            echo '</div>';
        }
    }
}