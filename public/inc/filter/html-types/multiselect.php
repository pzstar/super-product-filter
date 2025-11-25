<?php
defined('ABSPATH') || die();

$swpf_tax_show_count = (isset($swpf_settings['show_count'][$swpf_tax_name]) && $swpf_settings['show_count'][$swpf_tax_name] == 'on') ? true : false;

if ($swpf_tax_name == 'product_visibility') {
    ?>
    <div class="swpf-multiselect-wrap">
        <select class="swpf-multiselect" name="visibility[]" multiple>
            <?php
            if ($swpf_terms) {
                foreach ($swpf_terms as $swpf_key => $swpf_term) {
                    $swpf_selected = false;

                    if ($swpf_tax_show_count) {
                        $swpf_args = swpf_get_vars_query_args($swpf_current_filter_option, $swpf_settings, $swpf_tax_name, $swpf_term->slug);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count = $swpf_term_cquery->post_count;

                        $swpf_args = swpf_get_vars_query_args($swpf_current_filter_option, $swpf_settings, $swpf_tax_name, $swpf_term->slug, null, true);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count_ckk = $swpf_term_cquery->post_count;
                        wp_reset_postdata();
                        $swpf_post_count = min($swpf_post_count, $swpf_post_count_ckk, $swpf_term->count);
                    }

                    if (isset($swpf_current_filter_option['visibility']) && !empty($swpf_current_filter_option['visibility']) && is_array($swpf_current_filter_option['visibility'])) {
                        $swpf_selected = in_array($swpf_term->slug, $swpf_current_filter_option['visibility']) ? true : false;
                    }
                    ?>
                    <option data-termurl="<?php echo esc_url(get_term_link($swpf_term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($swpf_term->taxonomy); ?>" data-termid="<?php echo esc_attr($swpf_term->term_id); ?>" value="<?php echo esc_attr($swpf_term->slug); ?>" <?php selected($swpf_selected, true); ?>>
                        <?php
                        if (isset($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name']) && !empty($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'])) {
                            echo esc_html(apply_filters('swpf_translate_string', $swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($swpf_tax_name) . ' ' . absint($swpf_term->term_id)));
                        } else {
                            echo esc_html(ucwords(str_replace('-', ' ', $swpf_term->name)));
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

} elseif ($swpf_tax_name == 'product_cat') {
    $swpf_selected_cats = '';
    if (isset($swpf_current_filter_option['categories'])) {
        $swpf_selected_cats = is_array($swpf_current_filter_option['categories']) ? implode(',', $swpf_current_filter_option['categories']) : $swpf_current_filter_option['categories'];
    }
    echo '<div class="swpf-multiselect-wrap" data-placeholder="' . (isset($swpf_settings['placeholder_txt'][$swpf_tax_name]) ? esc_attr($swpf_settings['placeholder_txt'][$swpf_tax_name]) : esc_html__('Search', 'super-product-filter')) . '">';
    ?>
    <div class="swpf-multiselect-wrap">
        <select class="swpf-multiselect" name="categories[]" multiple>
            <?php
            if (isset($swpf_settings['config']['indent_cat']) && $swpf_settings['config']['indent_cat'] == 'on') {
                $swpf_all_terms = $swpf_settings['terms_customize'][$swpf_tax_name];
                $swpf_term_name_array = [];
                $swpf_term_count_array = [];
                $swpf_hide_terms = [];

                if ($swpf_all_terms) {
                    foreach ($swpf_all_terms as $swpf_key => $swpf_aterm) {
                        $swpf_term_name_array[$swpf_key] = (isset($swpf_aterm['term_name']) && !empty($swpf_aterm['term_name'])) ? esc_html(apply_filters('swpf_translate_string', $swpf_aterm['term_name'], 'Super WooCommerce Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($swpf_tax_name) . ' ' . absint($swpf_key))) : esc_html(ucwords(str_replace('-', ' ', get_term($swpf_key)->name)));
                        if (isset($swpf_settings['show_count']['product_cat']) && $swpf_settings['show_count']['product_cat'] == 'on') {
                            $swpf_term = get_term($swpf_key);
                            $swpf_args = swpf_get_vars_query_args($swpf_current_filter_option, $swpf_settings, $swpf_tax_name, $swpf_term->slug);
                            $swpf_term_cquery = new WP_Query($swpf_args);
                            $swpf_post_count = $swpf_term_cquery->post_count;

                            $swpf_args = swpf_get_vars_query_args($swpf_current_filter_option, $swpf_settings, $swpf_tax_name, $swpf_term->slug, null, true);
                            $swpf_term_cquery = new WP_Query($swpf_args);
                            $swpf_post_count_ckk = $swpf_term_cquery->post_count;
                            wp_reset_postdata();
                            $swpf_post_count = min($swpf_post_count, $swpf_post_count_ckk, $swpf_term->count);

                            $swpf_term_count_array[$swpf_key] = $swpf_post_count;
                        }
                    }
                }

                swpf_terms_dropdown(0, array(
                    'taxonomy' => 'product_cat',
                    'name' => 'categories',
                    'value_field' => 'slug',
                    'selected_cats' => $swpf_selected_cats,
                    'show_count' => $swpf_tax_show_count,
                    'term_name_array' => $swpf_term_name_array,
                    'term_count_array' => $swpf_term_count_array,
                    'hide_empty' => false,
                    'hide_terms' => $swpf_hide_terms,
                    "multiple" => true
                ), $swpf_terms);

            } else if ($swpf_terms) {
                foreach ($swpf_terms as $swpf_key => $swpf_term) {
                    $swpf_selected = false;
                    if (isset($swpf_current_filter_option['categories']) && !empty($swpf_current_filter_option['categories']) && is_array($swpf_current_filter_option['categories'])) {
                        $swpf_selected = in_array($swpf_term->slug, $swpf_current_filter_option['categories']) ? true : false;
                    }

                    if ($swpf_tax_show_count) {
                        $swpf_args = swpf_get_vars_query_args($swpf_current_filter_option, $swpf_settings, $swpf_tax_name, $swpf_term->slug);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count = $swpf_term_cquery->post_count;

                        $swpf_args = swpf_get_vars_query_args($swpf_current_filter_option, $swpf_settings, $swpf_tax_name, $swpf_term->slug, null, true);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count_ckk = $swpf_term_cquery->post_count;
                        wp_reset_postdata();
                        $swpf_post_count = min($swpf_post_count, $swpf_post_count_ckk, $swpf_term->count);
                    }

                    ?>
                    <option data-termurl="<?php echo esc_url(get_term_link($swpf_term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($swpf_term->taxonomy); ?>" data-termid="<?php echo esc_attr($swpf_term->term_id); ?>" value="<?php echo esc_attr($swpf_term->slug); ?>" <?php selected($swpf_selected, true); ?>>
                        <?php
                        if (isset($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name']) && !empty($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'])) {
                            echo esc_html(apply_filters('swpf_translate_string', $swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'], 'Super WooCommerce Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($swpf_tax_name) . ' ' . absint($swpf_term->term_id)));
                        } else {
                            echo esc_html(ucwords(str_replace('-', ' ', $swpf_term->name)));
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

} elseif ($swpf_tax_name == 'product_tag') {
    ?>
    <div class="swpf-multiselect-wrap">
        <select class="swpf-multiselect" name="tags[]" multiple>
            <?php
            if ($swpf_terms) {
                foreach ($swpf_terms as $swpf_key => $swpf_term) {
                    $swpf_selected = false;

                    if ($swpf_tax_show_count) {
                        $swpf_args = swpf_get_vars_query_args($swpf_current_filter_option, $swpf_settings, $swpf_tax_name, $swpf_term->slug);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count = $swpf_term_cquery->post_count;

                        $swpf_args = swpf_get_vars_query_args($swpf_current_filter_option, $swpf_settings, $swpf_tax_name, $swpf_term->slug, null, true);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count_ckk = $swpf_term_cquery->post_count;
                        wp_reset_postdata();
                        $swpf_post_count = min($swpf_post_count, $swpf_post_count_ckk, $swpf_term->count);
                    }

                    if (isset($swpf_current_filter_option['tags']) && !empty($swpf_current_filter_option['tags']) && is_array($swpf_current_filter_option['tags'])) {
                        $swpf_selected = in_array($swpf_term->slug, $swpf_current_filter_option['tags']) ? true : false;
                    }
                    ?>
                    <option data-termurl="<?php echo esc_url(get_term_link($swpf_term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($swpf_term->taxonomy); ?>" data-termid="<?php echo esc_attr($swpf_term->term_id); ?>" value="<?php echo esc_attr($swpf_term->slug); ?>" <?php selected($swpf_selected, true); ?>>
                        <?php
                        if (isset($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name']) && !empty($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'])) {
                            echo esc_html(apply_filters('swpf_translate_string', $swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($swpf_tax_name) . ' ' . absint($swpf_term->term_id)));
                        } else {
                            echo esc_html(ucwords(str_replace('-', ' ', $swpf_term->name)));
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

} elseif ($swpf_tax_name == 'product_brand') {
    ?>
    <div class="swpf-multiselect-wrap">
        <select class="swpf-multiselect" name="brands[]" multiple>
            <?php
            if ($swpf_terms) {
                foreach ($swpf_terms as $swpf_key => $swpf_term) {
                    $swpf_selected = false;

                    if ($swpf_tax_show_count) {
                        $swpf_args = swpf_get_vars_query_args($swpf_current_filter_option, $swpf_settings, $swpf_tax_name, $swpf_term->slug);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count = $swpf_term_cquery->post_count;

                        $swpf_args = swpf_get_vars_query_args($swpf_current_filter_option, $swpf_settings, $swpf_tax_name, $swpf_term->slug, null, true);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count_ckk = $swpf_term_cquery->post_count;
                        wp_reset_postdata();
                        $swpf_post_count = min($swpf_post_count, $swpf_post_count_ckk, swpf_get_brand_count($swpf_term->term_id));
                    }

                    if (isset($swpf_current_filter_option['brands']) && !empty($swpf_current_filter_option['brands']) && is_array($swpf_current_filter_option['brands'])) {
                        $swpf_selected = in_array($swpf_term->slug, $swpf_current_filter_option['brands']) ? true : false;
                    }
                    ?>
                    <option data-termurl="<?php echo esc_url(get_term_link($swpf_term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($swpf_term->taxonomy); ?>" data-termid="<?php echo esc_attr($swpf_term->term_id); ?>" value="<?php echo esc_attr($swpf_term->slug); ?>" <?php selected($swpf_selected, true); ?>>
                        <?php
                        if (isset($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name']) && !empty($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'])) {
                            echo esc_html(apply_filters('swpf_translate_string', $swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($swpf_tax_name) . ' ' . absint($swpf_term->term_id)));
                        } else {
                            echo esc_html(ucwords(str_replace('-', ' ', $swpf_term->name)));
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

} elseif (substr($swpf_tax_name, 0, 3) === 'pa_') {
    ?>
    <div class="swpf-multiselect-wrap">
        <select class="swpf-multiselect" name="attribute[<?php echo esc_attr($swpf_tax_name); ?>][]" multiple>
            <?php
            if ($swpf_terms) {
                foreach ($swpf_terms as $swpf_key => $swpf_term) {
                    $swpf_selected = false;

                    if ($swpf_tax_show_count) {
                        $swpf_args = swpf_get_vars_query_args($swpf_current_filter_option, $swpf_settings, $swpf_tax_name, $swpf_term->slug);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count = $swpf_term_cquery->post_count;

                        $swpf_args = swpf_get_vars_query_args($swpf_current_filter_option, $swpf_settings, $swpf_tax_name, $swpf_term->slug, null, true);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count_ckk = $swpf_term_cquery->post_count;
                        wp_reset_postdata();
                        $swpf_post_count = min($swpf_post_count, $swpf_post_count_ckk, $swpf_term->count);
                    }

                    if (isset($swpf_current_filter_option['attribute'][$swpf_tax_name]) && !empty($swpf_current_filter_option['attribute'][$swpf_tax_name]) && is_array($swpf_current_filter_option['attribute'][$swpf_tax_name])) {
                        $swpf_selected = in_array($swpf_term->slug, $swpf_current_filter_option['attribute'][$swpf_tax_name]) ? true : false;
                    }
                    ?>
                    <option data-termurl="<?php echo esc_url(get_term_link($swpf_term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($swpf_term->taxonomy); ?>" data-termid="<?php echo esc_attr($swpf_term->term_id); ?>" value="<?php echo esc_attr($swpf_term->slug); ?>" <?php selected($swpf_selected, true); ?>>
                        <?php
                        if (isset($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name']) && !empty($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'])) {
                            echo esc_html(apply_filters('swpf_translate_string', $swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($swpf_tax_name) . ' ' . absint($swpf_term->term_id)));
                        } else {
                            echo esc_html(ucwords(str_replace('-', ' ', $swpf_term->name)));
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

} elseif ($swpf_tax_name) {
    ?>
    <div class="swpf-multiselect-wrap">
        <select class="swpf-multiselect" name="attribute[<?php echo esc_attr($swpf_tax_name); ?>][]" multiple>
            <?php
            if ($swpf_terms) {
                foreach ($swpf_terms as $swpf_key => $swpf_term) {
                    $swpf_selected = false;

                    if ($swpf_tax_show_count) {
                        $swpf_args = swpf_get_vars_query_args($swpf_current_filter_option, $swpf_settings, $swpf_tax_name, $swpf_term->slug);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count = $swpf_term_cquery->post_count;

                        $swpf_args = swpf_get_vars_query_args($swpf_current_filter_option, $swpf_settings, $swpf_tax_name, $swpf_term->slug, null, true);
                        $swpf_term_cquery = new WP_Query($swpf_args);
                        $swpf_post_count_ckk = $swpf_term_cquery->post_count;
                        wp_reset_postdata();
                        $swpf_post_count = min($swpf_post_count, $swpf_post_count_ckk, $swpf_term->count);
                    }

                    if (isset($swpf_current_filter_option['attribute'][$swpf_tax_name]) && !empty($swpf_current_filter_option['attribute'][$swpf_tax_name]) && is_array($swpf_current_filter_option['attribute'][$swpf_tax_name])) {
                        $swpf_selected = in_array($swpf_term->slug, $swpf_current_filter_option['attribute'][$swpf_tax_name]) ? true : false;
                    }
                    ?>
                    <option data-termurl="<?php echo esc_url(get_term_link($swpf_term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($swpf_term->taxonomy); ?>" data-termid="<?php echo esc_attr($swpf_term->term_id); ?>" value="<?php echo esc_attr($swpf_term->slug); ?>" <?php selected($swpf_selected, true); ?>>
                        <?php
                        if (isset($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name']) && !empty($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'])) {
                            echo esc_html(apply_filters('swpf_translate_string', $swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($swpf_tax_name) . ' ' . absint($swpf_term->term_id)));
                        } else {
                            echo esc_html(ucwords(str_replace('-', ' ', $swpf_term->name)));
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