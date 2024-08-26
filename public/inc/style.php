<?php
defined('ABSPATH') || die();

function swpf_dynamic_styles() {

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
                $swpf_settings = Super_Product_Filter_Admin::recursive_parse_args($swpf_settings, Super_Product_Filter_Admin::default_settings_values());
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

            foreach ($swpf_colors as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $new_val) {
                        if ($swpf_settings[$key][$new_val]) {
                            $swpf_css_vars .= "--swpf-" . str_replace('_', '-', $key) . '-' . str_replace('_', '-', $new_val) . ":{$swpf_settings[$key][$new_val]};";
                        }
                    }
                } else {
                    if ($swpf_settings[$key][$val]) {
                        $swpf_css_vars .= "--swpf-" . str_replace('_', '-', $key) . '-' . str_replace('_', '-', $val) . ":{$swpf_settings[$key][$val]};";
                    }
                }
            }

            foreach ($swpf_px_size as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $new_val) {
                        if ($swpf_settings[$key][$new_val]) {
                            $swpf_css_vars .= "--swpf-" . str_replace('_', '-', $key) . '-' . str_replace('_', '-', $new_val) . ":{$swpf_settings[$key][$new_val]}px;";
                        }
                    }
                } else {
                    if ($swpf_settings[$key][$val]) {
                        $swpf_css_vars .= "--swpf-" . str_replace('_', '-', $key) . '-' . $val . ":{$swpf_settings[$key][$val]}px;";
                    }
                }
            }

            foreach ($swpf_dimension as $key => $val) {
                foreach (array('top', 'right', 'bottom', 'left') as $side) {
                    if ($swpf_settings[$key][$val][$side]) {
                        $swpf_css_vars .= "--swpf-" . str_replace('_', '-', $key) . "-" . str_replace('_', '-', $val) . "-" . $side . ":{$swpf_settings[$key][$val][$side]}px;";
                    }
                }
            }

            foreach ($swpf_typo as $key => $val) {
                foreach ($val as $params) {
                    if ($swpf_settings[$key][$params]) {
                        $unit = '';
                        if (in_array($params, array('size', 'letter_spacing'))) {
                            $unit = 'px';
                        }
                        $value = $swpf_settings[$key][$params];

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
                            $swpf_css_vars .= "--swpf-" . str_replace('_', '-', $key) . "-weight:{$weight_value};";
                            $swpf_css_vars .= "--swpf-" . str_replace('_', '-', $key) . "-style:{$style_value};";
                        } else {
                            $swpf_css_vars .= "--swpf-" . str_replace('_', '-', $key) . "-" . str_replace('_', '-', $params) . ":{$value}{$unit};";
                        }
                    }
                }
            }

            foreach ($swpf_select_type as $key) {
                $colorshape = $swpf_settings[$key]['shape'];
                if ($colorshape == 'swpf-round') {
                    $swpf_css_vars .= "--swpf-" . str_replace('_', '-', $key) . "-borderradius:50%;";
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
