/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* eslint-disable no-undef */
// jscs:disable jsDoc

require([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/alert',
    'loadingPopup',
    'mage/backend/floating-header'
], function (jQuery, confirm) {
    'use strict';

    /**
     * Delete some cms
     * This routine get cms page id explicitly, so even if currently selected tree node is out of sync
     * with this form, we surely delete same cms in the tree and at backend
     */
    function pageDelete(url)
    {
        confirm({
            content: 'Are you sure you want to delete this page?',
            actions: {
                confirm: function () {
                    location.href = url;
                }
            }
        });
    }

    function displayLoadingMask()
    {
        jQuery('body').loadingPopup();
    }

    window.pageDelete = pageDelete;
    window.displayLoadingMask = displayLoadingMask;
});
