<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Super_Product_Filter_CPT {
	public function __construct() {
        $this->include_files();

        add_action('init', array($this, 'register_post_type'));

        /* To add the Custom Column on Custom Post */
        add_filter('manage_swpf-product-filter_posts_columns', array($this, 'columns_head'));
        add_action('manage_swpf-product-filter_posts_custom_column', array($this, 'columns_content'), 10, 2);

        // Get posts by query
        add_filter('plugin_action_links_' . SWPF_BASENAME, array($this, 'add_settings_link'));

        add_filter('get_user_option_screen_layout_swpf-product-filter', function () {
            return 1;
        });
    }

    public function include_files() {
        include SWPF_PATH . 'admin/inc/cpt/metabox/init.php';
    }

    public function register_post_type() {
        $labels = array(
            'name' => _x('Super Product Filter', 'post type general name', 'super-product-filter'),
            'singular_name' => _x('Super Product Filter', 'post type singular name', 'super-product-filter'),
            'menu_name' => _x('Super Product Filter', 'admin menu', 'super-product-filter'),
            'name_admin_bar' => _x('Super Product Filter', 'add new on admin bar', 'super-product-filter'),
            'add_new' => _x('Add New', 'Super Product Filter', 'super-product-filter'),
            'add_new_item' => esc_html__('Add New Filter', 'super-product-filter'),
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
            'supports' => array('title'),
            /*
             * Filter presets are site configuration, not content. Without this the
             * post type inherits capability_type 'post', which lets any Contributor
             * or Author create and edit them. map_meta_cap resolves edit_post /
             * delete_post / read_post against the primitives below.
             */
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'capabilities' => array(
                'create_posts' => 'manage_options',
                'edit_posts' => 'manage_options',
                'edit_others_posts' => 'manage_options',
                'edit_private_posts' => 'manage_options',
                'edit_published_posts' => 'manage_options',
                'publish_posts' => 'manage_options',
                'read_private_posts' => 'manage_options',
                'delete_posts' => 'manage_options',
                'delete_others_posts' => 'manage_options',
                'delete_private_posts' => 'manage_options',
                'delete_published_posts' => 'manage_options',
            )
        );
        register_post_type('swpf-product-filter', $args);
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

    public function add_settings_link($links) {
        $settings_link = '<a href="' . esc_url(get_admin_url(null, 'edit.php?post_type=swpf-product-filter')) . '">' . esc_html__('Settings', 'super-product-filter') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
}

new Super_Product_Filter_CPT();