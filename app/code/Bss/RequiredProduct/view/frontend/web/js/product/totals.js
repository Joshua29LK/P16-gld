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
    'Bss_RequiredProduct/js/model/required-product-detail',
    'Bss_RequiredProduct/js/model/totals'
], function ($, uiRegistry, ko, Component, customerData, urlBuilder, requiredProductDetail, totals) {
    'use strict';

    return Component.extend({
        defaults: {
            totals: totals.getSubTotal(),
            label: $.mage.__('Subtotal (%s items) :')
        },

        /**
         * initialize
         */
        initialize: function () {
            this._super();
        }
    });
});
