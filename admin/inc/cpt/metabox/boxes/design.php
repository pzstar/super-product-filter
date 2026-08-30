<?php
defined('ABSPATH') || die();

/**
 * A setting that only the pro version can act on.
 *
 * The control is rendered for real but disabled, so the choices are visible and
 * the field cannot be changed or submitted.
 *
 * @param string $label   Field label.
 * @param array  $choices Options to show, first one being what the free version does.
 * @param string $desc    Short line under the field. Optional.
 */
if (!function_exists('swpf_pro_field')) :
function swpf_pro_field($label, $choices, $desc = '') {
    $buy = 'https://codecanyon.net/item/super-woocommerce-product-filters/49852702';
    ?>
    <div class="swpf-field-wrap swpf-pro-field">
        <label>
            <?php echo esc_html($label); ?>
            <a class="swpf-pro-badge" href="<?php echo esc_url($buy); ?>" target="_blank"><?php esc_html_e('Available in Pro', 'super-product-filter'); ?></a>
        </label>
        <div class="swpf-settings-input-field swpf-pro-locked">
            <select disabled>
                <?php foreach ($choices as $swpf_choice) { ?>
                    <option><?php echo esc_html($swpf_choice); ?></option>
                <?php } ?>
            </select>
        </div>
        <?php if ($desc) { ?>
            <p class="swpf-desc"><?php echo esc_html($desc); ?></p>
        <?php } ?>
    </div>
    <?php
}
endif;


// Custom Typography settings
$swpf_custom = isset($swpf_settings['custom']) ? $swpf_settings['custom'] : null;
$swpf_standard_fonts = swpf_get_standard_font_families();
$swpf_google_fonts = swpf_get_google_font_families();
$swpf_text_transforms = swpf_get_text_transform_choices();
$swpf_text_decorations = swpf_get_text_decoration_choices();
?>

<div class="swpf-options-fields-wrap tab-content swpf-settings-content" id="swpf-design-settings" style="display: none;">
    <div class="swpf-option-fields-inner-wrap">

        <ul class="swpf-sub-tabs">
            <li class="swpf-active"><a href="#" data-tab="swpf-design-filter-box"><?php echo esc_html__('Filter Box', 'super-product-filter'); ?></a></li>
            <li><a href="#" data-tab="swpf-design-offcanvas"><?php echo esc_html__('OffCanvas Menu', 'super-product-filter'); ?></a></li>
            <li><a href="#" data-tab="swpf-design-typography"><?php echo esc_html__('Typography', 'super-product-filter'); ?></a></li>
            <li><a href="#" data-tab="swpf-design-styles"><?php echo esc_html__('Filter Styles', 'super-product-filter'); ?></a></li>
        </ul>

        <div class="swpf-sub-panel-wrap">
            <div class="swpf-sub-panel swpf-design-filter-box" style="display: block">
                <div class="swpf-settings-list-row">

                    <div class="swpf-field-wrap">
                        <label><?php esc_html_e('Primary Color', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <input type="text" data-alpha-enabled="true" data-alpha-color-type="hex" class="color-picker swpf-color-picker" name="swpf_settings[primary_color]" value="<?php echo esc_attr($swpf_settings['primary_color']); ?>">
                        </div>
                    </div>

                    <div class="swpf-separator" style="margin: 0"></div>

                    <h3 style="margin: 0"><?php esc_html_e('Filter Box', 'super-product-filter') ?></h3>

                    <div class="swpf-two-column-row">
                        <div class="swpf-field-wrap">
                            <label><?php esc_html_e('Text Color', 'super-product-filter'); ?></label>
                            <div class="swpf-settings-input-field">
                                <input type="text" data-alpha-enabled="true" data-alpha-color-type="hex" class="color-picker swpf-color-picker" name="swpf_settings[filterbox][textcolor]" value="<?php echo esc_attr($swpf_settings['filterbox']['textcolor']); ?>">
                            </div>
                        </div>

                    </div>

                    <div class="swpf-separator" style="margin: 0"></div>

                    <h3 style="margin: 0"><?php esc_html_e('Filter Box Heading', 'super-product-filter'); ?></h3>

                    <div class="swpf-two-column-row">
                        <div class="swpf-field-wrap">
                            <label><?php esc_html_e('Text Color', 'super-product-filter'); ?></label>
                            <div class="swpf-settings-input-field">
                                <input type="text" data-alpha-enabled="true" data-alpha-color-type="hex" class="color-picker swpf-color-picker" name="swpf_settings[heading][textcolor]" value="<?php echo esc_attr($swpf_settings['heading']['textcolor']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="swpf-field-wrap">
                        <label><?php esc_html_e('Bottom Margin', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input type="number" name="swpf_settings[heading][marginbottom]" value="<?php echo esc_attr($swpf_settings['heading']['marginbottom']); ?>" class="swpf-range-input" min="0" max="100" step="1">px
                            </div>
                        </div>
                    </div>

                    <div class="swpf-separator" style="margin: 0"></div>

                    <div class="swpf-field-wrap">
                        <label><?php esc_html_e('Spacing Between Filters', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input type="number" name="swpf_settings[filterbox][spacing]" value="<?php echo esc_attr($swpf_settings['filterbox']['spacing']); ?>" class="swpf-range-input" min="0" max="200" step="1">px
                            </div>
                        </div>
                    </div>

                    <div class="swpf-field-wrap">
                        <label><?php esc_html_e('Item Spacing', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input type="number" name="swpf_settings[filterbox][itemspacing]" value="<?php echo esc_attr($swpf_settings['filterbox']['itemspacing']); ?>" class="swpf-range-input" min="0" max="50" step="1">px
                            </div>
                        </div>
                    </div>

                    <div class="swpf-field-wrap">
                        <label><?php esc_html_e('Max Height of Filter', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input type="number" name="swpf_settings[filterbox][height]" value="<?php echo esc_attr($swpf_settings['filterbox']['height']); ?>" class="swpf-range-input" min="0" max="1000" step="10">px
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
                                <option value="inherit" <?php selected($swpf_settings['heading_typo']['family'], 'inherit'); ?>><?php echo esc_html('Default', 'super-product-filter'); ?></option>
                                <?php
                                if ($swpf_standard_fonts) {
                                    ?>
                                    <optgroup label="Standard Fonts">
                                        <?php foreach ($swpf_standard_fonts as $swpf_standard_font) { ?>
                                            <option value="<?php echo esc_attr($swpf_standard_font); ?>" <?php selected($swpf_settings['heading_typo']['family'], $swpf_standard_font); ?>><?php echo esc_attr($swpf_standard_font); ?></option>
                                        <?php } ?>
                                    </optgroup>
                                    <?php
                                }

                                if ($swpf_google_fonts) {
                                    ?>
                                    <optgroup label="Google Fonts">
                                        <?php foreach ($swpf_google_fonts as $swpf_google_font) { ?>
                                            <option value="<?php echo esc_attr($swpf_google_font); ?>" <?php selected($swpf_settings['heading_typo']['family'], $swpf_google_font); ?>><?php echo esc_attr($swpf_google_font); ?>
                                            </option>
                                        <?php } ?>
                                    </optgroup>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-font-style">
                        <label><?php esc_html_e('Font Style', 'super-product-filter'); ?></label>
                        <?php
                        $swpf_header_title_family = $swpf_settings['heading_typo']['family'];
                        $swpf_font_weights = swpf_get_font_weight_choices($swpf_header_title_family);
                        if ($swpf_font_weights) {
                            ?>
                            <div class="swpf-settings-input-field">
                                <select name="swpf_settings[heading_typo][style]" class="typography_font_style">
                                    <?php foreach ($swpf_font_weights as $swpf_font_weight => $swpf_font_weight_label) { ?>
                                        <option value="<?php echo esc_attr($swpf_font_weight); ?>" <?php selected($swpf_settings['heading_typo']['style'], $swpf_font_weight); ?>><?php echo esc_html($swpf_font_weight_label); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-text-transform">
                        <label><?php esc_html_e('Text Transform', 'super-product-filter'); ?></label>
                        <?php if ($swpf_text_transforms) { ?>
                            <div class="swpf-settings-input-field">
                                <select name="swpf_settings[heading_typo][text_transform]" class="typography_text_transform">
                                    <?php foreach ($swpf_text_transforms as $swpf_key => $swpf_value) { ?>
                                        <option value="<?php echo esc_attr($swpf_key); ?>" <?php selected($swpf_settings['heading_typo']['text_transform'], $swpf_key); ?>><?php echo esc_html($swpf_value); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-text-decoration">
                        <label><?php esc_html_e('Text Decoration', 'super-product-filter'); ?></label>
                        <?php if ($swpf_text_decorations) { ?>
                            <div class="swpf-settings-input-field">
                                <select name="swpf_settings[heading_typo][text_decoration]" class="typography_text_decoration">
                                    <?php foreach ($swpf_text_decorations as $swpf_key => $swpf_value) { ?>
                                        <option value="<?php echo esc_attr($swpf_key); ?>" <?php selected($swpf_settings['heading_typo']['text_decoration'], $swpf_key); ?>><?php echo esc_html($swpf_value); ?>
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
                                <input type="number" name="swpf_settings[heading_typo][line_height]" value="<?php echo esc_attr($swpf_settings['heading_typo']['line_height']); ?>" class="swpf-range-input" min="0.5" max="5" step="0.1">
                            </div>
                        </div>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-letter-spacing">
                        <label><?php esc_html_e('Letter Spacing', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input type="number" name="swpf_settings[heading_typo][letter_spacing]" value="<?php echo esc_attr($swpf_settings['heading_typo']['letter_spacing']); ?>" class="swpf-range-input" min="-5" max="5" step="0.1">px
                            </div>
                        </div>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-letter-size">
                        <label><?php esc_html_e('Font Size', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input type="number" name="swpf_settings[heading_typo][size]" value="<?php echo esc_attr($swpf_settings['heading_typo']['size']); ?>" class="swpf-range-input" min="8" max="100" step="1">px
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
                                <option value="inherit" <?php selected($swpf_settings['content_typo']['family'], 'inherit'); ?>><?php echo esc_html('Default', 'super-product-filter'); ?></option>
                                <?php if ($swpf_standard_fonts) { ?>
                                    <optgroup label="Standard Fonts">
                                        <?php foreach ($swpf_standard_fonts as $swpf_standard_font) { ?>
                                            <option value="<?php echo esc_attr($swpf_standard_font); ?>" <?php selected($swpf_settings['content_typo']['family'], $swpf_standard_font); ?>>
                                                <?php echo esc_attr($swpf_standard_font); ?>
                                            </option>
                                        <?php } ?>
                                    </optgroup>
                                    <?php
                                }
                                if ($swpf_google_fonts) {
                                    ?>
                                    <optgroup label="Google Fonts">
                                        <?php foreach ($swpf_google_fonts as $swpf_google_font) { ?>
                                            <option value="<?php echo esc_attr($swpf_google_font); ?>" <?php selected($swpf_settings['content_typo']['family'], $swpf_google_font); ?>><?php echo esc_attr($swpf_google_font); ?></option>
                                        <?php } ?>
                                    </optgroup>
                                <?php } ?>
                            </select>
                        </div>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-font-style">
                        <label><?php esc_html_e('Font Style', 'super-product-filter'); ?></label>

                        <?php
                        $swpf_header_title_family = $swpf_settings['content_typo']['family'];
                        $swpf_font_weights = swpf_get_font_weight_choices($swpf_header_title_family);

                        if ($swpf_font_weights) {
                            ?>
                            <div class="swpf-settings-input-field">
                                <select name="swpf_settings[content_typo][style]" class="typography_font_style">
                                    <?php foreach ($swpf_font_weights as $swpf_font_weight => $swpf_font_weight_label) { ?>
                                        <option value="<?php echo esc_attr($swpf_font_weight); ?>" <?php selected($swpf_settings['content_typo']['style'], $swpf_font_weight); ?>><?php echo esc_html($swpf_font_weight_label); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php
                        }
                        ?>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-text-transform">
                        <label><?php esc_html_e('Text Transform', 'super-product-filter'); ?></label>
                        <?php
                        if ($swpf_text_transforms) {
                            ?>
                            <div class="swpf-settings-input-field">
                                <select name="swpf_settings[content_typo][text_transform]" class="typography_text_transform">
                                    <?php
                                    foreach ($swpf_text_transforms as $swpf_key => $swpf_value) {
                                        ?>
                                        <option value="<?php echo esc_attr($swpf_key) ?>" <?php selected($swpf_settings['content_typo']['text_transform'], $swpf_key); ?>><?php echo esc_html($swpf_value); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php
                        }
                        ?>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-line-height">
                        <label><?php esc_html_e('Line Height', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input type="number" name="swpf_settings[content_typo][line_height]" value="<?php echo esc_attr($swpf_settings['content_typo']['line_height']); ?>" class="swpf-range-input" min="0.5" max="5" step="0.1">
                            </div>
                        </div>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-letter-spacing">
                        <label><?php esc_html_e('Letter Spacing', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input type="number" name="swpf_settings[content_typo][letter_spacing]" value="<?php echo esc_attr($swpf_settings['content_typo']['letter_spacing']); ?>" class="swpf-range-input" min="-5" max="5" step="0.1">px
                            </div>
                        </div>
                    </li>

                    <li class="swpf-field-wrap swpf-typography-field swpf-typography-letter-size">
                        <label><?php esc_html_e('Font Size', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <div class="swpf-range-slider-field">
                                <div class="swpf-range-slider"></div>
                                <input type="number" name="swpf_settings[content_typo][size]" value="<?php echo esc_attr($swpf_settings['content_typo']['size']); ?>" class="swpf-range-input" min="8" max="100" step="1">px
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="swpf-sub-panel swpf-design-offcanvas">
                <h3><?php esc_html_e('Trigger Button', 'super-product-filter'); ?></h3>
                <div class="swpf-settings-list-row">
                    <div class="swpf-field-wrap">
                        <label><?php esc_html_e('Button Type', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <select name="swpf_settings[side_menu][button_icon_type]" data-condition="toggle" id="swpf-toggle-button-icon-type">
                                <option value="none" <?php selected($swpf_settings['side_menu']['button_icon_type'], 'none'); ?>><?php esc_html_e('Don\'t Display', 'super-product-filter'); ?></option>
                                <option value="default_icon" <?php selected($swpf_settings['side_menu']['button_icon_type'], 'default_icon'); ?>><?php esc_html_e('Font Icon', 'super-product-filter'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="swpf-field-wrap" data-condition-toggle="swpf-toggle-button-icon-type" data-condition-val="default_icon,custom_icon,hamburger_icon">
                        <label><?php esc_html_e('Button Shape', 'super-product-filter') ?></label>
                        <div class="swpf-settings-input-field">
                            <select name="swpf_settings[side_menu][button_shape]">
                                <option value="square" <?php selected($swpf_settings['side_menu']['button_shape'], 'square'); ?>><?php esc_html_e('Square', 'super-product-filter'); ?></option>
                                <option value="round" <?php selected($swpf_settings['side_menu']['button_shape'], 'round'); ?>><?php esc_html_e('Round', 'super-product-filter'); ?></option>
                                <option value="rounded-square" <?php selected($swpf_settings['side_menu']['button_shape'], 'rounded-square'); ?>><?php esc_html_e('Rounded Square', 'super-product-filter'); ?></option>
                                <option value="blob" <?php selected($swpf_settings['side_menu']['button_shape'], 'blob'); ?>><?php esc_html_e('Animating Blob', 'super-product-filter'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="swpf-field-wrap" data-condition-toggle="swpf-toggle-button-icon-type" data-condition-val="hamburger_icon">
                        <label><?php esc_html_e('Predefined Icon Style', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <select name="swpf_settings[side_menu][predefined_icon_style]">
                                <option value="style1" <?php selected($swpf_settings['side_menu']['predefined_icon_style'], 'style1'); ?>><?php esc_html_e('Style 1', 'super-product-filter'); ?></option>
                                <option value="style2" <?php selected($swpf_settings['side_menu']['predefined_icon_style'], 'style2'); ?>><?php esc_html_e('Style 2', 'super-product-filter'); ?></option>
                                <option value="style3" <?php selected($swpf_settings['side_menu']['predefined_icon_style'], 'style3'); ?>><?php esc_html_e('Style 3', 'super-product-filter'); ?></option>
                                <option value="style4" <?php selected($swpf_settings['side_menu']['predefined_icon_style'], 'style4'); ?>><?php esc_html_e('Style 4', 'super-product-filter'); ?></option>
                                <option value="style5" <?php selected($swpf_settings['side_menu']['predefined_icon_style'], 'style5'); ?>><?php esc_html_e('Style 5', 'super-product-filter'); ?></option>
                                <option value="style6" <?php selected($swpf_settings['side_menu']['predefined_icon_style'], 'style6'); ?>><?php esc_html_e('Style 6', 'super-product-filter'); ?></option>
                                <option value="style7" <?php selected($swpf_settings['side_menu']['predefined_icon_style'], 'style7'); ?>><?php esc_html_e('Style 7', 'super-product-filter'); ?></option>
                                <option value="style8" <?php selected($swpf_settings['side_menu']['predefined_icon_style'], 'style8'); ?>><?php esc_html_e('Style 8', 'super-product-filter'); ?></option>
                                <option value="style9" <?php selected($swpf_settings['side_menu']['predefined_icon_style'], 'style9'); ?>><?php esc_html_e('Style 9', 'super-product-filter'); ?></option>
                                <option value="style10" <?php selected($swpf_settings['side_menu']['predefined_icon_style'], 'style10'); ?>><?php esc_html_e('Style 10', 'super-product-filter'); ?></option>
                                <option value="style11" <?php selected($swpf_settings['side_menu']['predefined_icon_style'], 'style11'); ?>><?php esc_html_e('Style 11', 'super-product-filter'); ?></option>
                                <option value="style12" <?php selected($swpf_settings['side_menu']['predefined_icon_style'], 'style12'); ?>><?php esc_html_e('Style 12', 'super-product-filter'); ?></option>
                                <option value="style13" <?php selected($swpf_settings['side_menu']['predefined_icon_style'], 'style13'); ?>><?php esc_html_e('Style 13', 'super-product-filter'); ?></option>
                                <option value="style14" <?php selected($swpf_settings['side_menu']['predefined_icon_style'], 'style14'); ?>><?php esc_html_e('Style 14', 'super-product-filter'); ?></option>
                                <option value="style15" <?php selected($swpf_settings['side_menu']['predefined_icon_style'], 'style15'); ?>><?php esc_html_e('Style 15', 'super-product-filter'); ?></option>
                                <option value="style16" <?php selected($swpf_settings['side_menu']['predefined_icon_style'], 'style16'); ?>><?php esc_html_e('Style 16', 'super-product-filter'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="swpf-field-wrap" data-condition-toggle="swpf-toggle-button-icon-type" data-condition-val="default_icon">
                        <ul class="swpf-two-column-row">
                            <li>
                                <label><?php esc_html_e('Choose Open Trigger Icon', 'super-product-filter'); ?></label>
                                <div class="swpf-settings-input-field">
                                    <?php
                                    $swpf_inputName = 'swpf_settings[side_menu][open_trigger_icon]';
                                    $swpf_iconName = $swpf_settings['side_menu']['open_trigger_icon'];
                                    Super_Product_Filter_Admin::icon_field($swpf_inputName, $swpf_iconName);
                                    ?>
                                </div>
                            </li>

                            <li>
                                <label><?php esc_html_e('Choose Close Trigger Icon', 'super-product-filter'); ?></label>
                                <div class="swpf-settings-input-field">
                                    <?php
                                    $swpf_inputName = 'swpf_settings[side_menu][close_trigger_icon]';
                                    $swpf_iconName = $swpf_settings['side_menu']['close_trigger_icon'];
                                    Super_Product_Filter_Admin::icon_field($swpf_inputName, $swpf_iconName);
                                    ?>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="swpf-field-wrap" data-condition-toggle="swpf-toggle-button-icon-type" data-condition-val="default_icon,custom_icon,hamburger_icon">
                        <label><?php esc_html_e('Trigger Button Position', 'super-product-filter') ?></label>
                        <div class="swpf-settings-input-field">
                            <select name="swpf_settings[side_menu][position]" data-condition="toggle" id="swpf-toggle-button-position">
                                <option value="top-left" <?php selected($swpf_settings['side_menu']['position'], 'top-left'); ?>><?php esc_html_e('Top Left', 'super-product-filter') ?></option>
                                <option value="top-middle" <?php selected($swpf_settings['side_menu']['position'], 'top-middle'); ?>><?php esc_html_e('Top Middle', 'super-product-filter') ?></option>
                                <option value="top-right" <?php selected($swpf_settings['side_menu']['position'], 'top-right'); ?>><?php esc_html_e('Top Right', 'super-product-filter') ?></option>
                                <option value="bottom-left" <?php selected($swpf_settings['side_menu']['position'], 'bottom-left'); ?>><?php esc_html_e('Bottom Left', 'super-product-filter') ?></option>
                                <option value="bottom-middle" <?php selected($swpf_settings['side_menu']['position'], 'bottom-middle'); ?>><?php esc_html_e('Bottom Middle', 'super-product-filter') ?></option>
                                <option value="bottom-right" <?php selected($swpf_settings['side_menu']['position'], 'bottom-right'); ?>><?php esc_html_e('Bottom Right', 'super-product-filter') ?></option>
                                <option value="middle-left" <?php selected($swpf_settings['side_menu']['position'], 'middle-left'); ?>><?php esc_html_e('Middle Left', 'super-product-filter') ?></option>
                                <option value="middle-right" <?php selected($swpf_settings['side_menu']['position'], 'middle-right'); ?>><?php esc_html_e('Middle Right', 'super-product-filter') ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="swpf-field-wrap" data-condition-toggle="swpf-toggle-button-icon-type" data-condition-val="default_icon,custom_icon,hamburger_icon">
                        <ul class="swpf-two-column-row">
                            <li class="swpf-settings-list" data-condition-toggle="swpf-toggle-button-position" data-condition-val="top-left,top-middle,top-right">
                                <label><?php esc_html_e('Offset from Top', 'super-product-filter') ?></label>
                                <div class="swpf-settings-input-field">
                                    <input type="number" name="swpf_settings[side_menu][offset_top]" value="<?php echo esc_attr($swpf_settings['side_menu']['offset_top']); ?>"> px
                                </div>
                            </li>

                            <li class="swpf-settings-list" data-condition-toggle="swpf-toggle-button-position" data-condition-val="bottom-left,bottom-middle,bottom-right">
                                <label><?php esc_html_e('Offset from Bottom', 'super-product-filter') ?></label>
                                <div class="swpf-settings-input-field">
                                    <input type="number" name="swpf_settings[side_menu][offset_bottom]" value="<?php echo esc_attr($swpf_settings['side_menu']['offset_bottom']); ?>"> px
                                </div>
                            </li>

                            <li class="swpf-settings-list" data-condition-toggle="swpf-toggle-button-position" data-condition-val="top-left,middle-left,bottom-left">
                                <label><?php esc_html_e('Offset from Left', 'super-product-filter') ?></label>
                                <div class="swpf-settings-input-field">
                                    <input type="number" name="swpf_settings[side_menu][offset_left]" value="<?php echo esc_attr($swpf_settings['side_menu']['offset_left']); ?>"> px
                                </div>
                            </li>

                            <li class="swpf-settings-list" data-condition-toggle="swpf-toggle-button-position" data-condition-val="top-right,middle-right,bottom-right">
                                <label><?php esc_html_e('Offset from Right', 'super-product-filter') ?></label>
                                <div class="swpf-settings-input-field">
                                    <input type="number" name="swpf_settings[side_menu][offset_right]" value="<?php echo esc_attr($swpf_settings['side_menu']['offset_right']); ?>"> px
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
                                    <input type="number" name="swpf_settings[side_menu][toggle_button_size]" value="<?php echo esc_attr($swpf_settings['side_menu']['toggle_button_size']); ?>"> px
                                </div>
                            </li>

                            <li class="swpf-settings-list" data-condition-toggle="swpf-toggle-button-icon-type" data-condition-val="default_icon">
                                <label><?php esc_html_e('Icon Size', 'super-product-filter') ?></label>
                                <div class="swpf-settings-input-field">
                                    <input type="number" name="swpf_settings[side_menu][icon_size]" value="<?php echo esc_attr($swpf_settings['side_menu']['icon_size']); ?>"> px
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
                                    <input type="text" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" class="color-picker swpf-color-picker" name="swpf_settings[side_menu][button_bg_color]" value="<?php echo esc_attr($swpf_settings['side_menu']['button_bg_color']) ?>">
                                </div>
                            </li>

                            <li class="swpf-settings-list">
                                <label><?php esc_html_e('Background Color (Hover)', 'super-product-filter') ?></label>
                                <div class="swpf-settings-input-field swpf-color-input-field">
                                    <input type="text" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" class="color-picker swpf-color-picker" name="swpf_settings[side_menu][button_hover_bg_color]" value="<?php echo esc_attr($swpf_settings['side_menu']['button_hover_bg_color']) ?>">
                                </div>
                            </li>

                            <li class="swpf-settings-list">
                                <label><?php esc_html_e('Icon Color', 'super-product-filter') ?></label>
                                <div class="swpf-settings-input-field swpf-color-input-field">
                                    <input type="text" class="color-picker swpf-color-picker" name="swpf_settings[side_menu][button_icon_color]" value="<?php echo esc_attr($swpf_settings['side_menu']['button_icon_color']) ?>">
                                </div>
                            </li>

                            <li class="swpf-settings-list">
                                <label><?php esc_html_e('Icon Color (Hover)', 'super-product-filter') ?></label>
                                <div class="swpf-settings-input-field swpf-color-input-field">
                                    <input type="text" class="color-picker swpf-color-picker" name="swpf_settings[side_menu][button_hover_icon_color]" value="<?php echo esc_attr($swpf_settings['side_menu']['button_hover_icon_color']) ?>">
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
                                <input type="radio" name="swpf_settings[side_menu][panel_position]" value="left" <?php checked($swpf_settings['side_menu']['panel_position'], 'left'); ?>> <?php esc_html_e('Left', 'super-product-filter'); ?>
                            </label>
                            &nbsp;&nbsp;
                            <label>
                                <input type="radio" name="swpf_settings[side_menu][panel_position]" value="right" <?php checked($swpf_settings['side_menu']['panel_position'], 'right'); ?>> <?php esc_html_e('Right', 'super-product-filter'); ?>
                            </label>
                        </div>
                    </div>

                    <div class="swpf-field-wrap">
                        <label><?php esc_html_e('Panel Width', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <input class="swpf-range-input" type="number" name="swpf_settings[side_menu][panel_width]" value="<?php echo esc_attr($swpf_settings['side_menu']['panel_width']) ?>">
                            <select name="swpf_settings[side_menu][panel_width_unit]" class='swpf-unit'>
                                <option value="px" <?php selected($swpf_settings['side_menu']['panel_width_unit'], 'px'); ?>>px</option>
                                <option value="em" <?php selected($swpf_settings['side_menu']['panel_width_unit'], 'em'); ?>>em</option>
                                <option value="%" <?php selected($swpf_settings['side_menu']['panel_width_unit'], '%'); ?>>%</option>
                            </select>
                        </div>
                    </div>

                    <div class="swpf-field-wrap">
                        <label><?php esc_html_e('Panel Background Color', 'super-product-filter'); ?></label>
                        <div class="swpf-settings-input-field">
                            <input type="text" class="color-picker swpf-color-picker" name="swpf_settings[side_menu][panel_background_color]" value="<?php echo esc_attr($swpf_settings['side_menu']['panel_background_color']); ?>" />
                        </div>
                    </div>

                    <div class="swpf-field-wrap">
                        <label><?php esc_html_e('Show Scroll Bar', 'super-product-filter'); ?></label>

                        <div class="swpf-settings-input-field">
                            <div class="swpf-toggle-wrap">
                                <label class="swpf-toggle">
                                    <input type="checkbox" name="swpf_settings[side_menu][panel_show_scrollbar]" <?php checked($swpf_settings['side_menu']['panel_show_scrollbar'], 'on'); ?> class="swpf-filter-enable" data-condition="toggle" id="sidemenu-panel-show-scrollbar">
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
                                <input class="swpf-range-input" type="number" min="1" max="10" step="1" value="<?php echo esc_attr($swpf_settings['side_menu']['scrollbar_width']); ?>" name="swpf_settings[side_menu][scrollbar_width]" /> px
                            </div>
                        </div>
                    </div>

                    <div class="swpf-field-wrap" data-condition-toggle="sidemenu-panel-show-scrollbar">
                        <ul class="swpf-two-column-row">
                            <li>
                                <label><?php esc_html_e('Drag Rail Color', 'super-product-filter'); ?></label>
                                <div class="swpf-settings-input-field swpf-color-input-field">
                                    <input type="text" class="color-picker swpf-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="swpf_settings[side_menu][scrollbar_drag_rail_color]" value="<?php echo esc_attr($swpf_settings['side_menu']['scrollbar_drag_rail_color']); ?>" />
                                </div>
                            </li>

                            <li>
                                <label><?php esc_html_e('Drag Bar Color', 'super-product-filter'); ?></label>
                                <div class="swpf-settings-input-field swpf-color-input-field">
                                    <input type="text" class="color-picker swpf-color-picker" data-alpha-enabled="true" data-alpha-custom-width="30px" data-alpha-color-type="hex" name="swpf_settings[side_menu][scrollbar_drag_bar_color]" value="<?php echo esc_attr($swpf_settings['side_menu']['scrollbar_drag_bar_color']) ?>" />
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>


            <div class="swpf-sub-panel swpf-design-styles">
                <div class="swpf-settings-list-row">
                    <h3 style="margin: 0"><?php esc_html_e('Filter Styles', 'super-product-filter'); ?></h3>
                    <p class="swpf-desc" style="margin-top:6px">
                        <?php esc_html_e('The free version uses the first style of each control. Pro opens up the rest, along with the colour and size settings for each one.', 'super-product-filter'); ?>
                    </p>

                    <div class="swpf-two-column-row">
                        <?php
                        swpf_pro_field(
                            esc_html__('Checkbox &amp; Radio Style', 'super-product-filter'),
                            array(
                                esc_html__('Style 1 — plain (in use)', 'super-product-filter'),
                                esc_html__('Style 2 — filled with a tick', 'super-product-filter'),
                                esc_html__('Style 3 — outlined', 'super-product-filter'),
                                esc_html__('Style 4 — soft fill', 'super-product-filter'),
                                esc_html__('Style 5 — rounded fill', 'super-product-filter'),
                                esc_html__('Style 6 — inner square', 'super-product-filter'),
                                esc_html__('Style 7 — light tick', 'super-product-filter'),
                                esc_html__('Style 8 — solid block', 'super-product-filter'),
                                esc_html__('Style 9 — hand-drawn tick', 'super-product-filter'),
                                esc_html__('Style 10 — pill button', 'super-product-filter'),
                                esc_html__('Style 11 — bordered fill', 'super-product-filter'),
                            ),
                            esc_html__('11 styles in Pro.', 'super-product-filter')
                        );

                        swpf_pro_field(
                            esc_html__('Toggle Style', 'super-product-filter'),
                            array_merge(
                                array(esc_html__('Style 1 (in use)', 'super-product-filter')),
                                array_map(
                                    function ($swpf_n) {
                                        /* translators: %d: style number. */
                                        return sprintf(esc_html__('Style %d', 'super-product-filter'), $swpf_n);
                                    },
                                    range(2, 10)
                                )
                            ),
                            esc_html__('10 styles in Pro.', 'super-product-filter')
                        );

                        swpf_pro_field(
                            esc_html__('Button Style', 'super-product-filter'),
                            array_merge(
                                array(esc_html__('Style 1 (in use)', 'super-product-filter')),
                                array_map(
                                    function ($swpf_n) {
                                        /* translators: %d: style number. */
                                        return sprintf(esc_html__('Style %d', 'super-product-filter'), $swpf_n);
                                    },
                                    range(2, 10)
                                )
                            ),
                            esc_html__('10 styles in Pro.', 'super-product-filter')
                        );

                        swpf_pro_field(
                            esc_html__('Price Slider Style', 'super-product-filter'),
                            array_merge(
                                array(esc_html__('Style 1 (in use)', 'super-product-filter')),
                                array_map(
                                    function ($swpf_n) {
                                        /* translators: %d: style number. */
                                        return sprintf(esc_html__('Style %d', 'super-product-filter'), $swpf_n);
                                    },
                                    range(2, 10)
                                )
                            ),
                            esc_html__('10 styles in Pro.', 'super-product-filter')
                        );

                        swpf_pro_field(
                            esc_html__('Dropdown Style', 'super-product-filter'),
                            array(
                                esc_html__('Default (in use)', 'super-product-filter'),
                                esc_html__('Accent Bar', 'super-product-filter'),
                                esc_html__('Card', 'super-product-filter'),
                                esc_html__('Heavy', 'super-product-filter'),
                                esc_html__('Notched', 'super-product-filter'),
                                esc_html__('Offset', 'super-product-filter'),
                            ),
                            esc_html__('6 styles in Pro, plus a styled dropdown in place of the browser one.', 'super-product-filter')
                        );

                        swpf_pro_field(
                            esc_html__('Multi Select Style', 'super-product-filter'),
                            array_merge(
                                array(esc_html__('Style 1 (in use)', 'super-product-filter')),
                                array_map(
                                    function ($swpf_n) {
                                        /* translators: %d: style number. */
                                        return sprintf(esc_html__('Style %d', 'super-product-filter'), $swpf_n);
                                    },
                                    range(2, 6)
                                )
                            ),
                            esc_html__('6 styles in Pro.', 'super-product-filter')
                        );

                        swpf_pro_field(
                            esc_html__('Colour Swatch Style', 'super-product-filter'),
                            array(
                                esc_html__('Style 1 — corner badge (in use)', 'super-product-filter'),
                                esc_html__('Offset Shadow', 'super-product-filter'),
                                esc_html__('Tick', 'super-product-filter'),
                                esc_html__('Inset Dot', 'super-product-filter'),
                                esc_html__('Inner Ring', 'super-product-filter'),
                                esc_html__('Notch', 'super-product-filter'),
                            ),
                            esc_html__('6 styles in Pro, with square or round swatches.', 'super-product-filter')
                        );

                        swpf_pro_field(
                            esc_html__('Image Swatch Style', 'super-product-filter'),
                            array(
                                esc_html__('Style 1 — corner badge (in use)', 'super-product-filter'),
                                esc_html__('Overlay', 'super-product-filter'),
                                esc_html__('Spotlight', 'super-product-filter'),
                                esc_html__('Inset Frame', 'super-product-filter'),
                                esc_html__('Offset Shadow', 'super-product-filter'),
                                esc_html__('Caption Bar', 'super-product-filter'),
                            ),
                            esc_html__('6 styles in Pro.', 'super-product-filter')
                        );

                        swpf_pro_field(
                            esc_html__('Loading Animation', 'super-product-filter'),
                            array_merge(
                                array(esc_html__('3 included', 'super-product-filter')),
                                array_map(
                                    function ($swpf_n) {
                                        /* translators: %d: animation number. */
                                        return sprintf(esc_html__('Animation %d', 'super-product-filter'), $swpf_n);
                                    },
                                    range(4, 16)
                                ),
                                array(esc_html__('Your own image', 'super-product-filter'))
                            ),
                            esc_html__('16 animations in Pro, or upload your own.', 'super-product-filter')
                        );

                        swpf_pro_field(
                            esc_html__('Control Colours &amp; Sizes', 'super-product-filter'),
                            array(
                                esc_html__('Border, background, icon and active colours', 'super-product-filter'),
                                esc_html__('Size for every control', 'super-product-filter'),
                            ),
                            esc_html__('Set the colours and size of each control separately.', 'super-product-filter')
                        );
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>