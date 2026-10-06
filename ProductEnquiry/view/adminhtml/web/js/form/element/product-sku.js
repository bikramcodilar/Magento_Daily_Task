define([
    'Magento_Ui/js/form/element/abstract',
    'uiRegistry'
], function (Abstract, registry) {
    'use strict';

    return Abstract.extend({
        openProductModal: function () {
            registry.get(
                'codilar_productenquiry_enquiry_form.' +
                'codilar_productenquiry_enquiry_form.general.product_selector_modal',
                function (modal) {
                    modal.openModal();
                }
            );

            return this;
        }
    });
});
