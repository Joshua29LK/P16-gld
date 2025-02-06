/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'mage/url',
    'jquery',
    'escaper',
    'uiComponent',
    'Magento_Ui/js/modal/modal',
    'Superiortile_RequiredProduct/js/model/required-product-detail'
], function (ko, urlBuilder, $, escaper, Component, modal, requiredProductDetail) {
    'use strict';

    return Component.extend({
        defaults: {
            canShoButtonAdd: true,
            modals: []
        },

        /**
         * Init
         */
        initialize: function () {
            var self = this,
                url,
                productId,
                data;

            self._super();
            self.listProductSelector = '.required-products-wrapper-' + self.collectionId;
            if (!$(self.listProductSelector).length) {
                return;
            }

            self.createPopup();
            $(self.listProductSelector).on('click', '.required-item', function (e) {
                e.preventDefault();
                productId = $(e.currentTarget).attr('data-product-id');
                url = urlBuilder.build('/requiredproduct/product/view/id/%1/main_product/%2/type_id/%3'
                    .replace('%1', productId)
                    .replace('%2', self.mainProductId)
                    .replace('%3', self.collectionTypeId)
                );
                data = {
                    url: url,
                    productName: self.collectionName
                };
                self.modalPopup.modal('closeModal');
                requiredProductDetail.showPopupProductDetail(data, self.modalPopup);
            });
        },

        /**
         * initObservable
         */
        initObservable: function () {
            this._super().observe(['canShoButtonAdd']);
            return this;
        },

        /**
         * createPopup
         */
        createPopup() {
            var self = this,
                modals,
                options = {
                type: 'popup',
                modalClass: 'required-popup required-products-list-popup',
                responsive: true,
                buttons: [],
                title: $.mage.__('Select %1').replace('%1', self.collectionName)
            };

            if (!self.modalPopup) {
                self.modalPopup = $(self.listProductSelector);
                modal(options, $(self.modalPopup));
            }

            if (!window.requiredProductsListPopup) {
                window.requiredProductsListPopups = [];
            }
            modals = requiredProductDetail.requiredProductListModals()();

            modals[self.collectionId] = self.modalPopup;
            requiredProductDetail.requiredProductListModals(modals);
        },

        onClickAdd: function () {
            let isValid = true;
            let firstInvalidElement = null;

            const requiredFields = document.querySelectorAll(".product-custom-option[aria-required='true'], .product-custom-option[required]");

            requiredFields.forEach(field => {
                const label = document.querySelector(`label[for="${field.id}"]`);
                const labelText = label ? label.innerText.trim() : "";

                if (labelText === "Breedte (mm)" || labelText === "Hoogte (mm)") {
                    if ((field.type === "text" || field.type === "number" || field.tagName === "SELECT") && field.value.trim() === "") {
                        isValid = false;
                        field.classList.add("input-error");
                        if (!firstInvalidElement) firstInvalidElement = field;
                    }
                }
            });

            const errorBlock = document.querySelector(".required-products-block .mage-error");
            if (!isValid) {
                if (errorBlock) {
                    errorBlock.textContent = "Please fill in both 'Breedte (mm)' and 'Hoogte (mm)' fields to proceed.";
                    errorBlock.style.display = "block";
                }

                if (firstInvalidElement) {
                    firstInvalidElement.scrollIntoView({ behavior: "smooth", block: "center" });
                    firstInvalidElement.focus();
                }

                return;
            }

            if (errorBlock) {
                errorBlock.style.display = "none";
            }

            this.modalPopup.modal('openModal');
        }
    });
});
