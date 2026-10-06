define([
    'Magento_Ui/js/form/element/abstract'
], function (Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            productSelection: [],
            productItems: []
        },

        initialize: function () {
            this._super();

            this.observe(['productSelection', 'productItems']);

            this.productSelection.subscribe(
                this.updateSelectedProduct.bind(this)
            );

            this.updateSelectedProduct(this.productSelection());

            return this;
        },

        updateSelectedProduct: function (selectedIds) {
            var selectedId = Array.isArray(selectedIds)
                    ? selectedIds[0]
                    : selectedIds,
                products = this.productItems(),
                product;

            if (
                selectedId === null ||
                selectedId === undefined ||
                selectedId === ''
            ) {
                return this;
            }

            if (!Array.isArray(products) && products && Array.isArray(products.items)) {
                products = products.items;
            }

            if (!Array.isArray(products)) {
                return this;
            }

            product = products.find(function (item) {
                return String(item.entity_id) === String(selectedId);
            });

            if (product && product.sku) {
                this.value(product.sku);
            }

            return this;
        }
    });
});
