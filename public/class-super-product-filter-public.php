<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 */
class Super_Product_Filter_Public {

    private $plugin_name;
    private $version;
    public $settings = array();
    public $filter_shortcode_id = null;
    public $product_columns = null;
    public $post_per_page = null;
    public $shortcode_id = null;

    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->filter_shortcode_id = swpf_get_var('swpf_filter_sc');
        if ($this->filter_shortcode_id) {
            $this->settings = get_post_meta($this->filter_shortcode_id, 'swpf_settings', true);
        }

        $this->includes();

        add_shortcode('swpf_shortcode', array($this, 'add_shortcode'));

        add_action('wp_ajax_swpf_get_product_list', array($this, 'get_product_list'));
        add_action('wp_ajax_nopriv_swpf_get_product_list', array($this, 'get_product_list'));

        add_action('woocommerce_before_shop_loop', array($this, 'render_result'));

        add_action('woocommerce_product_query', array($this, 'filter_posts'), 11);

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

    public function includes() {
        include SWPF_PATH . 'public/inc/woo-helpers.php';
        include SWPF_PATH . 'public/inc/swpf-webfont-loader.php';
        include SWPF_PATH . 'public/inc/style.php';
    }

    public function add_numerical_order($terms, $taxonomies, $args, $term_query) {
        if (isset($args['orderby']) && $args['orderby'] == 'number') {
            $order = isset($args['order']) ? $args['order'] : 'ASC';
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

    public function enqueue_styles() {
        wp_enqueue_style('swpf-loaders', SWPF_URL . 'public/css/loaders.css', array(), $this->version);
        wp_enqueue_style('swpf-hover', SWPF_URL . 'public/css/hover-min.css', array(), $this->version);

        wp_enqueue_style('fontawesome-6.3.0', SWPF_URL . 'public/css/fontawesome-6.3.0.css', array(), $this->version);
        wp_enqueue_style('eleganticons', SWPF_URL . 'public/css/eleganticons.css', array(), $this->version);
        wp_enqueue_style('essentialicon', SWPF_URL . 'public/css/essentialicon.css', array(), $this->version);
        wp_enqueue_style('icofont', SWPF_URL . 'public/css/icofont.css', array(), $this->version);
        wp_enqueue_style('materialdesignicons', SWPF_URL . 'public/css/materialdesignicons.css', array(), $this->version);

        wp_enqueue_style('jquery-ui-slider', SWPF_URL . 'public/vendor/slider-ui/slider-ui.css', array(), $this->version, 'all');
        wp_enqueue_style('chosen', SWPF_URL . 'public/vendor/chosen/chosen.css', '', $this->version);

        wp_enqueue_style('swpf-animate', SWPF_URL . 'public/css/animate.css', array(), $this->version);

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/style.css', array(), $this->version);
        wp_add_inline_style($this->plugin_name, strip_tags(swpf_dynamic_styles()));

        wp_enqueue_style('swpf-fonts', swpf_fonts_url(), array(), NULL);
    }

    public function enqueue_scripts() {
        global $wp_query;

        /* enable this only when woo range slider is enabled */
        wp_enqueue_script('wc-jquery-ui-touchpunch', array('jquery-ui-core', 'jquery-ui-slider'));
        wp_enqueue_script('wc-price-slider', array('jquery-ui-slider', 'wc-jquery-ui-touchpunch'));
        /* enable this only when woo range slider is enabled */

        /* Enqueue jQuery Chosen */
        wp_enqueue_script('chosen-script', SWPF_URL . 'public/vendor/chosen/chosen.jquery.js', array('jquery'), $this->version);

        $js_obj = array(
            'plugin_url' => WP_PLUGIN_URL,
        );
        wp_localize_script('jquery-mCustomScrollbar', 'swpf_js_obj', $js_obj);

        $front_var = array(
            'ajax_nonce' => wp_create_nonce('swpf-frontend-ajax-nonce'),
            'ajax_url' => esc_url(admin_url('admin-ajax.php')),
            'wcLinks' => get_option('woocommerce_permalinks'),
            'shopUrl' => wc_get_page_permalink('shop'),
            'queryVars' => $GLOBALS['wp_query']->query_vars
        );

        if ($wp_query->is_tax()) {
            $front_var['isTax'] = 1;
            $front_var['queriedTerm'] = [
                'queried_tax' => $wp_query->queried_object->taxonomy,
                'queried_term_slug' => $wp_query->queried_object->slug
            ];
        }

        wp_enqueue_script($this->plugin_name, SWPF_URL . 'public/js/custom-script.js', array('jquery', 'jquery-ui-slider'), $this->version, true);

        /* Send php values to JS script */
        wp_localize_script($this->plugin_name, 'swpf_front_js_obj', $front_var);
    }

    public function get_product_list() {
        if (wp_verify_nonce(swpf_get_post('ajax_nonce'), 'swpf-frontend-ajax-nonce')) {
            include SWPF_PATH . 'public/inc/ajax-request.php';
        }
    }

    public function pagination_args($arg) {
        $post_data = swpf_get_post_data('swpf_form_data');
        if (!empty($post_data)) {
            $pagination_link = ($post_data['pagination_link']);
            $arg['base'] = $pagination_link;
        }
        return $arg;
    }

    public function filter_posts($wp_query, $post_data = false) {
        if ((isset($GLOBALS['current_screen']) && $GLOBALS['current_screen']->in_admin()) || is_customize_preview()) {
            return $wp_query;
        }

        $current_filter_option = $this->get_current_filter_options($post_data);

        if (empty($post_data)) {
            $current_filter_option = $this->get_current_filter_options_vars();
        }

        /* Get Current Paging Index */
        if (isset($wp_query->query_vars['paged']) && $wp_query->query_vars['paged'] != 1) {
            $GLOBALS['swpf_data']['current_page_index'] = intval($wp_query->query_vars['paged']);
        } else {
            $GLOBALS['swpf_data']['current_page_index'] = 1;
        }

        if (isset($GLOBALS['swpf_data'], $GLOBALS['swpf_data']['need_reset_paging']) && $GLOBALS['swpf_data']['need_reset_paging'] == 1) {
            $wp_query->query_vars['paged'] = 1;
            header("Location: " . get_permalink(wc_get_page_id('shop')));
            die();
        }

        if (isset($post_data['pagination_link'])) {
            add_filter('woocommerce_pagination_args', array($this, 'pagination_args'), 10, 1);
        }

        $filter_shortcode_id = swpf_get_var('swpf_filter_sc');

        if (!empty($post_data['swpf_filter_sc'])) {
            $this->filter_shortcode_id = absint($post_data['swpf_filter_sc']);
            $this->settings = get_post_meta($this->filter_shortcode_id, 'swpf_settings', true);
        } else if ($filter_shortcode_id) {
            $this->filter_shortcode_id = absint($filter_shortcode_id);
            $this->settings = get_post_meta($this->filter_shortcode_id, 'swpf_settings', true);
        }

        if (isset($this->settings) && !empty($this->settings)) {
            $tax_query = $meta_query = [];
            $relation = isset($this->settings['config']['logic_operator']) && !empty($this->settings['config']['logic_operator']) ? $this->settings['config']['logic_operator'] : 'AND';
            $tax_query['relation'] = $relation;

            foreach ($current_filter_option as $key => $option) {
                if ($key == 'categories') {
                    $krelation = isset($this->settings['multiselect_logic_operator']['product_cat']) ? $this->settings['multiselect_logic_operator']['product_cat'] : 'AND';
                    $tax_query[] = array(
                        'operator' => $krelation,
                        'taxonomy' => 'product_cat',
                        'field' => 'slug',
                        'terms' => is_array($option) ? $option : explode(',', $option)
                    );
                } else if ($key == 'tags') {
                    $krelation = isset($this->settings['multiselect_logic_operator']['product_tag']) ? $this->settings['multiselect_logic_operator']['product_tag'] : 'AND';
                    $tax_query[] = array(
                        'operator' => $krelation,
                        'taxonomy' => 'product_tag',
                        'field' => 'slug',
                        'terms' => is_array($option) ? $option : explode(',', $option)
                    );
                } else if ($key == 'attribute' || 0 === strpos($key, 'pa_')) {
                    foreach ($option as $key => $value) {
                        $krelation = isset($this->settings['multiselect_logic_operator'][$key]) ? $this->settings['multiselect_logic_operator'][$key] : 'AND';
                        $atts = (array) $value;
                        $tax_query[] = array(
                            'operator' => $krelation,
                            'taxonomy' => $key,
                            'field' => 'slug',
                            'terms' => $atts
                        );
                    }
                } else if ($key == 'visibility') {
                    $krelation = isset($this->settings['multiselect_logic_operator']['product_visibility']) ? $this->settings['multiselect_logic_operator']['product_visibility'] : 'AND';
                    $tax_query[] = array(
                        'operator' => $krelation,
                        'taxonomy' => 'product_visibility',
                        'field' => 'slug',
                        'terms' => is_array($option) ? $option : explode(',', $option)
                    );
                } elseif ($key == 'rating-from') {
                    $meta_query[] = array(
                        'key' => '_wc_average_rating',
                        'value' => intval($option),
                        'compare' => '>='
                    );
                } elseif ($key == 'price') {
                    $min_max_price = $this->get_filtered_price();
                    $min_price = isset($option['min_price']) && $option['min_price'] ? floatval($option['min_price']) : floor($min_max_price->min_price ?: 0);
                    $max_price = isset($option['max_price']) && $option['max_price'] ? floatval($option['max_price']) : ceil($min_max_price->max_price ?: 0);
                    $meta_query[] = array(
                        'key' => '_price',
                        'value' => array($min_price, $max_price),
                        'compare' => 'BETWEEN',
                        'type' => 'DECIMAL'
                    );
                } else if ($key == 'review') {
                    if (isset($option['review_from'])) {
                        $meta_query[] = array(
                            'key' => '_wc_review_count',
                            'value' => intval($option['review_from']),
                            'compare' => '>='
                        );
                    }
                } else if ($key == 'in-stock' && $option == '1') {
                    $meta_query[] = array(
                        'key' => '_stock_status',
                        'value' => 'instock',
                        'compare' => '=',
                    );
                } else if ($key == 'on-sale' && $option == '1') {
                    $wp_query->set('post__in', array_merge([0], wc_get_product_ids_on_sale()));
                }
            }

            $oby = isset($current_filter_option['orderby']) ? $current_filter_option['orderby'] : '';
            $order_array = explode('-', $oby);
            $order_by = $order_array[0];
            $order = isset($order_array[1]) ? $order_array[1] : '';
            $order_by = $order_by ? $order_by : (isset($this->settings['config']['orderby']) ? $this->settings['config']['orderby'] : 'menu_order');

            if ($order_by) {
                switch ($order_by) {
                    case 'id':
                        $order_by_query = 'ID';
                        break;
                    case 'menu_order':
                        $order_by_query = 'menu_order';
                        break;
                    case 'title':
                        $order_by_query = 'title';
                        $order = ('desc' === strtolower($order)) ? 'desc' : 'asc';
                        break;
                    case 'rand':
                        $order_by_query = 'rand';
                        break;
                    case 'date':
                        $order_by_query = 'date';
                        $order = ('asc' === strtolower($order)) ? 'asc' : 'desc';
                        break;
                    case 'price':
                        $order_by_query = 'meta_value_num';
                        $meta_key = '_price';
                        $order = ('desc' === strtolower($order)) ? 'desc' : 'asc';
                        break;
                    default:
                        $order_by_query = $order_by;
                        break;
                }
            }

            $wp_query->set('orderby', $order_by_query);

            if (isset($meta_key)) {
                $wp_query->set('meta_key', $meta_key);
            }

            if ($order) {
                $order = strtolower($order) == 'desc' ? 'DESC' : 'ASC';
                $wp_query->set('order', $order);
            }

            $selected_lo_specific_cat_ids = isset($this->settings['config']['lo_specific_cat']) && !empty($this->settings['config']['lo_specific_cat']) ? $this->settings['config']['lo_specific_cat'] : [];
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

            if (wp_doing_ajax() && swpf_get_post('is_prod_taxonomy') == 'yes') {
                $tax_query[] = array(
                    'taxonomy' => swpf_get_post('page_tax_name'),
                    'field' => 'id',
                    'terms' => swpf_get_post('page_cat_id'),
                    'operator' => 'IN'
                );
            }

            if (!wp_doing_ajax() && is_product_taxonomy()) {
                $term = get_queried_object();
                $term_name = $term->name;
                $tax_query[] = array(
                    'taxonomy' => get_queried_object()->taxonomy,
                    'field' => 'id',
                    'terms' => get_queried_object()->term_id,
                    'operator' => 'IN'
                );
            }

            $wp_query->set('post_type', 'product');
            $wp_query->set('wc_query', 'product_query');
            $wp_query->set('tax_query', $tax_query);
            $wp_query->set('meta_query', $meta_query);
        }
        return $wp_query;
    }

    public function get_current_filter_options($current_filter = []) {
        $filter_array = array();

        if (defined('DOING_AJAX') && DOING_AJAX) {
            foreach ($current_filter as $key => $option) {
                if ($key == 'categories' || $key == 'tags' || $key == 'visibility') {
                    if (!empty($option) && !empty($option[0])) {
                        $filter_array[$key] = (array) $option;
                    }
                } else if ($key == 'price') {
                    if ((isset($option['min_price']) && $option['min_price']) || (isset($option['max_price']) && $option['max_price'])) {
                        $filter_array[$key] = (array) $option;
                    }
                } else if ($key == 'rating-from' && $option != '0' && !empty($option)) {
                    $filter_array[$key] = (array) $option;
                } else if ($key == 'review-from' && $option != '0' && !empty($option)) {
                    $filter_array['review']['review_from'] = $option;
                } else if ($key == 'attribute') {
                    if (!empty($option)) {
                        foreach ($option as $optKey => $optVal) {
                            if (isset($optVal) && !empty($optVal[0])) {
                                $filter_array[$key][$optKey] = $optVal;
                            }
                        }
                    }
                } else if ($key == 'in-stock' && $option == '1') {
                    $filter_array[$key] = $option;
                } else if ($key == 'on-sale' && $option == '1') {
                    $filter_array[$key] = $option;
                } else if ($key === 'orderby') {
                    $filter_array[$key] = $option;
                }
            }
        } else {
            if (isset($current_filter) && !empty($current_filter)) {
                foreach ($current_filter as $key => $val) {
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
        }

        return $filter_array;
    }

    public function add_shortcode($atts = []) {
        $post_id = get_the_ID(); // Set post ID var.
        global $wp_query;
        if (!empty($atts['id'])) {
            $this->filter_shortcode_id = $atts['id'];
            $settings = get_post_meta($this->filter_shortcode_id, 'swpf_settings', true);
            if (!$settings) {
                $settings = Super_Product_Filter_Admin::default_settings_values();
            } else {
                $settings = Super_Product_Filter_Admin::recursive_parse_args($settings, Super_Product_Filter_Admin::default_settings_values());
            }
            $this->settings = $settings;
        }

        $query_arg = $wp_query->query;
        $post_data = $this->get_current_filter_options_vars();
        if ($post_data) {
            $query_arg = $post_data;
        }
        if (isset($wp_query->query_vars['product_cat']) && empty($query_arg['categories'])) {
            $query_arg['categories'] = $wp_query->query_vars['product_cat'];
        }
        if (isset($wp_query->query_vars['product_tag']) && empty($query_arg['tags'])) {
            $query_arg['tags'] = $wp_query->query_vars['product_tag'];
        }
        $current_filter_option = $this->get_current_filter_options($query_arg);
        $args = array(
            'current_filter_option' => $current_filter_option,
            'posid' => $this->filter_shortcode_id
        );

        ob_start();
        $panel_animation = $this->settings['side_menu']['panel_animation'];
        $panel_position = $this->settings['side_menu']['panel_position'];
        $show_animation = $this->settings['side_menu']['panel_show_animation'];
        $hide_animation = $this->settings['side_menu']['panel_hide_animation'];
        $panel_show_scrollbar = $this->settings['side_menu']['panel_show_scrollbar'];

        $side_menu_wrapper_classes = array(
            'swpf-sidemenu-wrapper',
            'swpf-sidemenu-wrapper-' . absint($this->filter_shortcode_id),
            'swpf-sidemenu-pos-' . esc_attr($panel_position),
            'swpf-scrollbar-' . esc_attr($panel_show_scrollbar),
            'swpf-click-outside-on',
            'swpf-sidemenu-hide',
            'swpf-responsive-sidemenu'
        );

        if ($panel_animation == 'custom') {
            $side_menu_wrapper_classes[] = 'swpf-panel-animation-enabled';
        }
        ?>
        <div class="<?php echo esc_attr(implode(' ', $side_menu_wrapper_classes)); ?>" 
        <?php
        if ($panel_animation == 'custom') {
            ?>
                 data-showanimation="animate--<?php echo esc_attr($show_animation); ?>"
                 data-hideanimation="animate--<?php echo esc_attr($hide_animation); ?>"
                 <?php
             }
             ?>>
                 <?php
                 $menu_toggle_button_settings = $this->settings['side_menu'];
                 $icon_type = $menu_toggle_button_settings['button_icon_type'];
                 if ($icon_type !== 'none') {
                     $open_trigger_icon = $menu_toggle_button_settings['open_trigger_icon'];
                     $close_trigger_icon = $menu_toggle_button_settings['close_trigger_icon'];
                     $position = $menu_toggle_button_settings['position'];
                     $shape = $menu_toggle_button_settings['button_shape'];
                     ?>
                <div class="swpf-sidemenu-trigger-block swpf-position-<?php echo esc_attr($position); ?> swpf-shape-<?php echo esc_attr($shape); ?>">
                    <div class="swpf-sidemenu-trigger-idle-animation">
                        <div class="swpf-sidemenu-trigger-hover-animation">
                            <div class="swpf-sidemenu-trigger">
                                <i class="swpf-sidemenu-trigger-open-icon <?php echo esc_attr($open_trigger_icon); ?>"></i>
                                <i class="swpf-sidemenu-trigger-close-icon <?php echo esc_attr($close_trigger_icon); ?>"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            } else {
                ?>
                <div class="swpf-sidemenu-trigger swpf-hidden"></div>
                <?php
            }
            ?>
            <div class="swpf-sidemenu-panel swpf-side-menu">
                <a class="swpf-panel-close swpf-pos-right" href="#"><i class="mdi-close"></i></a>
                <div class="swpf-sidemenu-panel-scroller">
                    <div class="swpf-sidemenu-panel-content">
                        <div class="swpf-responsive-filter-wrap" data-filter-id="<?php echo absint($this->filter_shortcode_id); ?>" data-responsive-width="<?php echo absint($this->settings['responsive_width']) ?>"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $responsive_html = ob_get_clean();

        add_action('wp_footer', function() use ($responsive_html) {
            echo wp_kses_post($responsive_html);
        });

        ob_start();
        ?>
        <div class="swpf-main-filter-wrap-<?php echo absint($this->filter_shortcode_id); ?>" data-filter-id="<?php echo absint($this->filter_shortcode_id); ?>">
            <?php
            $preloader = isset($this->settings['config']['preloaders']) && !empty($this->settings['config']['preloaders']) ? $this->settings['config']['preloaders'] : 'none';
            if ($preloader != 'none') {
                ?>
                <div class="swpf-ajax-loader">
                    <div class="swpf-preloader-wrap">
                        <?php include SWPF_PATH . 'public/inc/preloader/' . sanitize_text_field($preloader) . '.php' ?>
                    </div>
                </div>
                <?php
            } //preloaders

            if (isset($this->settings['advanced_settings']['custom_css']) && trim($this->settings['advanced_settings']['custom_css'])) {
                wp_register_style('swpf-custom-css-'. $post_id, false);
                wp_enqueue_style('swpf-custom-css-'. $post_id );
                wp_add_inline_style('swpf-custom-css-'. $post_id, strip_tags(swpf_css_strip_whitespace($this->settings['advanced_settings']['custom_css'])));
            }
            ?>

            <?php
            self::render_html(SWPF_PATH . 'public/inc/render-filter.php', $args);
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function get_related_term($page_taxnmy, $page_term_slug, $current_filter_taxname, $settings) {
        $query_args = array(
            $page_taxnmy => $page_term_slug,
            'post_type' => 'product',
            'posts_per_page' => -1 //Grabs ALL post
        );
        // Include or exclude terms by taxonomys
        if (isset($settings['include_exclude_filter'][$current_filter_taxname]) && $settings['include_exclude_filter'][$current_filter_taxname] == 'exclude-terms') {
            if (isset($settings['exclude_terms'][$current_filter_taxname]) && !empty($settings['exclude_terms'][$current_filter_taxname])) {
                $exclude_arr = $settings['exclude_terms'][$current_filter_taxname];
            }
        }
        if (isset($settings['include_exclude_filter'][$current_filter_taxname]) && $settings['include_exclude_filter'][$current_filter_taxname] == 'include-terms') {
            if (isset($settings['include_terms'][$current_filter_taxname]) && !empty($settings['include_terms'][$current_filter_taxname])) {
                $include_arr = $settings['include_terms'][$current_filter_taxname];
            }
        }
        $query = new WP_Query($query_args);
        $term_arr = array();
        $term_uniq = array();

        if ($query->have_posts()) :
            while ($query->have_posts()) : $query->the_post();
                $ter = get_the_terms(get_the_ID(), $current_filter_taxname);
                if (!empty($ter) && !is_wp_error($ter)) {
                    foreach ($ter as $tkey => $tval) {
                        $show = isset($include_arr) ? false : true;
                        $tvalue = (array) $tval;
                        if (isset($exclude_arr) && in_array($tvalue['term_id'], $exclude_arr)) {
                            $show = false;
                        }
                        if (isset($include_arr) && !in_array($tvalue['term_id'], $include_arr)) {
                            $show = true;
                        }
                        if (in_array($tvalue['slug'], $term_uniq) || !$show) {
                            continue;
                        }
                        $term_uniq[] = $tvalue['slug']; // current term slug is added because its unique
                        $term_arr[] = $tval; // Insert Current terms all details into arr
                    }
                }
            endwhile;
        endif;
        wp_reset_postdata();
        return $term_arr;
    }

    public function render_fields($settings, $taxonomy, $tax_name, $config, $current_filter_option = [], $count = 0) {
        $sc_id = $this->filter_shortcode_id;
        $sc_title = get_the_title($sc_id);
        $hide_field = false;
        $order = isset($settings['order'][$tax_name]) ? $settings['order'][$tax_name] : '';
        $orderby = isset($settings['orderby'][$tax_name]) ? $settings['orderby'][$tax_name] : '';
        $display_type = isset($settings['display_type'][$tax_name]) ? $settings['display_type'][$tax_name] : '';
        $korderby = isset($settings['orderby'][$tax_name]) ? $settings['orderby'][$tax_name] : 'name';
        $korder = isset($settings['order_type'][$tax_name]) ? $settings['order_type'][$tax_name] : 'ASC';
        $terms_attr = array(
            'taxonomy' => $tax_name,
            'orderby' => $korderby,
            'order' => $korder,
            'hide_empty' => false,
            'hierarchical' => true,
        );

        // Include or exclude terms by taxonomys
        if (isset($settings['include_exclude_filter'][$tax_name]) && $settings['include_exclude_filter'][$tax_name] == 'exclude-terms') {
            if (isset($settings['exclude_terms'][$tax_name]) && !empty($settings['exclude_terms'][$tax_name])) {
                $terms_attr['exclude'] = $settings['exclude_terms'][$tax_name];
            }
        }

        if (isset($settings['include_exclude_filter'][$tax_name]) && $settings['include_exclude_filter'][$tax_name] == 'include-terms') {
            if (isset($settings['include_terms'][$tax_name]) && !empty($settings['include_terms'][$tax_name])) {
                $terms_attr['include'] = $settings['include_terms'][$tax_name];
            }
        }

        $terms = get_terms($terms_attr);
        $orientationClass = [];
        if ($display_type == 'radio' || $display_type == 'checkbox' || $display_type == 'button' || $display_type == 'toggle' || $display_type == 'image' || $display_type == 'color') {
            if (isset($settings['field_orientation'][$tax_name]) && $settings['field_orientation'][$tax_name] == 'vertical') {
                array_push($orientationClass, 'swpf-field-vertical');
            } else if (isset($settings['field_orientation'][$tax_name]) && $settings['field_orientation'][$tax_name] == 'horizontal') {
                array_push($orientationClass, 'swpf-field-horizontal');
            }
        }

        // When on taxonomy->term archive page 
        if (wp_doing_ajax()) {
            if (is_array($config)) {
                if ($config['is_prod_taxonomy'] == 'yes') {
                    $get_related_term = $this->get_related_term($config['page_tax_name'], $config['page_term_name'], $tax_name, $settings);
                }
            } else {
                $config = (array) json_decode($config);
                if ($config['is_prod_taxonomy'] == 'yes') {
                    $get_related_term = $this->get_related_term($config['page_tax_name'], $config['page_term_name'], $tax_name, $settings);
                }
            }
        } else if (!wp_doing_ajax() && is_product_taxonomy()) {
            if (!is_array($config)) {
                $config = (array) json_decode($config);
            }
            $get_related_term = $this->get_related_term(get_queried_object()->taxonomy, get_queried_object()->slug, $tax_name, $settings);
        } else {
            if (!is_array($config)) {
                $config = (array) json_decode($config);
            }
            if ($config['is_prod_taxonomy'] == 'yes') {
                $get_related_term = $this->get_related_term($config['page_tax_name'], $config['page_term_name'], $tax_name, $settings);
            }
        }

        if (isset($get_related_term)) {
            $terms = $get_related_term;
        }

        if (!$hide_field) {
            ?>
            <div class="swpf-filter-item-wrap swpf-<?php echo esc_attr($tax_name) ?>-wrap swpf-tax-count-<?php echo esc_attr($count); ?>">
                <?php
                if (isset($settings['title_label'][$tax_name]) and ! empty($settings['title_label'][$tax_name])) {
                    ?>
                    <div class="swpf-filter-title">
                        <h4 class="swpf-filter-title-heading">
                            <?php
                            echo esc_html(apply_filters('swpf_translate_string', $settings['title_label'][$tax_name], 'Super Product Filter', esc_html($sc_title) . ' - Taxonomy Name ' . $tax_name));
                            ?>
                        </h4>
                        <?php if ($settings['config']['show_filter_list_toggle'] == 'on') { ?>
                            <i class="swpf-filter-title-toggle swpf-minus-icon"></i>
                        <?php } ?>
                    </div>
                    <?php
                }
                ?>

                <div class="swpf-filter-content">
                    <?php
                    if (!empty($terms) && !isset($terms->errors)) {
                        ?>
                        <div class="swpf-tax-list-wrapper <?php echo!empty($orientationClass) ? esc_attr(implode(' ', $orientationClass)) : ''; ?>">                    
                            <?php
                            $args = array(
                                'terms' => $terms,
                                'tax_name' => $tax_name,
                                'current_filter_option' => $current_filter_option,
                                'settings' => $settings,
                                'config' => $config,
                                'swpf_sc_title' => $sc_title
                            );

                            if (isset($settings['search_filter'][$tax_name]) && ($settings['search_filter'][$tax_name] == 'on') && $settings['display_type'][$tax_name] != 'dropdown' && $settings['display_type'][$tax_name] != 'multi_select') {
                                ?>
                                <div class="swpf-filter-search">
                                    <input type="text" class="swpf-filter-search-input" placeholder="<?php echo esc_attr__('Type to filter', 'super-product-filter'); ?>" />
                                </div>
                                <?php
                            }
                            switch ($display_type) {
                                case 'checkbox':
                                    self::render_html(SWPF_PATH . 'public/inc/html-types/checkbox.php', $args);
                                    break;
                                case 'toggle':
                                    self::render_html(SWPF_PATH . 'public/inc/html-types/toggle.php', $args);
                                    break;
                                case 'dropdown':
                                    self::render_html(SWPF_PATH . 'public/inc/html-types/dropdown.php', $args);
                                    break;
                                case 'multi_select':
                                    self::render_html(SWPF_PATH . 'public/inc/html-types/multiselect.php', $args);
                                    break;
                                case 'radio':
                                    self::render_html(SWPF_PATH . 'public/inc/html-types/radio.php', $args);
                                    break;
                                case 'button':
                                    self::render_html(SWPF_PATH . 'public/inc/html-types/button.php', $args);
                                    break;
                                case 'image':
                                    self::render_html(SWPF_PATH . 'public/inc/html-types/image-checkbox-select.php', $args);
                                    break;
                                case 'color':
                                    self::render_html(SWPF_PATH . 'public/inc/html-types/color-checkbox-select.php', $args);
                                    break;
                                default:
                                    self::render_html(SWPF_PATH . 'public/inc/html-types/checkbox.php', $args);
                                    break;
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        }
    }

    public function render_pricerange($settings, $current_filter_option, $min_price, $max_price) {
        $args = [
            'current_filter_option' => $current_filter_option,
            'settings' => $settings,
            'min_price' => $min_price,
            'max_price' => $max_price
        ];
        self::render_html(SWPF_PATH . 'public/inc/shortcodes/price.php', $args);
    }

    public function render_price($price) {
        $currency_pos = get_option('woocommerce_currency_pos');
        $price_html = '';
        switch ($currency_pos) {
            case 'left':
                $price_html = printf('<span class="woocommerce-Price-currencySymbol">%s</span><span class="price amount woocommerce-Price-amount">%s</span>', esc_html(get_woocommerce_currency_symbol()), esc_html($price));
                break;
            case 'right':
                $price_html = printf('<span class="price amount woocommerce-Price-amount">%s</span><span class="woocommerce-Price-currencySymbol">%s</span>', esc_html($price), esc_html(get_woocommerce_currency_symbol()));
                break;
            case 'left_space':
                $price_html = printf('<span class="woocommerce-Price-currencySymbol">%s</span> <span class="price amount woocommerce-Price-amount">%s</span>', esc_html(get_woocommerce_currency_symbol()), esc_html($price));
                break;
            case 'right_space':
                $price_html = printf('<span class="price amount woocommerce-Price-amount">%s</span> <span class="woocommerce-Price-currencySymbol">%s</span>', esc_html($price), esc_html(get_woocommerce_currency_symbol()));
                break;
        }
        return $price_html;
    }

    public function render_orderby($settings, $current_filter_option) {
        $args = array(
            'current_filter_option' => $current_filter_option,
            'settings' => $settings
        );
        self::render_html(SWPF_PATH . 'public/inc/shortcodes/order-by.php', $args);
    }

    public function render_ratings($settings, $current_filter_option) {
        $args = array(
            'current_filter_option' => $current_filter_option,
            'settings' => $settings
        );
        self::render_html(SWPF_PATH . 'public/inc/shortcodes/ratings.php', $args);
    }

    public function render_reviews($settings, $current_filter_option) {
        $args = array(
            'current_filter_option' => $current_filter_option,
            'settings' => $settings
        );
        self::render_html(SWPF_PATH . 'public/inc/shortcodes/reviews.php', $args);
    }

    public function render_onsale($settings, $current_filter_option) {
        $args = array(
            'current_filter_option' => $current_filter_option,
            'settings' => $settings
        );
        self::render_html(SWPF_PATH . 'public/inc/shortcodes/on-sale.php', $args);
    }

    public function render_instock($settings, $current_filter_option) {
        $args = array(
            'current_filter_option' => $current_filter_option,
            'settings' => $settings
        );
        self::render_html(SWPF_PATH . 'public/inc/shortcodes/in-stock.php', $args);
    }

    public function render_html($filepath, $data = array()) {
        if (isset($data['filepath'])) {
            unset($data['filepath']);
        }

        if (is_array($data) && !empty($data)) {
            extract($data);
        }

        $filepath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $filepath);
        include($filepath);
    }

    public function render_result() {
        $total_posts_found = $filtered_data = '';
        $settings = array();
        $sc_id = swpf_get_var('swpf_filter_sc');
        if (isset($this->settings) && !empty($this->settings)) {
            $settings = $this->settings;
        } else if ($sc_id) {
            $this->filter_shortcode_id = $sc_id;
            $settings = get_post_meta($sc_id, 'swpf_settings', true);
            if (!$settings) {
                $settings = Super_Product_Filter_Admin::default_settings_values();
            } else {
                $settings = Super_Product_Filter_Admin::recursive_parse_args($settings, Super_Product_Filter_Admin::default_settings_values());
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
                            if (isset($variant['attributes']["attribute_" . $attr_name]) AND in_array($variant['attributes']["attribute_" . $attr_name], $values)) {
                                $rate[$key] ++;
                            }
                        }
                    }
                }
                arsort($rate);
                $attr_key = array_key_first($rate);
                if (array_shift($rate)) {
                    if (isset($variations[$attr_key]["image_id"]) AND $variations[$attr_key]["image_id"]) {
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

    public function get_current_filter_options_vars() {
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

    public function product_per_page($products) {
        $products = isset($this->product_per_page) ? absint($this->product_per_page) : absint($products);
        return $products;
    }

    public function loop_columns() {
        $default = get_option('woocommerce_catalog_columns', 4);
        $cols = isset($this->product_columns) ? absint($this->product_columns) : absint($default);
        return $cols;
    }

    public static function get_filtered_price($tax_query = array(), $meta_query = array()) {
        global $wpdb, $wp_the_query;
        $args = $wp_the_query->query_vars;

        $meta_query = new WP_Meta_Query($meta_query);
        $tax_query = new WP_Tax_Query($tax_query);

        $meta_query_sql = $meta_query->get_sql('post', $wpdb->posts, 'ID');
        $tax_query_sql = $tax_query->get_sql($wpdb->posts, 'ID');

        $sql = "SELECT min(FLOOR(price_meta.meta_value + 0.0)) as min_price, max(CEILING(price_meta.meta_value + 0.0)) as max_price FROM {$wpdb->posts} ";
        $sql .= " LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id " . $tax_query_sql['join'] . $meta_query_sql['join'];
        $sql .= " WHERE {$wpdb->posts}.post_type = 'product'
            AND {$wpdb->posts}.post_status = 'publish'
            AND price_meta.meta_key IN ('" . implode("','", array_map('esc_sql', apply_filters('woocommerce_price_filter_meta_keys', array('_price')))) . "')
            AND price_meta.meta_value > '' ";
        $sql .= $tax_query_sql['where'] . $meta_query_sql['where'];

        $prices = $wpdb->get_row($sql);
        return $prices;
    }

}
