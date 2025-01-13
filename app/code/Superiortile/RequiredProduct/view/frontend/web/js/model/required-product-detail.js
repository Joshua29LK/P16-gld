/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'Magento_Ui/js/modal/modal'
], function ($, ko, modal) {
    'use strict';

    var selectedRequiredProductItems = ko.observable({}),
        requiredProductListModals = ko.observable({});

    return {
        /**
         * showPopupProductDetail
         */
        showPopupProductDetail: function (data, parentModal) {
            var options = {
                    type: 'popup',
                    modalClass: 'required-product-detail-popup',
                    responsive: true,
                    buttons: [],
                    title: $.mage.__('Select %1').replace('%1', data.productName)
                },
                element = $('#required-product-detail'),
                iframe = $('<iframe>').attr({
                    src: data.url + '?t=' +  Math.floor(Date.now() / 1000),
                    frameborder: 0,
                    allowfullscreen: 0,
                    style: 'opacity: 1; width: 100%; height: 100%;'
                }),
                buttonBack = $('<span class="action-back">' + $.mage.__('Back') + '</span>');

            if (element.find('.ox_quickview-preloader').length === 0) {
                element.prepend($('<div class="ox_quickview-preloader" data-role="loader"></div>'));
            }
            element.find('.ox_quickview-preloader').show();

            element.find('iframe').remove();
            element.append(iframe);
            modal(options, element);
            window.modalRequiredProductDetail = element.modal('openModal');
            window.modalRequiredProductDetail.closest('.modal-inner-wrap')
                .find('header.modal-header').prepend(buttonBack);

            buttonBack.click(function () {
                window.modalRequiredProductDetail.modal('closeModal');
                if (parentModal) {
                    parentModal.modal('openModal');
                }
            });
        },

        /** Show reservation popup window */
        closeModal: function () {
            window.modalRequiredProductDetail.modal('closeModal');
        },

        selectedRequiredItems: function (items) {
            if (items) {
                selectedRequiredProductItems(items);
            }

            return selectedRequiredProductItems();
        },

        requiredProductListModals: function (modals) {
            if (modals) {
                requiredProductListModals(modals);
            }

            return requiredProductListModals;
        }
    };
});
