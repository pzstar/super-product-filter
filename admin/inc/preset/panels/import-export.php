<?php
defined('ABSPATH') || die();
?>

<div class="swpf-options-fields-wrap tab-content swpf-settings-content" id="import-export-settings" style="display: none;">

    <div class="swpf-field-inline-wrap">
        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Export Settings', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <div class="swpf-imex-form">
                    <input type="hidden" form="swpf-imex-export-form" name="swpf_imex_action" value="export_settings" />
                    <input type="hidden" form="swpf-imex-export-form" name="swpf_filter_id" value="<?php echo esc_attr($post_id); ?>" />
                    <?php swpf_imex_nonce_field("swpf_imex_export_nonce", "swpf-imex-export-form"); ?>
                    <button class="button button-primary" form="swpf-imex-export-form" id="swpf_export" name="swpf_export"><i class='icofont-download'></i> <?php esc_html_e("Export Filter Settings", "super-product-filter") ?></button>
                </div>
                <p class="swpf-desc"><?php esc_html_e('Settings in Filters Tab is not exported. Settings Tab, Display Setting Tab, Designs Tab, and Custom Code Tab are exported.', 'super-product-filter'); ?></p>
            </div>
        </div>

        <div class="swpf-field-wrap">
            <label><?php esc_html_e('Import Settings', 'super-product-filter'); ?></label>
            <div class="swpf-settings-input-field">
                <div class="swpf-imex-form">
                    <div class="swpf-preview-zone hidden">
                        <div class="box box-solid">
                            <div class="box-body"></div>
                            <button type="button" class="button swpf-remove-preview">
                                <i class="icofont-close-circled"></i>
                            </button>
                        </div>
                    </div>
                    <div class="swpf-dropzone-wrapper">
                        <div class="swpf-dropzone-desc">
                            <i class="icofont-download"></i>
                            <p><?php esc_html_e("Choose an json file or drag it here", "super-product-filter"); ?></p>
                        </div>
                        <input type="file" form="swpf-imex-import-form" name="swpf_import_file" class="swpf-dropzone">
                    </div>
                    <button class="button button-primary" form="swpf-imex-import-form" id="swpf_import" type="submit" name="swpf_import"><i class='icofont-download'></i> <?php esc_html_e("Import", "super-product-filter") ?></button>
                    <input type="hidden" form="swpf-imex-import-form" name="swpf_imex_action" value="import_settings" />
                    <input type="hidden" form="swpf-imex-import-form" name="swpf_filter_id" value="<?php echo esc_attr($post_id); ?>" />
                    <?php swpf_imex_nonce_field("swpf_imex_import_nonce", "swpf-imex-import-form"); ?>

                </div>
            </div>
        </div>
    </div>
</div>