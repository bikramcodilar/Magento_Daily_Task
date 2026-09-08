define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    'use strict';

    return function (config, element) {
        let $modal = $(element);
        $modal.appendTo('body');
        modal({
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: $.mage.__('Product Enquiry'),
            buttons: []
        }, $modal);
        $('#product-enquiry-button').on('click', function () {
            $('#product-enquiry-message').empty();
            $modal.modal('openModal');
        });
        $('#product-enquiry-submit').on('click', function () {
            let name = $('#enquiry-name').val().trim();
            let email = $('#enquiry-email').val().trim();
            let quantity = $('#enquiry-quantity').val();
            let $message = $('#product-enquiry-message');
            $message.empty();
            if (!name || !email || !quantity || Number(quantity) <= 0) {
                $message
                    .addClass('message message-error')
                    .text($.mage.__('Please fill all required fields correctly.'));
                return;
            }
            $.ajax({
                url: config.submitUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    name: name,
                    email: email,
                    product_id: $('#enquiry-product-id').val(),
                    quantity: quantity,
                    form_key: $('#enquiry-form-key').val()
                }
            }).done(function (response) {
                if (response.success) {
                    $message
                        .removeClass('message-error')
                        .addClass('message message-success')
                        .text(response.message);
                    $('#enquiry-name, #enquiry-email').val('');
                    $('#enquiry-quantity').val(1);
                } else {
                    $message
                        .removeClass('message-success')
                        .addClass('message message-error')
                        .text(response.message);
                }
            }).fail(function () {
                $message
                    .addClass('message message-error')
                    .text($.mage.__('Unable to submit your enquiry. Please try again.'));
            });
        });
    };
});
