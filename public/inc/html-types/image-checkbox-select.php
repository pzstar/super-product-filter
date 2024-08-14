<?php
defined('ABSPATH') || die();

$tax_show_count = (isset($settings['show_count'][$tax_name]) && $settings['show_count'][$tax_name] == 'on') ? true : false;
?>

<div class="swpf-filter-item-list swpf-checkbox-image-select-type">
    <?php
    if ($tax_name == 'product_cat') {
        ?>
        <ul class="swpf-filter-product-category swpf-filter-product-category-image-checkbox <?php echo $settings['config']['indent_cat'] == 'on' ? 'swpf-indent-product-cat' : ''; ?>">
            <?php
            $all_terms = $settings['terms_customize'][$tax_name];
            $term_preview_array = [];

            if ($all_terms) {
                foreach ($all_terms as $key => $aterm) {
                    $term_preview_array[$key]['color'] = $aterm['term_color'];
                    $term_preview_array[$key]['image'] = $aterm['term_image'];
                }
            }

            if ($settings['field_orientation']['product_cat'] != 'horizontal' && (isset($settings['config']['indent_cat']) && $settings['config']['indent_cat'] == 'on')) {
                $selected_cats = array();
                if (isset($current_filter_option['categories'])) {
                    $selected_cats = is_array($current_filter_option['categories']) ? $current_filter_option['categories'] : implode(',', $current_filter_option['categories']);
                }

                $all_terms = $settings['terms_customize'][$tax_name];
                $term_name_array = [];
                $term_count_array = [];

                if ($all_terms) {
                    foreach ($all_terms as $key => $aterm) {
                        $term_name_array[$key] = (isset($aterm['term_name']) && !empty($aterm['term_name'])) ? esc_html(apply_filters('swpf_translate_string', $aterm['term_name'], 'Super Product Filter', esc_html($sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($key))) : esc_html(ucwords(str_replace('-', ' ', get_term($key)->name)));
                        if (isset($settings['show_count']['product_cat']) && $settings['show_count']['product_cat'] == 'on') {
                            $term = get_term($key);
                            $args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug);
                            $term_cquery = new WP_Query($args);
                            $post_count = $term_cquery->post_count;

                            $args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug, null, true);
                            $term_cquery = new WP_Query($args);
                            $post_count_ckk = $term_cquery->post_count;
                            wp_reset_postdata();

                            $post_count = min($post_count, $post_count_ckk, $term->count);
                            $term_count_array[$key] = $post_count;
                        }
                    }
                }

                echo swpf_terms_color_image_checkboxlist(0, array(
                    'taxonomy' => 'product_cat',
                    'name' => 'categories',
                    'value_field' => 'slug',
                    'selected_cats' => $selected_cats,
                    'show_count' => $tax_show_count,
                    'term_preview_array' => $term_preview_array,
                    'hide_term_name' => isset($settings['hide_term_name'][$tax_name]) && $settings['hide_term_name'][$tax_name] == 'on',
                    'type' => 'image',
                    'term_count_array' => $term_count_array,
                    'term_name_array' => $term_name_array,
                    'hide_terms' => [],
                        ), $terms);
            } else {
                if ($terms) {
                    foreach ($terms as $key => $term) {
                        $checked = false;

                        if ($tax_show_count) {
                            $args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug);
                            $term_cquery = new WP_Query($args);
                            $post_count = $term_cquery->post_count;

                            $args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug, null, true);
                            $term_cquery = new WP_Query($args);
                            $post_count_ckk = $term_cquery->post_count;
                            wp_reset_postdata();
                            $post_count = min($post_count, $post_count_ckk, $term->count);
                        }

                        if (isset($current_filter_option['categories']) && !empty($current_filter_option['categories']) && is_array($current_filter_option['categories'])) {
                            $checked = in_array($term->slug, $current_filter_option['categories']) ? true : false;
                        }
                        ?>
                        <li class="swpf-filter-item product_cat-<?php echo esc_attr($term->slug); ?>">
                            <label class="swpf-filter-label">
                                <input type="checkbox" id="swpf-term-<?php echo esc_attr($term->term_id) ?>" class="swpf-chkbox-term swpf-chkbox-term-<?php echo esc_attr($term->term_id); ?>" name="categories[]" data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php checked($checked, true); ?>>
                                <?php if ($settings['display_type'][$tax_name] == 'image') { ?>
                                    <span class="swpf-image-box"><span class="swpf-image-url"  style="background-image: url(<?php echo esc_url($settings['terms_customize'][$tax_name][$term->term_id]['term_image']); ?>)"></span></span>
                                <?php } else if ($settings['display_type'][$tax_name] == 'color') { ?>
                                    <span class="swpf-color-box" style="background: <?php echo esc_attr($settings['terms_customize'][$tax_name][$term->term_id]['term_color']); ?>"></span>
                                    <?php
                                }

                                if (isset($settings['hide_term_name'][$tax_name]) && $settings['hide_term_name'][$tax_name] != 'on') {
                                    ?>
                                    <span class="swpf-title">
                                        <?php
                                        if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                                            echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super Product Filter', esc_html($sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                                        } else {
                                            echo esc_html($term->name);
                                        }
                                        ?>
                                    </span>
                                    <?php
                                }

                                if ($tax_show_count) {
                                    ?>
                                    <span class="swpf-count">&nbsp;(<?php echo esc_attr($post_count); ?>)</span>
                                <?php } ?>
                            </label>
                        </li>
                        <?php
                    }
                }
            }
            ?>
        </ul>
        <?php
    } else {
        if ($terms) {
            foreach ($terms as $key => $term) {
                $checked = false;

                if ($tax_show_count) {
                    $args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug);
                    $term_cquery = new WP_Query($args);
                    $post_count = $term_cquery->post_count;

                    $args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug, null, true);
                    $term_cquery = new WP_Query($args);
                    $post_count_ckk = $term_cquery->post_count;
                    wp_reset_postdata();
                    $post_count = min($post_count, $post_count_ckk, $term->count);
                }

                if ($tax_name == 'product_visibility') {
                    if (isset($current_filter_option['visibility']) && !empty($current_filter_option['visibility']) && is_array($current_filter_option['visibility'])) {
                        $checked = in_array($term->slug, $current_filter_option['visibility']) ? true : false;
                    }
                    ?>
                    <div class="swpf-filter-item">
                        <label class="swpf-filter-label">
                            <input type="checkbox" id="swpf-term-<?php echo esc_attr($term->term_id) ?>" class="swpf-chkbox-term swpf-chkbox-term-<?php echo esc_attr($term->term_id); ?>" name="visibility[]" data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php checked($checked, true); ?>>

                            <?php if ($settings['display_type'][$tax_name] == 'image') { ?>
                                <span class="swpf-image-box"><span class="swpf-image-url"  style="background-image: url(<?php echo esc_url($settings['terms_customize'][$tax_name][$term->term_id]['term_image']); ?>)"></span></span>
                            <?php } else if ($settings['display_type'][$tax_name] == 'color') { ?>
                                <span class="swpf-color-box" style="background: <?php echo esc_attr($settings['terms_customize'][$tax_name][$term->term_id]['term_color']); ?>"></span>
                                <?php
                            }

                            if (isset($settings['hide_term_name'][$tax_name]) && $settings['hide_term_name'][$tax_name] != 'on') {
                                ?>
                                <span class="swpf-title">
                                    <?php
                                    if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                                        echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super Product Filter', esc_html($sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                                    } else {
                                        echo esc_html(ucwords(str_replace('-', ' ', $term->name)));
                                    }
                                    ?>
                                </span>
                                <?php
                            }

                            if ($tax_show_count) {
                                ?>
                                <span class="swpf-count">&nbsp;(<?php echo esc_attr($post_count); ?>)</span>
                                <?php
                            }
                            ?>
                        </label>
                    </div>
                    <?php
                } else if ($tax_name == 'product_tag') {
                    if (isset($current_filter_option['tags']) && !empty($current_filter_option['tags']) && is_array($current_filter_option['tags'])) {
                        $checked = in_array($term->slug, $current_filter_option['tags']) ? true : false;
                    }
                    ?>
                    <div class="swpf-filter-item">
                        <label class="swpf-filter-label">
                            <input type="checkbox" id="swpf-term-<?php echo esc_attr($term->term_id) ?>" class="swpf-chkbox-term swpf-chkbox-term-<?php echo esc_attr($term->term_id); ?>" name="tags[]" data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php checked($checked, true); ?>>
                            <?php if ($settings['display_type'][$tax_name] == 'image') { ?>
                                <span class="swpf-image-box"><span class="swpf-image-url"  style="background-image: url(<?php echo esc_url($settings['terms_customize'][$tax_name][$term->term_id]['term_image']); ?>)"></span></span>
                            <?php } else if ($settings['display_type'][$tax_name] == 'color') { ?>
                                <span class="swpf-color-box" style="background: <?php echo esc_attr($settings['terms_customize'][$tax_name][$term->term_id]['term_color']); ?>"></span>
                                <?php
                            }

                            if (isset($settings['hide_term_name'][$tax_name]) && $settings['hide_term_name'][$tax_name] != 'on') {
                                ?>
                                <span class="swpf-title">
                                    <?php
                                    if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                                        echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super Product Filter', esc_html($sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                                    } else {
                                        echo esc_html($term->name);
                                    }
                                    ?>
                                </span>
                                <?php
                            }

                            if ($tax_show_count) {
                                ?>
                                <span class="swpf-count">&nbsp;(<?php echo esc_attr($post_count); ?>)</span>
                                <?php
                            }
                            ?>
                        </label>
                    </div>
                    <?php
                } else if ((substr($tax_name, 0, 3) === 'pa_') && isset($term->term_id)) {
                    if (isset($current_filter_option['attribute'][$tax_name]) && !empty($current_filter_option['attribute'][$tax_name]) && is_array($current_filter_option['attribute'][$tax_name])) {
                        $checked = in_array($term->slug, $current_filter_option['attribute'][$tax_name]) ? true : false;
                    }
                    ?>
                    <div class="swpf-filter-item">
                        <label class="swpf-filter-label">
                            <input type="checkbox" id="swpf-term-<?php echo esc_attr($term->term_id) ?>" class="swpf-chkbox-term swpf-chkbox-term-<?php echo esc_attr($term->term_id); ?>" name="attribute[<?php echo esc_attr($tax_name); ?>][]" data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php checked($checked, true); ?>>
                            <?php
                            if ($settings['display_type'][$tax_name] == 'image') {
                                ?>
                                <span class="swpf-image-box"><span class="swpf-image-url"  style="background-image: url(<?php echo esc_url($settings['terms_customize'][$tax_name][$term->term_id]['term_image']); ?>)"></span></span>
                                <?php
                            } else if ($settings['display_type'][$tax_name] == 'color') {
                                ?>
                                <span class="swpf-color-box" style="background: <?php echo esc_attr($settings['terms_customize'][$tax_name][$term->term_id]['term_color']); ?>"></span>
                                <?php
                            }

                            if (isset($settings['hide_term_name'][$tax_name]) && $settings['hide_term_name'][$tax_name] != 'on') {
                                ?>
                                <span class="swpf-title">
                                    <?php
                                    if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                                        echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super Product Filter', esc_html($sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                                    } else {
                                        echo esc_html($term->name);
                                    }
                                    ?>
                                </span>
                                <?php
                            }

                            if ($tax_show_count) {
                                ?>
                                <span class="swpf-count">&nbsp;(<?php echo esc_attr($post_count); ?>)</span>
                                <?php
                            }
                            ?>
                        </label>
                    </div>
                    <?php
                } else if (isset($term->term_id)) {
                    if (isset($current_filter_option['attribute'][$tax_name]) && !empty($current_filter_option['attribute'][$tax_name]) && is_array($current_filter_option['attribute'][$tax_name])) {
                        $checked = in_array($term->slug, $current_filter_option['attribute'][$tax_name]) ? true : false;
                    }
                    ?>
                    <div class="swpf-filter-item">
                        <label class="swpf-filter-label">
                            <input type="checkbox" id="swpf-term-<?php echo esc_attr($term->term_id) ?>" class="swpf-chkbox-term swpf-chkbox-term-<?php echo esc_attr($term->term_id); ?>" name="attribute[<?php echo esc_attr($tax_name); ?>][]" data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php checked($checked, true); ?>>
                            <?php
                            if ($settings['display_type'][$tax_name] == 'image') {
                                ?>
                                <span class="swpf-image-box"><span class="swpf-image-url"  style="background-image: url(<?php echo esc_url($settings['terms_customize'][$tax_name][$term->term_id]['term_image']); ?>)"></span></span>
                                <?php
                            } else if ($settings['display_type'][$tax_name] == 'color') {
                                ?>
                                <span class="swpf-color-box" style="background: <?php echo esc_attr($settings['terms_customize'][$tax_name][$term->term_id]['term_color']); ?>"></span>
                                <?php
                            }

                            if (isset($settings['hide_term_name'][$tax_name]) && $settings['hide_term_name'][$tax_name] != 'on') {
                                ?>
                                <span class="swpf-title">
                                    <?php
                                    if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                                        echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super Product Filter', esc_html($sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                                    } else {
                                        echo esc_html($term->name);
                                    }
                                    ?>
                                </span>
                                <?php
                            }

                            if ($tax_show_count) {
                                ?>
                                <span class="swpf-count">&nbsp;(<?php echo esc_attr($post_count); ?>)</span>
                                <?php
                            }
                            ?>
                        </label>
                    </div>
                    <?php
                }
            } /* End For Terms Loop */
        }
    }
    ?>
</div>