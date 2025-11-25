<?php

class Super_Product_Filter_Render extends Super_Product_Filter_General {

    public function __construct() {

        add_action('wp_ajax_swpf_get_product_list', array($this, 'get_product_list'));
        add_action('wp_ajax_nopriv_swpf_get_product_list', array($this, 'get_product_list'));

        // Meta Filter
        add_action('woocommerce_product_query', array($this, 'filter_posts'), 11);

        // Shortcode
        add_shortcode('swpf_shortcode', array($this, 'add_shortcode'));
        add_shortcode('swpf_elem_shortcode', array($this, 'add_shortcode'));
    }

    public function get_product_list() {
        if (wp_verify_nonce(swpf_get_post('ajax_nonce'), 'swpf-frontend-ajax-nonce')) {
            include SWPF_PATH . 'public/inc/filter/render/ajax-request.php';
        }
    }

    public function filter_posts($wp_query, $post_data = false) {
        if ((isset($GLOBALS['current_screen']) && $GLOBALS['current_screen']->in_admin()) || is_customize_preview()) {
            return $wp_query;
        }

        $swpf_current_filter_option = self::get_current_filter_options($post_data);

        if (empty($post_data)) {
            $swpf_current_filter_option = self::get_current_filter_options_vars();
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
        } elseif ($filter_shortcode_id) {
            $this->filter_shortcode_id = absint($filter_shortcode_id);
            $this->settings = get_post_meta($this->filter_shortcode_id, 'swpf_settings', true);
        }

        if (isset($this->settings) && !empty($this->settings)) {
            $tax_query = $meta_query = [];
            $relation = isset($this->settings['config']['logic_operator']) && !empty($this->settings['config']['logic_operator']) ? $this->settings['config']['logic_operator'] : 'AND';
            $tax_query['relation'] = $relation;

            foreach ($swpf_current_filter_option as $swpf_key => $swpf_option) {
                if ($swpf_key == 'categories') {
                    $krelation = isset($this->settings['multiselect_logic_operator']['product_cat']) ? $this->settings['multiselect_logic_operator']['product_cat'] : 'AND';
                    $tax_query[] = array(
                        'operator' => $krelation,
                        'taxonomy' => 'product_cat',
                        'field' => 'slug',
                        'terms' => is_array($swpf_option) ? $swpf_option : explode(',', $swpf_option)
                    );

                } elseif ($swpf_key == 'tags') {
                    $krelation = isset($this->settings['multiselect_logic_operator']['product_tag']) ? $this->settings['multiselect_logic_operator']['product_tag'] : 'AND';
                    $tax_query[] = array(
                        'operator' => $krelation,
                        'taxonomy' => 'product_tag',
                        'field' => 'slug',
                        'terms' => is_array($swpf_option) ? $swpf_option : explode(',', $swpf_option)
                    );

                } elseif ($swpf_key == 'brands') {
                    $krelation = isset($this->settings['multiselect_logic_operator']['product_brand']) ? $this->settings['multiselect_logic_operator']['product_brand'] : 'AND';
                    $tax_query[] = array(
                        'operator' => $krelation,
                        'taxonomy' => 'product_brand',
                        'field' => 'slug',
                        'terms' => is_array($swpf_option) ? $swpf_option : explode(',', $swpf_option)
                    );

                } elseif ($swpf_key == 'attribute' || 0 === strpos($swpf_key, 'pa_')) {
                    foreach ($swpf_option as $swpf_key => $value) {
                        $krelation = isset($this->settings['multiselect_logic_operator'][$swpf_key]) ? $this->settings['multiselect_logic_operator'][$swpf_key] : 'AND';
                        $atts = (array) $value;
                        $tax_query[] = array(
                            'operator' => $krelation,
                            'taxonomy' => $swpf_key,
                            'field' => 'slug',
                            'terms' => $atts
                        );
                    }

                } elseif ($swpf_key == 'visibility') {
                    $krelation = isset($this->settings['multiselect_logic_operator']['product_visibility']) ? $this->settings['multiselect_logic_operator']['product_visibility'] : 'AND';
                    $tax_query[] = array(
                        'operator' => $krelation,
                        'taxonomy' => 'product_visibility',
                        'field' => 'slug',
                        'terms' => is_array($swpf_option) ? $swpf_option : explode(',', $swpf_option)
                    );

                } elseif ($swpf_key == 'rating-from') {
                    $meta_query[] = array(
                        'key' => '_wc_average_rating',
                        'value' => floatval($swpf_option[0]),
                        'compare' => '>=',
                        'type' => 'DECIMAL(3,2)'
                    );

                } elseif ($swpf_key == 'price') {
                    $swpf_min_max_price = Super_Product_Filter_General::get_filtered_price();
                    $swpf_min_price = isset($swpf_option['min_price']) && $swpf_option['min_price'] ? floatval($swpf_option['min_price']) : floor($swpf_min_max_price->min_price ?: 0);
                    $swpf_max_price = isset($swpf_option['max_price']) && $swpf_option['max_price'] ? floatval($swpf_option['max_price']) : ceil($swpf_min_max_price->max_price ?: 0);
                    $meta_query[] = array(
                        'key' => '_price',
                        'value' => array($swpf_min_price, $swpf_max_price),
                        'compare' => 'BETWEEN',
                        'type' => 'DECIMAL'
                    );

                } elseif ($swpf_key == 'review') {
                    if (isset($swpf_option['review_from'])) {
                        $meta_query[] = array(
                            'key' => '_wc_review_count',
                            'value' => intval($swpf_option['review_from']),
                            'compare' => '>=',
                            'type' => 'NUMERIC',
                        );
                    }

                } elseif ($swpf_key == 'in-stock' && $swpf_option == '1') {
                    $meta_query[] = array(
                        'key' => '_stock_status',
                        'value' => 'instock',
                        'compare' => '=',
                    );

                } elseif ($swpf_key == 'on-sale' && $swpf_option == '1') {
                    $wp_query->set('post__in', array_merge([0], wc_get_product_ids_on_sale()));
                }
            }

            $oby = isset($swpf_current_filter_option['orderby']) ? $swpf_current_filter_option['orderby'] : '';
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
                        $order_by_query = 'menu_order title';
                        $order = 'asc';
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

            $selected_lo_specific_cat_ids = [];

            if (!is_shop() && swpf_get_post('is_shop') != 'yes' && !is_product_taxonomy() && swpf_get_post('is_prod_taxonomy') != 'yes' && swpf_get_var('is_prod_taxonomy') != 'yes' && isset($this->settings['config']['lo_specific_cat']) && !empty($this->settings['config']['lo_specific_cat'])) {
                $selected_lo_specific_cat_ids = $this->settings['config']['lo_specific_cat'];
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

    public function pagination_args($arg) {
        $post_data = swpf_get_post_data('swpf_form_data');

        if (!empty($post_data)) {
            $pagination_link = ($post_data['pagination_link']);
            $arg['base'] = $pagination_link;
        }

        return $arg;
    }

    public function add_shortcode($atts = []) {
        $post_id = get_the_ID(); // Set post ID var.
        global $wp_query;

        if (!empty($atts['id'])) {
            $this->filter_shortcode_id = $atts['id'];
            $swpf_settings = get_post_meta($this->filter_shortcode_id, 'swpf_settings', true);
            if (!$swpf_settings) {
                $swpf_settings = Super_Product_Filter_Metabox::default_settings_values();
            } else {
                $swpf_settings = Super_Product_Filter_Admin::recursive_parse_args($swpf_settings, Super_Product_Filter_Metabox::default_settings_values());
            }
            $this->settings = $swpf_settings;
        }

        $query_arg = $wp_query->query;
        $post_data = self::get_current_filter_options_vars();

        if ($post_data) {
            $query_arg = $post_data;
        }

        if (isset($wp_query->query_vars['product_cat']) && empty($query_arg['categories'])) {
            $query_arg['categories'] = $wp_query->query_vars['product_cat'];
        }

        if (isset($wp_query->query_vars['product_tag']) && empty($query_arg['tags'])) {
            $query_arg['tags'] = $wp_query->query_vars['product_tag'];
        }

        if (isset($wp_query->query_vars['product_brand']) && empty($query_arg['brands'])) {
            $query_arg['brands'] = $wp_query->query_vars['product_brand'];
        }

        $swpf_current_filter_option = self::get_current_filter_options($query_arg);
        $swpf_posid = $this->filter_shortcode_id;

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
        <div class="<?php echo esc_attr(implode(' ', $side_menu_wrapper_classes)); ?>" <?php
            if ($panel_animation == 'custom') {
                ?> data-showanimation="animate--<?php echo esc_attr($show_animation); ?>" data-hideanimation="animate--<?php echo esc_attr($hide_animation); ?>" <?php
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

        add_action('wp_footer', function () use ($responsive_html) {
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
    
            ?>

            <?php
            include SWPF_PATH . 'public/inc/filter/render/filter.php';
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_fields($swpf_settings, $taxonomy, $swpf_tax_name, $swpf_config, $swpf_current_filter_option = [], $swpf_count = 0) {
        $swpf_sc_id = $this->filter_shortcode_id;
        $swpf_sc_title = get_the_title($swpf_sc_id);
        $hide_field = false;
        $order = isset($swpf_settings['order'][$swpf_tax_name]) ? $swpf_settings['order'][$swpf_tax_name] : '';
        $orderby = isset($swpf_settings['orderby'][$swpf_tax_name]) ? $swpf_settings['orderby'][$swpf_tax_name] : '';
        $display_type = isset($swpf_settings['display_type'][$swpf_tax_name]) ? $swpf_settings['display_type'][$swpf_tax_name] : '';
        $korderby = isset($swpf_settings['orderby'][$swpf_tax_name]) ? $swpf_settings['orderby'][$swpf_tax_name] : 'name';
        $korder = isset($swpf_settings['order_type'][$swpf_tax_name]) ? $swpf_settings['order_type'][$swpf_tax_name] : 'ASC';

        $orientationClass = [];
        if ($display_type == 'radio' || $display_type == 'checkbox' || $display_type == 'button' || $display_type == 'toggle' || $display_type == 'image' || $display_type == 'color') {
            if (isset($swpf_settings['field_orientation'][$swpf_tax_name]) && $swpf_settings['field_orientation'][$swpf_tax_name] == 'vertical') {
                array_push($orientationClass, 'swpf-field-vertical');

            } elseif (isset($swpf_settings['field_orientation'][$swpf_tax_name]) && $swpf_settings['field_orientation'][$swpf_tax_name] == 'horizontal') {
                array_push($orientationClass, 'swpf-field-horizontal');
            }
        }

        $page_tax_name = '';

        // When on taxonomy->term archive page 
        if (wp_doing_ajax()) {
            if (is_array($swpf_config)) {
                if ($swpf_config['is_prod_taxonomy'] == 'yes') {
                    $page_tax_name = $swpf_config['page_tax_name'];
                    $page_term_slug = $swpf_config['page_term_name'];
                }

            } else {
                $swpf_config = (array) json_decode($swpf_config);
                if ($swpf_config['is_prod_taxonomy'] == 'yes') {
                    $page_tax_name = $swpf_config['page_tax_name'];
                    $page_term_slug = $swpf_config['page_term_name'];
                }
            }

        } elseif (!wp_doing_ajax() && is_product_taxonomy()) {
            $page_tax_name = get_queried_object()->taxonomy;
            $page_term_slug = get_queried_object()->slug;

        } else {
            if (!is_array($swpf_config)) {
                $swpf_config = (array) json_decode($swpf_config);
            }
            if ($swpf_config['is_prod_taxonomy'] == 'yes') {
                $page_tax_name = $swpf_config['page_tax_name'];
                $page_term_slug = $swpf_config['page_term_name'];
            }
        }

        $swpf_terms_attr = array(
            'taxonomy' => $swpf_tax_name,
            'orderby' => $korderby,
            'order' => $korder,
            'hide_empty' => false,
            'hierarchical' => true,
        );

        if ($page_tax_name == $swpf_tax_name) {
            $swpf_terms_attr['slug'] = $page_term_slug;
        }

        // Include or exclude terms by taxonomys
        if (isset($swpf_settings['include_exclude_filter'][$swpf_tax_name]) && $swpf_settings['include_exclude_filter'][$swpf_tax_name] == 'exclude-terms') {
            if (isset($swpf_settings['exclude_terms'][$swpf_tax_name]) && !empty($swpf_settings['exclude_terms'][$swpf_tax_name])) {
                $swpf_terms_attr['exclude'] = $swpf_settings['exclude_terms'][$swpf_tax_name];
            }
        }

        if (isset($swpf_settings['include_exclude_filter'][$swpf_tax_name]) && $swpf_settings['include_exclude_filter'][$swpf_tax_name] == 'include-terms') {
            if (isset($swpf_settings['include_terms'][$swpf_tax_name]) && !empty($swpf_settings['include_terms'][$swpf_tax_name])) {
                $swpf_terms_attr['include'] = $swpf_settings['include_terms'][$swpf_tax_name];
            }
        }

        $swpf_terms = get_terms($swpf_terms_attr);

        if (!$hide_field) {
            ?>
            <div class="swpf-filter-item-wrap swpf-<?php echo esc_attr($swpf_tax_name) ?>-wrap swpf-tax-count-<?php echo esc_attr($swpf_count); ?>">
                <?php
                if (isset($swpf_settings['title_label'][$swpf_tax_name]) && !empty($swpf_settings['title_label'][$swpf_tax_name])) {
                    ?>
                    <div class="swpf-filter-title">
                        <h4 class="swpf-filter-title-heading">
                            <?php
                            echo esc_html(apply_filters('swpf_translate_string', $swpf_settings['title_label'][$swpf_tax_name], 'Super Product Filter', esc_html($swpf_sc_title) . ' - Taxonomy Name ' . $swpf_tax_name));
                            ?>
                        </h4>
                        <?php if ($swpf_settings['config']['show_filter_list_toggle'] == 'on') { ?>
                            <i class="swpf-filter-title-toggle swpf-minus-icon"></i>
                        <?php } ?>
                    </div>
                    <?php
                }
                ?>

                <div class="swpf-filter-content">
                    <?php
                    if (!empty($swpf_terms) && !isset($swpf_terms->errors)) {
                        ?>
                        <div class="swpf-tax-list-wrapper <?php echo !empty($orientationClass) ? esc_attr(implode(' ', $orientationClass)) : ''; ?>">
                            <?php
                            $swpf_sc_title = $swpf_sc_title;

                            if (isset($swpf_settings['search_filter'][$swpf_tax_name]) && ($swpf_settings['search_filter'][$swpf_tax_name] == 'on') && $swpf_settings['display_type'][$swpf_tax_name] != 'dropdown' && $swpf_settings['display_type'][$swpf_tax_name] != 'multi_select') {
                                ?>
                                <div class="swpf-filter-search">
                                    <input type="text" class="swpf-filter-search-input" placeholder="<?php echo esc_attr__('Type to filter', 'super-product-filter'); ?>" />
                                </div>
                                <?php
                            }
                            switch ($display_type) {
                                case 'checkbox':
                                    include SWPF_PATH . 'public/inc/filter/html-types/checkbox.php';
                                    break;

                                case 'toggle':
                                    include SWPF_PATH . 'public/inc/filter/html-types/toggle.php';
                                    break;

                                case 'dropdown':
                                    include SWPF_PATH . 'public/inc/filter/html-types/dropdown.php';
                                    break;

                                case 'multi_select':
                                    include SWPF_PATH . 'public/inc/filter/html-types/multiselect.php';
                                    break;

                                case 'radio':
                                    include SWPF_PATH . 'public/inc/filter/html-types/radio.php';
                                    break;

                                case 'button':
                                    include SWPF_PATH . 'public/inc/filter/html-types/button.php';
                                    break;

                                case 'image':
                                    include SWPF_PATH . 'public/inc/filter/html-types/image-checkbox-select.php';
                                    break;

                                case 'color':
                                    include SWPF_PATH . 'public/inc/filter/html-types/color-checkbox-select.php';
                                    break;

                                default:
                                    include SWPF_PATH . 'public/inc/filter/html-types/checkbox.php';
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

}

new Super_Product_Filter_Render();