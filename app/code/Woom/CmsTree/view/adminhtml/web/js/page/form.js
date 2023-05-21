/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global alert:true*/

define([
    'jquery',
    'Magento_Ui/js/modal/alert'
], function ($, alert) {
    'use strict';

    return function (config) {
        var pageForm = {
            options: {
                cmsIdSelector: 'input[name="page_id"]',
                cmsPathSelector: 'input[name="path"]',
                refreshUrl: config.refreshUrl
            },

            /**
             * Sending ajax to server to refresh field 'path'
             * @protected
             */
            refreshPath: function () {
                if (!$(this.options.cmsIdSelector)) {
                    return false;
                }
                $.ajax({
                    url: this.options.refreshUrl,
                    method: 'GET',
                    showLoader: true
                }).done(this._refreshPathSuccess.bind(this));
            },

            /**
             * Refresh field 'path' on ajax success
             * @param {Object} data
             * @private
             */
            _refreshPathSuccess: function (data) {
                console.log(data);
                if (data.error) {
                    alert({
                        content: data.message
                    });
                } else {
                    $(this.options.cmsIdSelector).val(data.page_id).change();
                    $(this.options.cmsPathSelector).val(data.path).change();
                }
            }
        };

        $('body').on('pageMove.tree', $.proxy(pageForm.refreshPath.bind(pageForm), this));
    };
});
