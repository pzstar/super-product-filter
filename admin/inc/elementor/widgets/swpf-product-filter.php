<?php

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class SwpfProductFilter extends Widget_Base {

    /** Widget Name */
    public function get_name() {
        return 'swpf-product-filter';
    }

    /** Widget Title */
    public function get_title() {
        return esc_html__('Super Product Filter', 'super-product-filter');
    }

    /** Icon */
    public function get_icon() {
        return 'eicon-taxonomy-filter';
    }

    /** Category */
    public function get_categories() {
        return ['super-product-filter'];
    }

    /** Controls */
    protected function register_controls() {
        $this->start_controls_section('content_section', [
            'label' => esc_html__('Super Product Filter', 'super-product-filter'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('swpf_filter_preset', [
            'label' => esc_html__('Select Filter Preset', 'super-product-filter'),
            'description' => esc_html__('To edit the filter settings click ', 'super-product-filter') . '<a target="_blank" href="' . admin_url('edit.php?post_type=swpf-product-filter') . '">' . esc_html__('here', 'super-product-filter') . '</a>',
            'type' => Controls_Manager::SELECT2,
            'options' => $this->get_filter_preset(),
            'label_block' => 'true',
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        if (isset($settings['swpf_filter_preset']) && !empty($settings['swpf_filter_preset']) && (get_post_status($settings['swpf_filter_preset']) == 'publish')) {
            echo do_shortcode('[swpf_elem_shortcode id="' . $settings['swpf_filter_preset'] . '"]');
        }
    }

    public function get_filter_preset() {
        $args = array(
            'post_type' => 'swpf-product-filter',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'order' => 'ASC',
            'orderby' => 'id'
        );

        $posts = get_posts($args);
        if (!empty($posts)) {
            $option = array();
            $option['none'] = esc_html__('None', 'super-product-filter');
            foreach ($posts as $post) {
                $option[$post->ID] = $post->post_title;
            }
        }
        return $option;
    }

}
