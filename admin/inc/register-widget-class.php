<?php
defined('ABSPATH') or die();

/*
 * Register Plugin Widget : SWPF_Widget
 */
if (!class_exists('SWPF_Widget')) {

    class SWPF_Widget extends WP_Widget {

        public function __construct() {
            parent::__construct('SWPF_Widget', 'Super Woocommerce Filter', array('description' => esc_html__('Select The Filter Preset', 'super-product-filter')));
        }

        public function widget($args, $instance) {
            echo $args['before_widget'];
            echo '<div class="swpf-widget-wrap">';
            if (!empty($instance['title'])) {
                echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
            }
            $id = isset($instance['swpf_id']) ? $instance['swpf_id'] : '';

            if ($id && (get_post_status($id) == 'publish')) {
                echo do_shortcode('[swpf_shortcode id="' . absint($id) . '"]');
            }
            echo '</div>';
            echo $args['after_widget'];
        }

        public function form($instance) {
            $id = isset($instance['swpf_id']) ? $instance['swpf_id'] : '';
            ?>
            <div class="swpf-widget-field-wrap">
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('swpf_id')); ?>"><?php esc_html_e('Select Filter Preset', 'super-product-filter'); ?></label>
                    <select name="<?php echo esc_attr($this->get_field_name('swpf_id')); ?>" class='widefat swpf-widget-selected-filter' id="<?php echo esc_attr($this->get_field_id('swpf_id')); ?>">
                        <option value=""><?php echo esc_html__('Select Filter', 'super-product-filter'); ?></option>
                        <?php
                        $args = array(
                            'post_type' => 'swpf-product-filter',
                            'post_status' => 'publish',
                            'posts_per_page' => -1,
                            'order' => 'ASC',
                            'orderby' => 'id'
                        );
                        $posts = get_posts($args);
                        if (!empty($posts)) {
                            foreach ($posts as $post) {
                                ?>
                                <option value="<?php echo esc_attr($post->ID); ?>" <?php selected($post->ID, $id); ?>><?php echo esc_html($post->post_title); ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </p>
            </div>
            <?php
        }

        public function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['swpf_id'] = isset($new_instance['swpf_id']) ? wp_strip_all_tags($new_instance['swpf_id']) : '';
            return $instance;
        }

    }

}