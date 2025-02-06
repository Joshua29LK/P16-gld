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
            selectedRequiredProduct: {},
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
                configurable = self.requiredConfigurable()[self.mainProductId] ?
                    self.requiredConfigurable()[self.mainProductId][self.collectionId] : {};

            if (configurable && configurable.product_id) {
                product = self.requiredProductsData[configurable.product_id] ?? null;

                if (product) {
                    product.qty = configurable.qty;
                    product.options = configurable.custom_options || {};
                    this.selectedRequiredInputQtyName = 'required-item[%1][%2]'
                        .replace('%1', this.collectionId)
                        .replace('%2', configurable.product_id);

                    product.attributes.qty = {
                        label: $.mage.__('Quantity'),
                        value: product.qty,
                        attribute_code: 'qty'
                    };
                    product.attributes.options = this.getCustomOptionDetails(product,configurable.custom_options);
                }
            }

            self.selectedRequiredProduct(product);
            productItemsData = self.selectedRequiredProductItems();
            if (product && product.id) {
                productItemsData[this.collectionId] = {
                    qty: product.qty,
                    price: product.price,
                    id: product.id,
                    product_id: product.id,
                    options: product.options
                };
            } else {
                delete productItemsData[this.collectionId];
            }

            self.selectedRequiredProductItems(productItemsData);
            requiredProductDetail.selectedRequiredItems(productItemsData);
        },

        /**
         * Function to get custom options label and value based on product data
         */
        getCustomOptionDetails: function(product, customOptionsData) {
            var optionsDetails = [];

            var customOptions = product.attributes.custom_options || [];

            Object.keys(customOptionsData).forEach(function (key) {
                var optionId = key;
                var optionValue = customOptionsData[key];

                if (!optionValue) return;

                var option = customOptions.find(function (opt) {
                    return opt.id === optionId;
                });

                if (option) {
                    var optionLabel = option.title;
                    var optionValues = option.values || [];
                    var optionType = option.type;

                    if (optionType === "field") {
                        optionsDetails.push({
                            label: optionLabel,
                            value: optionValue
                        });
                    } else if (optionType === "drop_down" || optionType === "radio") {
                        var selectedValue = optionValues.find(function (value) {
                            return value.id === optionValue;
                        });

                        if (selectedValue) {
                            optionsDetails.push({
                                label: optionLabel,
                                value: selectedValue.title
                            });
                        } else {
                            optionsDetails.push({
                                label: optionLabel,
                                value: optionValue
                            });
                        }
                    }
                }
            });
            return optionsDetails;
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
