<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class Super_Product_Filter_Admin {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->include_files();

        // Create a Setting Page
        add_action('admin_menu', array($this, 'register_submenu_page'));

        add_action('admin_footer', array($this, 'alert_message'));
        add_action('admin_footer', array($this, 'icon_choices'));
    }

    public function include_files() {
        include SWPF_PATH . 'admin/inc/cpt/init.php';
        include SWPF_PATH . 'admin/inc/general/init.php';
        include SWPF_PATH . 'admin/inc/google-fonts-list.php';
        include SWPF_PATH . 'admin/inc/helper-functions.php';
        include SWPF_PATH . 'admin/inc/icon-manager.php';
        include SWPF_PATH . 'admin/inc/block.php';
        include SWPF_PATH . 'admin/inc/elementor/loader.php';
        include SWPF_PATH . 'admin/inc/import-export/init.php';
    }

    public function enqueue_styles() {
        global $post_type, $pagenow, $current_screen;
        if ('swpf-product-filter' == $post_type || $pagenow == 'widgets.php' || in_array($current_screen->id, array('swpf-product-filter_page_swpf-general-settings', 'swpf-product-filter_page_swpf-metafield-settings'))) {
            wp_enqueue_style('fontawesome-6.3.0', SWPF_URL . 'public/css/fontawesome-6.3.0.css', array(), $this->version);
            wp_enqueue_style('eleganticons', SWPF_URL . 'public/css/eleganticons.css', array(), $this->version);
            wp_enqueue_style('essentialicon', SWPF_URL . 'public/css/essentialicon.css', array(), $this->version);
            wp_enqueue_style('materialdesignicons', SWPF_URL . 'public/css/materialdesignicons.css', array(), $this->version);
            wp_enqueue_style('icofont', SWPF_URL . 'public/css/icofont.css', array(), $this->version);

            /* Select2 */
            wp_enqueue_style('jquery-select2', SWPF_URL . 'admin/css/select2.min.css', array(), $this->version);
            wp_enqueue_style('wp-color-picker');

            wp_enqueue_style('jquery-ui-slider', SWPF_URL . 'public/vendor/slider-ui/slider-ui.css', array(), $this->version, 'all');

            wp_enqueue_style('selectize', SWPF_URL . 'public/vendor/selectize/selectize.css', array(), $this->version, 'all');
            wp_enqueue_style('chosen', SWPF_URL . 'public/vendor/chosen/chosen.css', array(), $this->version);

            wp_enqueue_style($this->plugin_name, SWPF_URL . 'admin/css/admin.css', array(), $this->version, 'all');
        }
    }

    public function enqueue_scripts() {
        global $post_type, $pagenow, $current_screen;
        if ('swpf-product-filter' == $post_type || $pagenow == 'widgets.php') {
            wp_enqueue_media();
            wp_enqueue_script('wp-color-picker');

            wp_enqueue_script('selectize', SWPF_URL . 'public/vendor/selectize/selectize.js', array('jquery'), $this->version, true);

            /* Select2 */
            wp_enqueue_script('jquery-select2', SWPF_URL . 'admin/js/select2.min.js', array('jquery'), $this->version, true);

            // CodeMirror Enqueue
            wp_enqueue_code_editor(array('type' => 'text/html'));

            /* Enqueue jQuery Chosen */
            wp_enqueue_script('chosen-script', SWPF_URL . 'public/vendor/chosen/chosen.jquery.js', array('jquery'), $this->version, true);

            wp_enqueue_script('wp-color-picker-alpha-min', SWPF_URL . 'public/vendor/wp-color-picker-alpha/wp-color-picker-alpha.min.js', array('wp-color-picker'), $this->version, true);

            /* Jquery Condition */
            wp_enqueue_script('jquery-condition', SWPF_URL . 'admin/js/jquery-condition.js', array('jquery'), $this->version, true);

            wp_enqueue_script($this->plugin_name, SWPF_URL . 'admin/js/admin.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-slider'), $this->version, false);

            $admin_var = array(
                'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
                'ajax_nonce' => wp_create_nonce('swpf-backend-ajax-nonce'),
                'admin_url' => esc_url(admin_url('post.php'))
            );
            if (swpf_get_post('tab') == 'swpf') {
                $admin_var['swpf_settings_save_link'] = 'admin.php?page=wc-settings&tab=swpf&settings_saved=1';
            }

            /* Send php values to JS script */
            wp_localize_script($this->plugin_name, 'swpf_admin_js_obj', $admin_var);
        }

        if ('swpf-product-filter' == $post_type) {
            wp_enqueue_script('swpf-metabox-settings', SWPF_URL . 'admin/js/swpf-metabox.js', array('jquery'), $this->version, true);
            wp_localize_script('swpf-metabox-settings', 'swpf_admin_metabox_obj', array(
                'posturl' => admin_url('post.php')
            ));
        }

        if ($current_screen->id == 'swpf-product-filter_page_swpf-general-settings') {
            wp_enqueue_script('swpf-general-settings', SWPF_URL . 'admin/js/swpf-general.js', array('jquery'), $this->version, true);
            wp_localize_script('swpf-general-settings', 'swpf_admin_general_obj', array(
                'ajaxurl' => admin_url('admin-ajax.php')
            ));
        }
    }

    public static function get_dropdown_indent($parent_id, $categories, $selected_ids, $cat_ids, $child_count = -1) {
        $html = '';
        $loop_categories = array_filter($categories, function ($cats) use ($parent_id) {
            return $cats->parent == $parent_id;
        });

        $selected_ids = isset($selected_ids) && !empty($selected_ids) ? $selected_ids : [];
        if (count($loop_categories)) {
            $child_count++;
            $visible_slugs = isset($cat_ids) ? $cat_ids : [];

            if ($loop_categories) {
                foreach ($loop_categories as $cat) {
                    $current_html = '';
                    if (in_array($cat->term_id, $selected_ids)) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    $child_html = self::get_dropdown_indent($cat->term_id, $categories, $selected_ids, $cat_ids, $child_count);

                    if (in_array($cat->term_id, $visible_slugs)) {
                        $current_html .= '<option value="' . esc_attr($cat->term_id) . '" ' . esc_attr($selected) . '>';
                        $i = 0;
                        while ($i < $child_count) {
                            $current_html .= '- ';
                            $i++;
                        }
                        $current_html .= esc_html(ucwords(str_replace('-', ' ', $cat->name)));
                        $current_html .= '</option>';
                        if (strlen($child_html)) {
                            $current_html .= $child_html;
                        }
                    }
                    $html .= $current_html;
                }
            }
        }
        return $html;
    }

    public function register_submenu_page() {
        add_submenu_page('edit.php?post_type=swpf-product-filter', esc_html__('Documentation', 'super-product-filter'), esc_html__('Documentation', 'super-product-filter'), 'manage_options', esc_url_raw('https://hashthemes.com/documentation/super-woocommerce-product-filter-documentation/'));
    }

    public static function recursive_parse_args($args, $defaults) {
        $new_args = (array) $defaults;
        if ($args) {
            foreach ($args as $key => $value) {
                if (is_array($value) && isset($new_args[$key])) {
                    $new_args[$key] = self::recursive_parse_args($value, $new_args[$key]);
                } else {
                    $new_args[$key] = $value;
                }
            }
        }

        return $new_args;
    }

    public static function sanitize_array($array = array(), $sanitize_rule = array()) {
        $new_args = (array) $array;

        if ($array) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $new_args[$key] = self::sanitize_array($value, isset($sanitize_rule[$key]) ? $sanitize_rule[$key] : 'sanitize_text_field');
                } else {
                    if (isset($sanitize_rule[$key]) && !empty($sanitize_rule[$key]) && function_exists($sanitize_rule[$key])) {
                        $sanitize_type = $sanitize_rule[$key];
                        $new_args[$key] = $sanitize_type($value);
                    } else {
                        $new_args[$key] = $value;
                    }
                }
            }
        }

        return $new_args;
    }

    public function alert_message() {
        ?>
        <div class="swpf-alert">
            <span class="swpf-alert-message"></span>
            <i class="icofont-close-line"></i>
        </div>
        <?php
    }

    public function icon_choices() {
        global $current_screen;
        if ('swpf-product-filter' == $current_screen->post_type) {
            ?>
            <div id="swpf-icon-box" class="swpf-icon-box">
                <div class="swpf-icon-search">
                    <select>
                        <?php
                        //See customizer-icon-manager.php file
                        $icons = apply_filters('swpf_register_icon', array());

                        if ($icons && is_array($icons)) {
                            foreach ($icons as $icon) {
                                if ($icon['name'] && $icon['label']) {
                                    ?>
                                    <option value="<?php echo esc_attr($icon['name']); ?>"><?php echo esc_html($icon['label']); ?></option>
                                    <?php
                                }
                            }
                        }
                        ?>

                    </select>
                    <input type="text" class="swpf-icon-search-input" placeholder="<?php echo esc_html__('Type to filter', 'super-product-filter'); ?>" />
                </div>
                <?php
                if ($icons && is_array($icons)) {
                    $active_class = ' active';
                    foreach ($icons as $icon) {
                        $icon_name = isset($icon['name']) && $icon['name'] ? $icon['name'] : '';
                        $icon_prefix = isset($icon['prefix']) && $icon['prefix'] ? $icon['prefix'] : '';
                        $icon_displayPrefix = isset($icon['displayPrefix']) && $icon['displayPrefix'] ? $icon['displayPrefix'] . ' ' : '';
                        ?>

                        <ul class="swpf-icon-list <?php echo esc_attr($icon_name) . esc_attr($active_class); ?>">
                            <?php
                            $icon_array = isset($icon['icons']) ? $icon['icons'] : '';
                            if (is_array($icon_array)) {
                                foreach ($icon_array as $icon_id) {
                                    ?>
                                    <li><i class="<?php echo esc_attr($icon_displayPrefix) . esc_attr($icon_prefix) . esc_attr($icon_id); ?>"></i></li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                        <?php
                        $active_class = '';
                    }
                }
                ?>

            </div>
            <?php
        }
    }

    public static function icon_field($inputName = '', $iconName = '') {
        ?>
        <div class="swpf-icon-box-wrap">
            <div class="swpf-selected-icon">
                <i class="<?php echo esc_attr($iconName); ?>"></i>
                <span><i class="swpf-down-icon"></i></span>
            </div>

            <input type="hidden" name="<?php echo esc_attr($inputName); ?>" value="<?php echo esc_attr($iconName); ?>" />
        </div>
        <?php
    }

    public static function get_order_by_options() {
        return array(
            'menu_order' => esc_html__('Default', 'super-product-filter'),
            'date' => esc_html__('Latest', 'super-product-filter'),
            'date-asc' => esc_html__('Oldest', 'super-product-filter'),
            'price' => esc_html__('Price: Low to High', 'super-product-filter'),
            'price-desc' => esc_html__('Price: High to Low', 'super-product-filter'),
            'title' => esc_html__('Title: A to Z', 'super-product-filter'),
            'title-desc' => esc_html__('Title: Z to A', 'super-product-filter'),
            'rand' => esc_html__('Random', 'super-product-filter'),
        );
    }

    public static function compare_to_rule($value, $rule) {
        $result = ($value == $rule['value']);

        // Allow "all" to match any value.
        if ($rule['value'] === 'all') {
            $result = true;
        }

        // Reverse result for "!=" operator.
        if ($rule['operator'] === '!=') {
            return !$result;
        }
        return $result;
    }

}
