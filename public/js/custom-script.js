"use strict";

const SuperWooProductFilterUtils = {
    stringMatches: function (string, regexp) {
        var matches = [];
        string.replace(regexp, function () {
            var arr = ([]).slice.call(arguments, 0);
            var extras = arr.splice(-2);
            arr.index = extras[0];
            arr.input = extras[1];
            matches.push(arr);
        });
        return matches.length ? matches : null;
    }
}

class SuperWooProductFilter {
    constructor(el) {
        if (!(el instanceof jQuery) || !el.hasClass('swpf-main-wrap')) {
            console.error('Invalid Form Obj!');
        }

        this.el = el;
        this.$el = {
            form: el.find('form')
        };

        this.config = JSON.parse(el.find('form').attr('data-config'));
        this.isPagination = false;
        this.initial();
    }

    initial() {
        this.renderPriceFilters();
        // add data-attr swpf-preset to pagination wrapper to make sure pagination works with filtered products
        jQuery(this.config.pagination_selector).attr('data-swpf-preset', this.config.swpf_preset);
        this.eventsBind();
    }

    getEl(selector) {
        if (this.$el[selector]) {
            return this.$el[selector];
        }

        const el = jQuery(selector, this.el);

        if (el.length) {
            this.$el[selector] = el;
            return el;
        }

        return false;
    }

    renderPriceFilters() {
        if (this.getEl('.swpf-pricerange.slider-price')) {
            const swpf = this;
            this.$el['.swpf-pricerange.slider-price'].each(function () {
                const $this = jQuery(this),
                    min = parseInt(jQuery(this).find('.price-min').val()),
                    max = parseInt(jQuery(this).find('.price-max').val());
                let from = min,
                    to = max;
                if ($this.find('.price-from').val() != '') {
                    from = parseInt($this.find('.price-from').val());
                }
                if ($this.find('.price-to').val() != '') {
                    to = parseInt($this.find('.price-to').val());
                }

                const rangeSlider = $this.find(".swpf-range-slider");
                const ionSlider = $this.find(".swpf-irs-slider");

                if (rangeSlider.length > 0) {
                    rangeSlider.slider({
                        range: true,
                        min: min,
                        max: max,
                        values: [from, to],
                        slide: function (event, ui) {
                            $this.find(".price-from").val(ui.values[0]);
                            $this.find('.swpf-price-from .price.amount').html(ui.values[0]);
                            $this.find(".price-to").val(ui.values[1]);
                            $this.find('.swpf-price-to .price.amount').html(ui.values[1]);
                        },
                        stop: function (event, ui) {
                            if (ui.values[0] == min && ui.values[1] == max) {
                                $this.find(".price-from").val('');
                                $this.find(".price-to").val('');
                            }
                            swpf.getEl('input[name="paged"]').val('');
                            if (swpf.$el.form.hasClass('swpf-instant-filtering')) {
                                swpf.filter();
                            }
                        }
                    });

                    jQuery("input.amount").val("$" + jQuery(".slider-range").slider("values", 0) + " - $" + jQuery(".slider-range").slider("values", 1));
                }

            });
        }
    }

    eventsBind() {
        if (this.$el.form.hasClass('swpf-instant-filtering')) {
            this.el.find('input:not(.swpf-irs-slider):not(.swpf-filter-search-input), select').on('change', (e) => {
                e.preventDefault();
                this.filter();
            });
        } else {
            this.el.on('click', '.swpf-form .swpf-form-submit', (e) => {
                e.preventDefault();
                jQuery('button.swpf-form-submit').addClass('swpf-button-clicked');
                this.filter();
            });
        }

        this.el.on('click', '.swpf-remove-filter-item,.swpf-clear-all', (e) => {
            e.preventDefault();
            this.removeFilterItem(e);
        });

        // Toggle Category Child Lists 
        this.$el.form.on('click', '.swpf-term-toggle', function () {
            if (!jQuery(this).hasClass('swpf-toggle-active')) {
                jQuery(this).addClass('swpf-toggle-active');
                jQuery(this).find('i').removeClass('fa-caret-down').addClass('fa-caret-up');
            } else {
                jQuery(this).removeClass('swpf-toggle-active');
                jQuery(this).find('i').removeClass('fa-caret-up').addClass('fa-caret-down');
            }
        });

        if (this.config.show_filter_toggle) {
            var mainEl = this.el;
            jQuery('body').on('click', '.swpf-filter-toggle-bar', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (jQuery(this).hasClass('swpf-filter-toggled')) {
                    jQuery(this).removeClass('swpf-filter-toggled');
                    jQuery(this).siblings('.swpf-main-wrap').slideUp();
                } else {
                    jQuery(this).addClass('swpf-filter-toggled');
                    jQuery(this).siblings('.swpf-main-wrap').slideDown();
                }
            });
        }

        // Add Active Class To Title Toggle Wrapper
        jQuery('.swpf-filter-title-toggle').each(function () {
            if (jQuery(this).hasClass('swpf-minus-icon')) {
                jQuery(this).parent('.swpf-filter-title').parent().addClass('swpf-active');
            } else {
                jQuery(this).parent('.swpf-filter-title').parent().removeClass('swpf-active');
            }
        });
        this.$el.form.on('click', '.swpf-filter-title-toggle', function () {
            jQuery(this).parent('.swpf-filter-title').parent().toggleClass('swpf-active');
            if (jQuery(this).parent('.swpf-filter-title').parent().hasClass('swpf-active')) {
                jQuery(this).removeClass('swpf-plus-icon').addClass('swpf-minus-icon');
                jQuery(this).parent().siblings().slideDown();
            } else {
                jQuery(this).removeClass('swpf-minus-icon').addClass('swpf-plus-icon');
                jQuery(this).parent().siblings().slideUp();
            }
        });

        jQuery("select.swpf-multiselect").each(function () {
            const multiSelectOpt = jQuery(this),
                placeholderTxt = multiSelectOpt.closest('.swpf-multiselect-wrap').attr('data-placeholder');

            multiSelectOpt.chosen({
                width: '100%',
                placeholder_text: placeholderTxt ? placeholderTxt : ''
            });
        })
    }

    removeFilterItem(e) {
        const filterItem = jQuery(e.currentTarget), //get the clear-filter button el
            filterItemVal = filterItem.val(), //get the clear-filter button el val
            filterItemArr = SuperWooProductFilterUtils.stringMatches(filterItem.attr('name'), /\[(.*?)\]/g);

        /* When Filter Item is Clicked */
        if (filterItemArr) {
            const filterItemName = filterItemArr[0][1];
            var selected_item;
            try {

                if (filterItemName == 'categories') {
                    if (this.getEl('.swpf-product_cat-wrap input')) {
                        selected_item = this.$el['.swpf-product_cat-wrap input'].filter(function () {
                            return this.value == filterItemVal;
                        });
                        selected_item.prop('checked', false);
                        selected_item.closest('.selected').removeClass('selected');
                    } else if (this.getEl('.swpf-product_cat-wrap option')) {
                        selected_item = this.$el['.swpf-product_cat-wrap option'].filter(function () {
                            return this.value == filterItemVal;
                        });
                        selected_item.prop('selected', false);
                    }

                } else if (filterItemName == 'attribute') {
                    var filterItemAttr = filterItemArr[1][1],
                        tagType = jQuery('.swpf-' + filterItemAttr + '-wrap').find('.swpf-tax-list-wrapper').children(':last-child').children(':first-child');
                    if (tagType.prop('tagName').toLowerCase() == 'select') {
                        // For Select Type
                        var selectOptionVal = jQuery('.swpf-' + filterItemAttr + '-wrap').find('select option[value=' + filterItemVal + ']');
                        selectOptionVal.prop('selected', false);
                    } else if (tagType.prop('tagName').toLowerCase() == 'div') {
                        // for input type
                        if (jQuery('.swpf-' + filterItemArr + '-wrap').find('.swpf-tax-list-wrapper').first()) {
                        }
                        selected_item = this.getEl('.swpf-' + filterItemAttr + '-wrap input').filter(function () {
                            return this.value == filterItemVal
                        });
                        selected_item.prop('checked', false);
                        selected_item.closest('.selected').removeClass('selected');
                    }

                } else if (filterItemName == 'tags') {
                    if (this.getEl('.swpf-product_tag-wrap input')) {
                        selected_item = this.$el['.swpf-product_tag-wrap input'].filter(function () {
                            return this.value == filterItemVal;
                        });
                        selected_item.prop('checked', false);
                        selected_item.closest('.selected').removeClass('selected');
                    } else if (this.getEl('.swpf-product_tag-wrap option')) {
                        selected_item = this.$el['.swpf-product_tag-wrap option'].filter(function () {
                            return this.value == filterItemVal;
                        });
                        selected_item.prop('selected', false);
                    }

                } else if (filterItemName == 'visibility') {
                    var filterItemAttr = filterItemArr[1][1],
                        tagType = jQuery('.swpf-product_visibility-wrap').find('.swpf-tax-list-wrapper').children(':first-child');
                    if (tagType.prop('tagName').toLowerCase() == 'select') {
                        var selectOptionVal = jQuery('.swpf-product_visibility-wrap').find('select option[value=' + filterItemVal + ']');
                        selectOptionVal.prop('selected', false);
                    } else if (tagType.prop('tagName').toLowerCase() == 'div') {
                        var removingTag = this.getEl('.swpf-product_visibility-wrap input[value="' + filterItemAttr + '"]');
                        removingTag.prop('checked', false);
                        removingTag.closest('.swpf-filter-item').removeClass('selected');
                    }

                } else if (filterItemName == 'price') {
                    this.getEl('input.price-to, input.price-from').attr('value', '');
                    if (this.getEl(".swpf-range-slider") && this.getEl(".swpf-filter-byprice.slider-price .price-min") && this.getEl(".swpf-filter-byprice.slider-price .price-max")) {
                        this.getEl(".swpf-range-slider").slider("values", [this.getEl(".swpf-filter-byprice.slider-price .price-min").val(), this.getEl(".swpf-filter-byprice.slider-price .price-max").val()]);
                    }

                    this.getEl(".swpf-filter-byprice.slider-price .swpf-price-from .price.amount").text(this.getEl(".swpf-filter-byprice.slider-price .price-min").val());
                    this.getEl(".swpf-filter-byprice.slider-price .swpf-price-to .price.amount").text(this.getEl(".swpf-filter-byprice.slider-price .price-max").val());

                } else if (filterItemName == 'rating-from') {
                    this.getEl('input[name*=rating-from]').attr('value', 0);
                    this.getEl('.swpf-list-rating .selected').removeClass('selected');
                    this.getEl('input[name*=rating-from]').prop('checked', false);

                } else if (filterItemName == 'on-sale') {
                    this.getEl('input[name*=on-sale]').prop('checked', false);

                } else if (filterItemName == 'in-stock') {
                    this.getEl('input[name*=in-stock]').prop('checked', false);

                } else if (filterItemName == 'review-from') {
                    this.getEl('input[name*=review-from]').attr('value', 0);

                } else {
                    if (this.getEl('.swpf-' + filterItemName + '-wrap input')) {
                        selected_item = this.$el['.swpf-' + filterItemName + '-wrap input'].filter(function () {
                            return this.value == filterItemVal;
                        });
                        selected_item.prop('checked', false);
                        selected_item.closest('.selected').removeClass('selected');
                    } else if (this.getEl('.swpf-' + filterItemName + '-wrap option')) {
                        selected_item = this.$el['.swpf-' + filterItemName + '-wrap option'].filter(function () {
                            return this.value == filterItemVal;
                        });
                        selected_item.prop('selected', false);
                    }
                }

                jQuery(document).find('select option[value="' + filterItemVal + '"').attr('selected', false);
                jQuery(document).find('select').trigger('chosen:updated');

                this.filter();
            } catch (err) {
                this.filter();
            }
        } else {
            // When the "Clear All" button is Clicked, clear all input,select and filter
            if (this.$el.form.hasClass('apply_ajax')) {
                // If Ajax is Enabled
                jQuery(".swpf-filter-type-dropdown").val(jQuery(".swpf-filter-type-dropdown option:first").val());
                this.getEl('input').prop('checked', false);

                if (this.getEl('input[name="paged"]')) {
                    this.$el['input[name="paged"]'].val('');
                }
                if (this.getEl('input[name="rating-from"]')) {
                    this.$el['input[name="rating-from"]'].val(0);
                }
                if (this.getEl('input[name="review-from"]')) {
                    this.$el['input[name="review-from"]'].val(0);
                }
                if (this.getEl('input.attr-from, input.attr-to')) {
                    this.$el['input.attr-from, input.attr-to'].val('');
                }
                if (this.getEl('.selected')) {
                    this.$el['.selected'].removeClass('selected');
                }
                if (this.getEl('select')) {
                    this.$el.form.find('select option').prop('selected', false);
                }

                // Reset price slider.
                if (this.getEl('input.price-to, input.price-from')) {
                    this.$el['input.price-to, input.price-from'].attr('value', '');
                }
                if (this.getEl(".swpf-range-slider") && this.getEl(".swpf-filter-byprice.slider-price .price-min") && this.getEl(".swpf-filter-byprice.slider-price .price-max")) {
                    this.$el[".swpf-range-slider"].slider("values", [this.$el[".swpf-filter-byprice.slider-price .price-min"].val(), this.$el[".swpf-filter-byprice.slider-price .price-max"].val()]);
                }
                if (this.getEl(".swpf-filter-byprice.slider-price .swpf-price-from .price.amount") && this.getEl(".swpf-filter-byprice.slider-price .price-min")) {
                    this.$el[".swpf-filter-byprice.slider-price .swpf-price-from .price.amount"].text(this.$el[".swpf-filter-byprice.slider-price .price-min"].val());
                }
                if (this.getEl(".swpf-filter-byprice.slider-price .swpf-price-to .price.amount") && this.getEl(".swpf-filter-byprice.slider-price .price-max")) {
                    this.$el[".swpf-filter-byprice.slider-price .swpf-price-to .price.amount"].text(this.$el[".swpf-filter-byprice.slider-price .price-max"].val());
                }
                this.filter(true);
            } else {
                // When AJAX is Disabled, Selects all url link before '?' and reloads
                window.location.href = window.location.href.split('?')[0];
                return;
            }
        }
    }

    filter(clearURL = false, scrollAfterFilter = true) {
        var getURLs = this.getURL();

        if (clearURL) {
            getURLs = getURLs.split("?")[0];
        }

        if (!this.isPagination) {
            this.getEl('input[name="paged"]').val('');
        }

        if (this.$el.form.hasClass('apply_ajax')) {
            this.ajaxFilter(scrollAfterFilter);
        } else {
            this.$el.form.attr('action', getURLs);
            this.$el.form.submit();
        }

        window.history.pushState({
            path: getURLs
        }, '', getURLs);
    }

    ajaxFilter(scrollAfterFilter = true) {
        var mainWrap = this,
            formParams = this.$el.form.serialize(),
            filterId = mainWrap.config.unique_id,
            posid = mainWrap.config.posid,
            current_page_id = mainWrap.config.current_page_id,
            is_prod_taxonomy = mainWrap.config.is_prod_taxonomy,
            page_cat_id = mainWrap.config.page_cat_id,
            page_tax_name = mainWrap.config.page_tax_name,
            page_term_name = mainWrap.config.page_term_name;

        if (swpf_front_js_obj.isTax) {
            formParams = jQuery.param(swpf_front_js_obj.queriedTerm) + '&' + formParams;
        }
        var formArray = this.$el.form.serializeArray();

        jQuery.ajax({
            url: swpf_front_js_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'swpf_get_product_list',
                swpf_form_data: formParams,
                unique_id: filterId,
                posid: posid,
                current_page_id: current_page_id,
                is_prod_taxonomy: is_prod_taxonomy,
                page_cat_id: page_cat_id,
                page_tax_name: page_tax_name,
                page_term_name: page_term_name,
                ajax_nonce: swpf_front_js_obj.ajax_nonce
            },
            beforeSend: function (res) {
                jQuery(document).trigger('swpf_before_filter');
                jQuery('body').addClass('swpf-filter-loading');
            },
            success: function (res) {
                // Quick Scroll To Top Before Filtering
                jQuery(mainWrap.config.product_selector).html(res['html_ul_products_content']);
                jQuery(mainWrap.config.product_count_selector).replaceWith(res['html_result_count_content']);

                // Run only when filtering not paginating
                if (mainWrap.isPagination == false) {
                    jQuery('#swpf-filter-preset-' + filterId).replaceWith(res['html_filter_panel']);
                    new SuperWooProductFilter(jQuery('#swpf-filter-preset-' + filterId)); // Reinitialize newly form.
                }

                var html_pagination_content = res['html_pagination_content'];
                if (typeof html_pagination_content != undefined) {
                    if (html_pagination_content.length == 0) {
                        jQuery(mainWrap.config.pagination_selector).hide();
                    } else {
                        var paginationEL = jQuery(mainWrap.config.pagination_selector);
                        if (paginationEL.length) {
                            paginationEL.show().html(html_pagination_content);
                            paginationEL.addClass('swpf-ajax-pagination');
                            paginationEL.attr('data-swpf-preset', mainWrap.config.swpf_preset);
                        } else {
                            jQuery(mainWrap.config.product_selector)
                                .after('<nav class="woocommerce-pagination swpf-ajax-pagination" data-swpf-preset="' + mainWrap.config.swpf_preset + '">' + html_pagination_content + '</nav>');
                        }
                    }
                }

                jQuery(document).find('.swpf-shown-items').html(res['html_post_count']);
                jQuery(document).find('.swpf-shown-filters').html(res['html_filtered_data']);
                jQuery(document).find('.swpf-header-filters').removeClass(function (index, className) {
                    return (className.match(/(^|\s)swpf-header-filters-\S+/g) || []).join(' ');
                }).addClass('swpf-header-filters-' + res['posid']);

                if (scrollAfterFilter && mainWrap.config.scroll_after_filter == true) {
                    // Smooth Scroll To Product Section After Filtering
                    var scrollToDiv = mainWrap.config.product_selector;
                    jQuery('html, body').animate({
                        scrollTop: (jQuery(scrollToDiv).length > 0) && jQuery(scrollToDiv).parent().offset().top - 25
                    }, 1000);
                }

                jQuery(document).trigger('swpf_after_filter');
                jQuery('body').removeClass('swpf-filter-loading');
                jQuery('body').removeClass('swpf-pagination-loading');
                jQuery('.woocommerce-pagination').removeClass('swpf-processing');
                jQuery('.swpf-shop-load-more').removeClass('swpf-button-clicked');

                const html_cols = res['html_columns'];
                if (html_cols) {
                    jQuery(mainWrap.config.product_selector).removeClass('columns-1').removeClass('columns-2').removeClass('columns-3').removeClass('columns-4').removeClass('columns-5').removeClass('columns-6').removeClass('columns-7').removeClass('columns-8').removeClass('columns-9').removeClass('columns-10').addClass('columns-' + html_cols);
                }
            }
        });
    }

    getURL() {
        let queries, currentUrl = window.location.href;
        queries = this.parseQueries();

        if (-1 !== currentUrl.indexOf('?')) {
            if (-1 !== currentUrl.indexOf('post_type') || -1 !== currentUrl.indexOf('product_cat') || -1 !== currentUrl.indexOf('product_tag')) {
                currentUrl = currentUrl.replace(/&.+/, '');
                queries = queries.replace('?', '&');
            } else {
                currentUrl = currentUrl.replace(/\/\?.+/, '/');
            }
        }

        // Remove `relation` param if there's less than 2 queries, including itself.
        if (queries.split('?').length <= 2 && queries.split(',').length < 2 && queries.split('&').length < 2) {
            queries = queries.replace(/[&|\?]relation=[^&]+/, '');
        }

        return currentUrl + queries;
    }

    parseQueries() {
        let filteredOptions = [],
            selectedOptions = this.$el.form.serializeArray();

        //merger value filter
        selectedOptions.forEach(function (value) {
            var existing = filteredOptions.filter(function (v, i) {
                return v.name == value.name;
            });
            if (existing.length) {
                var existingIndex = filteredOptions.indexOf(existing[0]);
                filteredOptions[existingIndex].value = filteredOptions[existingIndex].value.concat(value.value);
            } else {
                if (typeof value.value == 'string')
                    value.value = [value.value];
                filteredOptions.push(value);
            }
        });

        var queries = '',
            arg_name = '',
            arg_type = '',
            arg_val = '',
            temp;

        filteredOptions.forEach(function (data) {
            arg_name = data.name;
            arg_type = SuperWooProductFilterUtils.stringMatches(arg_name, /^.*?(?=\[)/g);
            if (arg_type == null) {
                arg_type = arg_name;
            }
            arg_val = data.value.toString();
            if (queries == '') {
                temp = '?';
            } else {
                temp = '&';
            }
            if (!!arg_val && !!arg_type) {
                if (arg_type[0][0] == 'categories') {
                    queries += temp + 'categories=' + arg_val;
                } else if (arg_type[0][0] == 'tags') {
                    queries += temp + 'tags=' + arg_val;
                } else if (arg_type[0][0] == 'rating-from') {
                    queries += temp + 'rating-from=' + arg_val;
                } else if (arg_type[0][0] == 'visibility') {
                    queries += temp + 'visibility=' + arg_val;
                } else if (arg_type[0][0] == 'paged') {
                    queries += temp + 'paged=' + arg_val;
                } else if (arg_type[0][0] == 'attribute') {
                    queries += temp + SuperWooProductFilterUtils.stringMatches(arg_name, /\[(.*?)\]/g)[0][1] + '=' + arg_val;
                } else if (arg_type[0][0] == 'price') {
                    queries += temp + SuperWooProductFilterUtils.stringMatches(arg_name, /\[(.*?)\]/g)[0][1] + '=' + arg_val.split(' ').join('');
                } else if (-1 !== arg_type.indexOf('range-m')) {
                    const rangeVal = parseFloat(arg_val);
                    if ($.isNumeric(rangeVal)) {
                        queries += temp + arg_name + '=' + rangeVal;
                    }
                } else {
                    if (arg_val != '0') {
                        if (arg_type != 'swpf_nonce_setting' && arg_type != 'filter_list_id' && arg_type != '_wp_http_referer' && arg_type != 'posts_per_page' && arg_type != 'pagination_link') {
                            queries += temp + arg_name + '=' + arg_val.split(' ');
                        }
                    }
                }
            }
        });

        return queries;
    }
}

jQuery(($) => {
    'use strict';
    var load_more_class = 'swpf-shop-load-more';

    var selector = {
        pagination: '.woocommerce-pagination',
        pagination_button: '.woocommerce-pagination a',
        next_button: '.woocommerce-pagination a.next',
        products_container: '.products',
        product_item: 'section.product',
        breadcrumb_container: '.breadcrumbs-container',
        load_more_button: '.' + load_more_class
    };

    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    const filterForm = $('.swpf-main-wrap').find('form');

    if (filterForm.length > 0) {
        /* Ajax Pagination */
        $(document).on('click', selector.pagination_button, function (e) {
            e.preventDefault();
            let target = $(e.currentTarget),
                pagination_wrap = target.closest('.woocommerce-pagination'),
                filterPreset = $('#' + pagination_wrap.data('swpf-preset'));
            let paged;
            // $('body').addClass('swpf-pagination-loading');

            if (target.hasClass('next')) {
                paged = parseInt(pagination_wrap.find('.current').text()) + 1;
            } else if (target.hasClass('prev')) {
                paged = parseInt(target.closest('nav.woocommerce-pagination').find('.current').text()) - 1;
            } else {
                paged = parseInt(target.text());
            }

            $('.swpf-main-wrap').find('input[name="paged"]').val(paged);
            const swpf_obj = new SuperWooProductFilter(filterPreset);
            swpf_obj.isPagination = true;
            swpf_obj.filter();
            swpf_obj.isPagination = false;
        });
    }

    $('.swpf-main-wrap').each(function () {
        const swpfFilter = $(this);
        var swpf_filter = new SuperWooProductFilter(swpfFilter);
        swpfFilter.hasClass('swpf-ajax-initial-filter-on') && getUrlParameter('swpf_filter') === '1' && swpf_filter.filter();
        return swpf_filter;
    });

    $(document).on('keyup', '.swpf-tax-list-wrapper .swpf-filter-search input', function (e) {
        var $input = $(this);
        var keyword = $input.val().toLowerCase();
        var search_criteria = $input.closest('.swpf-tax-list-wrapper').find('.swpf-filter-item-list .swpf-filter-item input');
        delay(function () {
            $(search_criteria).each(function () {
                const that = $(this);
                var search_test;

                if (!that.siblings('.swpf-title').text()) {
                    search_test = that.val().toLowerCase().indexOf(keyword.replace(/\s+/g, '-'));
                } else {
                    search_test = that.siblings('.swpf-title').text().toLowerCase().indexOf(keyword);
                }

                if (search_test > -1) {
                    const showEl = that.closest('.swpf-filter-item');
                    show_parent_search_el(showEl);
                    showEl.show();
                } else {
                    that.closest('.swpf-filter-item').hide();
                }
            });
        }, 500);
    });

    // Side Menu button trigger
    $(document).on('click', '.swpf-sidemenu-trigger', function () {
        var $wrap = $(this).closest('.swpf-sidemenu-wrapper');
        var $panel = $wrap.find('.swpf-sidemenu-panel');
        var $overlay = $wrap.find('.swpf-shape-overlays');

        if ($wrap.hasClass('swpf-panel-animation-enabled')) {
            var showAnimation = $wrap.data('showanimation');
            var hideAnimation = $wrap.data('hideanimation');

            if ($wrap.hasClass('swpf-panel-in-view') && hideAnimation) {
                $panel.addClass('animate--animated ' + hideAnimation);
                $panel.one('webkitAnimationEnd oanimationend oAnimationEnd msAnimationEnd animationend', function (e) {
                    $panel.removeClass('animate--animated ' + hideAnimation);
                });
                $wrap.removeClass('swpf-sidemenu-show swpf-panel-in-view');
                $wrap.addClass('swpf-sidemenu-hide');
            } else if (!$wrap.hasClass('swpf-panel-in-view') && showAnimation) {
                $panel.addClass('animate--animated ' + showAnimation);
                $panel.one('webkitAnimationEnd oanimationend oAnimationEnd msAnimationEnd animationend', function (e) {
                    $panel.removeClass('animate--animated ' + showAnimation);
                });
                $wrap.addClass('swpf-sidemenu-show swpf-panel-in-view');
                $wrap.removeClass('swpf-sidemenu-hide');

                /* Add animation class to trigger transitional delays*/
                if ($wrap.hasClass('swpf-sidemenu-show') && $wrap.hasClass('swpf-custom-content')) {
                    $wrap.addClass('swpf-sidemenu-animating');
                    var $lastele = $wrap.find('.swpf-menu-item-link-depth-0').last();
                    $lastele.one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function (e) {
                        $wrap.removeClass('swpf-sidemenu-animating');
                    });
                }
            }
        } else {
            $wrap.toggleClass('swpf-sidemenu-show');
            $wrap.toggleClass('swpf-sidemenu-hide');

            /* Add animation class to trigger transitional delays*/
            if ($wrap.hasClass('swpf-sidemenu-show') && $wrap.hasClass('swpf-custom-content')) {
                $wrap.addClass('swpf-sidemenu-animating');
                var $lastele = $wrap.find('.swpf-menu-item-link-depth-0').last();
                $lastele.one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function (e) {
                    $wrap.removeClass('swpf-sidemenu-animating');
                });
            }
        }

        /* Add animation class to trigger animation */
        var $triggerbutton = $wrap.find('.swpf-toggle-button');
        $triggerbutton.addClass('swpf-toggle-animating');
        $triggerbutton.one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function (e) {
            $triggerbutton.removeClass('swpf-toggle-animating');
        });

        return false;
    });

    /* close panel on clicking outside of the menu panel */
    $(document).on('click', function (e) {
        if ($('.swpf-sidemenu-wrapper').hasClass('swpf-sidemenu-show') && $('.swpf-sidemenu-wrapper').hasClass('swpf-click-outside-on')) {
            if ($(e.target).closest('.swpf-sidemenu-show.swpf-click-outside-on').length === 0) {
                $('.swpf-sidemenu-show.swpf-click-outside-on .swpf-sidemenu-trigger').trigger('click');
                return false;
            }
        }
    });

    $('.swpf-panel-close').on('click', function () {
        var $wrap = $(this).closest('.swpf-sidemenu-wrapper');
        $wrap.find('.swpf-sidemenu-trigger').trigger('click');
        return false;
    });

    /* scrollbar */
    $('.swpf-scrollbar-on .swpf-sidemenu-panel-scroller').mCustomScrollbar({
        theme: 'swpf-scrollbar-theme',
        scrollbarPosition: 'outside'
    })

    const delay = (function () {
        var timer = 0;
        return function (callback, ms) {
            clearTimeout(timer);
            timer = setTimeout(callback, ms);
        };
    })();

    $(window).on('resize', function () {
        delay(function () {
            var win = $(this);
            $(document).find('.swpf-responsive-filter-wrap').each(function () {
                const $ele = $(this);
                const filterId = $ele.attr('data-filter-id');
                const responsiveWidth = parseInt($ele.attr('data-responsive-width'));
                const mainFilter = $(document).find('.swpf-main-filter-wrap-' + filterId);
                if (mainFilter.length > 0) {
                    const is_sidemenu_filter = ($ele.closest('.swpf-sidemenu-panel-content').length > 0)
                    if ((win.width() >= responsiveWidth && !is_sidemenu_filter) || (win.width() < responsiveWidth && is_sidemenu_filter)) {
                        mainFilter.find('.swpf-range-irs-slider span.irs').remove();
                        mainFilter.find('input.irs-hidden-input').removeClass('irs-hidden-input');
                        $ele.swapWith(mainFilter);
                        new SuperWooProductFilter($(document).find('.swpf-main-wrap')); // Reinitialize newly form.
                    }
                }
            });
        }, 500);
    }).trigger('resize');

    jQuery.fn.swapWith = function (to) {
        return this.each(function () {
            var copy_to = $(to).clone(false);
            var copy_from = $(this).clone(false);
            $(to).replaceWith(copy_from);
            $(this).replaceWith(copy_to);
        });
    };

    const show_parent_search_el = (showEl) => {
        const parentEl = showEl.closest('ul');
        if (parentEl.hasClass('swpf-filter-children')) {
            const showEl = parentEl.closest('.swpf-filter-item');
            show_parent_search_el(showEl);
            showEl.show();
        }
    }

    function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
        return false;
    }

    $('body').find('[class^="swpf-open-sidemenu-"], [class*=" swpf-open-sidemenu-"]').on('click', function (e) {

        var classes = $(this).attr('class').split(' ');
        var classitem;
        $.each(classes, function (index, item) {
            if (item.indexOf('swpf-open-sidemenu-') >= 0) {
                classitem = item;
                return false;
            }
        });

        var $wrap = $('.swpf-sidemenu-wrapper-' + classitem.replace(/^\D+/g, ''));

        if ($wrap.length > 0) {
            $wrap.find('.swpf-sidemenu-trigger').trigger('click');
        }
        return false;
    });
});