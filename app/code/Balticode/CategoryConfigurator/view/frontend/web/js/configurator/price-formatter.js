define([
    'uiComponent'
], function (Component) {
    'use strict';

    return Component.extend({
        getFormattedPrice: function (price) {
            var floatPrice = parseFloat(price);

            if (isNaN(floatPrice)) {
                return 0;
            }

            var formattedPrice = floatPrice.toFixed(2);
            formattedPrice = formattedPrice.replace(".", ",");
            formattedPrice = formattedPrice.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

            return formattedPrice;
        }
    });
});