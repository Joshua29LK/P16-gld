/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/mage',
    'Magento_Catalog/product/view/validation',
    'CatalogAddToConfigurable'
], function ($) {
    'use strict';

    $.widget('mage.productValidate', {
        options: {
            bindSubmit: false,
            radioCheckboxClosest: '.nested'
        },

        /**
         * Uses Magento's validation widget for the form object.
         * @private
         */
        _create: function () {
            var bindSubmit = this.options.bindSubmit;

            this.element.find('#product-addtocart-button').removeAttr('disabled');
            this.element.validation({
                radioCheckboxClosest: this.options.radioCheckboxClosest,

                /**
                 * submitHandler
                 */
                submitHandler: function (form) {
                    var jqForm = $(form).CatalogAddToConfigurable({
                        bindSubmit: bindSubmit
                    });

                    jqForm.CatalogAddToConfigurable('submitForm', jqForm);

                    return false;
                }
            });
        }
    });

    return $.mage.productValidate;
});
