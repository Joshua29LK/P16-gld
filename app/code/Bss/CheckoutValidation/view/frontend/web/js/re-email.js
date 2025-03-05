/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_CheckoutValidation
 * @author     Extension Team
 * @copyright  Copyright (c) 2020-2021 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    'jquery',
    'uiComponent',
    'ko',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/view/form/element/email',
    'mage/validation'
], function ($, Component, ko, checkoutData, fullScreenLoader) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Bss_CheckoutValidation/form/element/reemail',
            isEmailMatch: false,
            email: checkoutData.getInputFieldEmailValue(),
            confirmEmail: '',
            listens: {
                confirmEmail: 'getConfirmEmail',
                isEmailMatch: 'getConfirmEmail'
            }
        },

        /**
         * Initializes regular properties of instance.
         *
         * @returns {Object} Chainable.
         */
        initConfig: function () {
            this._super();

            return this;
        },

        /**
         * Initializes observable properties of instance
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe(['email', 'confirmEmail', 'isEmailMatch']);

            return this;
        },
        /**
         * Get and check confirmation email
         *
         * @returns string
         */
        getConfirmEmail: function () {
            var loginFormSelector = 'form[data-role=email-with-possible-login]',
                usernameSelector = loginFormSelector + ' input[name=re-username]';
            this.email(checkoutData.getInputFieldEmailValue());
            this.confirmEmail($(usernameSelector).val());
            this.isEmailMatch(this.confirmEmail() == this.email());
            return $(usernameSelector).val();
        },
        /**
         * Remove readonly attribute
         *
         * @returns void
         */
        removeReadOnly: function () {
            var loginFormSelector = 'form[data-role=email-with-possible-login]',
                usernameSelector = loginFormSelector + ' input[name=re-username]';
            $(usernameSelector).attr("readonly", false);
        },
        /**
         * Add readonly attribute
         *
         * @returns void
         */
        addReadOnly: function () {
            var loginFormSelector = 'form[data-role=email-with-possible-login]',
                usernameSelector = loginFormSelector + ' input[name=re-username]';
            $(usernameSelector).attr("readonly", true);
        }
    });
});
