<?php
defined('ABSPATH') || die();

$tax_show_count = (isset($settings['show_count'][$tax_name]) && $settings['show_count'][$tax_name] == 'on') ? true : false;
?>

<div class="swpf-filter-select">
    <?php
    if ($tax_name == 'product_visibility') {
        ?>
        <select class="swpf-filter-type-dropdown" name="visibility">
            <?php if (isset($settings['skins']['dropdown']) && $settings['skins']['dropdown'] == 'swpf-dropdown-skin-2') { ?>
                <option data-display="Select"><?php esc_html_e('Select', 'super-product-filter'); ?></option>
            <?php } ?>
            <option value=""><?php esc_html_e('None', 'super-product-filter'); ?></option>
            <?php
            if ($terms) {
                foreach ($terms as $key => $term) {
                    $selected = false;

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

                    if (isset($current_filter_option['visibility']) && !empty($current_filter_option['visibility']) && is_array($current_filter_option['visibility'])) {
                        $selected = in_array($term->slug, $current_filter_option['visibility']) ? true : false;
                    }
                    ?>
                    <option data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php selected($selected, true); ?>>
                    <?php
                    if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                        echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super Product Filter', esc_html($sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                    } else {
                        echo esc_html(ucwords(str_replace('-', ' ', $term->name)));
                    }

                    if ($tax_show_count) {
                        ?>
                        <span class="swpf-count">&nbsp;(<?php echo esc_attr($post_count); ?>)</span>
                            <?php
                        }
                        ?>
                    </option>
                    <?php
                }
            }
            ?>
        </select>
            <?php
        } else if ($tax_name == 'product_cat') {
            $exclude_terms = $settings['include_exclude_filter']['product_cat'] == 'exclude-terms' ? $settings['exclude_terms']['product_cat'] : [];
            $include_terms = $settings['include_exclude_filter']['product_cat'] == 'include-terms' ? $settings['include_terms']['product_cat'] : [];
            $selected_cats = [];

            if (isset($current_filter_option['categories'])) {
                $selected_cats = is_array($current_filter_option['categories']) ? implode(',', $current_filter_option['categories']) : $current_filter_option['categories'];
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

            wp_dropdown_categories(
                    array(
                        'walker' => new SWPF_Walker_TaxonomyDropdown(),
                        'taxonomy' => 'product_cat',
                        'hierarchical' => ($settings['field_orientation']['product_cat'] != 'horizontal' && (isset($settings['config']['indent_cat']) && $settings['config']['indent_cat'] == 'on')),
                        'show_count' => isset($settings['show_count']['product_cat']) && $settings['show_count']['product_cat'] == 'on',
                        'echo' => true,
                        'hide_empty' => false,
                        'name' => 'categories[]',
                        'selected' => $selected_cats,
                        'class' => "swpf-filter-type-dropdown",
                        'show_option_none' => esc_html__('None', 'super-product-filter'),
                        'option_none_value' => '',
                        'value_field' => 'slug',
                        'exclude' => $exclude_terms,
                        'include' => $include_terms,
                        'term_count_array' => $term_count_array,
                        'term_name_array' => $term_name_array,
                        'hide_terms' => [],
                    )
            );
        } else if ($tax_name == 'product_tag') {
            ?>
        <select class="swpf-filter-type-dropdown" name="tags">
            <option value=""><?php esc_html_e('None', 'super-product-filter'); ?></option>
    <?php
    if ($terms) {
        foreach ($terms as $key => $term) {
            $selected = false;

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

            if (isset($current_filter_option['tags']) && !empty($current_filter_option['tags']) && is_array($current_filter_option['tags'])) {
                $selected = in_array($term->slug, $current_filter_option['tags']) ? true : false;
            }
            ?>
                    <option data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php selected($selected, true); ?>>
                    <?php
                    if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                        echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super Product Filter', esc_html($sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                    } else {
                        echo esc_html(ucwords(str_replace('-', ' ', $term->name)));
                    }

                    if ($tax_show_count) {
                        ?>
                        <span class="swpf-count">&nbsp;(<?php echo esc_attr($post_count); ?>)</span>
                            <?php
                        }
                        ?>
                    </option>
                    <?php
                }
            }
            ?>
        </select>
            <?php
        } else if ((substr($tax_name, 0, 3) === 'pa_')) {
            ?>
        <select class="swpf-filter-type-dropdown" name="attribute[<?php echo esc_attr($tax_name); ?>][]">
            <option value=""><?php esc_html_e('None', 'super-product-filter'); ?></option>
        <?php
        if ($terms) {
            foreach ($terms as $key => $term) {
                $selected = false;

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

                if (isset($current_filter_option['attribute'][$tax_name]) && !empty($current_filter_option['attribute'][$tax_name]) && is_array($current_filter_option['attribute'][$tax_name])) {
                    $selected = in_array($term->slug, $current_filter_option['attribute'][$tax_name]) ? true : false;
                }
                ?>
                    <option data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php selected($selected, true); ?>>
                    <?php
                    if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                        echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super Product Filter', esc_html($sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                    } else {
                        echo esc_html(ucwords(str_replace('-', ' ', $term->name)));
                    }

                    if ($tax_show_count) {
                        ?>
                        <span class="swpf-count">&nbsp;(<?php echo esc_attr($post_count); ?>)</span>
                            <?php
                        }
                        ?>
                    </option>
                    <?php
                }
            }
            ?>
        </select>
            <?php
        } else if ($tax_name) {
            ?>
        <select class="swpf-filter-type-dropdown" name="attribute[<?php echo esc_attr($tax_name); ?>][]">
            <option value=""><?php esc_html_e('None', 'super-product-filter'); ?></option>
        <?php
        if ($terms) {
            foreach ($terms as $key => $term) {
                $selected = false;

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

                if (isset($current_filter_option['attribute'][$tax_name]) && !empty($current_filter_option['attribute'][$tax_name]) && is_array($current_filter_option['attribute'][$tax_name])) {
                    $selected = in_array($term->slug, $current_filter_option['attribute'][$tax_name]) ? true : false;
                }
                ?>
                    <option data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php selected($selected, true); ?>>
                    <?php
                    if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                        echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super Product Filter', esc_html($sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                    } else {
                        echo esc_html(ucwords(str_replace('-', ' ', $term->name)));
                    }

                    if ($tax_show_count) {
                        ?>
                        <span class="swpf-count">&nbsp;(<?php echo esc_attr($post_count); ?>)</span>
                            <?php
                        }
                        ?>
                    </option>
                    <?php
                }
            }
            ?>
        </select>
        <?php } ?>
</div>