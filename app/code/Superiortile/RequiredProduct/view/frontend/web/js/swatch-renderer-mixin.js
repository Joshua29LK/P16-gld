/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'Magento_Catalog/js/price-utils',
    'Superiortile_RequiredProduct/js/model/totals'
], function ($, priceUtils, requiredTotals) {
    'use strict';

    var mixin = {
        _UpdatePrice: function () {
            var totalData,
                mainProductPrice,
                mainProductTotals,
                mainProductQty,
                newPrices;

            this._super();

            totalData = requiredTotals.getSubTotal()();
            newPrices = this._getNewPrices();

            mainProductQty = $('.product-info-main #qty').val();
            mainProductQty = parseInt(mainProductQty, 10);
            if (!mainProductQty) {
                mainProductQty = 1;
            }
            mainProductPrice = newPrices.finalPrice.amount;
            mainProductTotals = mainProductPrice * mainProductQty;
            totalData.mainProductPrice = mainProductPrice;
            totalData.mainProductTotals = mainProductTotals;
            totalData.totals = totalData.mainProductTotals + totalData.requiredProductTotals;
            totalData.formattedPrice = priceUtils.formatPrice(totalData.totals);
            requiredTotals.setSubTotalData(totalData);
        }
    };

    return function (targetWidget) {
        $.widget('mage.SwatchRenderer', targetWidget, mixin);

        return $.mage.SwatchRenderer;
    };
});
