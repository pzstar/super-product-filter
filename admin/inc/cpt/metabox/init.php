<?php

class Super_Product_Filter_Metabox {
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'settings_metabox'));
        add_action('save_post', array($this, 'save_metabox_settings'));

        add_action('admin_menu', function () {
            remove_meta_box('submitdiv', 'swpf-product-filter', 'side');
            remove_meta_box('slugdiv', 'swpf-product-filter', 'normal');
        });

        // Ajax Save Post
        add_action('save_post', array($this, 'save_metabox_settings_xhr'));

        add_action('init', array($this, 'register_translation_strings'), 100);

        add_action('wp_ajax_swpf_show_custom_term_options', array($this, 'show_custom_term_options'));
        add_action('wp_ajax_swpf_save_custom_term_options', array($this, 'save_custom_term_options'));
    }

    public function settings_metabox() {
        $current_screen = get_current_screen();
        add_meta_box('swpf-settings-metabox', esc_html__('Super Product Filter', 'super-product-filter'), array($this, 'settings_metabox_callback'), 'swpf-product-filter', 'normal', 'high');
    }

    public function settings_metabox_callback() {
        include SWPF_PATH . 'admin/inc/cpt/metabox/settings.php';
    }

    public function save_metabox_settings($post_id) {
        if (wp_verify_nonce(swpf_get_post('swpf_settings_nonce'), 'swpf-settings-nonce')) {
            $settings = get_post_meta($post_id, 'swpf_settings', true);
            $terms_customize = isset($settings['terms_customize']) ? $settings['terms_customize'] : array();
            $settings = swpf_get_post_data_arr('swpf_settings');

            if ($settings) {
                $settings['terms_customize'] = $terms_customize;
                $settings = Super_Product_Filter_Admin::recursive_parse_args($settings, self::checkbox_settings());
                $settings = Super_Product_Filter_Admin::sanitize_array($settings, self::sanitize_settings_rules());
                update_post_meta($post_id, 'swpf_settings', $settings);
            }
        }
        return;
    }

    public function save_metabox_settings_xhr($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        #If this is your post type
        if ('swpf-product-filter' === swpf_get_post('post_type')) {
            # Send JSON response
            if (swpf_get_post('save_post_ajax') == true) {
                wp_send_json_success($post_id);
            }
        }
    }

    public static function default_settings_values() {
        $taxonomies = swpf_get_taxonomies();
        $taxonomies_keys = array_keys($taxonomies);
        $return = array(
            'enable' => array(
                'price_range' => 'off',
                'reviews' => 'off',
                'ratings' => 'off',
                'on_sale' => 'off',
                'in_stock' => 'off',
            ),
            'title_label' => array(
                'price_range' => esc_html__('Price', 'super-product-filter'),
                'reviews' => esc_html__('Reviews', 'super-product-filter'),
                'ratings' => esc_html__('Ratings', 'super-product-filter'),
                'on_sale' => esc_html__('On Sale', 'super-product-filter'),
                'in_stock' => esc_html__('In Stock', 'super-product-filter'),
            ),
            'config' => array(
                'indent_cat' => 'off',
                'autosubmit' => 'off',
                'submit_btn_text' => esc_html__('Apply', 'super-product-filter'),
                'logic_operator' => 'AND',
                'lo_specific_cat' => array(),
                'orderby' => 'ID',
                'show_filter_list_toggle' => 'on',
                'product_selector' => 'ul.products',
                'product_count_selector' => '.woocommerce-result-count',
                'pagination_selector' => '.woocommerce-pagination',
                'product_columns' => '',
                'product_rows' => '',
                'preloaders' => 'preloader1',
                'scroll_after_filter' => 'on',
            ),
            'responsive_width' => 768,
            'shortcode' => '',
            'side_menu' => array(
                'button_icon_type' => 'default_icon',
                'button_shape' => 'round',
                'predefined_icon_style' => 'style1',
                'open_trigger_icon' => 'mdi-filter-outline',
                'close_trigger_icon' => 'mdi-close',
                'custom_open_trigger_icon' => '',
                'custom_close_trigger_icon' => '',
                'button_hover_animation' => '',
                'button_idle_animation' => '',
                'position' => 'bottom-right',
                'offset_top' => '',
                'offset_bottom' => '',
                'offset_left' => '',
                'offset_right' => '',
                'toggle_button_size' => 70,
                'icon_size' => 26,
                'image_size' => 100,
                'hamburger_width' => 25,
                'hamburger_spacing' => 8,
                'hamburger_thickness' => 1,
                'button_bg_color' => '',
                'button_hover_bg_color' => '',
                'button_icon_color' => '',
                'button_hover_icon_color' => '',
                'button_shadow_x' => '',
                'button_shadow_y' => '',
                'button_shadow_blur' => '',
                'button_shadow_color' => '',
                'panel_position' => 'left',
                'panel_width' => 400,
                'panel_width_unit' => 'px',
                'panel_animation' => 'default',
                'panel_show_animation' => 'bounceIn',
                'panel_hide_animation' => 'bounceOut',
                'panel_show_scrollbar' => 'off',
                'scrollbar_width' => '',
                'scrollbar_drag_rail_color' => '',
                'scrollbar_drag_bar_color' => '',
                'panel_background_color' => '',
            ),
            'heading_typo' => array(
                'family' => 'inherit',
                'style' => '',
                'text_transform' => 'inherit',
                'text_decoration' => 'inherit',
                'line_height' => '',
                'letter_spacing' => '',
                'size' => '',
            ),
            'content_typo' => array(
                'family' => 'inherit',
                'style' => '',
                'text_transform' => 'inherit',
                'text_decoration' => 'inherit',
                'line_height' => '',
                'letter_spacing' => '',
                'size' => '',
            ),
            'checkboxradio' => array(
                'skin' => 'swpf-checkboxradio-skin-1',
                'size' => '',
                'bgcolor' => '',
                'bgcolorhov' => '',
                'bgcoloractive' => '',
                'bordercolor' => '',
                'bordercolorhov' => '',
                'bordercoloractive' => '',
                'iconcolor' => '',
            ),
            'dropdown' => array(
                'skin' => 'swpf-dropdown-skin-1',
                'height' => '',
                'bordercolor' => '',
                'bgcolor' => '',
                'textcolor' => '',
            ),
            'multiselect' => array(
                'height' => '',
                'skin' => 'swpf-multiselect-skin-1',
                'bordercolor' => '',
                'bgcolor' => '',
                'textcolor' => '',
                'selectedbgcolor' => '',
                'selectedtextcolor' => '',
            ),
            'pricerangeslider' => array(
                'skin' => 'swpf-pricerangeslider-skin-1',
                'highlightcolor' => '',
                'barcolor' => '',
            ),
            'button' => array(
                'skin' => 'swpf-button-skin-1',
                'bgcolor' => '',
                'bordercolor' => '',
                'textcolor' => '',
                'bgcolor_hov' => '',
                'bordercolor_hov' => '',
                'textcolor_hov' => '',
                'borderradius' => '',
                'fontsize' => '',
                'padding' => array(
                    'top' => '',
                    'right' => '',
                    'bottom' => '',
                    'left' => '',
                )
            ),
            'toggle' => array(
                'skin' => 'swpf-toggle-skin-1',
                'bgcolor' => '',
                'inactivecolor' => '',
                'activecolor' => '',
            ),
            'color' => array(
                'size' => 30,
                'shape' => 'swpf-square',
                'bordercolor' => '',
                'activecolor' => '',
            ),
            'image' => array(
                'size' => 50,
                'padding' => '',
                'shape' => 'swpf-square',
                'bordercolor' => '',
            ),
            'rating' => array(
                'textcolor' => '',
                'textcolorhover' => '',
                'textcoloractive' => '',
            ),
            'filterbutton' => array(
                'bgcolor' => '',
                'bordercolor' => '',
                'textcolor' => '',
                'bgcolor_hov' => '',
                'bordercolor_hov' => '',
                'textcolor_hov' => '',
                'borderradius' => '',
                'fontsize' => '',
                'padding' => array(
                    'top' => '',
                    'right' => '',
                    'bottom' => '',
                    'left' => '',
                )
            ),
            'applybutton' => array(
                'bgcolor' => '',
                'bordercolor' => '',
                'textcolor' => '',
                'bgcolor_hov' => '',
                'bordercolor_hov' => '',
                'textcolor_hov' => '',
                'borderradius' => '',
                'fontsize' => '',
                'padding' => array(
                    'top' => '',
                    'right' => '',
                    'bottom' => '',
                    'left' => '',
                )
            ),
            'searchfilter' => array(
                'bgcolor' => '',
                'bordercolor' => '',
                'textcolor' => '',
                'borderradius' => '',
                'padding' => array(
                    'top' => '',
                    'right' => '',
                    'bottom' => '',
                    'left' => '',
                )
            ),
            'filterbox' => array(
                'height' => 0,
                'bgcolor' => '',
                'textcolor' => '',
                'borderradius' => '',
                'bordercolor' => '',
                'borderwidth' => '',
                'padding' => array(
                    'top' => '',
                    'bottom' => '',
                    'left' => '',
                    'right' => ''
                ),
                'spacing' => '',
                'itemspacing' => '',
                'shadow_x' => '',
                'shadow_y' => '',
                'shadow_blur' => '',
                'shadow_spread' => '',
                'shadow_color' => '',
                'enablebottomborder' => 'off',
            ),
            'heading' => array(
                'bgcolor' => '',
                'textcolor' => '',
                'borderradius' => '',
                'bordercolor' => '',
                'bordertop' => '',
                'borderbottom' => '',
                'borderleft' => '',
                'borderright' => '',
                'marginbottom' => '',
                'padding' => array(
                    'top' => '',
                    'bottom' => '',
                    'left' => '',
                    'right' => ''
                ),
            ),
            'widgetarea' => array(
                'column' => 3,
            ),
            'primary_color' => '',
        );
        $return['include_exclude_filter']['order_by'] = 'all';
        $return['include_terms']['order_by'] = array();

        if ($taxonomies_keys) {
            foreach ($taxonomies_keys as $key) {
                $return['enable'][$key] = 'off';
                $return['title_label'][$key] = esc_html(str_replace('Product ', '', ucwords($taxonomies[$key]->label)));
                $return['show_count'][$key] = 'off';
                $return['display_type'][$key] = 'radio';
                $return['placeholder_txt'][$key] = '';
                $return['multiselect_logic_operator'][$key] = 'IN';
                if (!($key == 'product_visibility')) {
                    $return['orderby'][$key] = 'term_id';
                    $return['order_type'][$key] = 'ASC';
                    $return['include_exclude_filter'][$key] = 'all';
                    $return['include_terms'][$key] = array();
                    $return['exclude_terms'][$key] = array();
                } else {
                    $return['include_exclude_filter'][$key] = 'all';
                    $return['include_terms'][$key] = array();
                }

                $return['field_orientation'][$key] = 'vertical';
                $return['hide_term_name'][$key] = 'off';
                $return['hide_term_name'][$key] = 'off';
                $return['terms_customize'][$key] = array();
                $return['search_filter'][$key] = 'off';
            }
        }

        return $return;
    }

    public static function sanitize_settings_rules() {
        $return = array(
            'enable' => array(
                'price_range' => 'swpf_sanitize_checkbox',
                'reviews' => 'swpf_sanitize_checkbox',
                'ratings' => 'swpf_sanitize_checkbox',
                'on_sale' => 'swpf_sanitize_checkbox',
                'in_stock' => 'swpf_sanitize_checkbox',
            ),
            'title_label' => array(
                'price_range' => 'sanitize_text_field',
                'reviews' => 'sanitize_text_field',
                'ratings' => 'sanitize_text_field',
                'on_sale' => 'sanitize_text_field',
                'in_stock' => 'sanitize_text_field',
            ),
            'list_order' => array(
                'order_by' => 'sanitize_text_field',
                'price_range' => 'sanitize_text_field',
                'reviews' => 'sanitize_text_field',
                'ratings' => 'sanitize_text_field',
                'on_sale' => 'sanitize_text_field',
                'in_stock' => 'sanitize_text_field',
            ),
            'config' => array(
                'indent_cat' => 'swpf_sanitize_checkbox',
                'autosubmit' => 'sanitize_text_field',
                'submit_btn_text' => 'sanitize_text_field',
                'logic_operator' => 'sanitize_text_field',
                'lo_specific_cat' => 'sanitize_text_field',
                'orderby' => 'sanitize_text_field',
                'show_filter_list_toggle' => 'swpf_sanitize_checkbox',
                'product_selector' => 'sanitize_text_field',
                'product_count_selector' => 'sanitize_text_field',
                'pagination_selector' => 'sanitize_text_field',
                'product_columns' => 'swpf_sanitize_number',
                'product_rows' => 'swpf_sanitize_number',
                'preloaders' => 'sanitize_text_field',
                'scroll_after_filter' => 'swpf_sanitize_checkbox',
            ),
            'responsive_width' => 'swpf_sanitize_number',
            'shortcode' => 'sanitize_text_field',
            'side_menu' => array(
                'togglebox_bg_color' => 'sanitize_text_field',
                'togglebox_font_color' => 'sanitize_text_field',
                'toggle_content_bg_color' => 'sanitize_text_field',
                'button_icon_type' => 'sanitize_text_field',
                'button_shape' => 'sanitize_text_field',
                'predefined_icon_style' => 'sanitize_text_field',
                'open_trigger_icon' => 'sanitize_text_field',
                'close_trigger_icon' => 'sanitize_text_field',
                'custom_open_trigger_icon' => 'sanitize_text_field',
                'custom_close_trigger_icon' => 'sanitize_text_field',
                'button_hover_animation' => 'sanitize_text_field',
                'button_idle_animation' => 'sanitize_text_field',
                'position' => 'sanitize_text_field',
                'offset_top' => 'swpf_sanitize_number',
                'offset_bottom' => 'swpf_sanitize_number',
                'offset_left' => 'swpf_sanitize_number',
                'offset_right' => 'swpf_sanitize_number',
                'toggle_button_size' => 'swpf_sanitize_number',
                'icon_size' => 'swpf_sanitize_number',
                'image_size' => 'swpf_sanitize_number',
                'hamburger_width' => 'swpf_sanitize_number',
                'hamburger_spacing' => 'swpf_sanitize_number',
                'hamburger_thickness' => 'swpf_sanitize_number',
                'button_bg_color' => 'swpf_sanitize_color',
                'button_hover_bg_color' => 'swpf_sanitize_color',
                'button_icon_color' => 'swpf_sanitize_color',
                'button_hover_icon_color' => 'swpf_sanitize_color',
                'button_shadow_x' => 'swpf_sanitize_number',
                'button_shadow_y' => 'swpf_sanitize_number',
                'button_shadow_blur' => 'swpf_sanitize_number',
                'button_shadow_color' => 'swpf_sanitize_color',
                'panel_position' => 'sanitize_text_field',
                'panel_width' => 'swpf_sanitize_number',
                'panel_width_unit' => 'sanitize_text_field',
                'panel_animation' => 'sanitize_text_field',
                'panel_show_animation' => 'sanitize_text_field',
                'panel_hide_animation' => 'sanitize_text_field',
                'panel_show_scrollbar' => 'swpf_sanitize_checkbox',
                'scrollbar_width' => 'swpf_sanitize_number',
                'scrollbar_drag_rail_color' => 'swpf_sanitize_color',
                'scrollbar_drag_bar_color' => 'swpf_sanitize_color',
                'panel_background_color' => 'swpf_sanitize_color',
            ),
            'heading_typo' => array(
                'family' => 'sanitize_text_field',
                'style' => 'sanitize_text_field',
                'text_transform' => 'sanitize_text_field',
                'text_decoration' => 'sanitize_text_field',
                'line_height' => 'swpf_sanitize_number',
                'letter_spacing' => 'swpf_sanitize_number',
                'size' => 'swpf_sanitize_number',
            ),
            'content_typo' => array(
                'family' => 'sanitize_text_field',
                'style' => 'sanitize_text_field',
                'text_transform' => 'sanitize_text_field',
                'text_decoration' => 'sanitize_text_field',
                'line_height' => 'swpf_sanitize_number',
                'letter_spacing' => 'swpf_sanitize_number',
                'size' => 'swpf_sanitize_number',
            ),
            'checkboxradio' => array(
                'skin' => 'sanitize_text_field',
                'size' => 'swpf_sanitize_number',
                'bgcolor' => 'swpf_sanitize_color',
                'bgcolorhov' => 'swpf_sanitize_color',
                'bgcoloractive' => 'swpf_sanitize_color',
                'bordercolor' => 'swpf_sanitize_color',
                'bordercolorhov' => 'swpf_sanitize_color',
                'bordercoloractive' => 'swpf_sanitize_color',
                'iconcolor' => 'swpf_sanitize_color',
            ),
            'dropdown' => array(
                'skin' => 'sanitize_text_field',
                'height' => 'swpf_sanitize_number',
                'bordercolor' => 'swpf_sanitize_color',
                'bgcolor' => 'swpf_sanitize_color',
                'textcolor' => 'swpf_sanitize_color',
            ),
            'multiselect' => array(
                'height' => 'swpf_sanitize_number',
                'skin' => 'sanitize_text_field',
                'bordercolor' => 'swpf_sanitize_color',
                'bgcolor' => 'swpf_sanitize_color',
                'textcolor' => 'swpf_sanitize_color',
                'selectedbgcolor' => 'swpf_sanitize_color',
                'selectedtextcolor' => 'swpf_sanitize_color',
            ),
            'pricerangeslider' => array(
                'skin' => 'sanitize_text_field',
                'highlightcolor' => 'swpf_sanitize_color',
                'barcolor' => 'swpf_sanitize_color',
            ),
            'button' => array(
                'skin' => 'sanitize_text_field',
                'bgcolor' => 'swpf_sanitize_color',
                'bordercolor' => 'swpf_sanitize_color',
                'textcolor' => 'swpf_sanitize_color',
                'bgcolor_hov' => 'swpf_sanitize_color',
                'bordercolor_hov' => 'swpf_sanitize_color',
                'textcolor_hov' => 'swpf_sanitize_color',
                'borderradius' => 'swpf_sanitize_number',
                'fontsize' => 'swpf_sanitize_number',
                'padding' => array(
                    'top' => 'swpf_sanitize_number',
                    'right' => 'swpf_sanitize_number',
                    'bottom' => 'swpf_sanitize_number',
                    'left' => 'swpf_sanitize_number',
                )
            ),
            'toggle' => array(
                'skin' => 'sanitize_text_field',
                'bgcolor' => 'swpf_sanitize_color',
                'inactivecolor' => 'swpf_sanitize_color',
                'activecolor' => 'swpf_sanitize_color',
            ),
            'color' => array(
                'size' => 'swpf_sanitize_number',
                'shape' => 'sanitize_text_field',
                'bordercolor' => 'swpf_sanitize_color',
                'activecolor' => 'swpf_sanitize_color',
            ),
            'image' => array(
                'size' => 'swpf_sanitize_number',
                'padding' => 'swpf_sanitize_number',
                'shape' => 'sanitize_text_field',
                'bordercolor' => 'swpf_sanitize_color',
            ),
            'rating' => array(
                'textcolor' => 'swpf_sanitize_color',
                'textcolorhover' => 'swpf_sanitize_color',
                'textcoloractive' => 'swpf_sanitize_color',
            ),
            'filterbutton' => array(
                'bgcolor' => 'swpf_sanitize_color',
                'bordercolor' => 'swpf_sanitize_color',
                'textcolor' => 'swpf_sanitize_color',
                'bgcolor_hov' => 'swpf_sanitize_color',
                'bordercolor_hov' => 'swpf_sanitize_color',
                'textcolor_hov' => 'swpf_sanitize_color',
                'borderradius' => 'swpf_sanitize_number',
                'fontsize' => 'swpf_sanitize_number',
                'padding' => array(
                    'top' => 'swpf_sanitize_number',
                    'right' => 'swpf_sanitize_number',
                    'bottom' => 'swpf_sanitize_number',
                    'left' => 'swpf_sanitize_number',
                )
            ),
            'applybutton' => array(
                'bgcolor' => 'swpf_sanitize_color',
                'bordercolor' => 'swpf_sanitize_color',
                'textcolor' => 'swpf_sanitize_color',
                'bgcolor_hov' => 'swpf_sanitize_color',
                'bordercolor_hov' => 'swpf_sanitize_color',
                'textcolor_hov' => 'swpf_sanitize_color',
                'borderradius' => 'swpf_sanitize_number',
                'fontsize' => 'swpf_sanitize_number',
                'padding' => array(
                    'top' => 'swpf_sanitize_number',
                    'right' => 'swpf_sanitize_number',
                    'bottom' => 'swpf_sanitize_number',
                    'left' => 'swpf_sanitize_number',
                )
            ),
            'searchfilter' => array(
                'bgcolor' => 'swpf_sanitize_color',
                'bordercolor' => 'swpf_sanitize_color',
                'textcolor' => 'swpf_sanitize_color',
                'borderradius' => 'swpf_sanitize_number',
                'padding' => array(
                    'top' => 'swpf_sanitize_number',
                    'right' => 'swpf_sanitize_number',
                    'bottom' => 'swpf_sanitize_number',
                    'left' => 'swpf_sanitize_number',
                )
            ),
            'filterbox' => array(
                'height' => 'swpf_sanitize_number',
                'bgcolor' => 'swpf_sanitize_color',
                'textcolor' => 'swpf_sanitize_color',
                'borderradius' => 'swpf_sanitize_number',
                'bordercolor' => 'swpf_sanitize_color',
                'borderwidth' => 'swpf_sanitize_number',
                'padding' => array(
                    'top' => 'swpf_sanitize_number',
                    'bottom' => 'swpf_sanitize_number',
                    'left' => 'swpf_sanitize_number',
                    'right' => 'swpf_sanitize_number'
                ),
                'shadow_x' => 'swpf_sanitize_number',
                'shadow_y' => 'swpf_sanitize_number',
                'shadow_blur' => 'swpf_sanitize_number',
                'shadow_spread' => 'swpf_sanitize_number',
                'shadow_color' => 'swpf_sanitize_color',
                'spacing' => 'swpf_sanitize_number',
                'itemspacing' => 'swpf_sanitize_number',
                'enablebottomborder' => 'swpf_sanitize_checkbox'
            ),
            'heading' => array(
                'bgcolor' => 'swpf_sanitize_color',
                'textcolor' => 'swpf_sanitize_color',
                'borderradius' => 'swpf_sanitize_number',
                'bordercolor' => 'swpf_sanitize_color',
                'bordertop' => 'swpf_sanitize_number',
                'borderbottom' => 'swpf_sanitize_number',
                'borderleft' => 'swpf_sanitize_number',
                'borderright' => 'swpf_sanitize_number',
                'marginbottom' => 'swpf_sanitize_number',
                'padding' => array(
                    'top' => 'swpf_sanitize_number',
                    'bottom' => 'swpf_sanitize_number',
                    'left' => 'swpf_sanitize_number',
                    'right' => 'swpf_sanitize_number'
                ),
            ),
            'widgetarea' => array(
                'column' => 'swpf_sanitize_number',
            ),
            'primary_color' => 'swpf_sanitize_color',
        );
        $return['include_exclude_filter']['order_by'] = 'sanitize_text_field';
        $return['include_terms']['order_by'] = array();

        $taxonomies = swpf_get_taxonomies();
        $taxonomies_keys = array_keys($taxonomies);

        if ($taxonomies_keys) {
            foreach ($taxonomies_keys as $key) {
                $return['enable'][$key] = 'swpf_sanitize_checkbox';
                $return['title_label'][$key] = 'sanitize_text_field';
                $return['show_count'][$key] = 'swpf_sanitize_checkbox';
                $return['display_type'][$key] = 'sanitize_text_field';
                $return['placeholder_txt'][$key] = 'sanitize_text_field';
                $return['multiselect_logic_operator'][$key] = 'sanitize_text_field';
                if (!($key == 'product_visibility')) {
                    $return['orderby'][$key] = 'sanitize_text_field';
                    $return['order_type'][$key] = 'sanitize_text_field';
                    $return['include_exclude_filter'][$key] = 'sanitize_text_field';
                    $return['include_terms'][$key] = array();
                    $return['exclude_terms'][$key] = array();
                } else {
                    $return['include_exclude_filter'][$key] = 'sanitize_text_field';
                    $return['include_terms'][$key] = array();
                }
                $return['field_orientation'][$key] = 'sanitize_text_field';
                $return['hide_term_name'][$key] = 'swpf_sanitize_checkbox';
                $return['hide_term_name'][$key] = 'swpf_sanitize_checkbox';
                $return['terms_customize'][$key] = array();
                $return['search_filter'][$key] = 'swpf_sanitize_checkbox';
                $return['list_order'][$key] = 'sanitize_text_field';
            }
        }

        return $return;
    }

    public function register_translation_strings() {
        $query = new WP_Query(
            array(
                'post_type' => 'swpf-product-filter',
                'posts_per_page' => -1,
                'post_status' => 'publish'
            )
        );

        if ($query->have_posts()):
            while ($query->have_posts()):
                $query->the_post();
                $postid = get_the_ID();
                $filter_title = get_the_title($postid);
                $settings = get_post_meta($postid, 'swpf_settings', true);
                $settings = Super_Product_Filter_Admin::recursive_parse_args($settings, self::default_settings_values());
                $order_lists = isset($settings['list_order']) && $settings['list_order'] ? $settings['list_order'] : array();
                $string_array = array();

                foreach ($order_lists as $tax_name) {
                    $string_array['Taxonomy Name ' . $tax_name] = isset($settings['title_label'][$tax_name]) ? esc_attr($settings['title_label'][$tax_name]) : '';
                    if (isset($settings['placeholder_txt'][$tax_name]) && ($tax_name == 'product_cat' || $tax_name == 'multi_select')) {
                        $string_array['Taxonomy Placeholder ' . $tax_name] = $settings['placeholder_txt'][$tax_name];
                    }

                    $termArgs = array(
                        'taxonomy' => $tax_name,
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'hierarchical' => 0,
                        'hide_empty' => 0,
                    );

                    $allTerms = get_terms($termArgs);
                    foreach ($allTerms as $tval) {
                        if (isset($tval->term_id)) {
                            if (isset($settings['terms_customize'][$tax_name][$tval->term_id]['term_name'])) {
                                $string_array['Term Name ' . esc_html($tax_name) . ' ' . $tval->term_id] = $settings['terms_customize'][$tax_name][$tval->term_id]['term_name'];
                            }
                        }
                    }
                }

                foreach ($string_array as $title => $strings) {
                    if (has_action('wpml_register_single_string')) {
                        do_action('wpml_register_single_string', 'Super Product Filter', $filter_title . ' - ' . $title, $strings);
                    }
                }

            endwhile;
        endif;
        wp_reset_postdata();
    }

    public static function checkbox_settings() {
        $return = array(
            'enable' => array(
                'price_range' => 'off',
                'reviews' => 'off',
                'ratings' => 'off',
                'on_sale' => 'off',
                'in_stock' => 'off',
            ),
            'config' => array(
                'indent_cat' => 'off',
                'show_filter_list_toggle' => 'off',
                'scroll_after_filter' => 'off',
            ),
            'side_menu' => array(
                'panel_show_scrollbar' => 'off',
            ),
            'filterbox' => array(
                'enablebottomborder' => 'off',
            )
        );
        $taxonomies = swpf_get_taxonomies(); // get all taxonomies object
        $taxonomies_keys = array_keys($taxonomies); // get only the taxo name array
        if ($taxonomies_keys) {
            foreach ($taxonomies_keys as $key) {
                $return['enable'][$key] = 'off';
                $return['show_count'][$key] = 'off';
                $return['hide_term_name'][$key] = 'off';
                $return['search_filter'][$key] = 'off';
            }
        }

        return $return;
    }

    public function show_custom_term_options() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (wp_verify_nonce(swpf_get_post('wp_nonce'), 'swpf-backend-ajax-nonce')) {
            $key = swpf_get_post('tax_key');
            $tax_id = swpf_get_post('tax_id');
            $terms_customize_settings = swpf_get_post('terms_customize_settings');
            $display_type = swpf_get_post('display_type');
            ?>
            <form class="swpf-custom-terms-options swpf-custom-terms-options-active swpf-tax-key-<?php echo esc_attr($key) ?> swpf-field-settings-display-type-<?php echo esc_attr($display_type) ?>" data-tax-id="<?php echo esc_attr($tax_id); ?>">
                <span class="swpf-custom-terms-option-close">X</span>
                <div class="swpf-custom-terms-field-outer-wrap">
                    <div class="swpf-custom-terms-field-grid">
                        <?php
                        $termArgs = array(
                            'taxonomy' => $key,
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'hierarchical' => 0,
                            'hide_empty' => 0,
                        );
                        $allTerms = get_terms($termArgs);
                        if (isset($allTerms) && !empty($allTerms)) {
                            foreach ($allTerms as $tkey => $tval) {
                                ?>
                                <div class="swpf-custom-term-field-wrap">
                                    <h4><?php echo esc_html(ucwords(str_replace('-', ' ', $tval->name))); ?></h4>

                                    <div class="swpf-custom-term-field swpf-field-wrap">
                                        <label><?php esc_html_e('Term Name', 'super-product-filter'); ?></label>
                                        <input type="text" name="swpf_settings[terms_customize][<?php echo esc_attr($key); ?>][<?php echo esc_attr($tval->term_id) ?>][term_name]" value="<?php echo isset($terms_customize_settings[$tval->term_id]['term_name']) ? esc_attr($terms_customize_settings[$tval->term_id]['term_name']) : ''; ?>">
                                    </div>

                                    <div class="swpf-custom-term-field swpf-field-wrap swpf-custom-color">
                                        <label><?php esc_html_e('Color', 'super-product-filter'); ?></label>
                                        <input type="text" data-alpha-enabled="true" data-alpha-color-type="hex" class="color-picker swpf-color-picker" name="swpf_settings[terms_customize][<?php echo esc_attr($key); ?>][<?php echo esc_attr($tval->term_id) ?>][term_color]" value="<?php echo isset($terms_customize_settings[$tval->term_id]['term_color']) ? esc_attr($terms_customize_settings[$tval->term_id]['term_color']) : ''; ?>">
                                    </div>

                                    <div class="swpf-custom-term-field swpf-field-wrap swpf-custom-image">
                                        <label><?php esc_html_e('Upload Custom Image', 'super-product-filter'); ?></label>
                                        <?php
                                        $has_image = false;
                                        $upload_class = "";
                                        if (isset($terms_customize_settings[$tval->term_id]['term_image']) && !empty($terms_customize_settings[$tval->term_id]['term_image'])) {
                                            $has_image = true;
                                            $upload_class = " swpf-image-uploaded";
                                        }
                                        ?>
                                        <div class="swpf-icon-image-uploader<?php echo esc_attr($upload_class); ?>">
                                            <div class="swpf-custom-menu-image-icon">
                                                <?php if ($has_image) { ?>
                                                    <img src="<?php echo esc_attr(isset($terms_customize_settings[$tval->term_id]['term_image']) ? esc_url($terms_customize_settings[$tval->term_id]['term_image']) : ''); ?>" width="100" />
                                                <?php } ?>
                                            </div>
                                            <div class="swpf-custom-img-action-field">
                                                <div class="swpf-image-remove"><?php esc_html_e('Remove', 'super-product-filter'); ?></div>
                                                <div class="swpf-image-upload"><?php esc_html_e('Upload', 'super-product-filter') ?></div>
                                            </div>
                                            <input type="hidden" class="swpf-upload-background-url" name="swpf_settings[terms_customize][<?php echo esc_attr($key) ?>][<?php echo esc_attr($tval->term_id) ?>][term_image]" value="<?php echo isset($terms_customize_settings[$tval->term_id]['term_image']) ? esc_url($terms_customize_settings[$tval->term_id]['term_image']) : ''; ?>" />
                                        </div> <!-- swpf-icon-image-uploader -->
                                    </div>
                                </div> <!-- swpf-custom-term-field-wrap -->
                                <?php
                            }
                        } // not empty terms 
                        else {
                            ?>
                            <div class="swpf-custom-term-field-wrap">
                                <?php esc_html_e('Sorry, Currently there are no any terms available.', 'super-product-filter'); ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div> <!-- swpf-custom-terms-field-grid -->
                    <div class="swpf-custom-terms-footer">
                        <p><?php esc_html_e('Please save the settings for any changes.', 'super-product-filter'); ?></p>
                        <button type="button" class="button button-primary button-large swpf-save-term-options"><?php esc_html_e('Save Settings', 'super-product-filter'); ?></button>
                    </div>
                </div> <!-- swpf-custom-terms-options -->
            </form> <!-- swpf-custom-terms-options -->
            <?php
            die();
        }
    }

    public function save_custom_term_options() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (wp_verify_nonce(swpf_get_post('wp_nonce'), 'swpf-backend-ajax-nonce')) {
            $tax_id = swpf_get_post('tax_id');
            $data = swpf_get_post('form_data');
            $settings = get_post_meta($tax_id, 'swpf_settings', true);

            if (!$settings) {
                $settings = self::default_settings_values();
            } else {
                $settings = Super_Product_Filter_Admin::recursive_parse_args($settings, self::default_settings_values());
            }
            $terms_customize = $data['swpf_settings[terms_customize'];
            $settings['terms_customize'][array_key_first($terms_customize)] = $data['swpf_settings[terms_customize'][array_key_first($terms_customize)];

            $settings = Super_Product_Filter_Admin::recursive_parse_args($settings, self::checkbox_settings());
            $settings = Super_Product_Filter_Admin::sanitize_array($settings, self::sanitize_settings_rules());
            update_post_meta($tax_id, 'swpf_settings', $settings);
            die();
        }
    }

    public static function swpf_animations() {
        $animations = [
            'show_animation' => array(
                'Bouncing Entrances' => array('bounceIn', 'bounceInDown', 'bounceInLeft', 'bounceInRight', 'bounceInUp'),
                'Fading Entrances' => array('fadeIn', 'fadeInDown', 'fadeInDownBig', 'fadeInLeft', 'fadeInLeftBig', 'fadeInRight', 'fadeInRightBig', 'fadeInUp', 'fadeInUpBig'),
                'Slide Entrance' => array('slideInUp', 'slideInDown', 'slideInLeft', 'slideInRight'),
                'Zoom Entrances' => array('zoomIn', 'zoomInDown', 'zoomInLeft', 'zoomInRight', 'zoomInUp'),
                'Flip Entrances' => array('flipInX', 'flipInY'),
                'Lightspeed Entrances' => array('lightSpeedInLeft', 'lightSpeedInRight'),
                'Back Entrances' => array('backInDown', 'backInLeft', 'backInRight', 'backInUp'),
                'Rotate Entrances' => array('rotateIn', 'rotateInDownLeft', 'rotateInDownRight', 'rotateInUpLeft', 'rotateInUpRight', 'rollIn')
            ),
            'hide_animation' => array(
                'Bouncing Exits' => array('bounceOut', 'bounceOutDown', 'bounceOutLeft', 'bounceOutRight', 'bounceOutUp'),
                'Fading Exits' => array('fadeOut', 'fadeOutDown', 'fadeOutDownBig', 'fadeOutLeft', 'fadeOutLeftBig', 'fadeOutRight', 'fadeOutRightBig', 'fadeOutUp', 'fadeOutUpBig'),
                'Slide Exits' => array('slideOutUp', 'slideOutDown', 'slideOutLeft', 'slideOutRight'),
                'Zoom Exits' => array('zoomOut', 'zoomOutDown', 'zoomOutLeft', 'zoomOutRight', 'zoomOutUp'),
                'Flip Exits' => array('flipOutX', 'flipOutY'),
                'Lightspeed Exits' => array('lightSpeedOutLeft', 'lightSpeedOutRight'),
                'Back Exits' => array('backOutDown', 'backOutLeft', 'backOutRight', 'backOutUp'),
                'Rotate Exits' => array('rotateOut', 'rotateOutDownLeft', 'rotateOutDownRight', 'rotateOutUpLeft', 'rotateOutUpRight', 'rollOut')
            ),
            'hover_animation' => array(
                'Grow' => 'hvr-grow',
                'Shrink' => 'hvr-shrink',
                'Pulse' => 'hvr-pulse',
                'Pulse Grow' => 'hvr-pulse-grow',
                'Pulse Shrink' => 'hvr-pulse-shrink',
                'Push' => 'hvr-push',
                'Pop' => 'hvr-pop',
                'Bounce In' => 'hvr-bounce-in',
                'Bounce Out' => 'hvr-bounce-out',
                'Tilt' => 'hvr-rotate',
                'Grow Tilt' => 'hvr-grow-rotate',
                'Float' => 'hvr-float',
                'Sink' => 'hvr-sink',
                'Bob' => 'hvr-bob',
                'Hang' => 'hvr-hang',
                'Skew' => 'hvr-skew',
                'Skew Forward' => 'hvr-skew-forward',
                'Skew Backward' => 'hvr-skew-backward',
                'Wobble Horizontal' => 'hvr-wobble-horizontal',
                'Wobble Vertical' => 'hvr-wobble-vertical',
                'Wobble To Bottom Right' => 'hvr-wobble-to-bottom-right',
                'Wobble To Top Right' => 'hvr-wobble-to-top-right',
                'Wobble Top' => 'hvr-wobble-top',
                'Wobble Bottom' => 'hvr-wobble-bottom',
                'Wobble Skew' => 'hvr-wobble-skew',
                'Buzz' => 'hvr-buzz',
                'Buzz Out' => 'hvr-buzz-out',
                'Forward' => 'hvr-forward',
                'Backward' => 'hvr-backward'
            ),
        ];
        return $animations;
    }

}

new Super_Product_Filter_Metabox();