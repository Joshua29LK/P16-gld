define([
    'jquery',
    'ko',
    'uiComponent',
    'priceFormatter',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function ($, ko, Component, priceFormatter) {
    'use strict';

    return Component.extend({
        renderPrice: ko.observable(false),
        productPrice: ko.observable(),
        productImageUrl: ko.observable(),
        productName: ko.observable(),
        productDescription: ko.observable(),
        currencyCode: ko.observable(),
        hasPreviousProduct: false,
        hasNextProduct: false,
        currentProductIndex: false,
        currentProduct: false,
        productList: [],

        defaults: {
            modalOptions: {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                modalClass: 'product-view-modal',
                clickableOverlay: true,
                buttons: false
            },
            modalId: '#product-view-modal',
            template: 'Balticode_CategoryConfigurator/configurator/step/product-modal'
        },

        initialize: function () {
            this._super();
            this.currencyCode(this.defaultCurrencyCode);
        },

        open: function (productList, productId) {
            this.productList = productList;
            this.selectDefaultCurrentProduct(productId);
            
            $(this.modalId).modal(this.modalOptions).modal('openModal');
        },

        close: function() {
            $(this.modalId).modal('closeModal');
        },

        selectDefaultCurrentProduct: function (productId) {
            var self = this;
            this.currentProduct = false;
            this.currentProductIndex = false;

            _.each(this.productList, function (product, index) {
                if (product.hasOwnProperty('product_id') && product['product_id'] == productId) {
                    self.currentProductIndex = index;
                    self.currentProduct = product;
                }
            });

            this.setObservables();
        },

        setObservables: function () {
            if (this.currentProduct === false) {
                return false;
            }

            var price = parseFloat(this.currentProduct.price);

            if (!isNaN(price) && price > 0) {
                this.productPrice(this.getFormattedPrice(price));
                this.renderPrice(true);
            } else {
                this.renderPrice(false);
            }

            this.productImageUrl(this.currentProduct.image);
            this.productName(this.currentProduct.name);
            this.productDescription(this.currentProduct.description);

            if (this.currentProductIndex === false) {
                return false;
            }

            this.hasPreviousProduct = this.checkProductListIndexIsSet(this.currentProductIndex - 1);
            this.hasNextProduct = this.checkProductListIndexIsSet(this.currentProductIndex + 1);
            this.toggleArrowsVisibility();
        },
        
        checkProductListIndexIsSet: function (index) {
            if (isNaN(index) || index < 0) {
                return false;
            }

            return this.productList[index] !== void 0;
        },

        selectNextProduct: function () {
            this.currentProductIndex += 1;
            this.currentProduct = this.productList[this.currentProductIndex];
            this.setObservables();
        },

        selectPreviousProduct: function () {
            this.currentProductIndex -= 1;
            this.currentProduct = this.productList[this.currentProductIndex];
            this.setObservables();
        },

        toggleArrowsVisibility: function () {
            this.toggleArrowVisibility(this.nextProductArrowSelector, this.hasNextProduct);
            this.toggleArrowVisibility(this.previousProductArrowSelector, this.hasPreviousProduct);
        },

        toggleArrowVisibility: function (arrowSelector, hasProduct) {
            var arrow = $(arrowSelector);

            if (!hasProduct) {
                arrow.addClass('arrow-hidden');

                return;
            }

            if (arrow.hasClass('arrow-hidden')) {
                arrow.removeClass('arrow-hidden');
            }
        },

        getFormattedPrice: function (price) {
            return priceFormatter().getFormattedPrice(price);
        }
    });
});