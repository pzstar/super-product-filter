<?php
defined('ABSPATH') || die();

$swpf_tax_show_count = (isset($swpf_settings['show_count'][$swpf_tax_name]) && $swpf_settings['show_count'][$swpf_tax_name] == 'on') ? true : false;
?>

<div class="swpf-filter-item-list swpf-checkbox-type">
    <?php
    if ($swpf_tax_name == 'product_cat') {
        ?>
        <ul class="swpf-filter-product-category swpf-filter-product-category-checkbox <?php echo esc_attr($swpf_settings['config']['indent_cat']) == 'on' ? ' swpf-indent-product-cat' : ''; ?>">
            <?php
            if ($swpf_settings['field_orientation']['product_cat'] != 'horizontal' && (isset($swpf_settings['config']['indent_cat']) && $swpf_settings['config']['indent_cat'] == 'on')) {
                $swpf_selected_cats = array();
                if (isset($swpf_current_filter_option['categories'])) {
                    $swpf_selected_cats = is_array($swpf_current_filter_option['categories']) ? $swpf_current_filter_option['categories'] : implode(',', $swpf_current_filter_option['categories']);
                }

                $swpf_all_terms = $swpf_settings['terms_customize'][$swpf_tax_name];
                $swpf_term_name_array = [];
                $swpf_term_count_array = [];

                if ($swpf_all_terms) {
                    foreach ($swpf_all_terms as $swpf_key => $swpf_aterm) {
                        $swpf_term = get_term($swpf_key);
                        $swpf_term_name_array[$swpf_key] = (isset($swpf_aterm['term_name']) && !empty($swpf_aterm['term_name'])) ? esc_html(apply_filters('swpf_translate_string', $swpf_aterm['term_name'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($swpf_tax_name) . ' ' . absint($swpf_key))) : esc_html(ucwords(str_replace('-', ' ', get_term($swpf_key)->name)));
                        if ($swpf_tax_show_count) {
                            $swpf_args = swpf_get_vars_query_args($swpf_current_filter_option, $swpf_settings, $swpf_tax_name, get_term($swpf_key)->slug);
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

                swpf_terms_checklist(0, array(
                    'taxonomy' => 'product_cat',
                    'name' => 'categories',
                    'value_field' => 'slug',
                    'selected_cats' => $swpf_selected_cats,
                    'show_count' => isset($swpf_settings['show_count']['product_cat']) && $swpf_settings['show_count']['product_cat'] == 'on',
                    'term_name_array' => $swpf_term_name_array,
                    'term_count_array' => $swpf_term_count_array,
                    'hide_empty' => false,
                    'hide_terms' => [],
                ), $swpf_terms);
            } else {
                if ($swpf_terms) {
                    foreach ($swpf_terms as $swpf_key => $swpf_term) {
                        $swpf_checked = false;
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

                        if (isset($swpf_current_filter_option['categories']) && !empty($swpf_current_filter_option['categories']) && is_array($swpf_current_filter_option['categories'])) {
                            $swpf_checked = in_array($swpf_term->slug, $swpf_current_filter_option['categories']) ? true : false;
                        }
                        ?>
                        <li class="swpf-filter-item swpf-product-cat-<?php echo esc_attr($swpf_term->slug); ?>">
                            <label class="swpf-filter-label">
                                <input type="checkbox" id="swpf-term-<?php echo esc_attr($swpf_term->term_id) ?>" class="swpf-chkbox-term swpf-chkbox-term-<?php echo esc_attr($swpf_term->term_id); ?>" name="categories[]" data-termurl="<?php echo esc_url(get_term_link($swpf_term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($swpf_term->taxonomy); ?>" data-termid="<?php echo esc_attr($swpf_term->term_id); ?>" value="<?php echo esc_attr($swpf_term->slug); ?>" <?php checked($swpf_checked, true); ?>>
                                <span class="swpf-title">
                                    <span class="swpf-term">
                                        <?php
                                        if (isset($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name']) && !empty($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'])) {
                                            echo esc_html(apply_filters('swpf_translate_string', $swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($swpf_tax_name) . ' ' . absint($swpf_term->term_id)));
                                        } else {
                                            echo esc_html(ucwords(str_replace('-', ' ', $swpf_term->name)));
                                        }
                                        ?>
                                    </span>
                                    <?php
                                    if ($swpf_tax_show_count) {
                                        ?>
                                        <span class="swpf-count">&nbsp;(<?php echo esc_attr($swpf_post_count); ?>)</span>
                                        <?php
                                    }
                                    ?>
                                </span>
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
        if ($swpf_terms) {
            foreach ($swpf_terms as $swpf_key => $swpf_term) {
                $swpf_checked = false;
                if ($swpf_tax_show_count) {
                    $swpf_args = swpf_get_vars_query_args($swpf_current_filter_option, $swpf_settings, $swpf_tax_name, $swpf_term->slug);
                    $swpf_term_cquery = new WP_Query($swpf_args);
                    $swpf_post_count = $swpf_term_cquery->post_count;

                    $swpf_args = swpf_get_vars_query_args($swpf_current_filter_option, $swpf_settings, $swpf_tax_name, $swpf_term->slug, null, true);
                    $swpf_term_cquery = new WP_Query($swpf_args);
                    $swpf_post_count_ckk = $swpf_term_cquery->post_count;
                    wp_reset_postdata();
                    $swpf_term_count = $swpf_tax_name == 'product_brand' ? swpf_get_brand_count($swpf_term->term_id) : $swpf_term->count;
                    $swpf_post_count = min($swpf_post_count, $swpf_post_count_ckk, $swpf_term_count);
                }

                if ($swpf_tax_name == 'product_visibility') {
                    if (isset($swpf_current_filter_option['visibility']) && !empty($swpf_current_filter_option['visibility']) && is_array($swpf_current_filter_option['visibility'])) {
                        $swpf_checked = in_array($swpf_term->slug, $swpf_current_filter_option['visibility']) ? true : false;
                    }
                    ?>
                    <div class="swpf-filter-item">
                        <label class="swpf-filter-label">
                            <input type="checkbox" id="swpf-term-<?php echo esc_attr($swpf_term->term_id) ?>" class="swpf-chkbox-term swpf-chkbox-term-<?php echo esc_attr($swpf_term->term_id); ?>" name="visibility[]" data-termurl="<?php echo esc_url(get_term_link($swpf_term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($swpf_term->taxonomy); ?>" data-termid="<?php echo esc_attr($swpf_term->term_id); ?>" value="<?php echo esc_attr($swpf_term->slug); ?>" <?php checked($swpf_checked, true); ?>>
                            <span class="swpf-title">
                                <span class="swpf-term">
                                    <?php
                                    if (isset($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name']) && !empty($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'])) {
                                        echo esc_html(apply_filters('swpf_translate_string', $swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($swpf_tax_name) . ' ' . absint($swpf_term->term_id)));
                                    } else {
                                        echo esc_html(ucwords(str_replace('-', ' ', $swpf_term->name)));
                                    }
                                    ?>
                                </span>
                                <?php
                                if ($swpf_tax_show_count) {
                                    ?>
                                    <span class="swpf-count">&nbsp;(<?php echo esc_attr($swpf_post_count); ?>)</span>
                                    <?php
                                }
                                ?>
                            </span>
                        </label>
                    </div>
                    <?php
                } elseif ($swpf_tax_name == 'product_tag') {
                    if (isset($swpf_current_filter_option['tags']) && !empty($swpf_current_filter_option['tags']) && is_array($swpf_current_filter_option['tags'])) {
                        $swpf_checked = in_array($swpf_term->slug, $swpf_current_filter_option['tags']) ? true : false;
                    }
                    ?>
                    <div class="swpf-filter-item">
                        <label class="swpf-filter-label">
                            <input type="checkbox" id="swpf-term-<?php echo esc_attr($swpf_term->term_id) ?>" class="swpf-chkbox-term swpf-chkbox-term-<?php echo esc_attr($swpf_term->term_id); ?>" name="tags[]" data-termurl="<?php echo esc_url(get_term_link($swpf_term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($swpf_term->taxonomy); ?>" data-termid="<?php echo esc_attr($swpf_term->term_id); ?>" value="<?php echo esc_attr($swpf_term->slug); ?>" <?php checked($swpf_checked, true); ?>>
                            <span class="swpf-title">
                                <span class="swpf-term">
                                    <?php
                                    if (isset($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name']) && !empty($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'])) {
                                        echo esc_html(apply_filters('swpf_translate_string', $swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($swpf_tax_name) . ' ' . absint($swpf_term->term_id)));
                                    } else {
                                        echo esc_html(ucwords(str_replace('-', ' ', $swpf_term->name)));
                                    }
                                    ?>
                                </span>
                                <?php
                                if ($swpf_tax_show_count) {
                                    ?>
                                    <span class="swpf-count">&nbsp;(<?php echo esc_attr($swpf_post_count); ?>)</span>
                                    <?php
                                }
                                ?>
                            </span>
                        </label>
                    </div>
                    <?php
                } elseif ($swpf_tax_name == 'product_brand') {
                    if (isset($swpf_current_filter_option['brand']) && !empty($swpf_current_filter_option['brand']) && is_array($swpf_current_filter_option['brand'])) {
                        $swpf_checked = in_array($swpf_term->slug, $swpf_current_filter_option['brand']) ? true : false;
                    }
                    ?>
                    <div class="swpf-filter-item">
                        <label class="swpf-filter-label">
                            <input type="checkbox" id="swpf-term-<?php echo esc_attr($swpf_term->term_id) ?>" class="swpf-chkbox-term swpf-chkbox-term-<?php echo esc_attr($swpf_term->term_id); ?>" name="brands[]" data-termurl="<?php echo esc_url(get_term_link($swpf_term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($swpf_term->taxonomy); ?>" data-termid="<?php echo esc_attr($swpf_term->term_id); ?>" value="<?php echo esc_attr($swpf_term->slug); ?>" <?php checked($swpf_checked, true); ?>>
                            <span class="swpf-title">
                                <span class="swpf-term">
                                    <?php
                                    if (isset($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name']) && !empty($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'])) {
                                        echo esc_html(apply_filters('swpf_translate_string', $swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($swpf_tax_name) . ' ' . absint($swpf_term->term_id)));
                                    } else {
                                        echo esc_html(ucwords(str_replace('-', ' ', $swpf_term->name)));
                                    }
                                    ?>
                                </span>
                                <?php
                                if ($swpf_tax_show_count) {
                                    ?>
                                    <span class="swpf-count">&nbsp;(<?php echo esc_attr($swpf_post_count); ?>)</span>
                                    <?php
                                }
                                ?>
                            </span>
                        </label>
                    </div>
                    <?php
                } elseif ((substr($swpf_tax_name, 0, 3) === 'pa_') && isset($swpf_term->term_id)) {
                    if (isset($swpf_current_filter_option['attribute'][$swpf_tax_name]) && !empty($swpf_current_filter_option['attribute'][$swpf_tax_name]) && is_array($swpf_current_filter_option['attribute'][$swpf_tax_name])) {
                        $swpf_checked = in_array($swpf_term->slug, $swpf_current_filter_option['attribute'][$swpf_tax_name]) ? true : false;
                    }
                    ?>
                    <div class="swpf-filter-item">
                        <label class="swpf-filter-label">
                            <input type="checkbox" id="swpf-term-<?php echo esc_attr($swpf_term->term_id) ?>" class="swpf-chkbox-term swpf-chkbox-term-<?php echo esc_attr($swpf_term->term_id); ?>" name="attribute[<?php echo esc_attr($swpf_tax_name); ?>][]" data-termurl="<?php echo esc_url(get_term_link($swpf_term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($swpf_term->taxonomy); ?>" data-termid="<?php echo esc_attr($swpf_term->term_id); ?>" value="<?php echo esc_attr($swpf_term->slug); ?>" <?php checked($swpf_checked, true); ?>>
                            <span class="swpf-title">
                                <span class="swpf-term">
                                    <?php
                                    if (isset($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name']) && !empty($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'])) {
                                        echo esc_html(apply_filters('swpf_translate_string', $swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($swpf_tax_name) . ' ' . absint($swpf_term->term_id)));
                                    } else {
                                        echo esc_html(ucwords(str_replace('-', ' ', $swpf_term->name)));
                                    }
                                    ?>
                                </span>
                                <?php
                                if ($swpf_tax_show_count) {
                                    ?>
                                    <span class="swpf-count">&nbsp;(<?php echo esc_attr($swpf_post_count); ?>)</span>
                                    <?php
                                }
                                ?>
                            </span>
                        </label>
                    </div>
                    <?php
                } elseif (isset($swpf_term->term_id)) {
                    if (isset($swpf_current_filter_option['attribute'][$swpf_tax_name]) && !empty($swpf_current_filter_option['attribute'][$swpf_tax_name]) && is_array($swpf_current_filter_option['attribute'][$swpf_tax_name])) {
                        $swpf_checked = in_array($swpf_term->slug, $swpf_current_filter_option['attribute'][$swpf_tax_name]) ? true : false;
                    }
                    ?>
                    <div class="swpf-filter-item">
                        <label class="swpf-filter-label">
                            <input type="checkbox" id="swpf-term-<?php echo esc_attr($swpf_term->term_id) ?>" class="swpf-chkbox-term swpf-chkbox-term-<?php echo esc_attr($swpf_term->term_id); ?>" name="attribute[<?php echo esc_attr($swpf_tax_name); ?>][]" data-termurl="<?php echo esc_url(get_term_link($swpf_term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($swpf_term->taxonomy); ?>" data-termid="<?php echo esc_attr($swpf_term->term_id); ?>" value="<?php echo esc_attr($swpf_term->slug); ?>" <?php checked($swpf_checked, true); ?>>
                            <span class="swpf-title">
                                <span class="swpf-term">
                                    <?php
                                    if (isset($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name']) && !empty($swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'])) {
                                        echo esc_html(apply_filters('swpf_translate_string', $swpf_settings['terms_customize'][$swpf_tax_name][$swpf_term->term_id]['term_name'], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Term Name ' . esc_html($swpf_tax_name) . ' ' . absint($swpf_term->term_id)));
                                    } else {
                                        echo esc_html(ucwords(str_replace('-', ' ', $swpf_term->name)));
                                    }
                                    ?>
                                </span>
                                <?php
                                if ($swpf_tax_show_count) {
                                    ?>
                                    <span class="swpf-count">&nbsp;(<?php echo esc_attr($swpf_post_count); ?>)</span>
                                    <?php
                                }
                                ?>
                            </span>
                        </label>
                    </div>
                    <?php
                }
            } /* End For Terms Loop */
        }
    }
    ?>
</div>