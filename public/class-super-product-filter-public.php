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

    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->includes();
    }

    public function includes() {
        include SWPF_PATH . 'public/inc/region-markers.php';
        include SWPF_PATH . 'public/inc/seo.php';
        include SWPF_PATH . 'public/inc/filter/general.php';
        include SWPF_PATH . 'public/inc/filter/init.php';
    }

    public function enqueue_styles() {
        wp_enqueue_style('swpf-loaders', SWPF_URL . 'public/css/loaders.css', array(), $this->version);
        wp_enqueue_style('swpf-hover', SWPF_URL . 'public/css/hover-min.css', array(), $this->version);

        /*
         * Registered, not enqueued. Each preset pulls in only the families it
         * uses as it renders; the filter's own chrome needs none of them.
         */
        $swpf_icon_css = array(
            'fontawesome-6.3.0' => 'public/css/fontawesome-6.3.0.css',
            'eleganticons' => 'public/css/eleganticons.css',
            'essentialicon' => 'public/css/essentialicon.css',
            'icofont' => 'public/css/icofont.css',
            'materialdesignicons' => 'public/css/materialdesignicons.css',
        );

        foreach ($swpf_icon_css as $swpf_handle => $swpf_path) {
            wp_register_style($swpf_handle, SWPF_URL . $swpf_path, array(), $this->version);
        }

        wp_enqueue_style('jquery-ui-slider', SWPF_URL . 'public/vendor/slider-ui/slider-ui.css', array(), $this->version, 'all');
        wp_enqueue_style('chosen', SWPF_URL . 'public/vendor/chosen/chosen.css', '', $this->version);

        wp_enqueue_style('swpf-animate', SWPF_URL . 'public/css/animate.css', array(), $this->version);

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/style.css', array(), $this->version);
        wp_add_inline_style($this->plugin_name, wp_strip_all_tags(self::swpf_dynamic_styles()));

        wp_enqueue_style('swpf-fonts', swpf_fonts_url(), array(), $this->version);
    }

    public function enqueue_scripts() {
        global $wp_query;

        /* enable this only when woo range slider is enabled */
        wp_enqueue_script('wc-jquery-ui-touchpunch');
        wp_enqueue_script('wc-price-slider');
        /* enable this only when woo range slider is enabled */

        /* Enqueue jQuery Chosen */
        wp_enqueue_script('chosen-script', SWPF_URL . 'public/vendor/chosen/chosen.jquery.js', array('jquery'), $this->version, true);

        $js_obj = array(
            'plugin_url' => WP_PLUGIN_URL,
        );
        /*
         * The side menu scrollbar option renders swpf-scrollbar-on and the script
         * calls mCustomScrollbar, but the library was only ever localized, never
         * registered, so the call threw on every page load.
         */
        wp_enqueue_style('jquery-mCustomScrollbar', SWPF_URL . 'public/vendor/mcscrollbar/jquery.mCustomScrollbar.css', array(), $this->version);
        wp_enqueue_script('jquery-mousewheel', SWPF_URL . 'public/vendor/mcscrollbar/jquery.mousewheel.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script('jquery-mCustomScrollbar', SWPF_URL . 'public/vendor/mcscrollbar/jquery.mCustomScrollbar.js', array('jquery', 'jquery-mousewheel'), $this->version, true);

        wp_localize_script('jquery-mCustomScrollbar', 'swpf_js_obj', $js_obj);

        $front_var = array(
            'ajax_nonce' => wp_create_nonce('swpf-frontend-ajax-nonce'),
            'ajax_url' => esc_url(admin_url('admin-ajax.php')),
            'wcLinks' => get_option('woocommerce_permalinks'),
            'shopUrl' => wc_get_page_permalink('shop'),
            'queryVars' => $GLOBALS['wp_query']->query_vars,
            'compat_mode' => Super_Product_Filter_General_Settings::is_compat_mode() ? 1 : 0
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

    public static function swpf_dynamic_styles() {
        $query = new WP_Query(
            array(
                'post_type' => 'swpf-product-filter',
                'posts_per_page' => -1,
                'post_status' => 'publish'
            )
        );

        $swpf_css = '';

        if ($query->have_posts()):
            while ($query->have_posts()):
                $query->the_post();
                $swpf_css_vars = '';
                $postid = get_the_ID();
                $swpf_settings = get_post_meta($postid, 'swpf_settings', true);
                $swpf_css_class_id = '.swpf-filter-id-' . esc_attr($postid) . ', .swpf-header-filters-' . esc_attr($postid) . ', .swpf-sidemenu-wrapper-' . esc_attr($postid);

                if (!$swpf_settings) {
                    return;
                } else {
                    $swpf_settings = Super_Product_Filter_Admin::recursive_parse_args($swpf_settings, Super_Product_Filter_Metabox::default_settings_values());
                }

                if ($swpf_settings['side_menu']['panel_width']) {
                    $swpf_css_vars .= "--swpf-panel-width:{$swpf_settings['side_menu']['panel_width']}{$swpf_settings['side_menu']['panel_width_unit']};";
                }

                if ($swpf_settings['side_menu']['offset_left']) {
                    $swpf_css_vars .= "--swpf-offset-left:{$swpf_settings['side_menu']['offset_left']}px;";
                }

                if ($swpf_settings['side_menu']['offset_right']) {
                    $swpf_css_vars .= "--swpf-offset-right:{$swpf_settings['side_menu']['offset_right']}px;";
                }

                if ($swpf_settings['side_menu']['offset_top']) {
                    $swpf_css_vars .= "--swpf-offset-top:{$swpf_settings['side_menu']['offset_top']}px;";
                }

                if ($swpf_settings['side_menu']['offset_bottom']) {
                    $swpf_css_vars .= "--swpf-offset-bottom:{$swpf_settings['side_menu']['offset_bottom']}px;";
                }

                if ($swpf_settings['side_menu']['toggle_button_size']) {
                    $swpf_css_vars .= "--swpf-trigger-btn-size:{$swpf_settings['side_menu']['toggle_button_size']}px;";
                }

                if ($swpf_settings['side_menu']['icon_size']) {
                    $swpf_css_vars .= "--swpf-trigger-btn-icon-size:{$swpf_settings['side_menu']['icon_size']}px;";
                }

                if ($swpf_settings['side_menu']['image_size']) {
                    $swpf_css_vars .= "--swpf-trigger-btn-image-size:{$swpf_settings['side_menu']['image_size']}%;";
                }

                if ($swpf_settings['side_menu']['hamburger_width']) {
                    $swpf_css_vars .= "--swpf-hamburger-width:{$swpf_settings['side_menu']['hamburger_width']}px;";
                }

                if ($swpf_settings['side_menu']['hamburger_spacing']) {
                    $swpf_css_vars .= "--swpf-hamburger-spacing:{$swpf_settings['side_menu']['hamburger_spacing']}px;";
                }

                if ($swpf_settings['side_menu']['hamburger_thickness']) {
                    $swpf_css_vars .= "--swpf-hamburger-thickness:{$swpf_settings['side_menu']['hamburger_thickness']}px;";
                }


                if (is_numeric($swpf_settings['side_menu']['button_shadow_x'])) {
                    $swpf_css_vars .= "--swpf-trigger-btn-shadow-x:{$swpf_settings['side_menu']['button_shadow_x']}px;";
                }

                if (is_numeric($swpf_settings['side_menu']['button_shadow_y'])) {
                    $swpf_css_vars .= "--swpf-trigger-btn-shadow-y:{$swpf_settings['side_menu']['button_shadow_y']}px;";
                }

                if (is_numeric($swpf_settings['side_menu']['button_shadow_blur'])) {
                    $swpf_css_vars .= "--swpf-trigger-btn-shadow-blur:{$swpf_settings['side_menu']['button_shadow_blur']}px;";
                }

                if ($swpf_settings['side_menu']['button_shadow_color']) {
                    $swpf_css_vars .= "--swpf-trigger-btn-shadow-color:{$swpf_settings['side_menu']['button_shadow_color']};";
                }

                if ($swpf_settings['side_menu']['button_bg_color']) {
                    $swpf_css_vars .= "--swpf-trigger-btn-bg-color:{$swpf_settings['side_menu']['button_bg_color']};";
                }

                if ($swpf_settings['side_menu']['button_hover_bg_color']) {
                    $swpf_css_vars .= "--swpf-trigger-btn-bg-color-hover:{$swpf_settings['side_menu']['button_hover_bg_color']};";
                }

                if ($swpf_settings['side_menu']['button_icon_color']) {
                    $swpf_css_vars .= "--swpf-trigger-btn-icon-color:{$swpf_settings['side_menu']['button_icon_color']};";
                }

                if ($swpf_settings['side_menu']['button_hover_icon_color']) {
                    $swpf_css_vars .= "--swpf-trigger-btn-hover-icon-color:{$swpf_settings['side_menu']['button_hover_icon_color']};";
                }

                if ($swpf_settings['side_menu']['panel_background_color']) {
                    $swpf_css_vars .= "--swpf-panel-background-color:{$swpf_settings['side_menu']['panel_background_color']};";
                }


                if (is_numeric($swpf_settings['side_menu']['scrollbar_width'])) {
                    $swpf_css_vars .= "--swpf-scrollbar-width:{$swpf_settings['side_menu']['scrollbar_width']}px;";
                }

                if ($swpf_settings['side_menu']['scrollbar_drag_rail_color']) {
                    $swpf_css_vars .= "--swpf-scrollbar-drag-rail-color:{$swpf_settings['side_menu']['scrollbar_drag_rail_color']};";
                }

                if ($swpf_settings['side_menu']['scrollbar_drag_bar_color']) {
                    $swpf_css_vars .= "--swpf-scrollbar-drag-bar-color:{$swpf_settings['side_menu']['scrollbar_drag_bar_color']};";
                }

                if ($swpf_settings['widgetarea']['column']) {
                    $swpf_css_vars .= "--swpf-widgetarea-gridcol:{$swpf_settings['widgetarea']['column']};";
                }

                if ($swpf_settings['primary_color']) {
                    $swpf_css_vars .= "--swpf-primary-color:{$swpf_settings['primary_color']};";
                }

                $swpf_colors = array(
                    'checkboxradio' => array(
                        'bgcolor',
                        'bgcolorhov',
                        'bgcoloractive',
                        'bordercolor',
                        'bordercolorhov',
                        'bordercoloractive',
                        'iconcolor',
                    ),
                    'dropdown' => array(
                        'bordercolor',
                        'bgcolor',
                        'textcolor'
                    ),
                    'multiselect' => array(
                        'bordercolor',
                        'bgcolor',
                        'textcolor',
                        'selectedtextcolor',
                        'selectedbgcolor'
                    ),
                    'pricerangeslider' => array(
                        'highlightcolor',
                        'barcolor'),
                    'button' => array(
                        'bgcolor',
                        'bordercolor',
                        'textcolor',
                        'bgcolor_hov',
                        'bordercolor_hov',
                        'textcolor_hov'
                    ),
                    'toggle' => array(
                        'bgcolor',
                        'inactivecolor',
                        'activecolor'
                    ),
                    'color' => 'bordercolor',
                    'image' => 'bordercolor',
                    'rating' => array(
                        'textcolor',
                        'textcolorhover',
                        'textcoloractive'
                    ),
                    'filterbutton' => array(
                        'bgcolor',
                        'bordercolor',
                        'textcolor',
                        'bgcolor_hov',
                        'bordercolor_hov',
                        'textcolor_hov'
                    ),
                    'applybutton' => array(
                        'bgcolor',
                        'bordercolor',
                        'textcolor',
                        'bgcolor_hov',
                        'bordercolor_hov',
                        'textcolor_hov'
                    ),
                    'searchfilter' => array(
                        'bgcolor',
                        'bordercolor',
                        'textcolor',
                    ),
                    'filterbox' => array(
                        'bgcolor',
                        'textcolor',
                        'bordercolor',
                        'shadow_color'
                    ),
                    'heading' => array(
                        'bgcolor',
                        'textcolor',
                        'bordercolor',
                    )
                );

                $swpf_px_size = array(
                    'checkboxradio' => 'size',
                    'dropdown' => 'height',
                    'color' => 'size',
                    'image' => array(
                        'size',
                        'padding'
                    ),
                    'filterbox' => array(
                        'height',
                        'borderradius',
                        'spacing',
                        'itemspacing',
                        'borderwidth',
                        'shadow_x',
                        'shadow_y',
                        'shadow_blur',
                        'shadow_spread'
                    ),
                    'heading' => array(
                        'borderradius',
                        'bordertop',
                        'borderbottom',
                        'borderleft',
                        'borderright',
                        'marginbottom'
                    ),
                    'multiselect' => 'height',
                    'button' => array(
                        'fontsize',
                        'borderradius'
                    ),
                    'filterbutton' => array(
                        'fontsize',
                        'borderradius'
                    ),
                    'applybutton' => array(
                        'fontsize',
                        'borderradius'
                    ),
                    'searchfilter' => array(
                        'borderradius',
                    ),
                );

                $swpf_dimension = array(
                    'button' => 'padding',
                    'filterbutton' => 'padding',
                    'applybutton' => 'padding',
                    'searchfilter' => 'padding',
                    'filterbox' => 'padding',
                    'heading' => 'padding'
                );

                $swpf_select_type = array(
                    'color',
                    'image',
                );

                $swpf_typo = array(
                    'heading_typo' => array(
                        'family',
                        'style',
                        'text_transform',
                        'text_decoration',
                        'size',
                        'letter_spacing',
                        'line_height',
                    ),
                    'content_typo' => array(
                        'family',
                        'style',
                        'text_transform',
                        'text_decoration',
                        'size',
                        'letter_spacing',
                        'line_height',
                    )
                );

                foreach ($swpf_colors as $swpf_key => $val) {
                    if (is_array($val)) {
                        foreach ($val as $new_val) {
                            if ($swpf_settings[$swpf_key][$new_val]) {
                                $swpf_css_vars .= "--swpf-" . str_replace('_', '-', $swpf_key) . '-' . str_replace('_', '-', $new_val) . ":{$swpf_settings[$swpf_key][$new_val]};";
                            }
                        }
                    } else {
                        if ($swpf_settings[$swpf_key][$val]) {
                            $swpf_css_vars .= "--swpf-" . str_replace('_', '-', $swpf_key) . '-' . str_replace('_', '-', $val) . ":{$swpf_settings[$swpf_key][$val]};";
                        }
                    }
                }

                foreach ($swpf_px_size as $swpf_key => $val) {
                    if (is_array($val)) {
                        foreach ($val as $new_val) {
                            if ($swpf_settings[$swpf_key][$new_val]) {
                                $swpf_css_vars .= "--swpf-" . str_replace('_', '-', $swpf_key) . '-' . str_replace('_', '-', $new_val) . ":{$swpf_settings[$swpf_key][$new_val]}px;";
                            }
                        }
                    } else {
                        if ($swpf_settings[$swpf_key][$val]) {
                            $swpf_css_vars .= "--swpf-" . str_replace('_', '-', $swpf_key) . '-' . $val . ":{$swpf_settings[$swpf_key][$val]}px;";
                        }
                    }
                }

                foreach ($swpf_dimension as $swpf_key => $val) {
                    foreach (array('top', 'right', 'bottom', 'left') as $side) {
                        if ($swpf_settings[$swpf_key][$val][$side]) {
                            $swpf_css_vars .= "--swpf-" . str_replace('_', '-', $swpf_key) . "-" . str_replace('_', '-', $val) . "-" . $side . ":{$swpf_settings[$swpf_key][$val][$side]}px;";
                        }
                    }
                }

                foreach ($swpf_typo as $swpf_key => $val) {
                    foreach ($val as $params) {
                        if ($swpf_settings[$swpf_key][$params]) {
                            $unit = '';
                            if (in_array($params, array('size', 'letter_spacing'))) {
                                $unit = 'px';
                            }
                            $value = $swpf_settings[$swpf_key][$params];

                            if ($params == 'style') {
                                if ($value == 'inherit') {
                                    $weight_value = $style_value = 'inherit';
                                } else {
                                    $weight_value = absint($value);
                                    if (strpos($value, 'italic')) {
                                        $style_value = 'italic';
                                    } else {
                                        $style_value = 'normal';
                                    }
                                }
                                $swpf_css_vars .= "--swpf-" . str_replace('_', '-', $swpf_key) . "-weight:{$weight_value};";
                                $swpf_css_vars .= "--swpf-" . str_replace('_', '-', $swpf_key) . "-style:{$style_value};";
                            } else {
                                $swpf_css_vars .= "--swpf-" . str_replace('_', '-', $swpf_key) . "-" . str_replace('_', '-', $params) . ":{$value}{$unit};";
                            }
                        }
                    }
                }

                foreach ($swpf_select_type as $swpf_key) {
                    $colorshape = $swpf_settings[$swpf_key]['shape'];
                    if ($colorshape == 'swpf-round') {
                        $swpf_css_vars .= "--swpf-" . str_replace('_', '-', $swpf_key) . "-borderradius:50%;";
                    }
                }

                $swpf_css .= $swpf_css_class_id . '{' . $swpf_css_vars . '}';

                $panel_width = $panel_unit = '';
                $panel_width = $swpf_settings['side_menu']['panel_width'];
                $panel_unit = $swpf_settings['side_menu']['panel_width_unit'];
                $responsive_width = $swpf_settings['responsive_width'];
                if ($panel_unit == '%') {
                    $panel_width = 768;
                    $panel_unit = 'px';
                }

                if ($panel_width && $panel_unit) {
                    $width = $panel_width . $panel_unit;
                    $swpf_css .= ".swpf-sidemenu-wrapper-{$postid} .swpf-sidemenu-panel{width:{$width}}";
                    $swpf_css .= "@media screen and (max-width:{$width}){
                        .swpf-sidemenu-wrapper-{$postid} .swpf-sidemenu-panel{ width: 100% !important;}
                    }";
                }

                $swpf_css .= "@media screen and (max-width:{$responsive_width}px){
                    .swpf-responsive-sidemenu.swpf-sidemenu-wrapper-{$postid}{ display:block;}
                    .swpf-widget-wrap .swpf-filter-id-{$postid}, .swpf-widget-area .swpf-filter-id-{$postid}{ display:none;}
                }";
            endwhile;
        endif;

        wp_reset_postdata();

        return swpf_css_strip_whitespace($swpf_css);
    }
}
