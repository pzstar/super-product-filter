<?php

class Super_Product_Filter_General {
    public $swpf_settings = array();
    public $filter_shortcode_id = null;
    public $product_columns = null;
    public $swpf_post_per_page = null;
    public $swpf_shortcode_id = null;

    public function __construct() {
        $this->filter_shortcode_id = swpf_get_var('swpf_filter_sc');
        if ($this->filter_shortcode_id) {
            $this->settings = get_post_meta($this->filter_shortcode_id, 'swpf_settings', true);
        }

    }

    public static function get_current_filter_options_vars() {
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
            'orderby',
            'relation'
        );

        foreach ($attributes as $attr) {
            $url_filter_options_array[] = 'pa_' . $attr->attribute_name;
        }

        foreach ($url_filter_options_array as $swpf_key) {
            $val = swpf_get_var($swpf_key);
            if ($val) {
                if ($swpf_key == 'categories' || $swpf_key == 'tags' || $swpf_key == 'visibility' || $swpf_key == 'brands') {
                    $filter_array[$swpf_key] = is_array($val) ? $val : explode(',', $val);
                } elseif ($swpf_key == 'min_price') {
                    $filter_array['price']['min_price'] = $val;
                } elseif ($swpf_key == 'max_price') {
                    $filter_array['price']['max_price'] = $val;
                } elseif ($swpf_key == 'rating-from') {
                    $filter_array[$swpf_key] = is_array($val) ? $val : explode(',', $val);
                } elseif ($swpf_key == 'review-from' && $val != '0' && !empty($val)) {
                    $filter_array['review']['review_from'] = $val;
                } elseif ($swpf_key == 'review-to' && $val != '0' && !empty($val)) {
                    $filter_array['review']['review_to'] = $val;
                } elseif ($swpf_key == 'on-sale' && $val == '1') {
                    $filter_array[$swpf_key] = $val;
                } elseif ($swpf_key === 'in-stock' && $val == '1') {
                    $filter_array[$swpf_key] = $val;
                } else {
                    if (substr($swpf_key, 0, 3) === 'pa_') {
                        $filter_array['attribute'][$swpf_key] = explode(',', $val);
                    }
                    if ($swpf_key === 'order_type') {
                        $filter_array['order'] = $val;
                    }
                    if ($swpf_key === 'orderby') {
                        $filter_array['orderby'] = $val;
                    }
                    if ($swpf_key === 'relation' && is_string($val)) {
                        $filter_array['relation'] = strtoupper($val);
                    }
                }
            }
        }

        return $filter_array;
    }

    public function get_current_filter_options($current_filter = []) {
        $filter_array = array();

        if (defined('DOING_AJAX') && DOING_AJAX) {
            foreach ($current_filter as $swpf_key => $swpf_option) {
                if ($swpf_key == 'categories' || $swpf_key == 'tags' || $swpf_key == 'visibility' || $swpf_key == 'brands') {
                    if (!empty($swpf_option) && !empty($swpf_option[0])) {
                        $filter_array[$swpf_key] = (array) $swpf_option;
                    }
                } elseif ($swpf_key == 'price') {
                    if ((isset($swpf_option['min_price']) && $swpf_option['min_price']) || (isset($swpf_option['max_price']) && $swpf_option['max_price'])) {
                        $filter_array[$swpf_key] = (array) $swpf_option;
                    }
                } elseif ($swpf_key == 'rating-from' && $swpf_option != '0' && !empty($swpf_option)) {
                    $filter_array[$swpf_key] = (array) $swpf_option;
                } elseif ($swpf_key == 'review-from' && $swpf_option != '0' && !empty($swpf_option)) {
                    $filter_array['review']['review_from'] = $swpf_option;
                } elseif ($swpf_key == 'attribute') {
                    if (!empty($swpf_option)) {
                        foreach ($swpf_option as $optKey => $optVal) {
                            if (isset($optVal) && !empty($optVal[0])) {
                                $filter_array[$swpf_key][$optKey] = $optVal;
                            }
                        }
                    }
                } elseif ($swpf_key == 'in-stock' && $swpf_option == '1') {
                    $filter_array[$swpf_key] = $swpf_option;
                } elseif ($swpf_key == 'on-sale' && $swpf_option == '1') {
                    $filter_array[$swpf_key] = $swpf_option;
                } elseif ($swpf_key === 'orderby') {
                    $filter_array[$swpf_key] = $swpf_option;
                }
            }
        } else {
            if (isset($current_filter) && !empty($current_filter)) {
                foreach ($current_filter as $swpf_key => $val) {
                    if ($swpf_key == 'categories' || $swpf_key == 'tags' || $swpf_key == 'visibility' || $swpf_key == 'brands') {
                        $filter_array[$swpf_key] = is_array($val) ? $val : explode(',', $val);
                    } elseif ($swpf_key == 'min_price') {
                        $filter_array['price']['min_price'] = $val;
                    } elseif ($swpf_key == 'max_price') {
                        $filter_array['price']['max_price'] = $val;
                    } elseif ($swpf_key == 'rating-from') {
                        $filter_array[$swpf_key] = is_array($val) ? $val : explode(',', $val);
                    } elseif ($swpf_key == 'review-from' && $val != '0' && !empty($val)) {
                        $filter_array['review']['review_from'] = $val;
                    } elseif ($swpf_key == 'review-to' && $val != '0' && !empty($val)) {
                        $filter_array['review']['review_to'] = $val;
                    } elseif ($swpf_key == 'on-sale' && $val == '1') {
                        $filter_array[$swpf_key] = $val;
                    } elseif ($swpf_key === 'in-stock' && $val == '1') {
                        $filter_array[$swpf_key] = $val;
                    } else {
                        if (substr($swpf_key, 0, 3) === 'pa_') {
                            $filter_array['attribute'][$swpf_key] = explode(',', $val);
                        }
                        if ($swpf_key === 'order_type') {
                            $filter_array['order'] = $val;
                        }
                        if ($swpf_key === 'orderby') {
                            $filter_array['orderby'] = $val;
                        }
                        if ($swpf_key === 'relation' && is_string($val)) {
                            $filter_array['relation'] = strtoupper($val);
                        }
                    }
                }
            }
        }

        return $filter_array;
    }

    public static function get_filtered_price($tax_query = array(), $meta_query = array()) {
        global $wpdb, $wp_the_query;
        $swpf_args = $wp_the_query->query_vars;

        $meta_query = new WP_Meta_Query($meta_query);
        $tax_query = new WP_Tax_Query($tax_query);

        $meta_query_sql = $meta_query->get_sql('post', $wpdb->posts, 'ID');
        $tax_query_sql = $tax_query->get_sql($wpdb->posts, 'ID');

        $price_meta_keys = apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price' ) );
        $swpf_key_placeholders = implode( ',', array_fill( 0, count( $price_meta_keys ), '%s' ) );

        $prices = $wpdb->get_row($wpdb->prepare("SELECT min(FLOOR(price_meta.meta_value + 0.0)) as min_price, max(CEILING(price_meta.meta_value + 0.0)) as max_price  FROM {$wpdb->posts} LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id {$tax_query_sql['join']} {$meta_query_sql['join']} WHERE {$wpdb->posts}.post_type = 'product' AND {$wpdb->posts}.post_status = 'publish' AND price_meta.meta_key IN ({$swpf_key_placeholders}) AND price_meta.meta_value > '' {$tax_query_sql['where']} {$meta_query_sql['where']}", ...$price_meta_keys));
        return $prices;
    }

}