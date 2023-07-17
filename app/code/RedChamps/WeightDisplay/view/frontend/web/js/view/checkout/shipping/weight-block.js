define([
    'uiComponent'
], function (Component) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'RedChamps_WeightDisplay/checkout/shipping/weight-block'
        },
        canShow: function () {
            return window.cartWeight.active &&
                window.cartWeight.amount &&
                (this.position == window.cartWeight.position || window.cartWeight.position == "both");
        },
        label: window.cartWeight.label,
        weight: window.cartWeight.amount
    });
});
