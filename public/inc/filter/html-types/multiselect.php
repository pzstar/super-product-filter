<?php
defined('ABSPATH') || die();

$swpf_tax_show_count = (isset($settings['show_count'][$tax_name]) && $settings['show_count'][$tax_name] == 'on') ? true : false;

if ($tax_name == 'product_visibility') {
    ?>
    <div class="swpf-multiselect-wrap">
        <select class="swpf-multiselect" name="visibility[]" multiple>
            <?php
            if ($terms) {
                foreach ($terms as $key => $term) {
                    $selected = false;

                    if ($swpf_tax_show_count) {
                        $swpf_args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count = $swpf_term_cquery->post_count;

                        $swpf_args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug, null, true);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count_ckk = $swpf_term_cquery->post_count;
                        wp_reset_postdata();
                        $swpf_post_count = min($swpf_post_count, $swpf_post_count_ckk, $term->count);
                    }

                    if (isset($current_filter_option['visibility']) && !empty($current_filter_option['visibility']) && is_array($current_filter_option['visibility'])) {
                        $selected = in_array($term->slug, $current_filter_option['visibility']) ? true : false;
                    }
                    ?>
                    <option data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php selected($selected, true); ?>>
                        <?php
                        if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                            echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                        } else {
                            echo esc_html(ucwords(str_replace('-', ' ', $term->name)));
                        }

                        if ($swpf_tax_show_count) {
                            ?>
                            <span class="swpf-count">&nbsp;(<?php echo esc_attr($swpf_post_count); ?>)</span>
                            <?php
                        }
                        ?>
                    </option>
                    <?php
                }
            }
            ?>
        </select>
    </div>
    <?php

} elseif ($tax_name == 'product_cat') {
    $exclude_terms = isset($settings['exclude_terms']['product_cat']) && $settings['include_exclude_filter']['product_cat'] == 'exclude-terms' ? $settings['exclude_terms']['product_cat'] : [];
    $include_terms = isset($settings['include_terms']['product_cat']) && $settings['include_exclude_filter']['product_cat'] == 'include-terms' ? $settings['include_terms']['product_cat'] : [];
    $selected_cats = '';
    if (isset($current_filter_option['categories'])) {
        $selected_cats = is_array($current_filter_option['categories']) ? implode(',', $current_filter_option['categories']) : $current_filter_option['categories'];
    }
    echo '<div class="swpf-multiselect-wrap" data-placeholder="' . (isset($settings['placeholder_txt'][$tax_name]) ? esc_attr($settings['placeholder_txt'][$tax_name]) : esc_html__('Search', 'super-product-filter')) . '">';
    ?>
    <div class="swpf-multiselect-wrap">
        <select class="swpf-multiselect" name="categories[]" multiple>
            <?php
            if (isset($settings['config']['indent_cat']) && $settings['config']['indent_cat'] == 'on') {
                $all_terms = $settings['terms_customize'][$tax_name];
                $term_name_array = [];
                $swpf_term_count_array = [];
                $hide_terms = [];

                if ($all_terms) {
                    foreach ($all_terms as $key => $aterm) {
                        $term_name_array[$key] = (isset($aterm['term_name']) && !empty($aterm['term_name'])) ? esc_html(apply_filters('swpf_translate_string', $aterm['term_name'], 'Super WooCommerce Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($key))) : esc_html(ucwords(str_replace('-', ' ', get_term($key)->name)));
                        if (isset($settings['show_count']['product_cat']) && $settings['show_count']['product_cat'] == 'on') {
                            $term = get_term($key);
                            $swpf_args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug);
                            $swpf_term_cquery = new WP_Query($swpf_args);
                            $swpf_post_count = $swpf_term_cquery->post_count;

                            $swpf_args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug, null, true);
                            $swpf_term_cquery = new WP_Query($swpf_args);
                            $swpf_post_count_ckk = $swpf_term_cquery->post_count;
                            wp_reset_postdata();
                            $swpf_post_count = min($swpf_post_count, $swpf_post_count_ckk, $term->count);

                            $swpf_term_count_array[$key] = $swpf_post_count;
                        }
                    }
                }

                swpf_terms_dropdown(0, array(
                    'taxonomy' => 'product_cat',
                    'name' => 'categories',
                    'value_field' => 'slug',
                    'selected_cats' => $selected_cats,
                    'show_count' => $swpf_tax_show_count,
                    'term_name_array' => $term_name_array,
                    'term_count_array' => $swpf_term_count_array,
                    'hide_empty' => false,
                    'hide_terms' => $hide_terms,
                    "multiple" => true
                ), $terms);

            } else if ($terms) {
                foreach ($terms as $key => $term) {
                    $selected = false;
                    if (isset($current_filter_option['categories']) && !empty($current_filter_option['categories']) && is_array($current_filter_option['categories'])) {
                        $selected = in_array($term->slug, $current_filter_option['categories']) ? true : false;
                    }

                    if ($swpf_tax_show_count) {
                        $swpf_args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count = $swpf_term_cquery->post_count;

                        $swpf_args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug, null, true);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count_ckk = $swpf_term_cquery->post_count;
                        wp_reset_postdata();
                        $swpf_post_count = min($swpf_post_count, $swpf_post_count_ckk, $term->count);
                    }

                    ?>
                    <option data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php selected($selected, true); ?>>
                        <?php
                        if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                            echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super WooCommerce Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                        } else {
                            echo esc_html(ucwords(str_replace('-', ' ', $term->name)));
                        }

                        if ($swpf_tax_show_count) {
                            ?>
                            <span class="swpf-count">&nbsp;(<?php echo esc_attr($swpf_post_count); ?>)</span>
                        <?php
                        }
                        ?>
                    </option>
                    <?php
                }
            }
            ?>
        </select>
    </div>
    <?php
    echo '</div>';

} elseif ($tax_name == 'product_tag') {
    ?>
    <div class="swpf-multiselect-wrap">
        <select class="swpf-multiselect" name="tags[]" multiple>
            <?php
            if ($terms) {
                foreach ($terms as $key => $term) {
                    $selected = false;

                    if ($swpf_tax_show_count) {
                        $swpf_args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count = $swpf_term_cquery->post_count;

                        $swpf_args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug, null, true);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count_ckk = $swpf_term_cquery->post_count;
                        wp_reset_postdata();
                        $swpf_post_count = min($swpf_post_count, $swpf_post_count_ckk, $term->count);
                    }

                    if (isset($current_filter_option['tags']) && !empty($current_filter_option['tags']) && is_array($current_filter_option['tags'])) {
                        $selected = in_array($term->slug, $current_filter_option['tags']) ? true : false;
                    }
                    ?>
                    <option data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php selected($selected, true); ?>>
                        <?php
                        if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                            echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                        } else {
                            echo esc_html(ucwords(str_replace('-', ' ', $term->name)));
                        }

                        if ($swpf_tax_show_count) {
                            ?>
                            <span class="swpf-count">&nbsp;(<?php echo esc_attr($swpf_post_count); ?>)</span>
                            <?php
                        }
                        ?>
                    </option>
                    <?php
                }
            }
            ?>
        </select>
    </div>
    <?php

} elseif ($tax_name == 'product_brand') {
    ?>
    <div class="swpf-multiselect-wrap">
        <select class="swpf-multiselect" name="brands[]" multiple>
            <?php
            if ($terms) {
                foreach ($terms as $key => $term) {
                    $selected = false;

                    if ($swpf_tax_show_count) {
                        $swpf_args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count = $swpf_term_cquery->post_count;

                        $swpf_args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug, null, true);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count_ckk = $swpf_term_cquery->post_count;
                        wp_reset_postdata();
                        $swpf_post_count = min($swpf_post_count, $swpf_post_count_ckk, swpf_get_brand_count($term->term_id));
                    }

                    if (isset($current_filter_option['brands']) && !empty($current_filter_option['brands']) && is_array($current_filter_option['brands'])) {
                        $selected = in_array($term->slug, $current_filter_option['brands']) ? true : false;
                    }
                    ?>
                    <option data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php selected($selected, true); ?>>
                        <?php
                        if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                            echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                        } else {
                            echo esc_html(ucwords(str_replace('-', ' ', $term->name)));
                        }

                        if ($swpf_tax_show_count) {
                            ?>
                            <span class="swpf-count">&nbsp;(<?php echo esc_attr($swpf_post_count); ?>)</span>
                            <?php
                        }
                        ?>
                    </option>
                    <?php
                }
            }
            ?>
        </select>
    </div>
    <?php

} elseif (substr($tax_name, 0, 3) === 'pa_') {
    ?>
    <div class="swpf-multiselect-wrap">
        <select class="swpf-multiselect" name="attribute[<?php echo esc_attr($tax_name); ?>][]" multiple>
            <?php
            if ($terms) {
                foreach ($terms as $key => $term) {
                    $selected = false;

                    if ($swpf_tax_show_count) {
                        $swpf_args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count = $swpf_term_cquery->post_count;

                        $swpf_args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug, null, true);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count_ckk = $swpf_term_cquery->post_count;
                        wp_reset_postdata();
                        $swpf_post_count = min($swpf_post_count, $swpf_post_count_ckk, $term->count);
                    }

                    if (isset($current_filter_option['attribute'][$tax_name]) && !empty($current_filter_option['attribute'][$tax_name]) && is_array($current_filter_option['attribute'][$tax_name])) {
                        $selected = in_array($term->slug, $current_filter_option['attribute'][$tax_name]) ? true : false;
                    }
                    ?>
                    <option data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php selected($selected, true); ?>>
                        <?php
                        if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                            echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                        } else {
                            echo esc_html(ucwords(str_replace('-', ' ', $term->name)));
                        }

                        if ($swpf_tax_show_count) {
                            ?>
                            <span class="swpf-count">&nbsp;(<?php echo esc_attr($swpf_post_count); ?>)</span>
                            <?php
                        }
                        ?>
                    </option>
                    <?php
                }
            }
            ?>
        </select>
    </div>
    <?php

} elseif ($tax_name) {
    ?>
    <div class="swpf-multiselect-wrap">
        <select class="swpf-multiselect" name="attribute[<?php echo esc_attr($tax_name); ?>][]" multiple>
            <?php
            if ($terms) {
                foreach ($terms as $key => $term) {
                    $selected = false;

                    if ($swpf_tax_show_count) {
                        $swpf_args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count = $swpf_term_cquery->post_count;

                        $swpf_args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug, null, true);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count_ckk = $swpf_term_cquery->post_count;
                        wp_reset_postdata();
                        $swpf_post_count = min($swpf_post_count, $swpf_post_count_ckk, $term->count);
                    }

                    if (isset($current_filter_option['attribute'][$tax_name]) && !empty($current_filter_option['attribute'][$tax_name]) && is_array($current_filter_option['attribute'][$tax_name])) {
                        $selected = in_array($term->slug, $current_filter_option['attribute'][$tax_name]) ? true : false;
                    }
                    ?>
                    <option data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php selected($selected, true); ?>>
                        <?php
                        if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                            echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                        } else {
                            echo esc_html(ucwords(str_replace('-', ' ', $term->name)));
                        }

                        if ($swpf_tax_show_count) {
                            ?>
                            <span class="swpf-count">&nbsp;(<?php echo esc_attr($swpf_post_count); ?>)</span>
                            <?php
                        }
                        ?>
                    </option>
                    <?php
                }
            }
            ?>
        </select>
    </div>
    <?php
}