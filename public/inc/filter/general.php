<?php

class Super_Product_Filter_General {
    public $settings = array();
    public $filter_shortcode_id = null;
    public $product_columns = null;
    public $post_per_page = null;
    public $shortcode_id = null;

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

    public function get_current_filter_options($current_filter = []) {
        $filter_array = array();

        if (defined('DOING_AJAX') && DOING_AJAX) {
            foreach ($current_filter as $key => $option) {
                if ($key == 'categories' || $key == 'tags' || $key == 'visibility' || $key == 'brands') {
                    if (!empty($option) && !empty($option[0])) {
                        $filter_array[$key] = (array) $option;
                    }
                } elseif ($key == 'price') {
                    if ((isset($option['min_price']) && $option['min_price']) || (isset($option['max_price']) && $option['max_price'])) {
                        $filter_array[$key] = (array) $option;
                    }
                } elseif ($key == 'rating-from' && $option != '0' && !empty($option)) {
                    $filter_array[$key] = (array) $option;
                } elseif ($key == 'review-from' && $option != '0' && !empty($option)) {
                    $filter_array['review']['review_from'] = $option;
                } elseif ($key == 'attribute') {
                    if (!empty($option)) {
                        foreach ($option as $optKey => $optVal) {
                            if (isset($optVal) && !empty($optVal[0])) {
                                $filter_array[$key][$optKey] = $optVal;
                            }
                        }
                    }
                } elseif ($key == 'in-stock' && $option == '1') {
                    $filter_array[$key] = $option;
                } elseif ($key == 'on-sale' && $option == '1') {
                    $filter_array[$key] = $option;
                } elseif ($key === 'orderby') {
                    $filter_array[$key] = $option;
                }
            }
        } else {
            if (isset($current_filter) && !empty($current_filter)) {
                foreach ($current_filter as $key => $val) {
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
        }

        return $filter_array;
    }

    public static function get_filtered_price($tax_query = array(), $meta_query = array()) {
        global $wpdb, $wp_the_query;
        $args = $wp_the_query->query_vars;

        $meta_query = new WP_Meta_Query($meta_query);
        $tax_query = new WP_Tax_Query($tax_query);

        $meta_query_sql = $meta_query->get_sql('post', $wpdb->posts, 'ID');
        $tax_query_sql = $tax_query->get_sql($wpdb->posts, 'ID');

        $price_meta_keys = apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price' ) );
        $key_placeholders = implode( ',', array_fill( 0, count( $price_meta_keys ), '%s' ) );

        $sql = "SELECT min(FLOOR(price_meta.meta_value + 0.0)) as min_price, max(CEILING(price_meta.meta_value + 0.0)) as max_price  FROM {$wpdb->posts} LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id {$tax_query_sql['join']} {$meta_query_sql['join']} WHERE {$wpdb->posts}.post_type = 'product' AND {$wpdb->posts}.post_status = 'publish' AND price_meta.meta_key IN ({$key_placeholders}) AND price_meta.meta_value > '' {$tax_query_sql['where']} {$meta_query_sql['where']}";

        $prices = $wpdb->get_row($wpdb->prepare($sql, ...$price_meta_keys));
        return $prices;
    }

}