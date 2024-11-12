define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'ko'
], function (Component, customerData, ko) {
    'use strict';

    return Component.extend({
        subtotal: ko.observable(''),
        tax: ko.observable(''),
        taxRate: ko.observable(''),

        initialize: function () {
            this._super();
            var cart = customerData.get('cart');
            this.updateCartData(cart());
            cart.subscribe(this.updateCartData.bind(this));
        },

        updateCartData: function (cartData) {
            if (cartData) {
                this.subtotal(cartData.subtotal_excl_tax);

                let subtotalExclTax = 0;
                let totalTax = 0;
                let taxRate = 0;
                
                cartData.items.forEach(function (item) {
                    let exclTax = parseFloat(item.product_price_value.excl_tax);
                    subtotalExclTax += exclTax * item.qty;
                });

                totalTax = cartData.subtotalAmount - subtotalExclTax;
                taxRate = ((totalTax / subtotalExclTax) * 100);
                taxRate = taxRate % 1 < 0.01 ? taxRate.toFixed(0) : taxRate.toFixed(2);
                
                this.tax('<span class="price">€ ' + totalTax.toFixed(2) + '</span>');
                this.taxRate(taxRate + '%');
            }
        }
    });
});
