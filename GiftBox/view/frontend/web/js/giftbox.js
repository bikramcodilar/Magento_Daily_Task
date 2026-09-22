define([
    'jquery',
    'Magento_Customer/js/customer-data'
], function ($, customerData) {
    'use strict';

    return function (config, element) {
        var $container = $(element);

        var requiredCoffeeCount = parseInt(
            config.requiredCoffeeCount,
            10
        );

        var $coffeeInputs = $container.find(
            'input[name="coffee_skus[]"]'
        );

        var $equipmentInputs = $container.find(
            'input[name="equipment_sku"]'
        );

        var $addButton = $container.find(
            '.giftbox-add-to-cart'
        );

        var $selectionMessage = $container.find(
            '.giftbox-selection-message'
        );

        var $subtotal = $container.find(
            '.giftbox-subtotal'
        );

        var $giftBoxPrice = $container.find(
            '.giftbox-price'
        );

        var giftMessageMaxLength = parseInt(
            config.giftMessageMaxLength,
            10
        );

        var $giftMessage = $container.find(
            '#giftbox-gift-message'
        );

        var $giftMessageCounter = $container.find(
            '.giftbox-message-counter'
        );

        var addUrl = config.addUrl;

        var $message = $container.find(
            '[data-role="giftbox-message"]'
        );

        var $buttonText = $container.find(
            '.giftbox-button-text'
        );

        var $buttonLoading = $container.find(
            '.giftbox-button-loading'
        );

        function getSelectedCoffeeCount() {
            return $coffeeInputs.filter(':checked').length;
        }

        function updateGiftMessageCounter() {
            var message = $giftMessage.val() || '';
            var characterCount = Array.from(message).length;
            $giftMessageCounter.text(
                characterCount +
                ' / ' +
                giftMessageMaxLength
            );
            $giftMessageCounter
                .removeClass('warning error');
            if (characterCount >= giftMessageMaxLength) {
                $giftMessageCounter.addClass('error');
            } else if (
                characterCount >= giftMessageMaxLength * 0.8
            ) {
                $giftMessageCounter.addClass('warning');
            }
        }

        function calculateSubtotal() {
            var subtotal = 0;
            $coffeeInputs
                .filter(':checked')
                .each(function () {
                    subtotal += parseFloat(
                        $(this).data('price')
                    ) || 0;
                });
            $equipmentInputs
                .filter(':checked')
                .each(function () {
                    subtotal += parseFloat(
                        $(this).data('price')
                    ) || 0;
                });
            return subtotal;
        }

        function updatePrice() {
            var subtotal = calculateSubtotal();
            $subtotal.text(
                subtotal.toFixed(2)
            );
            $giftBoxPrice.text(
                subtotal.toFixed(2)
            );
        }

        function updateCoffeeSelection() {
            var selectedCount = getSelectedCoffeeCount();
            if (selectedCount >= requiredCoffeeCount) {
                $coffeeInputs
                    .not(':checked')
                    .prop('disabled', true);
            } else {
                $coffeeInputs.prop(
                    'disabled',
                    false
                );
            }
            updateMessage();
            updateButton();
            updatePrice();
        }

        function updateMessage() {
            var selectedCount = getSelectedCoffeeCount();
            if (selectedCount === requiredCoffeeCount) {
                $selectionMessage
                    .text(
                        'You have selected all required coffees.'
                    )
                    .removeClass('error')
                    .addClass('success');
                return;
            }
            $selectionMessage
                .text(
                    'Please select ' +
                    requiredCoffeeCount +
                    ' coffees. ' +
                    selectedCount +
                    ' selected.'
                )
                .removeClass('success')
                .addClass('error');
        }

        function updateButton() {
            var selectedCount = getSelectedCoffeeCount();
            $addButton.prop(
                'disabled',
                selectedCount !== requiredCoffeeCount
            );
        }

        function addGiftBoxToCart() {
            var coffeeSkus = [];
            $coffeeInputs
                .filter(':checked')
                .each(function () {
                    coffeeSkus.push(
                        $(this).val()
                    );
                });
            var equipmentSku = $equipmentInputs
                .filter(':checked')
                .val() || '';
            var giftMessage = $giftMessage
                .val() || '';
            hideMessage();
            setLoading(true);
            $.ajax({
                url: addUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    coffee_skus: coffeeSkus,
                    equipment_sku: equipmentSku,
                    gift_message: giftMessage
                },
                success: function (response) {
                    if (!response.success) {
                        showMessage(
                            response.message ||
                            'Unable to add Gift Box to cart.',
                            'error'
                        );
                        setLoading(false);
                        return;
                    }
                    customerData.reload(
                        ['cart'],
                        true
                    );
                    showMessage(
                        response.message ||
                        'Gift Box added to cart.',
                        'success'
                    );
                    setTimeout(function () {
                        window.location.href =
                            response.cart_url;
                    }, 500);
                },
                error: function (xhr) {
                    var response = xhr.responseJSON;
                    var message =
                        response && response.message
                            ? response.message
                            : 'Unable to add Gift Box to cart.';
                    showMessage(
                        message,
                        'error'
                    );
                    setLoading(false);
                }
            });
        }

        function showMessage(message, type) {
            $message
                .removeClass('success error')
                .addClass(type)
                .text(message)
                .show();
        }

        function hideMessage() {
            $message
                .removeClass('success error')
                .text('')
                .hide();
        }

        function setLoading(isLoading) {
            if (isLoading) {
                $addButton.prop('disabled', true);
                $buttonText.hide();
                $buttonLoading.show();
                return;
            }
            $buttonText.show();
            $buttonLoading.hide();
            updateButton();
        }

        $coffeeInputs.on('change', function () {
            updateCoffeeSelection();
        });

        $equipmentInputs.on('change', function () {
            updatePrice();
        });

        $giftMessage.on(
            'input',
            updateGiftMessageCounter
        );

        $addButton.on(
            'click',
            addGiftBoxToCart
        );

        updateCoffeeSelection();

        updateGiftMessageCounter();
    };
});
