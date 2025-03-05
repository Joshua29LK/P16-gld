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
    'mage/translate',
    'Magento_Ui/js/model/messageList',
    'Bss_CheckoutValidation/js/model/err',
    'mage/validation'
    ], function ($, $t, messageList, err) {
        'use strict';

        return {

            /**
             * Validate email address
             *
             * @returns {Boolean}
             */
            validate: function () {
                var emailValidationResult = false;
                var loginFormSelector = 'form[data-role=email-with-possible-login]',
                    usernameSelector = loginFormSelector + ' input[name=username]',
                    reUsernameSelector = loginFormSelector + ' input[name=re-username]';
                var email = $(usernameSelector).val();
                var reEmail = $(reUsernameSelector).val();
                if(email === reEmail){ //should use Regular expression in real store.
                    emailValidationResult = true;
                }

                if (!emailValidationResult) {
                    messageList.addErrorMessage({ message: $t('The email addresses do not match.') });
                    err(true);
                } else {
                    if (err()) {
                        var messSelector = '[data-role=checkout-messages]';
                        $(messSelector).hide('blind', {}, 500);
                        err(false);
                    }
                }

                return emailValidationResult;
            }
        };
    }
);