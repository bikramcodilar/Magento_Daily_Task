define([
    'Magento_Ui/js/grid/columns/column',
    'uiRegistry'
], function (Column, registry) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Codilar_ProductEnquiry/grid/cells/product-radio',
            selectedProductId: null
        },

        initialize: function () {
            this._super();
            this.observe(['selectedProductId']);

            return this;
        },

        selectProduct: function (product) {
            var skuField =
                'codilar_productenquiry_enquiry_form.' +
                'codilar_productenquiry_enquiry_form.general.sku';
            var modal =
                'codilar_productenquiry_enquiry_form.' +
                'codilar_productenquiry_enquiry_form.general.product_selector_modal';

            this.selectedProductId(product.entity_id);

            registry.get(skuField, function (field) {
                field.value(product.sku);
            });

            registry.get(modal, function (modalComponent) {
                modalComponent.closeModal();
            });

            return this;
        }
    });
});
