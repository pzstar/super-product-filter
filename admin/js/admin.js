(function ($) {
    "use strict";

    $(document).ready(function ($) {

        var ajaxUrl = swpf_admin_js_obj.ajaxurl;
        var adminNonce = swpf_admin_js_obj.ajax_nonce;
        var admin_url = swpf_admin_js_obj.admin_url;

        $('.swpf-color-picker').wpColorPicker();

        codeMirrorDisplay();

        // Save swpf_settings using ajax
        $('#mainform').on('submit', function () {
            $('input[name=save]').hide();
            var data = {
                action: "swpf_save_options",
                formdata: $(this).serialize()
            };
            $.post(
                ajaxUrl,
                data,
                function (data) {
                    console.log(data);
                    return false;
                }
            );

            return false;
        });

        $(document).on('widget-added widget-updated', function (event, widget) {
            $(".swpf-selectize").selectize({
                plugins: ['remove_button', 'drag_drop'],
                delimiter: ',',
                persist: false
            });

            $('body').find('select.swpf-widget-selected-filter').each(function () {
                var filter_id = $(this).val();
                if (filter_id == '' || typeof filter_id == 'undefined') {
                    $(this).closest('.swpf-widget-field-wrap').find('a').attr('href', '#');
                }
            });
            $('body').on('change', '.swpf-widget-selected-filter', function (e) {
                var selected_filter_id = $(this).val();
                if (selected_filter_id != '') {
                    $(this).closest('.swpf-widget-field-wrap').find('a').attr('href', admin_url + '?action=edit&post=' + selected_filter_id);
                }
            });
        });

        $(".swpf-selectize").selectize({
            plugins: ['remove_button', 'drag_drop'],
            delimiter: ',',
            persist: false
        });

        $('.swpf-options-fields-wrap .swpf-field-sortable').sortable({
            containment: "parent",
            axis: "y",
            handle: "span.swpf-sortable-box"
        });

        /* Backend Tabs Toggle Buttons Actions */
        $('body').on('click', '.swpf-tab', function () {
            var selected_menu = $(this).data('tab');
            var hideDivs = $(this).data('tohide');

            // Display The Clicked Tab Content
            $('body').find('.' + hideDivs).hide();
            $('body').find('#' + selected_menu).show();

            // Add and remove the class for active tab
            $(this).parent().find('.swpf-tab').removeClass('swpf-tab-active');
            $(this).addClass('swpf-tab-active');

            if ($(this).find('input'))
                $(this).find('input').prop('checked', true);
        });

        $('body').on('click', '.swpf-sub-tabs li a', function (e) {
            e.preventDefault();
            var tab = $(this).attr('data-tab');
            $(this).closest('.swpf-sub-tabs').find('li').removeClass('swpf-active');
            $(this).parent('li').addClass('swpf-active');

            $('.swpf-sub-panel-wrap').find('.swpf-sub-panel').hide();
            $('.swpf-sub-panel-wrap').find('.' + tab).show();
        });

        $('span.swpf-toggle-box').on('click', function () {
            if ($(this).hasClass('active')) {
                $(this).removeClass('icofont-caret-up active').addClass('icofont-caret-down');
                $(this).closest('.swpf-tax-heading-wrap').next().slideUp();
            } else {
                $(this).removeClass('icofont-caret-down').addClass('icofont-caret-up active');
                $(this).closest('.swpf-tax-heading-wrap').next().slideDown();
            }
        });

        // Copy Shortcode
        $('body').on('click', '#swpf-copy-shortcode', function (e) {
            e.preventDefault();
            const copyCode = document.getElementById('swpf-shortcode-field');
            copyCode.select();
            document.execCommand('copy');
            var copiedText = window.getSelection().toString();
            $(this).closest('.swpf-display-with-shortcode').find('#swpf-copied-shortcode').html('Your shortcode ' + copiedText + ' is copied!').css('color', 'green');
            $(this).closest('.swpf-display-with-shortcode').find('#swpf-copied-shortcode').show().delay(1000).fadeOut();
        });

        /* Custom Image Upload */
        $('body').on('click', '.swpf-image-upload', function (e) {
            e.preventDefault();
            var $this = $(this);

            var image = wp.media({
                title: 'Upload Image',
                multiple: false
            }).open().on('select', function () {
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;

                if (typeof image_url !== 'undefined') {
                    $this.closest('.swpf-icon-image-uploader').addClass('swpf-image-uploaded');
                    $this.closest('.swpf-icon-image-uploader').find('.swpf-custom-menu-image-icon').html(''); // Empty the previous image
                    $this.closest('.swpf-icon-image-uploader').find('.swpf-upload-background-url').val(image_url);
                    $this.closest('.swpf-icon-image-uploader').find('.swpf-custom-menu-image-icon').append('<img src="' + image_url + '" width="100"/>');
                    $this.closest('.swpf-icon-image-uploader').find('.swpf-custom-menu-image-icon').show();
                } else {
                    $this.closest('.swpf-icon-image-uploader').find('.swpf-custom-menu-image-icon').hide();
                }
            });
        });

        /* Remove Uploaded Custom Image */
        $('body').on('click', '.swpf-image-remove', function () {
            $(this).closest('.swpf-icon-image-uploader').removeClass('swpf-image-uploaded');
            $(this).closest('.swpf-icon-image-uploader').find('.swpf-custom-menu-image-icon').html('');
            $(this).closest('.swpf-icon-image-uploader').find('.swpf-upload-background-url').val('');
        });

        // Hide Show Custom Terms Option On Popup
        $('body').on('click', '.swpf-show-custom-term-options', function (e) {
            e.preventDefault();
            const customFormOptBtn = $(this),
                taxKey = customFormOptBtn.attr('data-tax-key'),
                taxID = customFormOptBtn.attr('data-tax-id'),
                parent = customFormOptBtn.closest('.swpf-custom-term-options-wrap'),
                termOpt = $(document).find('.swpf-custom-terms-options.swpf-tax-key-' + taxKey),
                displayType = $(document).find('[name="swpf_settings[display_type][' + taxKey + ']"]').val();
            if (termOpt.length > 0) {
                termOpt
                    .detach()
                    .appendTo(parent)
                    .addClass('swpf-custom-terms-options-active')
                    .removeClass(function (index, css) {
                        return (css.match(/\bswpf-field-settings-display-type-\S+/g) || []).join(' ');
                    })
                    .addClass('swpf-field-settings-display-type-' + displayType);
            } else {
                const termsCustomizeSettings = customFormOptBtn.attr('data-terms-customize-settings');
                customFormOptBtn.addClass('swpf-btn-loading');
                $.ajax({
                    url: ajaxUrl,
                    type: "POST",
                    data: ({
                        'action': 'swpf_show_custom_term_options',
                        'tax_id': taxID,
                        'tax_key': taxKey,
                        'display_type': displayType,
                        'terms_customize_settings': JSON.parse(termsCustomizeSettings),
                        'wp_nonce': adminNonce
                    }),
                    success: function (response) {
                        customFormOptBtn.removeClass('swpf-btn-loading');
                        parent.append(response);
                        setTimeout(function () {
                            $(document).find('.swpf-custom-terms-options.swpf-tax-key-' + taxKey + ' .swpf-color-picker').wpColorPicker();
                        }, 500);
                    }
                });
                // $(this).closest('.swpf-custom-term-options-wrap').find('.swpf-custom-terms-options').addClass('swpf-custom-terms-options-active');
            }
        });

        // close Custom Terms Options Fields
        $('body').on('click', '.swpf-custom-terms-option-close', function () {
            const termOpt = $(this).parent('.swpf-custom-terms-options');
            termOpt.removeClass('swpf-custom-terms-options-active').detach().appendTo('body');
        });



        $('.swpf-range-input').each(function () {
            var $dis = $(this);
            var defaultValue = $dis.val() ? parseFloat($dis.val()) : '';
            $dis.prev('.swpf-range-slider').slider({
                range: "min",
                value: defaultValue,
                min: parseFloat($dis.attr('min')),
                max: parseFloat($dis.attr('max')),
                step: parseFloat($dis.attr('step')),
                slide: function (event, ui) {
                    $dis.val(ui.value);
                }
            });
        });

        // Update slider if the input field loses focus as it's most likely changed
        $('.swpf-range-input').blur(function () {
            var resetValue = isNaN($(this).val()) ? '' : $(this).val();

            if (resetValue) {
                var sliderMinValue = parseFloat($(this).attr('min'));
                var sliderMaxValue = parseFloat($(this).attr('max'));
                // Make sure our manual input value doesn't exceed the minimum & maxmium values
                if (resetValue < sliderMinValue) {
                    resetValue = sliderMinValue;
                }
                if (resetValue > sliderMaxValue) {
                    resetValue = sliderMaxValue;
                }
            }
            $(this).val(resetValue);
            $(this).prev('.swpf-range-slider').slider('value', resetValue);
        });

        $(document).on('change', '.typography_face', function () {

            var font_family = $(this).val();
            var $this = $(this);
            $.ajax({
                type: 'post',
                url: ajaxUrl,
                data: {
                    action: 'swpf_get_google_font_variants',
                    font_family: font_family,
                    wp_nonce: adminNonce
                },
                beforeSend: function () {
                    $this.closest('.swpf-typography-font-family').next('.swpf-typography-font-style').addClass('swpf-typography-loading');
                },
                success: function (response) {
                    $this.closest('.swpf-typography-font-family').next('.swpf-typography-font-style').removeClass('swpf-typography-loading');
                    $this.closest('.swpf-typography-font-family').next('.swpf-typography-font-style').find('select').html(response).trigger("chosen:updated").trigger('change');
                }
            });
        });

        /*Select all checkboxes*/
        $(document).on('change', '#swpf_display_in_all_pages', function () {  //"select all" change 
            $(".swpf_display_checkbox_pages").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
        });

        $(document).on('change', '#swpf_display_in_all_archives', function () {  //"select all" change 
            $(".swpf_display_checkbox_archives").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
        });

        //".swpf_display_checkbox_pages" change
        $(document).on('change', '.swpf_display_checkbox_pages', function () {

            //uncheck "select all", if one of the listed checkbox item is unchecked
            if (false == $(this).prop("checked")) { //if this item is unchecked
                $("#swpf_display_in_all_pages").prop('checked', false); //change "select all" checked status to false
            }

            //check "select all" if all checkbox items are checked
            if ($('.swpf_display_checkbox_pages:checked').length == $('.swpf_display_checkbox_pages').length) {
                $("#swpf_display_in_all_pages").prop('checked', true);
            }
        });

        $(document).on('change', '.swpf_display_checkbox_archives', function () {
            //uncheck "select all", if one of the listed checkbox item is unchecked
            if (false == $(this).prop("checked")) { //if this item is unchecked
                $("#swpf_display_in_all_archives").prop('checked', false); //change "select all" checked status to false
            }
            //check "select all" if all checkbox items are checked
            if ($('.swpf_display_checkbox_archives:checked').length == $('.swpf_display_checkbox_archives').length) {
                $("#swpf_display_in_all_archives").prop('checked', true);
            }
        });
        $('body').find(".typography_face, .typography_font_style, .typography_text_transform, .typography_text_decoration").chosen({width: "200%"});

        var delay = (function () {
            var timer = 0;
            return function (callback, ms) {
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            };
        })();

        $(document).on('click', '.swpf-hide-show-cpt-posts', function () {
            var posttype = '#swpf-cpt-' + $(this).data('posttype');
            $(this).is(':checked') ? $(posttype).hide() : $(posttype).show();
        });

        // Icon Control JS
        $('body').on('click', '.swpf-icon-box-wrap .swpf-icon-list li', function () {
            var icon_class = $(this).find('i').attr('class');
            $(this).closest('.swpf-icon-box').find('.swpf-icon-list li').removeClass('icon-active');
            $(this).addClass('icon-active');
            $(this).closest('.swpf-icon-box').prev('.swpf-selected-icon').children('i').attr('class', '').addClass(icon_class);
            $(this).closest('.swpf-icon-box').slideUp()
            $(this).closest('.swpf-icon-box').next('input').val(icon_class).trigger('change');
        });

        $('body').on('click', '.swpf-icon-box-wrap .swpf-selected-icon', function () {
            if (!$(this).next().is('.swpf-icon-box')) {
                var iconbox = $('#swpf-icon-box').clone();
                iconbox.removeAttr('id');
                iconbox.insertAfter($(this));
            }
            $(this).next().slideToggle();
        });

        $('body').on('change', '.swpf-icon-box-wrap .swpf-icon-search select', function () {
            var $ele = $(this);
            var selected = $ele.val();
            $ele.parent('.swpf-icon-search').siblings('.swpf-icon-list').hide().removeClass('active');
            $ele.parent('.swpf-icon-search').siblings('.' + selected).show().addClass('active');
            $ele.closest('.swpf-icon-box').find('.swpf-icon-search-input').val('');
            $ele.parent('.swpf-icon-search').siblings('.' + selected).find('li').show();
        });

        $('body').on('keyup', '.swpf-icon-box-wrap .swpf-icon-search input', function (e) {
            var $input = $(this);
            var keyword = $input.val().toLowerCase();
            var search_criteria = $input.closest('.swpf-icon-box').find('.swpf-icon-list.active i');
            delay(function () {
                $(search_criteria).each(function () {
                    if ($(this).attr('class').indexOf(keyword) > -1) {
                        $(this).parent().show();
                    } else {
                        $(this).parent().hide();
                    }
                });
            }, 500);
        });

        $(document).on('click', '.swpf-filters-title-toggle', function () {
            jQuery(this).parent('.swpf-filters-title').parent().toggleClass('swpf-active');
            if (jQuery(this).parent('.swpf-filters-title').parent().hasClass('swpf-active')) {
                jQuery(this).removeClass('icofont-plus').addClass('icofont-minus');
                jQuery(this).parent().siblings().slideDown();
            } else {
                jQuery(this).removeClass('icofont-minus').addClass('icofont-plus');
                jQuery(this).parent().siblings().slideUp();
            }
        });

        $(document).find('a.swpf-filter-scroll-to-section').on('click', function (e) {
            if (this.hash !== "") {
                e.preventDefault();
                var hash = this.hash;
                $('.swpf-each-items-wrap').removeClass('swpf-highlight');

                if ($(hash).is(':visible')) {
                    $('html, body').animate({
                        scrollTop: $(hash).offset().top - 200
                    }, 800, function () {
                        $(hash).addClass('swpf-highlight');
                        setTimeout(function () {
                            $(hash).removeClass('swpf-highlight');
                        }, 4000);
                    });
                }
            }
        });

        /*Code mirror activation*/
        function codeMirrorDisplay() {
            $.each($('.swpf-codemirror-css-textarea'), function (key, value) {
                const $codeMirrorCSSEditors = $(this);

                if ($codeMirrorCSSEditors.length) {
                    var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
                    editorSettings.codemirror = _.extend(
                        {},
                        editorSettings.codemirror,
                        {
                            lineNumbers: true,
                            lineWrapping: true,
                            autoRefresh: true,
                            mode: 'css',
                        }
                    );
                    var editor = wp.codeEditor.initialize($codeMirrorCSSEditors, editorSettings);
                }
            });


            $.each($('.swpf-codemirror-js-textarea'), function (key, value) {
                const $codeMirrorJSEditors = $(this);

                if ($codeMirrorJSEditors.length) {
                    var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
                    editorSettings.codemirror = _.extend(
                        {},
                        editorSettings.codemirror,
                        {
                            lineNumbers: true,
                            lineWrapping: true,
                            autoRefresh: true,
                            mode: 'javascript',
                        }
                    );
                    var editor = wp.codeEditor.initialize($codeMirrorJSEditors, editorSettings);
                }
            });
        }

        /* Custom File Upload */
        function swpfReadFile(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    var htmlPreview =
                        '<p>' + input.files[0].name + '</p>';
                    var wrapperZone = $(input).parent();
                    var previewZone = $(input).parent().parent().find('.swpf-preview-zone');
                    var boxZone = $(input).parent().parent().find('.swpf-preview-zone').find('.box').find('.box-body');

                    wrapperZone.removeClass('dragover');
                    previewZone.removeClass('hidden');
                    boxZone.empty();
                    boxZone.append(htmlPreview);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        function reset(e) {
            e.wrap('<form>').closest('form').get(0).reset();
            e.unwrap();
        }

        $(".swpf-dropzone").change(function () {
            swpfReadFile(this);
        });

        $('.swpf-dropzone-wrapper').on('dragover', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('dragover');
        });

        $('.swpf-dropzone-wrapper').on('dragleave', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
        });

        $('.swpf-remove-preview').on('click', function () {
            var boxZone = $(this).parents('.swpf-preview-zone').find('.box-body');
            var previewZone = $(this).parents('.swpf-preview-zone');
            var dropzone = $(this).parents('.swpf-settings-input-field').find('.swpf-dropzone');
            boxZone.empty();
            previewZone.addClass('hidden');
            reset(dropzone);
        });

        // Linked button
        $('.swpf-linked').on('click', function () {
            $(this).closest('.swpf-dimension-fields').addClass('swpf-not-linked');
        });

        // Unlinked button
        $('.swpf-unlinked').on('click', function () {
            $(this).closest('.swpf-dimension-fields').removeClass('swpf-not-linked');
        });

        // Values linked inputs
        $('.swpf-dimension-fields input').on('input', function () {
            var $val = $(this).val();
            $(this).closest('.swpf-dimension-fields:not(.swpf-not-linked)').find('input').each(function (key, value) {
                $(this).val($val).change();
            });
        });


        $(document).on('click', '.swpf-save-term-options', function (e) {
            e.preventDefault();
            const customFormOptSaveBtn = $(this);
            customFormOptSaveBtn.addClass('swpf-btn-loading');
            const customTermForm = $(this).closest('form'),
                taxID = customTermForm.attr('data-tax-id');

            var formData = {};
            $.each(customTermForm.serializeArray(), function () {
                formData[this.name] = this.value;
            });

            $.ajax({
                type: "POST",
                url: ajaxUrl,
                data: ({
                    'action': 'swpf_save_custom_term_options',
                    'form_data': formData,
                    'tax_id': taxID,
                    'wp_nonce': adminNonce
                }),
                success: function () {
                    customFormOptSaveBtn.removeClass('swpf-btn-loading');
                    $('.swpf-alert').addClass('swpf-alert-success');
                    $('.swpf-alert span').html('Settings Saved!');
                    $('.swpf-alert').addClass('swpf-alert-active');
                    clearTimeout();
                    setTimeout(function () {
                        if ($('.swpf-alert').hasClass('swpf-alert-active')) {
                            $('.swpf-alert').removeClass('swpf-alert-active');
                            $('.swpf-alert').removeClass('swpf-alert-success swpf-alert-warning swpf-alert-neutral');
                        }
                    }, 3500);
                }
            });
        })
    });

})(jQuery);