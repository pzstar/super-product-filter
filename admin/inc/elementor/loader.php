<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class SWPF_Elementor_Widget_Loader {

    private static $instance = null;

    /**
     * Initialize integration hooks
     *
     * @return void
     */
    public function __construct() {
        // Elementor hooks
        add_action('elementor/init', array($this, 'add_elementor_widget_categories'));
		add_action('elementor/widgets/register', array($this, 'register_swpf_product_filter'));
    }

    function add_elementor_widget_categories() {
        $groups = array(
            'super-product-filter' => esc_html__('Super Product Filter Elements', 'super-product-filter')
        );

        foreach ($groups as $key => $value) {
            \Elementor\Plugin::$instance->elements_manager->add_category($key, ['title' => $value], 1);
        }
    }

	function register_swpf_product_filter($widgets_manager) {
        require_once(__DIR__ . '/widgets/swpf-product-filter.php');
		$widgets_manager->register(new \SwpfProductFilter());
	}


    /**
     * Creates and returns an instance of the class
     * @since 1.0.0
     * @access public
     * return object
     */
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

}

if (!function_exists('swpf_elementor_widget_loader')) {

    /**
     * Returns an instance of the plugin class.
     * @since  1.0.0
     * @return object
     */
    function swpf_elementor_widget_loader() {
        return SWPF_Elementor_Widget_Loader::get_instance();
    }

}
swpf_elementor_widget_loader();
