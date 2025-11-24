<?php

class Super_Product_Filter_Init extends Super_Product_Filter_General {

    public function __construct() {
        $this->includes();

        add_action('woocommerce_before_shop_loop', array($this, 'render_result'));

        add_filter('wp_dropdown_cats', array($this, 'wp_dropdown_cats_multiple'), 10, 2);

        add_filter('swpf_translate_string', array($this, 'translate_string'), 10, 3);

        add_action('wp_head', array($this, 'remove_actions'));

        add_filter('get_terms', array($this, 'add_numerical_order'), 10, 4);

        if ($this->filter_shortcode_id && !empty($this->settings)) {
            if (isset($this->settings['config']['product_columns']) && !empty($this->settings['config']['product_columns'])) {
                $product_columns = absint($this->settings['config']['product_columns']);
            }

            if (isset($product_columns) && $product_columns > 0) {
                if (isset($this->settings['config']['product_rows']) && !empty($this->settings['config']['product_rows'])) {
                    $product_rows = absint($this->settings['config']['product_rows']);
                }

                $this->product_columns = $product_columns;
                add_filter('loop_shop_columns', array($this, 'loop_columns'), 999);

                if (isset($product_rows) && $product_rows > 0) {
                    $post_per_page = $product_columns * $product_rows;
                    $this->post_per_page = $post_per_page;
                    add_filter('loop_shop_per_page', array($this, 'product_per_page'), 30);
                }
            }
        }
    }

    public function render_result() {
        $total_posts_found = $filtered_data = '';
        $settings = array();
        $swpf_sc_id = swpf_get_var('swpf_filter_sc');
        if (isset($this->settings) && !empty($this->settings)) {
            $settings = $this->settings;
        } elseif ($swpf_sc_id) {
            $this->filter_shortcode_id = $swpf_sc_id;
            $settings = get_post_meta($swpf_sc_id, 'swpf_settings', true);
            if (!$settings) {
                $settings = Super_Product_Filter_Metabox::default_settings_values();
            } else {
                $settings = Super_Product_Filter_Admin::recursive_parse_args($settings, Super_Product_Filter_Metabox::default_settings_values());
            }
            $this->settings = $settings;
        }

        $filter_id = 0;
        $filter_class = array('swpf-header-filters');
        $filter_class[] = isset($settings['filterbox']['enablebottomborder']) && $settings['filterbox']['enablebottomborder'] == 'on' ? 'swpf-enablebottomborder' : '';
        if (isset($settings['shortcode'])) {
            $shortcode = $settings['shortcode'];
            $id_pos_start = strpos($shortcode, 'id=', 0) + 4;
            $id_pos_end = strpos($shortcode, '"', $id_pos_start);
            $filter_id = intval(substr($shortcode, $id_pos_start, $id_pos_end));
            $filter_class[] = 'swpf-header-filters-' . esc_attr($filter_id);
        }
        ?>
        <div class="<?php echo esc_attr(implode(' ', $filter_class)); ?>">
            <div class="swpf-shown-items"><?php echo esc_html($total_posts_found); ?></div>
            <div class="swpf-shown-filters"><?php echo wp_kses_post($filtered_data); ?></div>
        </div>
        <?php
    }

    public function add_numerical_order($terms, $taxonomies, $swpf_args, $term_query) {
        if (isset($swpf_args['orderby']) && $swpf_args['orderby'] == 'number') {
            $order = isset($swpf_args['order']) ? $swpf_args['order'] : 'ASC';

            if ($order == 'ASC') {
                array_multisort(array_column($terms, 'name'), SORT_ASC, SORT_NATURAL, $terms);
            } else {
                array_multisort(array_column($terms, 'name'), SORT_DESC, SORT_NATURAL, $terms);
            }
        }

        return $terms;
    }

    public function remove_actions() {
        remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
    }

    public function includes() {
        include SWPF_PATH . 'public/inc/woo-helpers.php';
        include SWPF_PATH . 'public/inc/swpf-webfont-loader.php';
        include SWPF_PATH . 'public/inc/filter/render/init.php';
    }

    public function replacing_template_loop_product_thumbnail() {
        // Remove product images from the shop loop
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
        // Adding variation attr image instead
        add_action('woocommerce_before_shop_loop_item_title', array($this, 'wc_template_loop_product_replaced_thumb'), 10);
    }

    public function wc_template_loop_product_replaced_thumb() {
        global $product;

        $attributes_array = array();
        $attributes = wc_get_attribute_taxonomies();
        foreach ($attributes as $attr) {
            $attributes_array[] = 'pa_' . $attr->attribute_name;
        }

        if ($product->is_type("variable")) {
            $need_array = array();
            $request = swpf_get_request_data('swpf_form_data');
            foreach ($attributes_array as $attr_val) {
                if (isset($request['attribute'][$attr_val]) && !empty($request['attribute'][$attr_val])) {
                    $need_array[$attr_val] = is_array($request['attribute'][$attr_val]) ? $request['attribute'][$attr_val] : explode(',', $request['attribute'][$attr_val]);
                }
            }
            $rate = array();
            if (count($need_array)) {
                $variations = $product->get_available_variations();
                foreach ($variations as $key => $variant) {
                    if (isset($variant['attributes'])) {
                        $rate[$key] = 0;
                        foreach ($need_array as $attr_name => $values) {
                            if (isset($variant['attributes']["attribute_" . $attr_name]) && in_array($variant['attributes']["attribute_" . $attr_name], $values)) {
                                $rate[$key]++;
                            }
                        }
                    }
                }
                arsort($rate);
                $attr_key = array_key_first($rate);
                if (array_shift($rate)) {
                    if (isset($variations[$attr_key]["image_id"]) && $variations[$attr_key]["image_id"]) {
                        $image_size = apply_filters('single_product_archive_thumbnail_size', 'woocommerce_thumbnail');
                        $image = wp_get_attachment_image($variations[$attr_key]["image_id"], $image_size, false, array());
                        if ($image) {
                            echo wp_kses_post(wp_unslash($image));
                            return;
                        }
                    }
                }
            }
        }
        echo wp_kses_post(woocommerce_get_product_thumbnail());
    }

    public function wp_dropdown_cats_multiple($output, $r) {
        if (isset($r['multiple']) && $r['multiple']) {
            $output = preg_replace('/^<select/i', '<select multiple', $output);
            $output = str_replace("name='{$r['name']}'", "name='{$r['name']}[]'", $output);
            foreach (array_map('trim', explode(",", $r['selected'])) as $value) {
                $output = str_replace("value=\"{$value}\"", "value=\"{$value}\" selected", $output);
            }
        }
        return $output;
    }

    public function translate_string($original_value, $domain, $name = '') {
        $wpml_translation = apply_filters('wpml_translate_single_string', $original_value, $domain, $name);
        if ($wpml_translation === $original_value && function_exists('pll__')) {
            return pll__($original_value);
        }
        return $wpml_translation;
    }

    public function product_per_page($products) {
        $products = isset($this->product_per_page) ? absint($this->product_per_page) : absint($products);
        return $products;
    }

    public function loop_columns() {
        $default = get_option('woocommerce_catalog_columns', 4);
        $cols = isset($this->product_columns) ? absint($this->product_columns) : absint($default);
        return $cols;
    }
}

new Super_Product_Filter_Init();