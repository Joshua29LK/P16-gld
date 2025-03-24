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
    'Bss_RequiredProduct/js/model/required-product-detail'
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
            $(self.listProductSelector).on('click', '.product-info-container', function (e) {
                e.preventDefault();
                productId = $(e.currentTarget).attr('data-product-id');
                var hasErrorClass = $(e.currentTarget).hasClass('required-error');
                var hasError = hasErrorClass ? 1 : 0;

                const requiredFields = document.querySelectorAll(".product-custom-option[aria-required='true'], .product-custom-option[required]");
                var breedteValue = '';
                var hoogteValue = '';
                requiredFields.forEach(field => {
                    const label = document.querySelector(`label[for="${field.id}"]`);
                    const labelText = label ? label.innerText.trim() : "";

                    if (labelText === "Breedte (mm)") {
                        breedteValue = field.value;
                    }

                    if (labelText === "Hoogte (mm)") {
                        hoogteValue = field.value;
                    }
                });

                console.log(breedteValue,hoogteValue)
                url = urlBuilder.build('/requiredproduct/product/view/id/%1/main_product/%2/type_id/%3/breedte_value/%4/hoogte_value/%5'
                    .replace('%1', productId)
                    .replace('%2', self.mainProductId)
                    .replace('%3', self.collectionTypeId)
                    .replace('%4', breedteValue)
                    .replace('%5', hoogteValue)
                );
                if (hasError) {
                    url = url + '/required_error/1';
                    $(e.currentTarget).removeClass('required-error');
                }
                data = {
                    url: url,
                    productName: self.collectionName
                };
                self.modalPopup.modal('closeModal');
                requiredProductDetail.showPopupProductDetail(data, self.modalPopup);
            });
            $(self.listProductSelector).on('click', '.tocart', function (e) {
                e.preventDefault();
                productId = $(e.currentTarget).parents('.required-item').attr('data-product-id');
                var form = $(e.currentTarget).parents('#product_addtocart_form_' + productId);
                var formKey = form.find('input[name="form_key"]').attr('value');
                var formData = new FormData(form[0]),
                    mainPId = self.mainProductId,
                    typeID = self.collectionTypeId;
                if (!$(e.currentTarget).hasClass('can-not-add')) {
                    formData.append('product', productId);
                    formData.append('main_product', mainPId);
                    formData.append('type_id', typeID);
                    formData.append('is_update_qty', 0);
                    formData.append('form_key', formKey);
                    formData.append('qty', 1);
                    $.ajax({
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
                            $('body').trigger('processStart');
                        },

                        /**
                         * success
                         */
                        success: function () {
                            $('body').trigger('processStop');
                            self.modalPopup.modal('closeModal');
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
                            $('body').trigger('processStop');
                        }
                    });
                } else {
                    $(e.currentTarget).parents('.required-item').find('.product-info-container').addClass('required-error');
                    $(e.currentTarget).parents('.required-item').find('.product-info-container').trigger('click');
                }
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
