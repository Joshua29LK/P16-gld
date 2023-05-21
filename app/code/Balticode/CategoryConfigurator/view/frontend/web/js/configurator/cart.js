define([
    'uiComponent',
    'jquery',
    'ko',
    'uiRegistry',
    'priceFormatter'
], function (Component, $, ko, uiRegistry, priceFormatter) {
    'use strict';

    return Component.extend({
        configuratorComponentName: 'configurator',
        cartItems: ko.observableArray([]),
        cartTotal: ko.observable(0),
        configuratorCartErrorText: ko.observable('Enkele selecties zijn niet geldig, controleer a.u.b. uw invoer.'),
        errorFieldSelector: '#configurator-cart-error',

        initialize: function () {
            this._super();

            this.cartItems({});
        },

        updateCart: function (stepIdentifier, productsData) {
            var cartItemData = [];
            var self = this;
            var sortOrder = 0;

            uiRegistry.get(stepIdentifier, function (step) {
                sortOrder = step.getSortOrder();
            });

            _.each(productsData, function (productData) {
                var cartItem = self.prepareCartItemDataFromProduct(productData);

                if (cartItem !== false) {
                    cartItem.sortOrder = sortOrder;

                    cartItemData.push(cartItem);
                }
            });

            this.cartItems()[stepIdentifier] = cartItemData;
            this.cartItems.valueHasMutated();

            this.updateCartTotal();

            if (this.checkProductDataValidity()) {
                this.hideErrorMessage();
            }
        },

        prepareCartItemDataFromProduct: function (product) {
            if (!product.hasOwnProperty('name') || !product.hasOwnProperty('price')) {
                return false;
            }

            var finalPrice = parseFloat(product['price']);

            if (product.hasOwnProperty('selected_options')) {
                finalPrice += this.calculateTotalOptionsPrice(product['selected_options']);
            }

            if (isNaN(finalPrice)) {
                return false;
            }

            return {
                name: product['name'],
                price: finalPrice
            }
        },

        calculateTotalOptionsPrice: function (options) {
            var optionsPrice = 0;

            options.forEach(function (option) {
                if (option.hasOwnProperty('value') && option['value'].hasOwnProperty('price')) {
                    optionsPrice += parseFloat(option['value']['price']);
                }
            });

            return optionsPrice;
        },

        updateCartTotal: function () {
            var cartTotal = 0;
            var self = this;

            _.each(this.cartItems(), function (stepCartItems) {
                cartTotal += self.calculateTotalStepCartItemsPrice(stepCartItems);
            });

            this.cartTotal(cartTotal);
        },

        calculateTotalStepCartItemsPrice: function (stepCartItems) {
            var stepCartItemsTotal = 0;

            stepCartItems.forEach(function (cartItem) {
                if (cartItem.hasOwnProperty('price')) {
                    stepCartItemsTotal += cartItem['price'];
                }
            });

            return stepCartItemsTotal;
        },

        addProductsToCart: function () {
            var selectedItemsData = this.requestSelectedProductData();
            var self = this;

            if (typeof selectedItemsData === 'undefined' || selectedItemsData === false) {
                return false;
            }

            if (this.checkProductDataValidity() === false) {
                this.showErrorMessage();

                return false;
            }

            this.setAddToCartButtonState(true);

            $.ajax({
                url: this.action,
                type: 'POST',
                data : selectedItemsData,
                showLoader: true,
                success: function (response) {
                    if (response.hasOwnProperty('error')) {
                        return self.setAddToCartButtonState(false);
                    }

                    if (response.hasOwnProperty('redirectUrl')) {
                        window.location.href = response['redirectUrl'];
                    }
                }
            });
        },

        requestSelectedProductData: function () {
            var productData = false;

            uiRegistry.get(this.configuratorComponentName, function (configuratorComponent) {
                productData = configuratorComponent.prepareSelectedValues();
            });

            return productData;
        },

        checkProductDataValidity: function () {
            var result = false;

            uiRegistry.get(this.configuratorComponentName, function (configuratorComponent) {
                result = configuratorComponent.isSelectedProductsDataValid();
            });

            return result;
        },

        showErrorMessage: function () {
            $(this.errorFieldSelector).show();
        },

        hideErrorMessage: function () {
            $(this.errorFieldSelector).hide();
        },

        setAddToCartButtonState: function (isDisabled) {
            $(this.buttonIdentifier).prop('disabled', isDisabled);
        },
        
        getAllStepsCartItems: function () {
            var allStepsCartItems = [];
            var self = this;

            _.each(this.cartItems(), function (stepCartItems) {
                allStepsCartItems = allStepsCartItems.concat(stepCartItems);
            });

            allStepsCartItems.sort(this.compareCartItems);

            return allStepsCartItems;
        },

        compareCartItems: function (firstItem, secondItem) {
            return firstItem.sortOrder - secondItem.sortOrder;
        },

        getFormattedPrice: function (price) {
            return priceFormatter().getFormattedPrice(price);
        }
    });
});