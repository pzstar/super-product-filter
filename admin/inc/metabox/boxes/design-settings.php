<?php
defined('ABSPATH') || die();
?>

<div class="swpf-options-fields-wrap tab-content swpf-settings-content" id="swpf-design-settings" style="display: none;">
    <div class="swpf-option-fields-inner-wrap">

        <ul class="swpf-sub-tabs">
            <li class="swpf-active"><a href="#" data-tab="swpf-design-filter-box"><?php echo esc_html__('Filter Box', 'super-product-filter'); ?></a></li>
            <li><a href="#" data-tab="swpf-design-offcanvas"><?php echo esc_html__('OffCanvas Menu', 'super-product-filter'); ?></a></li>
            <li><a href="#" data-tab="swpf-design-typography"><?php echo esc_html__('Typography', 'super-product-filter'); ?></a></li>
        </ul>

        <div class="swpf-sub-panel-wrap">
            <div class="swpf-sub-panel swpf-design-filter-box" style="display: block">
                <div class="swpf-settings-list-row">

                    <div class="swpf-field-wrap">
                        <label><?php esc_html_e('Primary Color', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <input type="text" data-alpha-enabled="true" data-alpha-color-type="hex" class="color-picker swpf-color-picker" name="swpf_settings[primary_color]" value="<?php echo esc_attr($settings['primary_color']); ?>">
                        </div>
                    </div>

                    <div class="swpf-separator" style="margin: 0"></div>

                    <h3 style="margin: 0"><?php esc_html_e('Filter Box', 'super-product-filter') ?></h3>

                    <div class="swpf-two-column-row">
                        <div class="swpf-field-wrap">
                            <label><?php esc_html_e('Text Color', 'super-product-filter'); ?></label>
                            <div class="swpf-settings-input-field">
                                <input type="text" data-alpha-enabled="true" data-alpha-color-type="hex" class="color-picker swpf-color-picker" name="swpf_settings[filterbox][textcolor]" value="<?php echo esc_attr($settings['filterbox']['textcolor']); ?>">
                            </div>
                        </div>

                    </div>

                    <div class="swpf-separator" style="margin: 0"></div>

                    <h3 style="margin: 0"><?php esc_html_e('Filter Box Heading', 'super-product-filter'); ?></h3>

                    <div class="swpf-two-column-row">
                        <div class="swpf-field-wrap">
                            <label><?php esc_html_e('Text Color', 'super-product-filter'); ?></label>
                            <div class="swpf-settings-input-field">
                                <input type="text" data-alpha-enabled="true" data-alpha-color-type="hex" class="color-picker swpf-color-picker" name="swpf_settings[heading][textcolor]" value="<?php echo esc_attr($settings['heading']['textcolor']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="swpf-field-wrap">
                        <label><?php esc_html_e('Bottom Margin', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input type="number" name="swpf_settings[heading][marginbottom]" value="<?php echo esc_attr($settings['heading']['marginbottom']); ?>" class="swpf-range-input" min="0" max="100" step="1">px
                            </div>
                        </div>
                    </div>

                    <div class="swpf-separator" style="margin: 0"></div>

                    <div class="swpf-field-wrap">
                        <label><?php esc_html_e('Spacing Between Filters', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input type="number" name="swpf_settings[filterbox][spacing]" value="<?php echo esc_attr($settings['filterbox']['spacing']); ?>" class="swpf-range-input" min="0" max="200" step="1">px
                            </div>
                        </div>
                    </div>

                    <div class="swpf-field-wrap">
                        <label><?php esc_html_e('Item Spacing', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input type="number" name="swpf_settings[filterbox][itemspacing]" value="<?php echo esc_attr($settings['filterbox']['itemspacing']); ?>" class="swpf-range-input" min="0" max="50" step="1">px
                            </div>
                        </div>
                    </div>

                    <div class="swpf-field-wrap">
                        <label><?php esc_html_e('Max Height of Filter', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input type="number" name="swpf_settings[filterbox][height]" value="<?php echo esc_attr($settings['filterbox']['height']); ?>" class="swpf-range-input" min="0" max="1000" step="10">px
                            </div>
                            <p class="swpf-field-desc"><?php echo esc_html__('Set 0 for auto height.', 'super-product-filter') ?></p>
                            <p class="swpf-field-desc"><?php echo esc_html__('A scroll bar will appear if the height of the filter exceed Max Height.', 'super-product-filter') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="swpf-sub-panel swpf-design-typography">
                <h3><?php esc_html_e('Heading Typography', 'super-product-filter') ?></h3>
                <ul class="swpf-typography-fields">
                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-font-family">
                        <label><?php esc_html_e('Font Family', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <select name="swpf_settings[heading_typo][family]" class="typography_face">
                                <option value="inherit" <?php selected($settings['heading_typo']['family'], 'inherit'); ?>><?php echo esc_html('Default', 'super-product-filter'); ?></option>
                                <?php if ($standard_fonts) { ?>
                                    <optgroup label="Standard Fonts">
                                        <?php foreach ($standard_fonts as $standard_font) { ?>
                                            <option value="<?php echo esc_attr($standard_font); ?>" <?php selected($settings['heading_typo']['family'], $standard_font); ?>><?php echo esc_attr($standard_font); ?></option>
                                        <?php } ?>
                                    </optgroup>
                                    <?php
                                }
                                if ($google_fonts) {
                                    ?>
                                    <optgroup label="Google Fonts">
                                        <?php foreach ($google_fonts as $google_font) { ?>
                                            <option value="<?php echo esc_attr($google_font); ?>" <?php selected($settings['heading_typo']['family'], $google_font); ?>><?php echo esc_attr($google_font); ?>
                                            </option>
                                        <?php } ?>
                                    </optgroup>
                                <?php } ?>
                            </select>
                        </div>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-font-style">
                        <label><?php esc_html_e('Font Style', 'super-product-filter'); ?></label>
                        <?php
                        $header_title_family = $settings['heading_typo']['family'];
                        $font_weights = swpf_get_font_weight_choices($header_title_family);
                        if ($font_weights) {
                            ?>
                            <div class="swpf-settings-input-field">
                                <select name="swpf_settings[heading_typo][style]" class="typography_font_style">
                                    <?php foreach ($font_weights as $font_weight => $font_weight_label) { ?>
                                        <option value="<?php echo esc_attr($font_weight); ?>" <?php selected($settings['heading_typo']['style'], $font_weight); ?>><?php echo esc_html($font_weight_label); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-text-transform">
                        <label><?php esc_html_e('Text Transform', 'super-product-filter'); ?></label>
                        <?php if ($text_transforms) { ?>
                            <div class="swpf-settings-input-field">
                                <select name="swpf_settings[heading_typo][text_transform]" class="typography_text_transform">
                                    <?php foreach ($text_transforms as $key => $value) { ?>
                                        <option value="<?php echo esc_attr($key); ?>" <?php selected($settings['heading_typo']['text_transform'], $key); ?>><?php echo esc_html($value); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-text-decoration">
                        <label><?php esc_html_e('Text Decoration', 'super-product-filter'); ?></label>
                        <?php if ($text_decorations) { ?>
                            <div class="swpf-settings-input-field">
                                <select name="swpf_settings[heading_typo][text_decoration]" class="typography_text_decoration">
                                    <?php foreach ($text_decorations as $key => $value) { ?>
                                        <option value="<?php echo esc_attr($key); ?>" <?php selected($settings['heading_typo']['text_decoration'], $key); ?>><?php echo esc_html($value); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                    </li>
                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-line-height">
                        <label><?php esc_html_e('Line Height', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input type="number" name="swpf_settings[heading_typo][line_height]" value="<?php echo esc_attr($settings['heading_typo']['line_height']); ?>" class="swpf-range-input" min="0.5" max="5" step="0.1">
                            </div>
                        </div>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-letter-spacing">
                        <label><?php esc_html_e('Letter Spacing', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input type="number" name="swpf_settings[heading_typo][letter_spacing]" value="<?php echo esc_attr($settings['heading_typo']['letter_spacing']); ?>" class="swpf-range-input" min="-5" max="5" step="0.1">px
                            </div>
                        </div>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-letter-size">
                        <label><?php esc_html_e('Font Size', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input type="number" name="swpf_settings[heading_typo][size]" value="<?php echo esc_attr($settings['heading_typo']['size']); ?>" class="swpf-range-input" min="8" max="100" step="1">px
                            </div>
                        </div>
                    </li>
                </ul>

                <div class="swpf-spacer"></div>
                <div class="swpf-separator"></div>

                <h3><?php esc_html_e('Content Typography', 'super-product-filter') ?></h3>
                <ul class="swpf-typography-fields">
                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-font-family">
                        <label><?php esc_html_e('Font Family', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <select name="swpf_settings[content_typo][family]" class="typography_face">
                                <option value="inherit" <?php selected($settings['content_typo']['family'], 'inherit'); ?>><?php echo esc_html('Default', 'super-product-filter'); ?></option>
                                <?php if ($standard_fonts) { ?>
                                    <optgroup label="Standard Fonts">
                                        <?php foreach ($standard_fonts as $standard_font) { ?>
                                            <option value="<?php echo esc_attr($standard_font); ?>" <?php selected($settings['content_typo']['family'], $standard_font); ?>>
                                                <?php echo esc_attr($standard_font); ?>
                                            </option>
                                        <?php } ?>
                                    </optgroup>
                                    <?php
                                }
                                if ($google_fonts) {
                                    ?>
                                    <optgroup label="Google Fonts">
                                        <?php foreach ($google_fonts as $google_font) { ?>
                                            <option value="<?php echo esc_attr($google_font); ?>" <?php selected($settings['content_typo']['family'], $google_font); ?>><?php echo esc_attr($google_font); ?></option>
                                        <?php } ?>
                                    </optgroup>
                                <?php } ?>
                            </select>
                        </div>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-font-style">
                        <label><?php esc_html_e('Font Style', 'super-product-filter'); ?></label>

                        <?php
                        $header_title_family = $settings['content_typo']['family'];
                        $font_weights = swpf_get_font_weight_choices($header_title_family);
                        if ($font_weights) {
                            ?>
                            <div class="swpf-settings-input-field">
                                <select name="swpf_settings[content_typo][style]" class="typography_font_style">
                                    <?php foreach ($font_weights as $font_weight => $font_weight_label) { ?>
                                        <option value="<?php echo esc_attr($font_weight); ?>" <?php selected($settings['content_typo']['style'], $font_weight); ?>><?php echo esc_html($font_weight_label); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-text-transform">
                        <label><?php esc_html_e('Text Transform', 'super-product-filter'); ?></label>
                        <?php if ($text_transforms) { ?>
                            <div class="swpf-settings-input-field">
                                <select name="swpf_settings[content_typo][text_transform]" class="typography_text_transform">
                                    <?php foreach ($text_transforms as $key => $value) { ?>
                                        <option value="<?php echo esc_attr($key) ?>" <?php selected($settings['content_typo']['text_transform'], $key); ?>><?php echo esc_html($value); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-line-height">
                        <label><?php esc_html_e('Line Height', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input type="number" name="swpf_settings[content_typo][line_height]" value="<?php echo esc_attr($settings['content_typo']['line_height']); ?>" class="swpf-range-input" min="0.5" max="5" step="0.1">
                            </div>
                        </div>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-letter-spacing">
                        <label><?php esc_html_e('Letter Spacing', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input type="number" name="swpf_settings[content_typo][letter_spacing]" value="<?php echo esc_attr($settings['content_typo']['letter_spacing']); ?>" class="swpf-range-input" min="-5" max="5" step="0.1">px
                            </div>
                        </div>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-letter-size">
                        <label><?php esc_html_e('Font Size', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input type="number" name="swpf_settings[content_typo][size]" value="<?php echo esc_attr($settings['content_typo']['size']); ?>" class="swpf-range-input" min="8" max="100" step="1">px
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="swpf-sub-panel swpf-design-offcanvas">
                <?php
                $animations = $this->animations();
                $hover_animations = isset($animations['hover_animation']) ? $animations['hover_animation'] : array();
                $show_animations = isset($animations['show_animation']) ? $animations['show_animation'] : array();
                $hide_animations = isset($animations['hide_animation']) ? $animations['hide_animation'] : array();
                ?>
                <h3><?php esc_html_e('Trigger Button', 'super-product-filter'); ?></h3>
                <div class="swpf-settings-list-row">
                    <div class="swpf-field-wrap">
                        <label><?php esc_html_e('Button Type', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <select name="swpf_settings[side_menu][button_icon_type]" data-condition="toggle" id="swpf-toggle-button-icon-type">
                                <option value="none" <?php selected($settings['side_menu']['button_icon_type'], 'none'); ?>><?php esc_html_e('Don\'t Display', 'super-product-filter'); ?></option>
                                <option value="default_icon" <?php selected($settings['side_menu']['button_icon_type'], 'default_icon'); ?>><?php esc_html_e('Font Icon', 'super-product-filter'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="swpf-field-wrap" data-condition-toggle="swpf-toggle-button-icon-type" data-condition-val="default_icon,custom_icon,hamburger_icon">
                        <label><?php esc_html_e('Button Shape', 'super-product-filter') ?></label>
                        <div class="swpf-settings-input-field">
                            <select name="swpf_settings[side_menu][button_shape]">                        
                                <option value="square" <?php selected($settings['side_menu']['button_shape'], 'square'); ?>><?php esc_html_e('Square', 'super-product-filter'); ?></option>
                                <option value="round" <?php selected($settings['side_menu']['button_shape'], 'round'); ?>><?php esc_html_e('Round', 'super-product-filter'); ?></option>
                                <option value="rounded-square" <?php selected($settings['side_menu']['button_shape'], 'rounded-square'); ?>><?php esc_html_e('Rounded Square', 'super-product-filter'); ?></option>
                                <option value="blob" <?php selected($settings['side_menu']['button_shape'], 'blob'); ?>><?php esc_html_e('Animating Blob', 'super-product-filter'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="swpf-field-wrap" data-condition-toggle="swpf-toggle-button-icon-type" data-condition-val="hamburger_icon">
                        <label><?php esc_html_e('Predefined Icon Style', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <select name="swpf_settings[side_menu][predefined_icon_style]">
                                <option value="style1" <?php selected($settings['side_menu']['predefined_icon_style'], 'style1'); ?>><?php esc_html_e('Style 1', 'super-product-filter'); ?></option>
                                <option value="style2" <?php selected($settings['side_menu']['predefined_icon_style'], 'style2'); ?>><?php esc_html_e('Style 2', 'super-product-filter'); ?></option>
                                <option value="style3" <?php selected($settings['side_menu']['predefined_icon_style'], 'style3'); ?>><?php esc_html_e('Style 3', 'super-product-filter'); ?></option>
                                <option value="style4" <?php selected($settings['side_menu']['predefined_icon_style'], 'style4'); ?>><?php esc_html_e('Style 4', 'super-product-filter'); ?></option>
                                <option value="style5" <?php selected($settings['side_menu']['predefined_icon_style'], 'style5'); ?>><?php esc_html_e('Style 5', 'super-product-filter'); ?></option>
                                <option value="style6" <?php selected($settings['side_menu']['predefined_icon_style'], 'style6'); ?>><?php esc_html_e('Style 6', 'super-product-filter'); ?></option>
                                <option value="style7" <?php selected($settings['side_menu']['predefined_icon_style'], 'style7'); ?>><?php esc_html_e('Style 7', 'super-product-filter'); ?></option>
                                <option value="style8" <?php selected($settings['side_menu']['predefined_icon_style'], 'style8'); ?>><?php esc_html_e('Style 8', 'super-product-filter'); ?></option>
                                <option value="style9" <?php selected($settings['side_menu']['predefined_icon_style'], 'style9'); ?>><?php esc_html_e('Style 9', 'super-product-filter'); ?></option>
                                <option value="style10" <?php selected($settings['side_menu']['predefined_icon_style'], 'style10'); ?>><?php esc_html_e('Style 10', 'super-product-filter'); ?></option>
                                <option value="style11" <?php selected($settings['side_menu']['predefined_icon_style'], 'style11'); ?>><?php esc_html_e('Style 11', 'super-product-filter'); ?></option>
                                <option value="style12" <?php selected($settings['side_menu']['predefined_icon_style'], 'style12'); ?>><?php esc_html_e('Style 12', 'super-product-filter'); ?></option>
                                <option value="style13" <?php selected($settings['side_menu']['predefined_icon_style'], 'style13'); ?>><?php esc_html_e('Style 13', 'super-product-filter'); ?></option>
                                <option value="style14" <?php selected($settings['side_menu']['predefined_icon_style'], 'style14'); ?>><?php esc_html_e('Style 14', 'super-product-filter'); ?></option>
                                <option value="style15" <?php selected($settings['side_menu']['predefined_icon_style'], 'style15'); ?>><?php esc_html_e('Style 15', 'super-product-filter'); ?></option>
                                <option value="style16" <?php selected($settings['side_menu']['predefined_icon_style'], 'style16'); ?>><?php esc_html_e('Style 16', 'super-product-filter'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="swpf-field-wrap" data-condition-toggle="swpf-toggle-button-icon-type" data-condition-val="default_icon">
                        <ul class="swpf-two-column-row">
                            <li>
                                <label><?php esc_html_e('Choose Open Trigger Icon', 'super-product-filter'); ?></label>
                                <div class="swpf-settings-input-field">
                                    <?php
                                    $inputName = 'swpf_settings[side_menu][open_trigger_icon]';
                                    $iconName = $settings['side_menu']['open_trigger_icon'];
                                    $this->icon_field($inputName, $iconName);
                                    ?>
                                </div>
                            </li>

                            <li>
                                <label><?php esc_html_e('Choose Close Trigger Icon', 'super-product-filter'); ?></label>
                                <div class="swpf-settings-input-field">
                                    <?php
                                    $inputName = 'swpf_settings[side_menu][close_trigger_icon]';
                                    $iconName = $settings['side_menu']['close_trigger_icon'];
                                    $this->icon_field($inputName, $iconName);
                                    ?>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="swpf-field-wrap" data-condition-toggle="swpf-toggle-button-icon-type" data-condition-val="default_icon,custom_icon,hamburger_icon">
                        <label><?php esc_html_e('Trigger Button Position', 'super-product-filter') ?></label>
                        <div class="swpf-settings-input-field">
                            <select name="swpf_settings[side_menu][position]" data-condition="toggle" id="swpf-toggle-button-position">
                                <option value="top-left" <?php selected($settings['side_menu']['position'], 'top-left'); ?>><?php esc_html_e('Top Left', 'super-product-filter') ?></option>
                                <option value="top-middle" <?php selected($settings['side_menu']['position'], 'top-middle'); ?>><?php esc_html_e('Top Middle', 'super-product-filter') ?></option>
                                <option value="top-right" <?php selected($settings['side_menu']['position'], 'top-right'); ?>><?php esc_html_e('Top Right', 'super-product-filter') ?></option>
                                <option value="bottom-left" <?php selected($settings['side_menu']['position'], 'bottom-left'); ?>><?php esc_html_e('Bottom Left', 'super-product-filter') ?></option>
                                <option value="bottom-middle" <?php selected($settings['side_menu']['position'], 'bottom-middle'); ?>><?php esc_html_e('Bottom Middle', 'super-product-filter') ?></option>
                                <option value="bottom-right" <?php selected($settings['side_menu']['position'], 'bottom-right'); ?>><?php esc_html_e('Bottom Right', 'super-product-filter') ?></option>
                                <option value="middle-left" <?php selected($settings['side_menu']['position'], 'middle-left'); ?>><?php esc_html_e('Middle Left', 'super-product-filter') ?></option>
                                <option value="middle-right" <?php selected($settings['side_menu']['position'], 'middle-right'); ?>><?php esc_html_e('Middle Right', 'super-product-filter') ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="swpf-field-wrap" data-condition-toggle="swpf-toggle-button-icon-type" data-condition-val="default_icon,custom_icon,hamburger_icon">
                        <ul class="swpf-two-column-row">
                            <li class="swpf-settings-list" data-condition-toggle="swpf-toggle-button-position" data-condition-val="top-left,top-middle,top-right">
                                <label><?php esc_html_e('Offset from Top', 'super-product-filter') ?></label>
                                <div class="swpf-settings-input-field">
                                    <input type="number" name="swpf_settings[side_menu][offset_top]" value="<?php echo esc_attr($settings['side_menu']['offset_top']); ?>"> px
                                </div>
                            </li>

                            <li class="swpf-settings-list" data-condition-toggle="swpf-toggle-button-position" data-condition-val="bottom-left,bottom-middle,bottom-right">
                                <label><?php esc_html_e('Offset from Bottom', 'super-product-filter') ?></label>
                                <div class="swpf-settings-input-field">
                                    <input type="number" name="swpf_settings[side_menu][offset_bottom]" value="<?php echo esc_attr($settings['side_menu']['offset_bottom']); ?>"> px
                                </div>
                            </li>

                            <li class="swpf-settings-list" data-condition-toggle="swpf-toggle-button-position" data-condition-val="top-left,middle-left,bottom-left">
                                <label><?php esc_html_e('Offset from Left', 'super-product-filter') ?></label>
                                <div class="swpf-settings-input-field">
                                    <input type="number" name="swpf_settings[side_menu][offset_left]" value="<?php echo esc_attr($settings['side_menu']['offset_left']); ?>"> px
                                </div>
                            </li>

                            <li class="swpf-settings-list" data-condition-toggle="swpf-toggle-button-position" data-condition-val="top-right,middle-right,bottom-right">
                                <label><?php esc_html_e('Offset from Right', 'super-product-filter') ?></label>
                                <div class="swpf-settings-input-field">
                                    <input type="number" name="swpf_settings[side_menu][offset_right]" value="<?php echo esc_attr($settings['side_menu']['offset_right']); ?>"> px
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="swpf-separator" style="margin: 0"></div>

                    <div class="swpf-field-wrap" data-condition-toggle="swpf-toggle-button-icon-type" data-condition-val="default_icon,custom_icon,hamburger_icon">
                        <h3><?php esc_html_e('Trigger Button Size', 'super-product-filter') ?></h3>
                        <ul class="swpf-two-column-row">
                            <li class="swpf-settings-list">
                                <label><?php esc_html_e('Button Size', 'super-product-filter') ?></label>
                                <div class="swpf-settings-input-field">
                                    <input type="number" name="swpf_settings[side_menu][toggle_button_size]" value="<?php echo esc_attr($settings['side_menu']['toggle_button_size']); ?>"> px
                                </div>
                            </li>

                            <li class="swpf-settings-list" data-condition-toggle="swpf-toggle-button-icon-type" data-condition-val="default_icon">
                                <label><?php esc_html_e('Icon Size', 'super-product-filter') ?></label>
                                <div class="swpf-settings-input-field">
                                    <input type="number" name="swpf_settings[side_menu][icon_size]" value="<?php echo esc_attr($settings['side_menu']['icon_size']); ?>"> px
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="swpf-separator" style="margin: 0" data-condition-toggle="swpf-toggle-button-icon-type" data-condition-val="default_icon,custom_icon,hamburger_icon"></div>

                    <div class="swpf-field-wrap" data-condition-toggle="swpf-toggle-button-icon-type" data-condition-val="default_icon,custom_icon,hamburger_icon">
                        <h3><?php esc_html_e('Trigger Button Colors', 'super-product-filter') ?></h3>
                        <ul class="swpf-two-column-row">
                            <li class="swpf-settings-list">
                                <label><?php esc_html_e('Background Color', 'super-product-filter') ?></label>
                                <div class="swpf-settings-input-field swpf-color-input-field">
                                    <input type="text" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" class="color-picker swpf-color-picker" name="swpf_settings[side_menu][button_bg_color]" value="<?php echo esc_attr($settings['side_menu']['button_bg_color']) ?>">
                                </div>
                            </li>

                            <li class="swpf-settings-list">
                                <label><?php esc_html_e('Background Color (Hover)', 'super-product-filter') ?></label>
                                <div class="swpf-settings-input-field swpf-color-input-field">
                                    <input type="text" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" class="color-picker swpf-color-picker" name="swpf_settings[side_menu][button_hover_bg_color]" value="<?php echo esc_attr($settings['side_menu']['button_hover_bg_color']) ?>">
                                </div>
                            </li>

                            <li class="swpf-settings-list">
                                <label><?php esc_html_e('Icon Color', 'super-product-filter') ?></label>
                                <div class="swpf-settings-input-field swpf-color-input-field">
                                    <input type="text" class="color-picker swpf-color-picker" name="swpf_settings[side_menu][button_icon_color]" value="<?php echo esc_attr($settings['side_menu']['button_icon_color']) ?>">
                                </div>
                            </li>

                            <li class="swpf-settings-list">
                                <label><?php esc_html_e('Icon Color (Hover)', 'super-product-filter') ?></label>
                                <div class="swpf-settings-input-field swpf-color-input-field">
                                    <input type="text" class="color-picker swpf-color-picker" name="swpf_settings[side_menu][button_hover_icon_color]" value="<?php echo esc_attr($settings['side_menu']['button_hover_icon_color']) ?>">
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="swpf-field-wrap" data-condition-toggle="swpf-toggle-button-icon-type" data-condition-val="none">
                        <h3><?php esc_html_e('OffCanvas Panel Trigger Class', 'super-product-filter'); ?></h3>

                        <label style="font-size:16px">swpf-open-sidemenu-<?php echo esc_attr($post_id); ?></label>
                        <div class="swpf-settings-input-field">
                            <p class="swpf-desc"><?php esc_html_e('You can use the above class name to trigger the OffCanvas Panel with any link, icon or button.', 'super-product-filter'); ?></p>
                        </div>
                    </div>

                    <div class="swpf-separator" style="margin: 0"></div>

                    <div class="swpf-field-wrap">
                        <h3><?php esc_html_e('OffCanvas Panel', 'super-product-filter') ?></h3>
                        <label><?php esc_html_e('Panel Position', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field swpf-setting-checkbox-field">
                            <label>
                                <input type="radio" name="swpf_settings[side_menu][panel_position]" value="left" <?php checked($settings['side_menu']['panel_position'], 'left'); ?>> <?php esc_html_e('Left', 'super-product-filter'); ?>
                            </label>
                            &nbsp;&nbsp;
                            <label>
                                <input type="radio" name="swpf_settings[side_menu][panel_position]" value="right" <?php checked($settings['side_menu']['panel_position'], 'right'); ?>> <?php esc_html_e('Right', 'super-product-filter'); ?> 
                            </label>
                        </div>
                    </div>

                    <div class="swpf-field-wrap">
                        <label><?php esc_html_e('Panel Width', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <input class="swpf-range-input" type="number" name="swpf_settings[side_menu][panel_width]" value="<?php echo esc_attr($settings['side_menu']['panel_width']) ?>">
                            <select name="swpf_settings[side_menu][panel_width_unit]" class='swpf-unit'>
                                <option value="px" <?php selected($settings['side_menu']['panel_width_unit'], 'px'); ?>>px</option>
                                <option value="em" <?php selected($settings['side_menu']['panel_width_unit'], 'em'); ?>>em</option>
                                <option value="%" <?php selected($settings['side_menu']['panel_width_unit'], '%'); ?>>%</option>
                            </select>
                        </div>  
                    </div>

                    <div class="swpf-field-wrap">
                        <label><?php esc_html_e('Panel Background Color', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <input type="text" class="color-picker swpf-color-picker" name="swpf_settings[side_menu][panel_background_color]" value="<?php echo esc_attr($settings['side_menu']['panel_background_color']); ?>"/>
                        </div>
                    </div>

                    <div class="swpf-field-wrap">
                        <label><?php esc_html_e('Show Scroll Bar', 'super-product-filter'); ?></label>

                        <div class="swpf-settings-input-field">
                            <div class="swpf-toggle-wrap">
                                <label class="swpf-toggle">
                                    <input type="checkbox" name="swpf_settings[side_menu][panel_show_scrollbar]" <?php checked($settings['side_menu']['panel_show_scrollbar'], 'on'); ?> class="swpf-filter-enable" data-condition="toggle" id="sidemenu-panel-show-scrollbar">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="swpf-field-wrap" data-condition-toggle="sidemenu-panel-show-scrollbar">
                        <label><?php esc_html_e('Scroll Bar Width', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input class="swpf-range-input" type="number" min="1" max="10" step="1" value="<?php echo esc_attr($settings['side_menu']['scrollbar_width']); ?>" name="swpf_settings[side_menu][scrollbar_width]"/> px
                            </div>
                        </div>
                    </div>

                    <div class="swpf-field-wrap" data-condition-toggle="sidemenu-panel-show-scrollbar">
                        <ul class="swpf-two-column-row">
                            <li>
                                <label><?php esc_html_e('Drag Rail Color', 'super-product-filter'); ?></label>
                                <div class="swpf-settings-input-field swpf-color-input-field">
                                    <input type="text" class="color-picker swpf-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="swpf_settings[side_menu][scrollbar_drag_rail_color]" value="<?php echo esc_attr($settings['side_menu']['scrollbar_drag_rail_color']); ?>"/>
                                </div>
                            </li>

                            <li>
                                <label><?php esc_html_e('Drag Bar Color', 'super-product-filter'); ?></label>
                                <div class="swpf-settings-input-field swpf-color-input-field">
                                    <input type="text" class="color-picker swpf-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="swpf_settings[side_menu][scrollbar_drag_bar_color]" value="<?php echo esc_attr($settings['side_menu']['scrollbar_drag_bar_color']) ?>"/>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>