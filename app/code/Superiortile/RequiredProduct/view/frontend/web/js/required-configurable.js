/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiRegistry',
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'mage/url',
    'Superiortile_RequiredProduct/js/model/required-product-detail',
    'Superiortile_RequiredProduct/js/model/totals',
    'Magento_Catalog/js/price-utils'
], function ($, uiRegistry, ko, Component, customerData, url, requiredProductDetail, requiredTotals, priceUtils) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Superiortile_RequiredProduct/product/view/selected-required-product',
            selectedRequiredProduct: ko.observableArray([]),
            requiredProductsData: {},
            selectedRequiredInputName: {},
            collectionId: null,
            formId: 'product_addtocart_form',
            selectedRequiredProductItems: ko.observable({}),
            subTotalData: ko.observable({}),
            priceBox: '.product-info-main [data-role="priceBox"]',
            mainProductQtySelector: '.product-info-main #qty'
        },

        /**
         * initialize
         */
        initialize: function () {
            this._super();
            this.selectedRequiredProductItems.subscribe(this.updateSubtotal, this);
        },

        /**
         * updateSubtotal
         */
        updateSubtotal: function () {
            var productItemsData = this.selectedRequiredProductItems(),
                qty = 0,
                requiredProductTotals = 0,
                self = this,
                totalQty,
                mainProductPrice,
                mainProductTotalPrice,
                mainProductQty,
                subtotalData;

            /*eslint-disable no-unused-vars*/
            for (const [key, item] of Object.entries(productItemsData)) {
                qty += item.qty;
                requiredProductTotals += item.price * item.qty;
            }
            mainProductPrice = $(self.priceBox).find('[data-price-type="finalPrice"]').attr('data-price-amount');
            mainProductPrice = requiredTotals.getSubTotal()().mainProductPrice ?
                requiredTotals.getSubTotal()().mainProductPrice : parseFloat(mainProductPrice);
            mainProductQty = parseInt($(this.mainProductQtySelector).val(), 10);
            totalQty = qty + mainProductQty;
            mainProductTotalPrice = mainProductPrice * mainProductQty;

            subtotalData = {
                qty: qty,
                totalQty: totalQty,
                mainProductQty: mainProductQty,
                mainProductTotalPrice: mainProductTotalPrice,
                mainProductPrice: mainProductPrice,
                requiredProductTotals: requiredProductTotals,
                totals: mainProductTotalPrice + requiredProductTotals,
                formattedPrice: priceUtils.formatPrice(mainProductTotalPrice + requiredProductTotals)
            };

            requiredTotals.setSubTotalData(subtotalData);
        },

        /**
         * initObservable
         */
        initObservable: function () {
            var self = this;

            this._super().observe(['selectedRequiredProduct', 'requiredConfigurable']);
            $(this.mainProductQtySelector).on('change', function () {
                self.updateSubtotal();
            });
            this.requiredConfigurable = customerData.get('required-configurable');
            this.requiredConfigurable.subscribe(this.updateSelectedRequiredProduct, this);
            this.selectedRequiredProduct.subscribe(function (value) {
                uiRegistry.async('index = requiredProducts-' + self.collectionId)(function (component) {
                    if (component) {
                        if (value) {
                            component.canShoButtonAdd(false);
                        } else {
                            component.canShoButtonAdd(true);
                        }
                    }
                });
            });
            this.updateSelectedRequiredProduct();
            return this;
        },

        /**
         * update Selected Required Product
         */
        updateSelectedRequiredProduct: function () {
            var self = this,
                product,
                productItemsData,
                configurable = self.requiredConfigurable()[self.mainProductId];

                Object.keys(configurable).forEach(function(key) {
                    var product = self.requiredProductsData[configurable[key]['product_id']] || null;

                    if (product) {
                        product.qty = configurable.qty;
                        self.selectedRequiredInputQtyName = 'required-item[%1][%2]'
                            .replace('%1', self.collectionId)
                            .replace('%2', configurable[key]['product_id']);
                        product.attributes.qty = {
                            label: $.mage.__('Quantity'),
                            value: product.qty,
                            attribute_code: 'qty',
                        };
                       
                        if (
                            !self.selectedRequiredProduct().map(p => p.product_id).includes(product.id)
                        ) {
                            self.selectedRequiredProduct.push(product);
                        }
                    }

                });
        },

        /**
         * edit Selected Required Product
         */
        editSelectedRequiredProduct: function () {
            var self = this;

            requiredProductDetail.requiredProductListModals()()[self.collectionId].modal('openModal');
        },

        /**
         * removeSelectedRequiredProduct
         */
        removeSelectedRequiredProduct: function (component, event) {
            var self = this,
                configurable = self.requiredConfigurable()[self.mainProductId] ?
                    self.requiredConfigurable()[self.mainProductId][self.collectionId] : {};

            configurable.form_key = $.mage.cookies.get('form_key');

            $.ajax({
                url: url.build('requiredproduct/product/remove'),
                data: configurable,
                type: 'post',
                dataType: 'json',
                cache: false,
                showLoader: true,

                /**
                 * beforeSend
                 */
                beforeSend: function () {
                    $(event.currentTarget).prop('disabled', true);
                },

                /**
                 * success
                 */
                success: function (res) {
                    console.log(res);
                },

                /**
                 * error
                 */
                error: function (err) {
                    console.log(err);
                },

                /**
                 * complete
                 */
                complete: function () {
                    $(event.currentTarget).prop('disabled', false);
                }
            });
        }
    });
});
