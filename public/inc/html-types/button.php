<?php
defined('ABSPATH') || die();

$tax_show_count = (isset($settings['show_count'][$tax_name]) && $settings['show_count'][$tax_name] == 'on') ? true : false;
?>

<div class="swpf-filter-item-list swpf-button-type">
    <?php
    if ($tax_name == 'product_cat') {
        echo '<ul class="swpf-filter-product-category swpf-filter-product-category-button">';
    }

    if ($terms) {
        foreach ($terms as $key => $term) {
            $checked = false;

            if ($tax_show_count) {
                $args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug);
                $term_cquery = new WP_Query($args);
                $post_count = $term_cquery->post_count;

                $args = swpf_get_vars_query_args($current_filter_option, $settings, $tax_name, $term->slug, null, true);
                $term_cquery = new WP_Query($args);
                $post_count_ckk = $term_cquery->post_count;
                wp_reset_postdata();
                $post_count = min($post_count, $post_count_ckk, $term->count);
            }

            if ($tax_name == 'product_visibility') {
                if (isset($current_filter_option['visibility']) && !empty($current_filter_option['visibility']) && is_array($current_filter_option['visibility'])) {
                    $checked = in_array($term->slug, $current_filter_option['visibility']) ? true : false;
                }
                ?>
                <div class="swpf-filter-item">
                    <label>
                        <input type="checkbox" class="swpf-chkbox-term swpf-chkbox-term-<?php echo esc_attr($term->term_id); ?>" name="visibility[]" data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php checked($checked, true); ?>>
                        <span class="swpf-title">
                            <span class="swpf-title-text">
                                <?php
                                if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                                    echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super Product Filter', esc_html($sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                                } else {
                                    echo esc_html(ucwords(str_replace('-', ' ', $term->name)));
                                }
                                ?>
                            </span>
                            <?php
                            if ($tax_show_count) {
                                ?>
                                <span class="swpf-count">&nbsp;(<?php echo esc_attr($post_count); ?>)</span>
                                <?php
                            }
                            ?>
                        </span>
                    </label>
                </div>
                <?php
            } else if ($tax_name == 'product_cat') {
                if (isset($current_filter_option['categories']) && !empty($current_filter_option['categories']) && is_array($current_filter_option['categories'])) {
                    $checked = in_array($term->slug, $current_filter_option['categories']) ? true : false;
                }
                ?>
                <li class="swpf-filter-item">
                    <label>
                        <input type="checkbox" class="swpf-chkbox-term swpf-chkbox-term-<?php echo esc_attr($term->term_id); ?>" name="categories[]" data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php checked($checked, true); ?>>
                        <span class="swpf-title">
                            <span class="swpf-title-text">
                                <?php
                                if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                                    echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super Product Filter', esc_html($sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                                } else {
                                    echo esc_html(ucwords(str_replace('-', ' ', $term->name)));
                                }
                                ?>
                            </span>
                            <?php
                            if ($tax_show_count) {
                                ?>
                                <span class="swpf-count">&nbsp;(<?php echo esc_attr($post_count); ?>)</span>
                                <?php
                            }
                            ?>
                        </span>
                    </label>
                </li>
                <?php
            } else if ($tax_name == 'product_tag') {
                if (isset($current_filter_option['tags']) && !empty($current_filter_option['tags']) && is_array($current_filter_option['tags'])) {
                    $checked = in_array($term->slug, $current_filter_option['tags']) ? true : false;
                }
                ?>
                <div class="swpf-filter-item">
                    <label>
                        <input type="checkbox" class="swpf-chkbox-term swpf-chkbox-term-<?php echo esc_attr($term->term_id); ?>" name="tags[]" data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php checked($checked, true); ?>>
                        <span class="swpf-title">
                            <span class="swpf-title-text">
                                <?php
                                if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                                    echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super Product Filter', esc_html($sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                                } else {
                                    echo esc_html(ucwords(str_replace('-', ' ', $term->name)));
                                }
                                ?>
                            </span>
                            <?php
                            if ($tax_show_count) {
                                ?>
                                <span class="swpf-count">&nbsp;(<?php echo esc_attr($post_count); ?>)</span>
                                <?php
                            }
                            ?>
                        </span>
                    </label>
                </div>
                <?php
            } else if ((substr($tax_name, 0, 3) === 'pa_') && isset($term->term_id)) {
                if (isset($current_filter_option['attribute'][$tax_name]) && !empty($current_filter_option['attribute'][$tax_name]) && is_array($current_filter_option['attribute'][$tax_name])) {
                    $checked = in_array($term->slug, $current_filter_option['attribute'][$tax_name]) ? true : false;
                }
                ?>
                <div class="swpf-filter-item">
                    <label>
                        <input type="checkbox" class="swpf-chkbox-term swpf-chkbox-term-<?php echo esc_attr($term->term_id); ?>" name="attribute[<?php echo esc_attr($tax_name); ?>][]" data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php checked($checked, true); ?>>
                        <span class="swpf-title">
                            <span class="swpf-title-text">
                                <?php
                                if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                                    echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super Product Filter', esc_html($sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                                } else {
                                    echo esc_html(ucwords(str_replace('-', ' ', $term->name)));
                                }
                                ?>
                            </span>
                            <?php
                            if ($tax_show_count) {
                                ?>
                                <span class="swpf-count">&nbsp;(<?php echo esc_attr($post_count); ?>)</span>
                                <?php
                            }
                            ?>
                        </span>
                    </label>
                </div>
                <?php
            } else if (isset($term->term_id)) {
                if (isset($current_filter_option['attribute'][$tax_name]) && !empty($current_filter_option['attribute'][$tax_name]) && is_array($current_filter_option['attribute'][$tax_name])) {
                    $checked = in_array($term->slug, $current_filter_option['attribute'][$tax_name]) ? true : false;
                }
                ?>
                <div class="swpf-filter-item">
                    <label>
                        <input type="checkbox" class="swpf-chkbox-term swpf-chkbox-term-<?php echo esc_attr($term->term_id); ?>" name="attribute[<?php echo esc_attr($tax_name); ?>][]" data-termurl="<?php echo esc_url(get_term_link($term->term_id)); ?>" data-taxonomy="<?php echo esc_attr($term->taxonomy); ?>" data-termid="<?php echo esc_attr($term->term_id); ?>" value="<?php echo esc_attr($term->slug); ?>" <?php checked($checked, true); ?>>
                        <span class="swpf-title">
                            <span class="swpf-title-text">
                                <?php
                                if (isset($settings['terms_customize'][$tax_name][$term->term_id]['term_name']) && !empty($settings['terms_customize'][$tax_name][$term->term_id]['term_name'])) {
                                    echo esc_html(apply_filters('swpf_translate_string', $settings['terms_customize'][$tax_name][$term->term_id]['term_name'], 'Super Product Filter', esc_html($sc_title) . ' - Term Name ' . esc_html($tax_name) . ' ' . absint($term->term_id)));
                                } else {
                                    echo esc_html(ucwords(str_replace('-', ' ', $term->name)));
                                }
                                ?>
                            </span>
                            <?php
                            if ($tax_show_count) {
                                ?>
                                <span class="swpf-count">&nbsp;(<?php echo esc_attr($post_count); ?>)</span>
                                <?php
                            }
                            ?>
                        </span>
                    </label>
                </div>
                <?php
            }
        }
    }

    if ($tax_name == 'product_cat') {
        echo '</ul>';
    }
    ?>
</div>