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

        add_action('init', array($this, 'register_post_type'));

        add_action('init', array($this, 'register_translation_strings'), 100);

        add_action('add_meta_boxes', array($this, 'settings_metabox'));
        add_action('save_post', array($this, 'save_metabox_settings'));

        // Ajax Save Post
        add_action('admin_head-post.php', array($this, 'settings_metabox_xhr'));
        add_action('admin_head-post-new.php', array($this, 'settings_metabox_xhr'));
        add_action('save_post', array($this, 'save_metabox_settings_xhr'));

        add_action('widgets_init', array($this, 'widgets_init'), 90);

        /* To add the Custom Column on Custom Post */
        add_filter('manage_swpf-product-filter_posts_columns', array($this, 'columns_head'));
        add_action('manage_swpf-product-filter_posts_custom_column', array($this, 'columns_content'), 10, 2);

        // Create a Setting Page
        add_action('admin_menu', array($this, 'register_submenu_page'));

        add_action('admin_footer', array($this, 'alert_message'));
        add_action('admin_footer', array($this, 'icon_choices'));

        // Process a settings export that generates a .json file of the cart settings
        add_action('admin_init', array($this, 'process_settings_export'));

        // Process a settings import from a json file
        add_action('admin_init', array($this, 'process_settings_import'));

        add_action('wp_ajax_nopriv_swpf_show_custom_term_options', array($this, 'show_custom_term_options'));
        add_action('wp_ajax_swpf_show_custom_term_options', array($this, 'show_custom_term_options'));

        add_action('wp_ajax_nopriv_swpf_save_custom_term_options', array($this, 'save_custom_term_options'));
        add_action('wp_ajax_swpf_save_custom_term_options', array($this, 'save_custom_term_options'));

        add_action('admin_menu', function () {
            remove_meta_box('submitdiv', 'swpf-product-filter', 'side');
            remove_meta_box('slugdiv', 'swpf-product-filter', 'normal');
        });

        add_filter('get_user_option_screen_layout_swpf-product-filter', function () {
            return 1;
        });

        // Get posts by query
        add_filter('plugin_action_links_' . SWPF_BASENAME, array($this, 'add_settings_link'));
    }

    public function include_files() {
        include SWPF_PATH . 'admin/inc/register-widget-class.php';
        include SWPF_PATH . 'admin/inc/google-fonts-list.php';
        include SWPF_PATH . 'admin/inc/helper-functions.php';
        include SWPF_PATH . 'admin/inc/icon-manager.php';
        include SWPF_PATH . 'admin/inc/swpf-block.php';
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

            wp_enqueue_script('selectize', SWPF_URL . 'public/vendor/selectize/selectize.js', array('jquery'), $this->version);

            /* Select2 */
            wp_enqueue_script('jquery-select2', SWPF_URL . 'admin/js/select2.min.js', array('jquery'), $this->version, true);

            // CodeMirror Enqueue
            wp_enqueue_code_editor(array('type' => 'text/html'));

            /* Enqueue jQuery Chosen */
            wp_enqueue_script('chosen-script', SWPF_URL . 'public/vendor/chosen/chosen.jquery.js', array('jquery'), $this->version);

            wp_enqueue_script('wp-color-picker-alpha-min', SWPF_URL . 'public/vendor/wp-color-picker-alpha/wp-color-picker-alpha.min.js', array('wp-color-picker'), $this->version);

            /* Jquery Condition */
            wp_enqueue_script('jquery-condition', SWPF_URL . 'admin/js/jquery-condition.js', array('jquery'), $this->version);

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
    }

    public function get_dropdown_indent($parent_id, $categories, $selected_ids, $cat_ids, $child_count = -1) {
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
                    $child_html = $this->get_dropdown_indent($cat->term_id, $categories, $selected_ids, $cat_ids, $child_count);

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

    public function columns_head($defaults) {
        $defaults['shortcodes'] = esc_html__('Shortcodes', 'super-product-filter');
        $defaults['template'] = esc_html__('Template Include', 'super-product-filter');
        unset($defaults['date']);   // remove it from the columns list
        $defaults['date'] = esc_html__('Date', 'super-product-filter');
        return $defaults;
    }

    public function columns_content($column, $post_ID) {
        if ($column == 'shortcodes') {
            $id = $post_ID;
            ?>
            [swpf_shortcode id="<?php echo esc_attr($id); ?>"]
            <?php
        }
        if ($column == 'template') {
            $id = $post_ID;
            ?>
            &lt;?php echo do_shortcode("[swpf_shortcode id='<?php echo esc_attr($id); ?>']"); ?&gt;
            <?php
        }
    }

    public function widgets_init() {
        register_sidebar([
            'name' => esc_html__('SWPF Widget Area', 'super-product-filter'),
            'id' => 'swpf-sidebar',
            'description' => esc_html__('The main sidebar appears on the right on each page except the front page template', 'super-product-filter'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="swpf-widget-title">',
            'after_title' => '</h3>',
        ]);
        register_widget('SWPF_Widget');
    }

    public function save_metabox_settings($post_id) {
        if (wp_verify_nonce(swpf_get_post('swpf_settings_nonce'), 'swpf-settings-nonce')) {
            $settings = get_post_meta($post_id, 'swpf_settings', true);
            $terms_customize = isset($settings['terms_customize']) ? $settings['terms_customize'] : array();
            $settings = swpf_get_post_data_arr('swpf_settings');
            if($settings) {
                $settings['terms_customize'] = $terms_customize;
                $settings = self::recursive_parse_args($settings, self::checkbox_settings());
                $settings = self::sanitize_array($settings, self::sanitize_settings_rules());
                update_post_meta($post_id, 'swpf_settings', $settings);
            }
        }
        return;
    }

    public function settings_metabox_xhr() {
        global $post;
        if ('swpf-product-filter' === $post->post_type) {
            ob_start();
            ?>
            // Avoid collisions with other libraries
            (function ($) {
                "use strict";
                // Make sure the document is ready
                $(document).ready(function () {

                    $(document).on('submit', 'form#post', function (e) {
                        e.preventDefault()

                        $('.swpf-settings-footer .button').addClass('swpf-button-loader');

                        // This is the post.php url we localized (via php) above
                        var url = '<?php echo esc_url(admin_url('post.php')) ?>'
                        // Serialize form data
                        var data = $('form#post').serializeArray();                 // Tell PHP what we're doing
                        // NOTE: "name" and "value" are the array keys. This is important. I use int(1) for the value to make sure we don't get a string server-side.
                        data.push({name: 'save_post_ajax', value: 1})
                        data.push({name: 'post_status', value: 'publish'})

                        // Replaces wp.autosave.initialCompareString
                        var ajax_updated = false

                        /**
                         * Supercede the WP beforeunload function to remove                  * the confirm dialog when leaving the page (if we saved via ajax)
                         *
                         * The following line of code SHOULD work in $.post.done(), but
                         *     for some reason, wp.autosave.initialCompareString isn't changed
                         *     when called from wp-includes/js/autosave.js
                         * wp.autosave.initialCompareString = wp.autosave.getCompareString();
                         */
                        $(window).unbind('beforeunload.edit-post')
                        $(window).on('beforeunload.edit-post', function () {
                            var editor = typeof tinymce !== 'undefined' && tinymce.get('content')

                            // Use our "ajax_updated" var instead of wp.autosave.initialCompareString
                            if ((editor && !editor.isHidden() && editor.isDirty()) ||
                                (wp.autosave && wp.autosave.getCompareString() !== ajax_updated)) {
                                return postL10n.saveAlert
                            }
                        })


                        // Post it
                        $.post(url, data, function (response) {
                            // Validate response
                            if (response.success) {
                                // Mark TinyMCE as saved
                                if (typeof tinyMCE !== 'undefined') {
                                    for (id in tinyMCE.editors) {
                                        if (tinyMCE.get(id))
                                            tinyMCE.get(id).setDirty(false)
                                    }
                                }
                                // Update the saved content for the beforeunload check
                                ajax_updated = wp.autosave.getCompareString();
                            }
                            $('.swpf-alert').addClass('swpf-alert-success');
                            $('.swpf-alert span').html('Settings Saved');
                            $('.swpf-alert').addClass('swpf-alert-active');
                            $('.swpf-settings-footer .button').removeClass('swpf-button-loader');
                            clearTimeout();
                            setTimeout(function () {
                                if ($('.swpf-alert').hasClass('swpf-alert-active')) {
                                    $('.swpf-alert').removeClass('swpf-alert-active');
                                    $('.swpf-alert').removeClass('swpf-alert-success swpf-alert-warning swpf-alert-neutral');
                                }
                            }, 3500);
                            history.pushState("", document.title, url + '?' + 'post=' + response.data + '&action=edit');
                        }).fail(function (response) {
                            console.log('ERROR: Could not contact server. ', response)
                        }).done(function () {
                            if (wp.autosave) {
                                wp.autosave.enableButtons();
                            }

                            $('#publishing-action .spinner').removeClass('is-active');
                        })

                        return false
                    })
                })
            })(jQuery)
            <?php
            wp_register_script('swpf-admin-save-post', '',);
            wp_enqueue_script('swpf-admin-save-post');
            wp_add_inline_script('swpf-admin-save-post', ob_get_clean());
        }
    }

    public function save_metabox_settings_xhr($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        #If this is your post type
        if ('swpf-product-filter' === swpf_get_post('post_type')) {
            # Send JSON response
            if (swpf_get_post('save_post_ajax') == true) {
                wp_send_json_success($post_id);
            }
        }
    }

    public function register_post_type() {
        $labels = array(
            'name' => _x('Super Product Filter', 'post type general name', 'super-product-filter'),
            'singular_name' => _x('Super Product Filter', 'post type singular name', 'super-product-filter'),
            'menu_name' => _x('Super Product Filter', 'admin menu', 'super-product-filter'),
            'name_admin_bar' => _x('Super Product Filter', 'add new on admin bar', 'super-product-filter'),
            'add_new' => _x('Add New', 'Super Product Filter', 'super-product-filter'),
            'add_new_item' => esc_html__('Add New Super Product Filter', 'super-product-filter'),
            'new_item' => esc_html__('New Filter Preset', 'super-product-filter'),
            'edit_item' => esc_html__('Edit Filter Preset', 'super-product-filter'),
            'view_item' => esc_html__('View Filter Preset', 'super-product-filter'),
            'all_items' => esc_html__('All Filter Preset', 'super-product-filter'),
            'search_items' => esc_html__('Search Filter Preset', 'super-product-filter'),
            'parent_item_colon' => esc_html__('Parent Filter Preset', 'super-product-filter'),
            'not_found' => esc_html__('No Filter Preset found.', 'super-product-filter'),
            'not_found_in_trash' => esc_html__('No Filter Preset found in Trash.', 'super-product-filter')
        );

        $args = array(
            'labels' => $labels,
            'description' => esc_html__('Description', 'super-product-filter'),
            'public' => false,
            'publicly_queryable' => false, // hides preview button
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA2MS42IDYxLjUiPjxnPjxwYXRoIGQ9Im00Ny4yIDEyLjItNC42IDItMTEuNS01LjNoLS40bC0xMS41IDUuMi00LjYtMi4xIDE2LjMtNy41Wk0yNC44IDE3bC0uNC40djVsLTQuOCAyLjJWMTVsMTAuNy00Ljl2NC4zWm0xNy4zIDIuNC0xMC43LTQuOXYtNC4zbDEwLjYgNVptLTI4LjQgOC41di0xNWw0LjggMi4ydjEwLjRsLjMuNEw0MiAzNi43bC4xIDkuNi00LjktMi4ydi01bC0uMy0uNVpNNDcuOSAxM3Y0LjJsLTQuOCAyLjItLjEtNC4yWm0tNS40IDIyLjdMMjAuMyAyNS42bDQuNi0yLjEgMjIuMyAxMC4yWm01LjQgMTMuOC0xNi42IDcuNnYtNC4ybDExLjQtNS4zLjQtLjRWMzYuN2w0LjgtMi4yWm0tOC44LTEuMy04LjIgMy43LTE2LjMtNy41IDQuNi0yLjEgMTEuNSA1LjNoLjRsNS42LTIuNiA0LjYgMi4xWm0tMjUuNCAxLjN2LTQuM2wxNi42IDcuNlY1N1pNMCA1NS40VjYuM2E2IDYgMCAwIDEgMS44LTQuNEE2IDYgMCAwIDEgNi4zIDBoNDkuMWE2IDYgMCAwIDEgNC40IDEuOCA2IDYgMCAwIDEgMS44IDQuNHY0OS4xYTYuMTUgNi4xNSAwIDAgMS02LjIgNi4ySDYuM2E2IDYgMCAwIDEtNC40LTEuOEE1LjkxIDUuOTEgMCAwIDEgMCA1NS40Wm01OC43IDMuM2E0LjUzIDQuNTMgMCAwIDAgMS40LTMuM1Y2LjNBNC41MyA0LjUzIDAgMCAwIDU4LjcgM2E0LjUzIDQuNTMgMCAwIDAtMy4zLTEuNEg2LjNBNC4xOCA0LjE4IDAgMCAwIDMgM2E0LjcxIDQuNzEgMCAwIDAtMS40IDMuM3Y0OS4xQTQuNTMgNC41MyAwIDAgMCAzIDU4LjdhNC41MyA0LjUzIDAgMCAwIDMuMyAxLjRoNDkuMWE0LjUzIDQuNTMgMCAwIDAgMy4zLTEuNFoiIHN0eWxlPSJmaWxsOiNhN2FhYWQiLz48L2c+PC9zdmc+',
            'query_var' => true,
            'rewrite' => array('slug' => 'swpf-product-filter'),
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title')
        );
        register_post_type('swpf-product-filter', $args);
    }

    public function settings_metabox() {
        $current_screen = get_current_screen();
        add_meta_box('swpf-settings-metabox', esc_html__('Super Product Filter', 'super-product-filter'), array($this, 'settings_metabox_callback'), 'swpf-product-filter', 'normal', 'high');
    }

    public function settings_metabox_callback() {
        include SWPF_PATH . 'admin/inc/metabox/settings-metabox.php';
    }

    public function register_submenu_page() {
        add_submenu_page('edit.php?post_type=swpf-product-filter', esc_html__('Settings', 'super-product-filter'), esc_html__('Settings', 'super-product-filter'), 'manage_options', 'swpf-general-settings', array($this, 'generalsettingsconfiguration'));
        add_submenu_page('edit.php?post_type=swpf-product-filter', esc_html__('Documentation', 'super-product-filter'), esc_html__('Documentation', 'super-product-filter'), 'manage_options', esc_url_raw('https://hashthemes.com/documentation/super-woocommerce-product-filter-documentation/'));
    }

    public function generalsettingsconfiguration() {
        include SWPF_PATH . 'admin/inc/general/settings.php';
    }

    public function handle_generalsettingsform() {
        if (!wp_verify_nonce(swpf_get_post('swpf_nonce'), 'swpf_nonce_update_general_settings')) {
            ?>
            <div class="swpf-error-notice swpf-save-notice">
                <p><?php esc_html_e('Sorry, settings not saved. Please try again.', 'super-product-filter'); ?></p>
            </div> <?php
            exit;
        } else {
            $general_settings = swpf_get_post_data_arr('swpf_general_settings');
            $general_settings = self::recursive_parse_args($general_settings, self::checkbox_general_settings());
            $general_settings = self::sanitize_array($general_settings, self::sanitize_general_setting_rules());

            update_option('swpf_general_settings', $general_settings);
            ?>
            <div class="swpf-success-notice swpf-save-notice">
                <p><?php esc_html_e('Settings Saved!', 'super-product-filter'); ?></p>
            </div>
            <?php
        }
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

    public static function checkbox_general_settings() {
        return array(
            'load_fonts_locally' => 'off',
        );
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

    public static function sanitize_general_setting_rules() {
        return array(
            'load_fonts_locally' => 'swpf_sanitize_checkbox',
        );
    }

    public static function default_general_settings_values() {
        return array(
            'load_fonts_locally' => 'off',
        );
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

    public function alert_message() {
        ?>
        <div class="swpf-alert">
            <span class="swpf-alert-message"></span>
            <i class="icofont-close-line"></i>
        </div>
        <?php
    }

    public static function animations() {
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

    public function icon_field($inputName = '', $iconName = '') {
        ?>
        <div class="swpf-icon-box-wrap">
            <div class="swpf-selected-icon">
                <i class="<?php echo esc_attr($iconName); ?>"></i>
                <span><i class="swpf-down-icon"></i></span>
            </div>

            <input type="hidden" name="<?php echo esc_attr($inputName); ?>" value="<?php echo esc_attr($iconName); ?>"/>
        </div>
        <?php
    }

    public function register_translation_strings() {
        $query = new WP_Query(
                array(
            'post_type' => 'swpf-product-filter',
            'posts_per_page' => -1,
            'post_status' => 'publish'
                )
        );

        if ($query->have_posts()) :
            while ($query->have_posts()) :
                $query->the_post();
                $postid = get_the_ID();
                $filter_title = get_the_title($postid);
                $settings = get_post_meta($postid, 'swpf_settings', true);
                $settings = self::recursive_parse_args($settings, self::default_settings_values());
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

    public function process_settings_export() {

        if (empty(swpf_get_post('swpf_imex_action')) || 'export_settings' != swpf_get_post('swpf_imex_action') || empty(swpf_get_post('swpf_filter_id')))
            return;

        if (!wp_verify_nonce(swpf_get_post('swpf_imex_export_nonce'), 'swpf_imex_export_nonce'))
            return;

        if (!current_user_can('manage_options'))
            return;
        $filter_id = swpf_get_post('swpf_filter_id');

        if ('publish' == get_post_status($filter_id) || 'draft' == get_post_status($filter_id)) {
            $settings = get_post_meta($filter_id, 'swpf_settings', true);
            $unset_array = array('enable', 'title_label', 'list_order', 'show_count', 'hide_term_name', 'search_filter', 'placeholder_txt', 'multiselect_logic_operator', 'include_exclude_filter', 'field_orientation', 'terms_customize', 'orderby', 'order_type', 'display_option', 'shortcode', 'display');
            foreach ($unset_array as $key) {
                unset($settings[$key]);
            }
            unset($settings['config']['lo_specific_cat']);


            ignore_user_abort(true);

            nocache_headers();
            header('Content-Type: application/json; charset=utf-8');
            header('Content-Disposition: attachment; filename=swpf-' . $filter_id . '-' . gmdate('m-d-Y') . '.json');
            header("Expires: 0");

            echo wp_json_encode($settings);
            exit;
        } else {
            wp_die(__('Please update post before you export', 'super-product-filter'));
        }
    }

    public function process_settings_import() {

        if (empty(swpf_get_post('swpf_imex_action')) || 'import_settings' != swpf_get_post('swpf_imex_action') || empty(swpf_get_post('swpf_filter_id')))
            return;

        if (!wp_verify_nonce(swpf_get_post('swpf_imex_import_nonce'), 'swpf_imex_import_nonce'))
            return;

        if (!current_user_can('manage_options'))
            return;

        $filename = sanitize_file_name($_FILES['swpf_import_file']['name']);
        $extension = explode('.', $filename);
        $extension = end($extension);

        if ($extension != 'json') {
            wp_die(__('Please upload a valid .json file', 'super-product-filter'));
        }

        $import_file = sanitize_text_field($_FILES['swpf_import_file']['tmp_name']);

        if (empty($import_file)) {
            wp_die(__('Please upload a file to import', 'super-product-filter'));
        }

        // Retrieve the settings from the file and convert the json object to an array.
        $imdat = json_decode(file_get_contents($import_file), true);

        $filter_id = swpf_get_post('swpf_filter_id');

        if ('publish' == get_post_status($filter_id) || 'draft' == get_post_status($filter_id)) {
            $old_settings = get_post_meta($filter_id, 'swpf_settings', true);
            $settings = self::import_images($imdat);
            $settings = self::recursive_parse_args($settings, $old_settings);
            $settings = self::sanitize_array($settings, self::sanitize_settings_rules());
            update_post_meta($filter_id, 'swpf_settings', $settings);

            $location = sanitize_text_field($_SERVER['HTTP_REFERER']);
            wp_safe_redirect($location . '&swpfalert=Settings%20Imported%20Successfully');
            exit();
        } else {
            wp_die(__('Please update post before you import', 'super-product-filter'));
        }
    }

    private static function import_images($mods) {
        if ($mods) {
            foreach ($mods as $key => $value) {
                //For repeater fields
                if (is_array($value)) {
                    foreach ($value as $data_key => $data_value) {
                        if (self::is_image_url($data_value)) {
                            $sub_data = self::media_handle_sideload($data_value);
                            if (!is_wp_error($sub_data))
                                $value[$data_key] = $sub_data->url;
                        } else {
                            $value[$data_key] = $data_value;
                        }
                    }

                    $mods[$key] = $value;
                } elseif (self::is_image_url($value)) {
                    $data = self::media_handle_sideload($value);
                    if (!is_wp_error($data))
                        $mods[$key] = $data->url;
                }
            }
        }
        return $mods;
    }

    private static function is_image_url($url) {
        if (is_string($url) && preg_match('/\.(jpg|jpeg|png|gif)/i', $url)) {
            return true;
        }

        return false;
    }

    private static function media_handle_sideload($file) {
        $data = new stdClass();

        if (!function_exists('media_handle_sideload')) {
            require_once( ABSPATH . 'wp-admin/includes/media.php' );
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
        }

        if (!empty($file)) {
            // Set variables for storage, fix file filename for query strings.
            preg_match('/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches);
            $file_array = array();
            $file_array['name'] = basename($matches[0]);

            // Download file to temp location.
            $file_array['tmp_name'] = download_url($file);

            // If error storing temporarily, return the error.
            if (is_wp_error($file_array['tmp_name'])) {
                return $file_array['tmp_name'];
            }

            // Do the validation and storage stuff.
            $id = media_handle_sideload($file_array, 0);

            // If error storing permanently, unlink.
            if (is_wp_error($id)) {
                @unlink($file_array['tmp_name']);
                return $id;
            }

            // Build the object to return.
            $meta = wp_get_attachment_metadata($id);
            $data->attachment_id = $id;
            $data->url = wp_get_attachment_url($id);
            $data->thumbnail_url = wp_get_attachment_thumb_url($id);
            $data->height = $meta['height'];
            $data->width = $meta['width'];
        }

        return $data;
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
        $result = ( $value == $rule['value'] );

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
                                        <input
                                            type="text"
                                            name="swpf_settings[terms_customize][<?php echo esc_attr($key); ?>][<?php echo esc_attr($tval->term_id) ?>][term_name]"
                                            value="<?php echo isset($terms_customize_settings[$tval->term_id]['term_name']) ? esc_attr($terms_customize_settings[$tval->term_id]['term_name']) : ''; ?>"
                                        >
                                    </div>

                                    <div class="swpf-custom-term-field swpf-field-wrap swpf-custom-color">
                                        <label><?php esc_html_e('Color', 'super-product-filter'); ?></label>
                                        <input
                                            type="text"
                                            data-alpha-enabled="true"
                                            data-alpha-color-type="hex"
                                            class="color-picker swpf-color-picker"
                                            name="swpf_settings[terms_customize][<?php echo esc_attr($key); ?>][<?php echo esc_attr($tval->term_id) ?>][term_color]"
                                            value="<?php echo isset($terms_customize_settings[$tval->term_id]['term_color']) ? esc_attr($terms_customize_settings[$tval->term_id]['term_color']) : ''; ?>"
                                        >
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
                                            <div class="swpf-custom-menu-image-icon" >
                                                <?php if ($has_image) { ?>
                                                    <img src="<?php echo esc_attr(isset($terms_customize_settings[$tval->term_id]['term_image']) ? esc_url($terms_customize_settings[$tval->term_id]['term_image']) : ''); ?>" width="100"/>
                                                <?php } ?>
                                            </div>
                                            <div class="swpf-custom-img-action-field">
                                                <div class="swpf-image-remove"><?php esc_html_e('Remove', 'super-product-filter'); ?></div>
                                                <div class="swpf-image-upload"><?php esc_html_e('Upload', 'super-product-filter') ?></div>
                                            </div>
                                            <input type="hidden" class="swpf-upload-background-url" name="swpf_settings[terms_customize][<?php echo esc_attr($key) ?>][<?php echo esc_attr($tval->term_id) ?>][term_image]" value="<?php echo isset($terms_customize_settings[$tval->term_id]['term_image']) ? esc_url($terms_customize_settings[$tval->term_id]['term_image']) : ''; ?>"/>
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
                $settings = self::recursive_parse_args($settings, self::default_settings_values());
            }
            $terms_customize = $data['swpf_settings[terms_customize'];
            $settings['terms_customize'][array_key_first($terms_customize)] = $data['swpf_settings[terms_customize'][array_key_first($terms_customize)];

            $settings = self::recursive_parse_args($settings, self::checkbox_settings());
            $settings = self::sanitize_array($settings, self::sanitize_settings_rules());
            update_post_meta($tax_id, 'swpf_settings', $settings);
            die();
        }
    }

    public function add_settings_link($links) {
        $settings_link = '<a href="' . esc_url(get_admin_url(null, 'edit.php?post_type=swpf-product-filter')) . '">' . esc_html__('Settings', 'super-product-filter') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

}
