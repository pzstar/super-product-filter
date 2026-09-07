/* The filter builder's background save.
 *
 * The form still posts to admin-post.php on its own, so the builder works with
 * no scripting at all. This intercepts that submit and sends the same fields to
 * the same save routine over ajax instead, which keeps the panels' scroll
 * position, their open tab and every colour picker exactly where they were -
 * all of which a redirect throws away.
 *
 * Because the save now happens without leaving the page, the builder is also in
 * a position to know whether what is on screen has been written yet, so it says
 * so and warns before that answer is lost.
 */
(function ($) {
    "use strict";

    $(function () {
        var $form = $('#swpf-builder-form');

        if (!$form.length || typeof swpfBuilder === 'undefined') {
            return;
        }

        var $state = $form.find('.swpf-topbar-state');
        var $save = $form.find('.swpf-settings-footer button[type="submit"]');
        var $alert = $('.swpf-alert');

        var dirty = false;
        var saving = false;
        var alertTimer = null;

        /* ---- what the topbar says ----------------------------------------- */

        var STATES = {
            'new': swpfBuilder.unsavedNew,
            saved: swpfBuilder.saved,
            unsaved: swpfBuilder.unsaved,
            saving: swpfBuilder.saving
        };

        function setState(state) {
            $state.attr('data-state', state).text(STATES[state] || '');
        }

        function markDirty() {
            if (dirty || saving) {
                return;
            }

            dirty = true;
            setState('unsaved');
        }

        /* ---- the toast ----------------------------------------------------- */

        function notify(message, type) {
            if (!$alert.length) {
                return;
            }

            window.clearTimeout(alertTimer);

            $alert
                .removeClass('swpf-alert-success swpf-alert-warning swpf-alert-neutral')
                .addClass('swpf-alert-' + type)
                .addClass('swpf-alert-active')
                .find('.swpf-alert-message').text(message);

            alertTimer = window.setTimeout(function () {
                $alert.removeClass('swpf-alert-active swpf-alert-success swpf-alert-warning swpf-alert-neutral');
            }, 3500);
        }

        /* ---- saving --------------------------------------------------------- */

        /* A preset created here has an id, an address and a trash link for the
           first time, none of which the page it was created from could have. */
        function adopt(data) {
            $form.find('input[name="filter"]').val(data.id);

            if (window.history && window.history.replaceState && data.url) {
                window.history.replaceState({}, document.title, data.url);
            }

            if (!data.trash) {
                return;
            }

            var $actions = $form.find('.swpf-topbar-actions');
            var $trash = $actions.find('.swpf-topbar-trash');

            if ($trash.length) {
                $trash.attr('href', data.trash);
                return;
            }

            $('<a/>', {
                'class': 'swpf-topbar-trash',
                href: data.trash,
                text: swpfBuilder.trashLabel
            }).appendTo($actions);
        }

        function failed(message) {
            /* Nothing was written, so the form is still ahead of what is
               stored and the warning on the way out still applies. */
            dirty = true;
            setState('unsaved');
            notify(message || swpfBuilder.failed, 'warning');
        }

        /*
         * The settings go over as one JSON field rather than as one POST
         * variable per input.
         *
         * A preset renders roughly eighty fields for every taxonomy on the
         * site, so a shop with fifty attributes posts several thousand
         * variables. PHP stops at max_input_vars - 1000 by default, and it
         * drops the excess silently: no error, no warning, just the tail of the
         * form missing. The panels that render last, Designs among them, were
         * the ones being cut, so typography came back as defaults after a save
         * that reported success.
         *
         * max_input_vars does not apply to a single field, and json_decode is
         * not bound by it either, so the whole settings tree arrives intact
         * however many taxonomies a shop has.
         */
        function payload() {
            var data = {};
            var settings = {};

            $.each($form.serializeArray(), function (i, field) {
                var path = field.name.match(/^swpf_settings((?:\[[^\]]*\])+)$/);

                if (!path) {
                    /* action, nonces, the preset id and its name travel as they
                       always did - a handful of fields, well inside any limit. */
                    data[field.name] = field.value;
                    return;
                }

                var keys = path[1].match(/\[[^\]]*\]/g).map(function (k) {
                    return k.slice(1, -1);
                });

                var node = settings;

                for (var i2 = 0; i2 < keys.length; i2++) {
                    var key = keys[i2];
                    var last = i2 === keys.length - 1;

                    /* name="...[]" appends rather than assigns. */
                    if (key === '') {
                        if (last) {
                            node.push(field.value);
                        } else {
                            node.push({});
                            node = node[node.length - 1];
                        }
                        continue;
                    }

                    if (last) {
                        node[key] = field.value;
                    } else {
                        if (!(key in node)) {
                            node[key] = keys[i2 + 1] === '' ? [] : {};
                        }
                        node = node[key];
                    }
                }
            });

            data.swpf_settings_json = JSON.stringify(settings);

            return data;
        }

        function save() {
            if (saving) {
                return;
            }

            saving = true;
            $save.addClass('swpf-button-loader').prop('disabled', true);
            setState('saving');

            $.post(swpfBuilder.ajaxurl, payload())
                .done(function (response) {
                    if (!response || !response.success || !response.data) {
                        failed(response && response.data ? response.data.message : '');
                        return;
                    }

                    adopt(response.data);

                    dirty = false;
                    setState('saved');
                    notify(response.data.message, 'success');
                })
                .fail(function () {
                    failed();
                })
                .always(function () {
                    saving = false;
                    $save.removeClass('swpf-button-loader').prop('disabled', false);
                });
        }

        /* ---- wiring ---------------------------------------------------------- */

        /* The browser has already enforced the required name by the time a
           submit event arrives, so there is nothing left to check here. */
        $form.on('submit', function (event) {
            event.preventDefault();
            save();
        });

        $form.on('change input', 'input, select, textarea', markDirty);

        /* Discarding the preset discards its unsaved edits by definition, so
           the trash link does not argue about them on the way out. */
        $form.on('click', '.swpf-topbar-trash', function () {
            dirty = false;
        });

        $(window).on('beforeunload', function () {
            if (dirty) {
                return swpfBuilder.confirmLeave;
            }
        });

        /* Colour pickers, select boxes and the conditional fields all settle by
           firing change at their own inputs, which is setup rather than an
           edit. They run from ready handlers registered ahead of this one, so
           by now they are done and the slate can be wiped. */
        dirty = false;
    });
})(jQuery);
