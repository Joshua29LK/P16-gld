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
define(function () {
    'use strict';

    return function (target) {
        return target.extend({

            /**
             *
             * @param {Column} elem
             */
            getTemplate: function (elem) {
                var reemailEnabled = window.checkoutConfig.reemail_enabled;
                return !reemailEnabled ? this._super() : 'Bss_CheckoutValidation/form/element/email';
            }
        });
    };
});
