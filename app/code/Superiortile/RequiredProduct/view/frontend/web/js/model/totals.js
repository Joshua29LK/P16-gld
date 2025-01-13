/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko'
], function ($, ko) {
    'use strict';

    var subTotalData = ko.observable({});

    return {
        /**
         * setSubTotalData
         */
        setSubTotalData: function (data) {
            subTotalData(data);
        },

        /**
         * getSubTotal
         */
        getSubTotal: function () {
            return subTotalData;
        }
    };
});
