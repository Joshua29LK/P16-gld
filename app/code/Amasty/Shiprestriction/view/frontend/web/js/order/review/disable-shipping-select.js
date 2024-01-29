define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('amshiprestrictions.disableShippingSelect', {
        options: {
            selectors: {
                shippingSelect: '#shipping-method',
                emptyOptionsValue: 'optgroup option[value=""]'
            }
        },

        /**
         * @private
         * @returns {void}
         */
        _create: function () {
            this.disableShippingOptionsWithEmptyValue();
        },

        /**
         * @returns {void}
         */
        disableShippingOptionsWithEmptyValue: function () {
            $(this.options.selectors.shippingSelect)
                .find(this.options.selectors.emptyOptionsValue)
                .attr('disabled', true);
        }
    });

    return $.amshiprestrictions.disableShippingSelect;
});
