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
        return floatval($input);
    } else {
        return '';
    }
}

function swpf_sanitize_color($color) {
    // Is this an rgba color or a hex?
    $mode = (false === strpos($color, 'rgba')) ? 'hex' : 'rgba';
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
    /*
     * Exactly what the filter walkers emit. The inline styles carry the colour
     * and image swatches, so leaving style out here renders those filters blank.
     * wp_kses still runs its own CSS filter over the value, which is what stops
     * a url(javascript:...) getting through.
     */
    return array(
        'ul' => array(
            'class' => array(),
            'style' => array(),
        ),
        'li' => array(
            'class' => array(),
            'data-id' => array(),
        ),
        'input' => array(
            'id' => array(),
            'class' => array(),
            'type' => array(),
            'name' => array(),
            'checked' => array(),
            'value' => array(),
        ),
        'label' => array(
            'class' => array(),
        ),
        'span' => array(
            'class' => array(),
            'style' => array(),
        ),
        'div' => array(
            'class' => array(),
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
                $term_value = esc_attr($category->{$value_field});
                $term_taxonomy = esc_attr($taxonomy);
                $output .= "\n<li class='swpf-filter-item swpf-{$term_taxonomy}-{$term_value}'>";
                $output .= '<label class="swpf-filter-label">';
                $output .= '<input value="' . $term_value . '" type="checkbox" name="' . esc_attr($name) . '[]" id="in-' . $term_taxonomy . '-' . $term_value . '"' . checked(in_array($category->{$value_field}, $selected_cats), true, false) . '/>';
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
                $term_value = esc_attr($category->{$value_field});
                $term_taxonomy = esc_attr($taxonomy);
                $output .= "\n<li class='swpf-filter-item swpf-{$term_taxonomy}-{$term_value}'>";
                $output .= '<label class="swpf-filter-label">';
                $output .= '<input value="' . $term_value . '" type="radio" name="' . esc_attr($name) . '" id="in-' . $term_taxonomy . '-' . $term_value . '"' . checked(in_array($category->{$value_field}, $selected_cats), true, false) . ' /> ';
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
                $term_value = esc_attr($category->{$value_field});
                $term_taxonomy = esc_attr($taxonomy);
                $output .= "\n<li class='swpf-filter-item swpf-{$term_taxonomy}-{$term_value}'>";
                $output .= '<label class="swpf-filter-label">';
                $output .= '<span class="swpf-toggle-wrap">';
                $output .= '<input value="' . $term_value . '" type="checkbox" name="' . esc_attr($name) . '[]" id="in-' . $term_taxonomy . '-' . $term_value . '"' . checked(in_array($category->{$value_field}, $selected_cats), true, false) . '/>';
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
                $term_value = esc_attr($category->{$value_field});
                $term_taxonomy = esc_attr($taxonomy);
                $output .= "\n<li class='swpf-filter-item swpf-{$term_taxonomy}-{$term_value}'>";
                $output .= '<label class="swpf-filter-label">';
                $output .= '<input class="swpf-chkbox-term" value="' . $term_value . '" type="checkbox" name="' . esc_attr($name) . '[]" id="in-' . $term_taxonomy . '-' . $term_value . '"' . checked(in_array($category->{$value_field}, $selected_cats), true, false) . '/>';
                $output .= '<span class="swpf-' . esc_attr($type) . '-box" ' . ($type == "color" && isset($term_preview_array[$category->term_id]['color']) ? 'style="background-color:' . esc_attr($term_preview_array[$category->term_id]['color']) . '"' : "") . '>';
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


/* Dropdown */
if (!class_exists('SWPF_Walker_Category_Dropdown')) {

    class SWPF_Walker_Category_Dropdown extends Walker {

        var $tree_type = 'category';
        var $db_fields = array(
            'parent' => 'parent',
            'id' => 'term_id'
        );

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
                $term_value = esc_attr($category->{$value_field});
                $output .= '<option class="level-' . absint($depth) . '" value="' . $term_value . '" id="in-' . esc_attr($taxonomy) . '-' . $term_value . '"' . selected(in_array($category->{$value_field}, $selected_cats), true, false) . '>';
                $indent = str_repeat("&nbsp;", $depth * 5);
                $output .= $indent;
                $output .= $hide_term_name ? '' : (isset($term_name_array[$category->term_id]) ? esc_html($term_name_array[$category->term_id]) : esc_html($category->name));
                if ($show_count) {
                    $output .= '&nbsp;(';
                    $output .= isset($term_count_array[$category->term_id]) ? esc_html($term_count_array[$category->term_id]) : esc_html($category->count);
                    $output .= ')';
                }
            }
        }

        function end_el(&$output, $category, $depth = 0, $args = []) {
            extract($args);
            if (!in_array($category->term_id, $hide_terms)) {
                $output .= "</option>\n";
            }
        }

    }

}

if (!function_exists('swpf_terms_dropdown')) {

    function swpf_terms_dropdown($post_id = 0, $args = array(), $terms = NULL) {
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
            $walker = new SWPF_Walker_Category_Dropdown;
        }

        $args = array(
            'taxonomy' => $taxonomy,
            'name' => $name,
            'value_field' => empty($value_field) ? 'term_id' : $value_field,
            'show_count' => $show_count,
            'term_preview_array' => empty($term_preview_array) ? array() : $term_preview_array,
            'hide_term_name' => isset($hide_term_name) ? $hide_term_name : 'off',
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
            $args['selected_cats'] = explode(',', $selected_cats);
        }

        $categories = $terms ? $terms : (array) get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false));

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
            $temp_val = call_user_func_array(array(&$walker, 'walk'), array($checked_categories, 0, $args));
            echo $temp_val ? wp_kses($temp_val, ['option' => ['value' => true, 'selected' => true, 'class' => true, 'id' => true]]) : '';
        }
        // Then the rest of them
        $temp_val = call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args));
        echo $temp_val ? wp_kses($temp_val, ['option' => ['value' => true, 'selected' => true, 'class' => true, 'id' => true]]) : '';
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

/**
 * The filter preset a request is asking to render.
 *
 * The id arrives from the browser, so it is only trusted once it turns out to
 * name a filter preset. Any other post id resolves to 0 instead, which leaves
 * the caller rendering default settings rather than reading the meta of a post
 * that has nothing to do with this plugin.
 *
 * @param string $param Request key holding the id.
 * @return int Preset id, or 0 when the id names anything else.
 */
function swpf_get_preset_id($param = 'posid') {
    $swpf_posid = absint(swpf_get_post($param));

    if (!$swpf_posid || 'swpf-product-filter' !== get_post_type($swpf_posid)) {
        return 0;
    }

    return $swpf_posid;
}

/**
 * The value to show in a region selector override field.
 *
 * Presets saved before automatic detection carry the old stock selector, which
 * is what detection finds anyway. Showing it would read as a deliberate
 * override, so it is displayed empty and clears itself on the next save.
 *
 * @param string $value Stored value.
 * @param string $stock The selector that used to be the default.
 * @return string
 */
function swpf_selector_override_value($value, $stock) {
    $value = is_string($value) ? trim($value) : '';

    return in_array($value, (array) $stock, true) ? '' : $value;
}

/**
 * Enqueue the icon fonts one preset needs, at the point it renders.
 *
 * Scanning every preset on the site means a shop with many presets loads every
 * family on every page. Doing it as each preset renders keeps a page to the
 * fonts it genuinely shows. Styles requested this late print in the footer.
 *
 * @param int $preset_id Filter preset.
 * @return void
 */
function swpf_enqueue_preset_icons($preset_id) {
    static $done = array();

    $preset_id = absint($preset_id);

    if (!$preset_id || isset($done[$preset_id])) {
        return;
    }

    $done[$preset_id] = true;

    $settings = get_post_meta($preset_id, 'swpf_settings', true);

    if (!is_array($settings)) {
        return;
    }

    foreach (swpf_detect_icon_families($settings) as $handle) {
        wp_enqueue_style($handle);
    }
}

/**
 * Icon font handles referenced by one settings array.
 *
 * @param array $settings Preset settings.
 * @return array Style handles.
 */
function swpf_detect_icon_families($settings) {
    $families = array();

    $elegant = array();
    if (class_exists('SWPF_Icon_Manager')) {
        $manager = SWPF_Icon_Manager::instance();
        if (method_exists($manager, 'elegant_icon_array')) {
            $elegant = (array) $manager->elegant_icon_array();
        }
    }

    array_walk_recursive($settings, function ($value) use (&$families, $elegant) {
        if (!is_string($value) || '' === $value) {
            return;
        }

        if (0 === strpos($value, 'mdi-')) {
            $families['materialdesignicons'] = true;
        } elseif (0 === strpos($value, 'icofont-')) {
            $families['icofont'] = true;
        } elseif (0 === strpos($value, 'essentialicon-')) {
            $families['essentialicon'] = true;
        } elseif (false !== strpos($value, 'fa-')) {
            $families['fontawesome-6.3.0'] = true;
        } elseif ($elegant && in_array($value, $elegant, true)) {
            $families['eleganticons'] = true;
        }
    });

    /**
     * Filter the icon font handles a preset is considered to need.
     *
     * @param array $families Style handles.
     * @param array $settings Preset settings.
     */
    return (array) apply_filters('swpf_preset_icon_families', array_keys($families), $settings);
}

/**
 * Query keys the filter reads from the URL.
 *
 * Shared by the filter parser and the SEO controls so the two cannot drift apart.
 *
 * @return array
 */
function swpf_get_filter_query_keys() {
    $keys = array(
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
        'orderby',
        'relation',
        's',
    );

    if (function_exists('wc_get_attribute_taxonomies')) {
        foreach (wc_get_attribute_taxonomies() as $swpf_attribute) {
            $keys[] = 'pa_' . $swpf_attribute->attribute_name;
        }
    }

    return apply_filters('swpf_filter_query_keys', $keys);
}

function swpf_get_post_data($param, $sanitize = 'sanitize_text_field') {
    $post_data = array();
    if (isset($_POST[$param]) && is_string($_POST[$param])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
        parse_str($_POST[$param], $post_data); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    }

    return Super_Product_Filter_Admin::sanitize_array($post_data, array(), $sanitize);
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
    if (isset($_REQUEST[$param]) && is_string($_REQUEST[$param])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        parse_str($_REQUEST[$param], $post_data); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    }

    return Super_Product_Filter_Admin::sanitize_array($post_data, array(), $sanitize);
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
        'brands',
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
            if ($key == 'categories' || $key == 'tags' || $key == 'visibility' || $key == 'brands') {
                $filter_array[$key] = is_array($val) ? $val : explode(',', $val);
            } elseif ($key == 'min_price') {
                $filter_array['price']['min_price'] = $val;
            } elseif ($key == 'max_price') {
                $filter_array['price']['max_price'] = $val;
            } elseif ($key == 'rating-from') {
                $filter_array[$key] = is_array($val) ? $val : explode(',', $val);
            } elseif ($key == 'review-from' && $val != '0' && !empty($val)) {
                $filter_array['review']['review_from'] = $val;
            } elseif ($key == 'review-to' && $val != '0' && !empty($val)) {
                $filter_array['review']['review_to'] = $val;
            } elseif ($key == 'on-sale' && $val == '1') {
                $filter_array[$key] = $val;
            } elseif ($key === 'in-stock' && $val == '1') {
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

/**
 * The file to render for one of the filter's templates.
 *
 * Looks in the active theme first, so a site can replace any part of the filter
 * markup without editing the plugin, and falls back to the version that ships
 * here. Overrides live in a folder named after the plugin, matching how
 * WooCommerce templates are overridden:
 *
 *     yourtheme/super-product-filter/fields/price.php
 *
 * The path is returned rather than included, because the templates run inside
 * their caller and go on using the variables that caller has already set up.
 *
 * @param string $template Path below the plugin's filter directory, such as
 *                         'fields/price.php' or 'html-types/checkbox.php'.
 * @return string Absolute path to include.
 */
function swpf_locate_template($template) {
    $template = ltrim($template, '/');
    $default = SWPF_PATH . 'public/inc/filter/' . $template;

    /**
     * Filter the folder a theme keeps its overrides in.
     *
     * @param string $folder Folder name, relative to the theme root.
     */
    $folder = trailingslashit(apply_filters('swpf_template_folder', 'super-product-filter'));

    // Child theme first, then parent, which is what locate_template does.
    $found = locate_template(array($folder . $template));

    if (!$found || !file_exists($found)) {
        $found = $default;
    }

    /**
     * Filter the template that will be rendered.
     *
     * @param string $found    Path settled on.
     * @param string $template Template being looked up.
     * @param string $default  Path to the version shipped with the plugin.
     */
    return apply_filters('swpf_locate_template', $found, $template, $default);
}

/**
 * Apply the filters that are not taxonomy terms to a term count query.
 *
 * The count beside a term answers "how many products would I see if I ticked
 * this", so it has to respect everything else the shopper has already chosen.
 * The taxonomy parts are handled by each caller; these are the rest, matched to
 * how the product query applies them so the number and the result agree.
 *
 * @param string $swpf_key        Filter key from the current selection.
 * @param mixed  $swpf_option     Its value.
 * @param array  $swpf_meta_query Meta query being built, by reference.
 * @param mixed  $swpf_post_in    Post ids the count must be limited to, by reference.
 * @return bool Whether this key belonged here.
 */
function swpf_count_query_extra_filter($swpf_key, $swpf_option, &$swpf_meta_query, &$swpf_post_in) {
    if ('rating-from' === $swpf_key) {
        $swpf_rating = is_array($swpf_option) ? reset($swpf_option) : $swpf_option;
        if ('' !== $swpf_rating && null !== $swpf_rating) {
            $swpf_meta_query[] = array(
                'key' => '_wc_average_rating',
                'value' => floatval($swpf_rating),
                'compare' => '>=',
                'type' => 'DECIMAL(3,2)',
            );
        }

        return true;
    }

    if ('review' === $swpf_key) {
        if (isset($swpf_option['review_from'])) {
            $swpf_meta_query[] = array(
                'key' => '_wc_review_count',
                'value' => intval($swpf_option['review_from']),
                'compare' => '>=',
                'type' => 'NUMERIC',
            );
        }

        return true;
    }

    if ('in-stock' === $swpf_key && '1' == $swpf_option) {
        $swpf_meta_query[] = array(
            'key' => '_stock_status',
            'value' => 'instock',
            'compare' => '=',
        );

        return true;
    }

    if ('on-sale' === $swpf_key && '1' == $swpf_option) {
        // The zero keeps the list non empty, so nothing on sale means no results
        // rather than the limit being ignored.
        $swpf_post_in = array_merge(array(0), wc_get_product_ids_on_sale());

        return true;
    }

    return false;
}

function swpf_get_vars_query_args($current_filter_option, $settings, $tax, $term, $type = null, $exclude_curtax = false) {

    $krelation = 'AND';

    $tax_query = $meta_query = [];
    $swpf_count_post_in = null;
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
        } elseif ($key == 'tags') {
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
        } elseif ($key == 'brands') {
            if (!(($tax == 'product_brand') && $exclude_curtax)) {
                $krelation = isset($settings['multiselect_logic_operator']['product_brand']) ? $settings['multiselect_logic_operator']['product_brand'] : 'AND';
                $ftroption = is_array($option) ? $option : explode(',', $option);
                if (!$is_var_set && ($tax == 'product_brand')) {
                    $ftroption[] = $term;
                    $is_var_set = true;
                }
                $tax_query[] = array(
                    'operator' => $krelation,
                    'taxonomy' => 'product_brand',
                    'field' => 'slug',
                    'terms' => $ftroption
                );
            }
        } elseif (($key == 'attribute' || 0 === strpos($key, 'pa_'))) {
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
        } elseif ($key == 'visibility') {
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
            $min_max_price = Super_Product_Filter_General::get_filtered_price();
            $min_price = isset($option['min_price']) ? floatval($option['min_price']) : floor($min_max_price->min_price ?: 0);
            $max_price = isset($option['max_price']) ? floatval($option['max_price']) : ceil($min_max_price->max_price ?: 0);
            $meta_query[] = array(
                'key' => '_price',
                'value' => array($min_price, $max_price),
                'compare' => 'BETWEEN',
                'type' => 'DECIMAL'
            );
        } elseif (swpf_count_query_extra_filter($key, $option, $meta_query, $swpf_count_post_in)) {
            continue;
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
        } elseif ($tax == 'product_tag') {
            $krelation = isset($settings['multiselect_logic_operator']['product_tag']) ? $settings['multiselect_logic_operator']['product_tag'] : 'AND';
            $tax_query[] = array(
                'operator' => $krelation,
                'taxonomy' => 'product_tag',
                'field' => 'slug',
                'terms' => array($term)
            );
        } elseif (($type == 'attribute' || 0 === strpos($tax, 'pa_'))) {
            $krelation = isset($settings['multiselect_logic_operator'][$tax]) ? $settings['multiselect_logic_operator'][$tax] : 'AND';
            $tax_query[] = array(
                'operator' => $krelation,
                'taxonomy' => $tax,
                'field' => 'slug',
                'terms' => array($term)
            );
        } elseif ($tax == 'product_visibility') {
            $krelation = isset($settings['multiselect_logic_operator']['product_visibility']) ? $settings['multiselect_logic_operator']['product_visibility'] : 'AND';
            $tax_query[] = array(
                'operator' => $krelation,
                'taxonomy' => 'product_visibility',
                'field' => 'slug',
                'terms' => array($term)
            );
        }
    }


    $selected_lo_specific_cat_ids = [];

    if (!is_shop() && swpf_get_post('is_shop') != 'yes' && !is_product_taxonomy() && swpf_get_post('is_prod_taxonomy') != 'yes' && swpf_get_var('is_prod_taxonomy') != 'yes' && isset($settings['config']['lo_specific_cat']) && !empty($settings['config']['lo_specific_cat'])) {
        $selected_lo_specific_cat_ids = $settings['config']['lo_specific_cat'];
    }
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

    if (null !== $swpf_count_post_in) {
        $args['post__in'] = $swpf_count_post_in;
    }
    return $args;
}

function swpf_get_vars_query_args_tax($current_filter_option, $settings, $tax) {

    $tax_query = $meta_query = [];
    $swpf_count_post_in = null;
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
        } elseif ($key == 'tags') {
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
        } elseif ($key == 'brands') {
            $krelation = isset($settings['multiselect_logic_operator']['product_brand']) ? $settings['multiselect_logic_operator']['product_brand'] : 'AND';
            $ftroption = is_array($option) ? $option : explode(',', $option);
            if (!$is_var_set && ($tax == 'product_brand')) {
                $krelation = 'EXISTS';
                $ftroption = [];
                $is_var_set = true;
            }
            $tax_query[] = array(
                'operator' => $krelation,
                'taxonomy' => 'product_brand',
                'field' => 'slug',
                'terms' => $ftroption
            );
        } elseif ($key == 'attribute' || 0 === strpos($key, 'pa_')) {
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
            $min_max_price = Super_Product_Filter_General::get_filtered_price();
            $min_price = isset($option['min_price']) ? floatval($option['min_price']) : floor($min_max_price->min_price ?: 0);
            $max_price = isset($option['max_price']) ? floatval($option['max_price']) : ceil($min_max_price->max_price ?: 0);
            $meta_query[] = array(
                'key' => '_price',
                'value' => array($min_price, $max_price),
                'compare' => 'BETWEEN',
                'type' => 'DECIMAL'
            );
        } elseif (swpf_count_query_extra_filter($key, $option, $meta_query, $swpf_count_post_in)) {
            continue;
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
        } elseif ($tax == 'product_tag') {
            $tax_query[] = array(
                'operator' => $krelation,
                'taxonomy' => 'product_tag',
                'field' => 'slug',
                'terms' => array()
            );
        } elseif (0 === strpos($tax, 'pa_')) {
            $tax_query[] = array(
                'operator' => $krelation,
                'taxonomy' => $tax,
                'field' => 'slug',
                'terms' => array()
            );
        }
    }

    $selected_lo_specific_cat_ids = [];

    if (!is_shop() && swpf_get_post('is_shop') != 'yes' && !is_product_taxonomy() && swpf_get_post('is_prod_taxonomy') != 'yes' && swpf_get_var('is_prod_taxonomy') != 'yes' && isset($settings['config']['lo_specific_cat']) && !empty($settings['config']['lo_specific_cat'])) {
        $selected_lo_specific_cat_ids = $settings['config']['lo_specific_cat'];
    }

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

    if (null !== $swpf_count_post_in) {
        $args['post__in'] = $swpf_count_post_in;
    }

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

if (!function_exists('swpf_get_brand_count')) {
    function swpf_get_brand_count($brand_term_id) {
        $products = wc_get_products(array(
            'status' => 'publish',
            'limit' => -1,
            'return' => 'ids',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_brand',
                    'field' => 'term_id',
                    'terms' => $brand_term_id,
                ),
            ),
        ));
        return count($products);
    }
}
