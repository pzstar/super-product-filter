// Avoid collisions with other libraries
(function ($) {
    "use strict";
    // Make sure the document is ready
    $(document).ready(function () {

        $(document).on('submit', 'form#post', function (e) {
            e.preventDefault()

            $('.swpf-settings-footer .button').addClass('swpf-button-loader');

            // This is the post.php url we localized (via php) above
            var url = swpf_admin_metabox_obj.posturl
            // Serialize form data
            var data = $('form#post').serializeArray();                 // Tell PHP what we're doing
            // NOTE: "name" and "value" are the array keys. This is important. I use int(1) for the value to make sure we don't get a string server-side.
            data.push({name: 'save_post_ajax', value: 1})
            data.push({name: 'post_status', value: 'publish'})

            // Replaces wp.autosave.initialCompareString
            var ajax_updated = false

            /**
             * Supercede the WP beforeunload function to remove                  * the confirm dialog when leaving the page (if we saved via ajax)
             *
             * The following line of code SHOULD work in $.post.done(), but
             *     for some reason, wp.autosave.initialCompareString isn't changed
             *     when called from wp-includes/js/autosave.js
             * wp.autosave.initialCompareString = wp.autosave.getCompareString();
             */
            $(window).unbind('beforeunload.edit-post')
            $(window).on('beforeunload.edit-post', function () {
                var editor = typeof tinymce !== 'undefined' && tinymce.get('content')

                // Use our "ajax_updated" var instead of wp.autosave.initialCompareString
                if ((editor && !editor.isHidden() && editor.isDirty()) ||
                    (wp.autosave && wp.autosave.getCompareString() !== ajax_updated)) {
                    return postL10n.saveAlert
                }
            })


            // Post it
            $.post(url, data, function (response) {
                // Validate response
                if (response.success) {
                    // Mark TinyMCE as saved
                    if (typeof tinyMCE !== 'undefined') {
                        for (id in tinyMCE.editors) {
                            if (tinyMCE.get(id))
                                tinyMCE.get(id).setDirty(false)
                        }
                    }
                    // Update the saved content for the beforeunload check
                    ajax_updated = wp.autosave.getCompareString();
                }
                $('.swpf-alert').addClass('swpf-alert-success');
                $('.swpf-alert span').html('Settings Saved');
                $('.swpf-alert').addClass('swpf-alert-active');
                $('.swpf-settings-footer .button').removeClass('swpf-button-loader');
                clearTimeout();
                setTimeout(function () {
                    if ($('.swpf-alert').hasClass('swpf-alert-active')) {
                        $('.swpf-alert').removeClass('swpf-alert-active');
                        $('.swpf-alert').removeClass('swpf-alert-success swpf-alert-warning swpf-alert-neutral');
                    }
                }, 3500);
                history.pushState("", document.title, url + '?' + 'post=' + response.data + '&action=edit');
            }).fail(function (response) {
                console.log('ERROR: Could not contact server. ', response)
            }).done(function () {
                if (wp.autosave) {
                    wp.autosave.enableButtons();
                }

                $('#publishing-action .spinner').removeClass('is-active');
            })

            return false
        })
    })
})(jQuery)