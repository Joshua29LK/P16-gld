/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'catalogAddToCart',
    'jquery-ui-modules/widget',
    'jquery-ui-modules/core'
], function ($) {
    'use strict';

    $.widget('mage.CatalogAddToConfigurable', $.mage.catalogAddToCart, {

        options: {
            isLoaderEnabled: true,
            addToCartButtonTextDefault: $.mage.__('Add to Configuration')
        },

        /**
         * ajaxSubmit
         */
        ajaxSubmit: function (form) {
            var self = this,
                formData;

            self.disableAddToCartButton(form);
            formData = new FormData(form[0]);

            window.parent.ajaxCartTransport = false;

            window.parent.jQuery.ajax({
                url: form.attr('action'),
                data: formData,
                type: 'post',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,

                /**
                 * beforeSend
                 */
                beforeSend: function () {
                    if (self.isLoaderEnabled()) {
                        $(window.parent.document.body).trigger(self.options.processStart);
                    }
                },

                /**
                 * success
                 */
                success: function () {
                    window.parent.modalRequiredProductDetail.modal('closeModal');
                    self.enableAddToCartButton(form);
                },

                /**
                 * error
                 */
                error: function () {
                    window.parent.location.reload();
                },

                /**
                 * complete
                 */
                complete: function (res) {
                    if (res.state() === 'rejected') {
                        window.parent.location.reload();
                    }
                }
            });
        }
    });

    return $.mage.CatalogAddToConfigurable;
});
