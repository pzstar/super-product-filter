<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */
class Super_Product_Filter {

    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        if (defined('SWPF_VERSION')) {
            $this->version = SWPF_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'super-product-filter';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once SWPF_PATH . 'includes/class-super-product-filter-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once SWPF_PATH . 'includes/class-super-product-filter-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once SWPF_PATH . 'admin/class-super-product-filter-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once SWPF_PATH . 'public/class-super-product-filter-public.php';

        $this->loader = new Super_Product_Filter_Loader();
    }

    private function set_locale() {

        $plugin_i18n = new Super_Product_Filter_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    private function define_admin_hooks() {

        $plugin_admin = new Super_Product_Filter_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    private function define_public_hooks() {

        $plugin_public = new Super_Product_Filter_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }

}
