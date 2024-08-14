<?php
defined('ABSPATH') || die();

function swpf_sanitize_url($url) {
    $sanitized_url = wp_strip_all_tags(stripslashes(filter_var($url, FILTER_VALIDATE_URL)));
    return $sanitized_url;
}

function swpf_sanitize_checkbox($input) {
    if ($input == 'on') {
        return 'on';
    } else {
        return 'off';
    }
}

function swpf_sanitize_number($input) {
    if (is_numeric($input)) {
        return intval($input);
    } else {
        return '';
    }
}

function swpf_sanitize_color($color) {
    // Is this an rgba color or a hex?
    $mode = ( false === strpos($color, 'rgba') ) ? 'hex' : 'rgba';
    if ('rgba' === $mode) {
        $color = str_replace(' ', '', $color);
        sscanf($color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha);
        return 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $alpha . ')';
    } else {
        return sanitize_hex_color($color);
    }
}

function swpf_sanitize_value($sanitize, &$value) {
    if (!empty($sanitize)) {
        if (is_array($value)) {
            $temp_values = $value;
            foreach ($temp_values as $k => $v) {
                $value[$k] = swpf_sanitize_value($sanitize, $value[$k]);
            }
        } else {
            $value = call_user_func($sanitize, htmlspecialchars_decode($value));
        }
    }

    return $value;
}

function swpf_get_taxonomies() {
    $taxonomies = array();
    if (empty($taxonomies)) {
        $taxonomies = get_object_taxonomies('product', 'objects');
        unset($taxonomies['product_shipping_class']);
        unset($taxonomies['product_type']);
    }
    return $taxonomies;
}

function swpf_get_checkbox_allowed_protocols() {
    return array(
        'ul' => array(
            'class' => array()
        ),
        'li' => array(
            'class' => array()
        ),
        'input' => array(
            'id' => array(),
            'type' => array(),
            'name' => array(),
            'checked' => array(),
            'value' => array(),
        ),
        'span' => array(
            'class' => array()
        ),
        'label' => array(
            'class' => array()
        ),
    );
}


/* Check List */
if (!class_exists('SWPF_Walker_Category_Checklist')) {

    class SWPF_Walker_Category_Checklist extends Walker {

        var $tree_type = 'category';
        var $db_fields = array(
            'parent' => 'parent',
            'id' => 'term_id'
        );

        function start_lvl(&$output, $depth = 0, $args = []) {
            $indent = str_repeat("\t", $depth);
            $output .= "$indent<ul class='swpf-filter-children'>\n";
        }

        function end_lvl(&$output, $depth = 0, $args = []) {
            $indent = str_repeat("\t", $depth);
            $output .= "$indent</ul>\n";
        }

        function start_el(&$output, $category, $depth = 0, $args = [], $id = 0) {
            extract($args);
            if (empty($taxonomy)) {
                $taxonomy = 'category';
            }

            if (empty($name)) {
                if ($taxonomy == 'category') {
                    $name = 'post_category';
                } else {
                    $name = $taxonomy;
                }
            }
            if (!in_array($category->term_id, $hide_terms)) {
                $output .= "\n<li class='swpf-filter-item swpf-{$taxonomy}-{$category->{$value_field}}'>";
                $output .= '<label class="swpf-filter-label">';
                $output .= '<input value="' . $category->{$value_field} . '" type="checkbox" name="' . $name . '[]" id="in-' . $taxonomy . '-' . $category->{$value_field} . '"' . checked(in_array($category->{$value_field}, $selected_cats), true, false) . '/>';
                $output .= '<span class="swpf-title">';
                $output .= '<span class="swpf-term">';
                $output .= isset($term_name_array[$category->term_id]) ? esc_html($term_name_array[$category->term_id]) : esc_html($category->name);
                $output .= '</span>';
                if ($show_count) {
                    $output .= '<span class="swpf-count">&nbsp;(';
                    $output .= isset($term_count_array[$category->term_id]) ? esc_html($term_count_array[$category->term_id]) : esc_html($category->count);
                    $output .= ')</span>';
                }
                $output .= '</span>';
                $output .= '</label>';
            }
        }

        function end_el(&$output, $category, $depth = 0, $args = []) {
            extract($args);
            if (!in_array($category->term_id, $hide_terms)) {
                $output .= "</li>\n";
            }
        }

    }

}

if (!function_exists('swpf_terms_checklist')) {

    function swpf_terms_checklist($post_id = 0, $args = array(), $terms = NULL) {
        $defaults = array(
            'selected_cats' => false,
            'walker' => null,
            'taxonomy' => 'category',
            'hide_terms' => [],
            'checked_ontop' => false,
            'name' => ''
        );

        extract(wp_parse_args($args, $defaults), EXTR_SKIP);

        if (empty($walker) || !is_a($walker, 'Walker')) {
            $walker = new SWPF_Walker_Category_Checklist;
        }

        $args = array(
            'taxonomy' => $taxonomy,
            'name' => $name,
            'value_field' => empty($value_field) ? 'term_id' : $value_field,
            'show_count' => $show_count,
            'hide_terms' => isset($hide_terms) ? $hide_terms : [],
            'term_name_array' => isset($term_name_array) ? $term_name_array : [],
            'term_count_array' => isset($term_count_array) ? $term_count_array : []
        );

        $tax = get_taxonomy($taxonomy);
        $args['disabled'] = !current_user_can($tax->cap->assign_terms);

        if (is_array($selected_cats)) {
            $args['selected_cats'] = $selected_cats;
        } elseif ($post_id) {
            $args['selected_cats'] = wp_get_object_terms($post_id, $taxonomy, array_merge($args, array('fields' => 'ids')));
        } else {
            $args['selected_cats'] = array();
        }

        $categories = $terms ? $terms : (array) get_terms($taxonomy);

        if ($checked_ontop) {
            // Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
            $checked_categories = array();
            $keys = array_keys($categories);

            foreach ($keys as $k) {
                if (in_array($categories[$k]->term_id, $args['selected_cats'])) {
                    $checked_categories[] = $categories[$k];
                    unset($categories[$k]);
                }
            }

            // Put checked cats on top
            echo wp_kses(call_user_func_array(array(&$walker, 'walk'), array($checked_categories, 0, $args)), swpf_get_checkbox_allowed_protocols());
        }
        // Then the rest of them
        echo wp_kses(call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args)), swpf_get_checkbox_allowed_protocols());
    }

}

/* Radio List */
if (!class_exists('SWPF_Walker_Category_Radiolist')) {

    class SWPF_Walker_Category_Radiolist extends Walker {

        var $tree_type = 'category';
        var $db_fields = array('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this

        function start_lvl(&$output, $depth = 0, $args = []) {
            $indent = str_repeat("\t", $depth);
            $output .= "$indent<ul class='swpf-filter-children'>\n";
        }

        function end_lvl(&$output, $depth = 0, $args = []) {
            $indent = str_repeat("\t", $depth);
            $output .= "$indent</ul>\n";
        }

        function start_el(&$output, $category, $depth = 0, $args = [], $id = 0) {
            extract($args);
            if (empty($taxonomy)) {
                $taxonomy = 'category';
            }

            if (empty($name)) {
                if ($taxonomy == 'category') {
                    $name = 'post_category';
                } else {
                    $name = $taxonomy;
                }
            }

            if (!in_array($category->term_id, $hide_terms)) {
                $output .= "\n<li class='swpf-filter-item swpf-{$taxonomy}-{$category->{$value_field}}'>";
                $output .= '<label class="swpf-filter-label">';
                $output .= '<input value="' . $category->{$value_field} . '" type="radio" name="' . $name . '" id="in-' . $taxonomy . '-' . $category->{$value_field} . '"' . checked(in_array($category->{$value_field}, $selected_cats), true, false) . ' /> ';
                $output .= '<span class="swpf-title">';
                $output .= '<span class="swpf-term">';
                $output .= isset($term_name_array[$category->term_id]) ? esc_html($term_name_array[$category->term_id]) : esc_html($category->name);
                $output .= '</span>';
                if ($show_count) {
                    $output .= '<span class="swpf-count">&nbsp;(';
                    $output .= isset($term_count_array[$category->term_id]) ? esc_html($term_count_array[$category->term_id]) : esc_html($category->count);
                    $output .= ')</span>';
                }
                $output .= '</span>';
                $output .= '</label>';
            }
        }

        function end_el(&$output, $category, $depth = 0, $args = []) {
            extract($args);
            if (!in_array($category->term_id, $hide_terms)) {
                $output .= "</li>\n";
            }
        }

    }

}

if (!function_exists('swpf_terms_radiolist')) {

    function swpf_terms_radiolist($post_id = 0, $args = array(), $terms = NULL) {
        $defaults = array(
            'selected_cats' => false,
            'walker' => null,
            'taxonomy' => 'category',
            'checked_ontop' => false,
            'name' => '',
            'hide_terms' => [],
            'term_name_array' => isset($term_name_array) ? $term_name_array : [],
            'term_count_array' => isset($term_count_array) ? $term_count_array : []
        );
        extract(wp_parse_args($args, $defaults), EXTR_SKIP);

        if (empty($walker) || !is_a($walker, 'Walker')) {
            $walker = new SWPF_Walker_Category_Radiolist;
        }
        $args = array(
            'taxonomy' => $taxonomy,
            'name' => $name,
            'hide_terms' => isset($hide_terms) ? $hide_terms : [],
            'value_field' => empty($value_field) ? 'term_id' : $value_field,
            'show_count' => $show_count
        );
        $tax = get_taxonomy($taxonomy);
        $args['disabled'] = !current_user_can($tax->cap->assign_terms);

        if (is_array($selected_cats)) {
            $args['selected_cats'] = $selected_cats;
        } elseif ($post_id) {
            $args['selected_cats'] = wp_get_object_terms($post_id, $taxonomy, array_merge($args, array('fields' => 'ids')));
        } else {
            $args['selected_cats'] = array();
        }

        $categories = $terms ? $terms : (array) get_terms($taxonomy);

        if ($checked_ontop) {
            // Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
            $checked_categories = array();
            $keys = array_keys($categories);

            foreach ($keys as $k) {
                if (in_array($categories[$k]->term_id, $args['selected_cats'])) {
                    $checked_categories[] = $categories[$k];
                    unset($categories[$k]);
                }
            }

            // Put checked cats on top
            echo wp_kses(call_user_func_array(array(&$walker, 'walk'), array($checked_categories, 0, $args)), swpf_get_checkbox_allowed_protocols());
        }

        // Then the rest of them
        echo wp_kses(call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args)), swpf_get_checkbox_allowed_protocols());
    }

}

/* Toggle Button */
if (!class_exists('SWPF_Walker_Category_Toggle')) {

    class SWPF_Walker_Category_Toggle extends Walker {

        var $tree_type = 'category';
        var $db_fields = array(
            'parent' => 'parent',
            'id' => 'term_id'
        );

        function start_lvl(&$output, $depth = 0, $args = []) {
            $indent = str_repeat("\t", $depth);
            $output .= "$indent<ul class='swpf-filter-children'>\n";
        }

        function end_lvl(&$output, $depth = 0, $args = []) {
            $indent = str_repeat("\t", $depth);
            $output .= "$indent</ul>\n";
        }

        function start_el(&$output, $category, $depth = 0, $args = [], $id = 0) {
            extract($args);
            if (empty($taxonomy)) {
                $taxonomy = 'category';
            }

            if (empty($name)) {
                if ($taxonomy == 'category') {
                    $name = 'post_category';
                } else {
                    $name = $taxonomy;
                }
            }

            if (!in_array($category->term_id, $hide_terms)) {
                $output .= "\n<li class='swpf-filter-item swpf-{$taxonomy}-{$category->{$value_field}}'>";
                $output .= '<label class="swpf-filter-label">';
                $output .= '<span class="swpf-toggle-wrap">';
                $output .= '<input value="' . $category->{$value_field} . '" type="checkbox" name="' . $name . '[]" id="in-' . $taxonomy . '-' . $category->{$value_field} . '"' . checked(in_array($category->{$value_field}, $selected_cats), true, false) . '/>';
                $output .= '<span class="swpf-toggle-knob"></span>';
                $output .= '</span>';
                $output .= '<span class="swpf-title">';
                $output .= '<span class="swpf-term">';
                $output .= isset($term_name_array[$category->term_id]) ? esc_html($term_name_array[$category->term_id]) : esc_html($category->name);
                $output .= '</span>';
                if ($show_count) {
                    $output .= '<span class="swpf-count">&nbsp;(';
                    $output .= isset($term_count_array[$category->term_id]) ? esc_html($term_count_array[$category->term_id]) : esc_html($category->count);
                    $output .= ')</span>';
                }
                $output .= '</span>';
                $output .= '</label>';
            }
        }

        function end_el(&$output, $category, $depth = 0, $args = []) {
            extract($args);
            if (!in_array($category->term_id, $hide_terms)) {
                $output .= "</li>\n";
            }
        }

    }

}

if (!function_exists('swpf_terms_togglelist')) {

    function swpf_terms_togglelist($post_id = 0, $args = array(), $terms = NULL) {
        $defaults = array(
            'selected_cats' => false,
            'walker' => null,
            'taxonomy' => 'category',
            'checked_ontop' => false,
            'name' => '',
            'hide_terms' => [],
        );

        extract(wp_parse_args($args, $defaults), EXTR_SKIP);

        if (empty($walker) || !is_a($walker, 'Walker')) {
            $walker = new SWPF_Walker_Category_Toggle;
        }

        $args = array(
            'taxonomy' => $taxonomy,
            'name' => $name,
            'value_field' => empty($value_field) ? 'term_id' : $value_field,
            'show_count' => $show_count,
            'term_name_array' => isset($term_name_array) ? $term_name_array : [],
            'term_count_array' => isset($term_count_array) ? $term_count_array : [],
            'hide_terms' => isset($hide_terms) ? $hide_terms : [],
        );

        $tax = get_taxonomy($taxonomy);
        $args['disabled'] = !current_user_can($tax->cap->assign_terms);

        if (is_array($selected_cats)) {
            $args['selected_cats'] = $selected_cats;
        } elseif ($post_id) {
            $args['selected_cats'] = wp_get_object_terms($post_id, $taxonomy, array_merge($args, array('fields' => 'ids')));
        } else {
            $args['selected_cats'] = array();
        }

        $categories = $terms ? $terms : (array) get_terms($taxonomy);

        if ($checked_ontop) {
            // Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
            $checked_categories = array();
            $keys = array_keys($categories);

            foreach ($keys as $k) {
                if (in_array($categories[$k]->term_id, $args['selected_cats'])) {
                    $checked_categories[] = $categories[$k];
                    unset($categories[$k]);
                }
            }

            // Put checked cats on top
            echo wp_kses(call_user_func_array(array(&$walker, 'walk'), array($checked_categories, 0, $args)), swpf_get_checkbox_allowed_protocols());
        }
        // Then the rest of them
        echo wp_kses(call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args)), swpf_get_checkbox_allowed_protocols());
    }

}

/* Color / Image Checkbox */
if (!class_exists('SWPF_Walker_Category_Color_Image_Checkbox')) {

    class SWPF_Walker_Category_Color_Image_Checkbox extends Walker {

        var $tree_type = 'category';
        var $db_fields = array(
            'parent' => 'parent',
            'id' => 'term_id'
        );

        function start_lvl(&$output, $depth = 0, $args = []) {
            $indent = str_repeat("\t", $depth);
            $output .= "$indent<ul class='swpf-filter-children'>\n";
        }

        function end_lvl(&$output, $depth = 0, $args = []) {
            $indent = str_repeat("\t", $depth);
            $output .= "$indent</ul>\n";
        }

        function start_el(&$output, $category, $depth = 0, $args = [], $id = 0) {
            extract($args);
            if (empty($taxonomy)) {
                $taxonomy = 'category';
            }

            if (empty($name)) {
                if ($taxonomy == 'category') {
                    $name = 'post_category';
                } else {
                    $name = $taxonomy;
                }
            }
            if (!in_array($category->term_id, $hide_terms)) {
                $output .= "\n<li class='swpf-filter-item swpf-{$taxonomy}-{$category->{$value_field}}'>";
                $output .= '<label class="swpf-filter-label">';
                $output .= '<input class="swpf-chkbox-term" value="' . $category->{$value_field} . '" type="checkbox" name="' . $name . '[]" id="in-' . $taxonomy . '-' . $category->{$value_field} . '"' . checked(in_array($category->{$value_field}, $selected_cats), true, false) . '/>';
                $output .= '<span class="swpf-' . esc_attr($type) . '-box" ' . ($type == "color" ? 'style="background-color:' . esc_attr($term_preview_array[$category->term_id]['color']) . '"' : "") . '>';
                if ($type == 'image') {
                    $output .= '<span class="swpf-image-url"  style="background-image: url(' . esc_url($term_preview_array[$category->term_id]['image']) . ')"></span>';
                }
                $output .= '</span>';
                $output .= '<span class="swpf-title">';
                $output .= '<span class="swpf-term">';
                $output .= $hide_term_name ? '' : (isset($term_name_array[$category->term_id]) ? esc_html($term_name_array[$category->term_id]) : esc_html($category->name));
                $output .= '</span>';
                if ($show_count) {
                    $output .= '<span class="swpf-count">&nbsp;(';
                    $output .= isset($term_count_array[$category->term_id]) ? esc_html($term_count_array[$category->term_id]) : esc_html($category->count);
                    $output .= ')</span>';
                }
                $output .= '</span>';
                $output .= '</label>';
            }
        }

        function end_el(&$output, $category, $depth = 0, $args = []) {
            extract($args);
            if (!in_array($category->term_id, $hide_terms)) {
                $output .= "</li>\n";
            }
        }

    }

}

if (!function_exists('swpf_terms_color_image_checkboxlist')) {

    function swpf_terms_color_image_checkboxlist($post_id = 0, $args = array(), $terms = NULL) {
        $defaults = array(
            'selected_cats' => false,
            'walker' => null,
            'taxonomy' => 'category',
            'hide_terms' => [],
            'checked_ontop' => false,
            'name' => ''
        );

        extract(wp_parse_args($args, $defaults), EXTR_SKIP);

        if (empty($walker) || !is_a($walker, 'Walker')) {
            $walker = new SWPF_Walker_Category_Color_Image_Checkbox;
        }

        $args = array(
            'taxonomy' => $taxonomy,
            'name' => $name,
            'value_field' => empty($value_field) ? 'term_id' : $value_field,
            'show_count' => $show_count,
            'term_preview_array' => empty($term_preview_array) ? array() : $term_preview_array,
            'hide_term_name' => empty($hide_term_name) ? $hide_term_name : 'off',
            'type' => empty($type) ? 'color' : $type,
            'hide_terms' => isset($hide_terms) ? $hide_terms : [],
            'term_name_array' => isset($term_name_array) ? $term_name_array : [],
            'term_count_array' => isset($term_count_array) ? $term_count_array : []
        );

        $tax = get_taxonomy($taxonomy);
        $args['disabled'] = !current_user_can($tax->cap->assign_terms);

        if (is_array($selected_cats)) {
            $args['selected_cats'] = $selected_cats;
        } elseif ($post_id) {
            $args['selected_cats'] = wp_get_object_terms($post_id, $taxonomy, array_merge($args, array('fields' => 'ids')));
        } else {
            $args['selected_cats'] = array();
        }

        $categories = $terms ? $terms : (array) get_terms($taxonomy);

        if ($checked_ontop) {
            // Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
            $checked_categories = array();
            $keys = array_keys($categories);

            foreach ($keys as $k) {
                if (in_array($categories[$k]->term_id, $args['selected_cats'])) {
                    $checked_categories[] = $categories[$k];
                    unset($categories[$k]);
                }
            }

            // Put checked cats on top
            echo wp_kses(call_user_func_array(array(&$walker, 'walk'), array($checked_categories, 0, $args)), swpf_get_checkbox_allowed_protocols());
        }
        // Then the rest of them
        echo wp_kses(call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args)), swpf_get_checkbox_allowed_protocols());
    }

}

if (!class_exists('SWPF_Walker_TaxonomyDropdown')) {

    class SWPF_Walker_TaxonomyDropdown extends Walker_CategoryDropdown {

        function start_el(&$output, $category, $depth = 0, $args = [], $id = 0) {
            $pad = str_repeat('&nbsp;', $depth * 2);
            $cat_name = isset($args['term_name_array'][$category->term_id]) ? esc_html($args['term_name_array'][$category->term_id]) : esc_html($category->name);
            $hide_terms = isset($args['hide_terms']) ? $args['hide_terms'] : [];

            if (!isset($args['value'])) {
                $args['value'] = ( $category->taxonomy != 'category' ? 'slug' : 'id' );
            }

            $value = ($args['value'] == 'slug' ? $category->slug : $category->term_id);

            if (!in_array($category->term_id, $hide_terms)) {

                $output .= "\t<option class=\"level-$depth\" value=\"" . $value . "\"";
                if ($value === (string) $args['selected']) {
                    $output .= ' selected="selected"';
                }

                $output .= '>';
                $output .= $pad . $cat_name;
                if ($args['show_count']) {
                    $output .= '&nbsp;(';
                    $output .= isset($args['term_count_array'][$category->term_id]) ? esc_html($args['term_count_array'][$category->term_id]) : esc_html($category->count);
                    $output .= ')';
                }
                $output .= "</option>\n";
            }
        }

    }

}

function swpf_get_var($param, $sanitize = 'sanitize_text_field', $default = '') {
    if (isset($_GET[$param])) {
        $value = wp_unslash($_GET[$param]);
    } else {
        $value = $default;
    }

    return swpf_sanitize_value($sanitize, $value);
}

function swpf_get_post($param, $sanitize = 'sanitize_text_field', $default = '') {
    if (isset($_POST[$param])) {
        $value = wp_unslash($_POST[$param]);
    } else {
        $value = $default;
    }

    return swpf_sanitize_value($sanitize, $value);
}

function swpf_get_post_data($param) {
    $post_data = array();
    if (isset($_POST[$param])) {
        parse_str($_POST[$param], $post_data);
    }

    return Super_Product_Filter_Admin::sanitize_array($post_data);
}

function swpf_get_post_data_arr($param) {
    $post_data = [];
    if (isset($_POST[$param])) {
        $post_data = $_POST[$param];
    }
    return $post_data && is_array($post_data) ? Super_Product_Filter_Admin::sanitize_array($post_data) : [];
}

function swpf_get_request_data($param, $sanitize = 'sanitize_text_field', $default = '') {
    $post_data = array();
    if (isset($_REQUEST[$param])) {
        parse_str($_REQUEST[$param], $post_data);
    }

    return Super_Product_Filter_Admin::sanitize_array($post_data);
}

function swpf_css_strip_whitespace($css) {
    $replace = array(
        "#/\*.*?\*/#s" => "", // Strip C style comments.
        "#\s\s+#" => " ", // Strip excess whitespace.
    );
    $search = array_keys($replace);
    $css = preg_replace($search, $replace, $css);

    $replace = array(
        ": " => ":",
        "; " => ";",
        " {" => "{",
        " }" => "}",
        ", " => ",",
        "{ " => "{",
        ";}" => "}", // Strip optional semicolons.
        ",\n" => ",", // Don't wrap multiple selectors.
        "\n}" => "}", // Don't wrap closing braces.
        "} " => "}", // Put each rule on it's own line.
    );
    $search = array_keys($replace);
    $css = str_replace($search, $replace, $css);

    return trim($css);
}

function swpf_get_current_filter_options_vars() {
    $filter_array = array();
    $attributes = wc_get_attribute_taxonomies();
    $url_filter_options_array = array(
        'categories',
        'tags',
        'visibility',
        'min_price',
        'max_price',
        'review-from',
        'review-to',
        'rating-from',
        'on-sale',
        'in-stock',
        'order',
        'order_by',
        'relation'
    );

    foreach ($attributes as $attr) {
        $url_filter_options_array[] = 'pa_' . $attr->attribute_name;
    }

    foreach ($url_filter_options_array as $key) {
        $val = swpf_get_var($key);
        if ($val) {
            if ($key == 'categories' || $key == 'tags' || $key == 'visibility') {
                $filter_array[$key] = is_array($val) ? $val : explode(',', $val);
            } else if ($key == 'min_price') {
                $filter_array['price']['min_price'] = $val;
            } else if ($key == 'max_price') {
                $filter_array['price']['max_price'] = $val;
            } else if ($key == 'rating-from') {
                $filter_array[$key] = is_array($val) ? $val : explode(',', $val);
            } else if ($key == 'review-from' && $val != '0' && !empty($val)) {
                $filter_array['review']['review_from'] = $val;
            } else if ($key == 'review-to' && $val != '0' && !empty($val)) {
                $filter_array['review']['review_to'] = $val;
            } else if ($key == 'on-sale' && $val == '1') {
                $filter_array[$key] = $val;
            } else if ($key === 'in-stock' && $val == '1') {
                $filter_array[$key] = $val;
            } else {
                if (substr($key, 0, 3) === 'pa_') {
                    $filter_array['attribute'][$key] = explode(',', $val);
                }
                if ($key === 'order_type') {
                    $filter_array['order'] = $val;
                }
                if ($key === 'orderby') {
                    $filter_array['orderby'] = $val;
                }
                if ($key === 'relation' && is_string($val)) {
                    $filter_array['relation'] = strtoupper($val);
                }
            }
        }
    }
    return $filter_array;
}

function swpf_get_vars_query_args($current_filter_option, $settings, $tax, $term, $type = null, $exclude_curtax = false) {

    $krelation = 'AND';

    $tax_query = $meta_query = [];
    $relation = isset($settings['config']['logic_operator']) && !empty($settings['config']['logic_operator']) ? $settings['config']['logic_operator'] : 'AND';
    $tax_query['relation'] = $relation;

    $is_var_set = false;

    foreach ($current_filter_option as $key => $option) {
        if ($key == 'categories') {
            if (!(($tax == 'product_cat') && $exclude_curtax)) {
                $ftroption = is_array($option) ? $option : explode(',', $option);
                if ($tax == 'product_cat') {
                    $ftroption = [];
                }
                if (!$is_var_set && ($tax == 'product_cat')) {
                    $ftroption[] = $term;
                    $is_var_set = true;
                }
                $krelation = isset($settings['multiselect_logic_operator']['product_cat']) ? $settings['multiselect_logic_operator']['product_cat'] : 'AND';
                $tax_query[] = array(
                    'operator' => $krelation,
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => $ftroption
                );
            }
        } else if ($key == 'tags') {
            if (!(($tax == 'product_tag') && $exclude_curtax)) {
                $krelation = isset($settings['multiselect_logic_operator']['product_tag']) ? $settings['multiselect_logic_operator']['product_tag'] : 'AND';
                $ftroption = is_array($option) ? $option : explode(',', $option);
                if (!$is_var_set && ($tax == 'product_tag')) {
                    $ftroption[] = $term;
                    $is_var_set = true;
                }
                $tax_query[] = array(
                    'operator' => $krelation,
                    'taxonomy' => 'product_tag',
                    'field' => 'slug',
                    'terms' => $ftroption
                );
            }
        } else if (($key == 'attribute' || 0 === strpos($key, 'pa_'))) {
            foreach ($option as $optkey => $value) {
                if (!(($tax == $optkey) && $exclude_curtax)) {
                    $krelation = isset($settings['multiselect_logic_operator'][$optkey]) ? $settings['multiselect_logic_operator'][$optkey] : 'AND';
                    $atts = (array) $value;
                    if (!$is_var_set && ($tax == $optkey)) {
                        $atts[] = $term;
                        $is_var_set = true;
                    }
                    $tax_query[] = array(
                        'operator' => $krelation,
                        'taxonomy' => $optkey,
                        'field' => 'slug',
                        'terms' => $atts
                    );
                }
            }
        } else if ($key == 'visibility') {
            if (!(($tax == 'product_visibility') && $exclude_curtax)) {
                $krelation = isset($settings['multiselect_logic_operator']['product_visibility']) ? $settings['multiselect_logic_operator']['product_visibility'] : 'AND';
                $ftroption = is_array($option) ? $option : explode(',', $option);
                if (!$is_var_set && ($tax == 'product_visibility')) {
                    $ftroption[] = $term;
                    $is_var_set = true;
                }
                $tax_query[] = array(
                    'operator' => $krelation,
                    'taxonomy' => 'product_visibility',
                    'field' => 'slug',
                    'terms' => $ftroption
                );
            }
        } elseif (($key == 'price')) {
            if (!$is_var_set && ($tax == 'price')) {
                $is_var_set = true;
            }
            $min_max_price = Super_Product_Filter_Public::get_filtered_price();
            $min_price = isset($option['min_price']) ? floatval($option['min_price']) : floor($min_max_price->min_price ?: 0);
            $max_price = isset($option['max_price']) ? floatval($option['max_price']) : ceil($min_max_price->max_price ?: 0);
            $meta_query[] = array(
                'key' => '_price',
                'value' => array($min_price, $max_price),
                'compare' => 'BETWEEN',
                'type' => 'DECIMAL'
            );
        }
    }

    if (!$is_var_set) {
        if ($tax == 'product_cat') {
            $krelation = isset($settings['multiselect_logic_operator']['product_cat']) ? $settings['multiselect_logic_operator']['product_cat'] : 'AND';
            $tax_query[] = array(
                'operator' => $krelation,
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => array($term)
            );
        } else if ($tax == 'product_tag') {
            $krelation = isset($settings['multiselect_logic_operator']['product_tag']) ? $settings['multiselect_logic_operator']['product_tag'] : 'AND';
            $tax_query[] = array(
                'operator' => $krelation,
                'taxonomy' => 'product_tag',
                'field' => 'slug',
                'terms' => array($term)
            );
        } else if (($type == 'attribute' || 0 === strpos($tax, 'pa_'))) {
            $krelation = isset($settings['multiselect_logic_operator'][$tax]) ? $settings['multiselect_logic_operator'][$tax] : 'AND';
            $tax_query[] = array(
                'operator' => $krelation,
                'taxonomy' => $tax,
                'field' => 'slug',
                'terms' => array($term)
            );
        } else if ($tax == 'product_visibility') {
            $krelation = isset($settings['multiselect_logic_operator']['product_visibility']) ? $settings['multiselect_logic_operator']['product_visibility'] : 'AND';
            $tax_query[] = array(
                'operator' => $krelation,
                'taxonomy' => 'product_visibility',
                'field' => 'slug',
                'terms' => array($term)
            );
        }
    }

    $selected_lo_specific_cat_ids = isset($settings['config']['lo_specific_cat']) && !empty($settings['config']['lo_specific_cat']) ? $settings['config']['lo_specific_cat'] : [];
    if (count($selected_lo_specific_cat_ids) != 0) {
        $add_tax_query['relation'] = 'AND';
        $add_tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field' => 'id',
            'terms' => is_array($selected_lo_specific_cat_ids) ? $selected_lo_specific_cat_ids : explode(',', $selected_lo_specific_cat_ids)
        );
        $add_tax_query[] = $tax_query;
        $tax_query = $add_tax_query;
    }
    $args = array(
        'post_type' => 'product',
        'wc_query' => 'product_query',
        'tax_query' => $tax_query,
        'meta_query' => $meta_query,
        'no_found_rows' => true,
        'fields' => 'ids',
        'posts_per_page' => -1
    );
    return $args;
}

function swpf_get_vars_query_args_tax($current_filter_option, $settings, $tax) {

    $tax_query = $meta_query = [];
    $relation = isset($settings['config']['logic_operator']) && !empty($settings['config']['logic_operator']) ? $settings['config']['logic_operator'] : 'AND';
    $tax_query['relation'] = $relation;

    $is_var_set = false;

    foreach ($current_filter_option as $key => $option) {
        if ($key == 'categories') {
            $krelation = isset($settings['multiselect_logic_operator']['product_tag']) ? $settings['multiselect_logic_operator']['product_tag'] : 'AND';
            $ftroption = is_array($option) ? $option : explode(',', $option);
            if (!$is_var_set && ($tax == 'product_cat')) {
                $krelation = 'EXISTS';
                $ftroption = [];
                $is_var_set = true;
            }
            $krelation = isset($settings['multiselect_logic_operator']['product_cat']) ? $settings['multiselect_logic_operator']['product_cat'] : 'AND';
            $tax_query[] = array(
                'operator' => $krelation,
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $ftroption
            );
        } else if ($key == 'tags') {
            $krelation = isset($settings['multiselect_logic_operator']['product_tag']) ? $settings['multiselect_logic_operator']['product_tag'] : 'AND';
            $ftroption = is_array($option) ? $option : explode(',', $option);
            if (!$is_var_set && ($tax == 'product_tag')) {
                $krelation = 'EXISTS';
                $ftroption = [];
                $is_var_set = true;
            }
            $tax_query[] = array(
                'operator' => $krelation,
                'taxonomy' => 'product_tag',
                'field' => 'slug',
                'terms' => $ftroption
            );
        } else if ($key == 'attribute' || 0 === strpos($key, 'pa_')) {
            foreach ($option as $optkey => $value) {
                $krelation = isset($settings['multiselect_logic_operator'][$optkey]) ? $settings['multiselect_logic_operator'][$optkey] : 'AND';
                $atts = (array) $value;
                if (!$is_var_set && ($tax == $optkey)) {
                    $krelation = 'EXISTS';
                    $atts = [];
                    $is_var_set = true;
                }
                $tax_query[] = array(
                    'operator' => $krelation,
                    'taxonomy' => $optkey,
                    'field' => 'slug',
                    'terms' => $atts
                );
            }
        } elseif ($key == 'price') {
            if (!$is_var_set && ($tax == 'price')) {
                $is_var_set = true;
            }
            $min_max_price = Super_Product_Filter_Public::get_filtered_price();
            $min_price = isset($option['min_price']) ? floatval($option['min_price']) : floor($min_max_price->min_price ?: 0);
            $max_price = isset($option['max_price']) ? floatval($option['max_price']) : ceil($min_max_price->max_price ?: 0);
            $meta_query[] = array(
                'key' => '_price',
                'value' => array($min_price, $max_price),
                'compare' => 'BETWEEN',
                'type' => 'DECIMAL'
            );
        }
    }

    if (!$is_var_set) {
        $krelation = 'EXISTS';
        if ($tax == 'product_cat') {
            $tax_query[] = array(
                'operator' => $krelation,
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => array()
            );
        } else if ($tax == 'product_tag') {
            $tax_query[] = array(
                'operator' => $krelation,
                'taxonomy' => 'product_tag',
                'field' => 'slug',
                'terms' => array()
            );
        } else if (0 === strpos($tax, 'pa_')) {
            $tax_query[] = array(
                'operator' => $krelation,
                'taxonomy' => $tax,
                'field' => 'slug',
                'terms' => array()
            );
        }
    }

    $selected_lo_specific_cat_ids = isset($settings['config']['lo_specific_cat']) && !empty($settings['config']['lo_specific_cat']) ? $settings['config']['lo_specific_cat'] : [];
    if (count($selected_lo_specific_cat_ids) != 0) {
        $add_tax_query['relation'] = 'AND';
        $add_tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field' => 'id',
            'terms' => is_array($selected_lo_specific_cat_ids) ? $selected_lo_specific_cat_ids : explode(',', $selected_lo_specific_cat_ids)
        );
        $add_tax_query[] = $tax_query;
        $tax_query = $add_tax_query;
    }
    $args = array(
        'post_type' => 'product',
        'wc_query' => 'product_query',
        'tax_query' => $tax_query,
        'meta_query' => $meta_query,
        'no_found_rows' => true,
        'fields' => 'ids',
        'posts_per_page' => -1
    );
    return $args;
}

function swpf_get_all_filters() {
    global $wpdb;
    $custom_post_type = 'swpf-product-filter';
    $all_filters = array();
    $results = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = %s and post_status = 'publish'", $custom_post_type), ARRAY_A);
    if ($results) {
        foreach ($results as $index => $post) {
            $all_filters[$post['ID']] = $post['post_title'];
        }
    }
    return $all_filters;
}
