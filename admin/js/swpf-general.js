(function ($) {
    "use strict";

    $(document).ready(function ($) {

        $('.swpf-save-settings.swpf-general-settings-btn button').on('click', function (e) {
            e.preventDefault();
            const $formBtn = $(this)
            const $form = $formBtn.closest('form');
            $formBtn.addClass('swpf-button-loader');

            var formData = $form.serializeArray();
            var formData = new FormData($form[0]);
            formData.append('action', 'swpf_general_settings_save');

            $.ajax({
                url: swpf_admin_general_obj.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        $('.swpf-alert').addClass('swpf-alert-success');
                        $('.swpf-alert span').html(response.data.message);
                        $('.swpf-alert').addClass('swpf-alert-active');
                        $formBtn.removeClass('swpf-button-loader');
                        clearTimeout();

                        setTimeout(function () {
                            if ($('.swpf-alert').hasClass('swpf-alert-active')) {
                                $('.swpf-alert').removeClass('swpf-alert-active');
                                $('.swpf-alert').removeClass('swpf-alert-success swpf-alert-warning swpf-alert-neutral');
                            }
                        }, 3500);
                    } else {
                        console.log('Failed to save.');
                    }
                }
            });
        });

    });

})(jQuery);