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
 * @category  BSS
 * @package   Bss_CheckoutMsg
 * @author    Extension Team
 * @copyright Copyright (c) 2024 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_Checkout/js/checkout-data',
        'Magento_Ui/js/model/messageList'
    ],
    function($, ko, Component, checkoutData, messageList) {
        'use strict';

        return Component.extend({
            initialize: function() {
                this._super();
                if (this.customMessage) {
                    messageList.addErrorMessage({message: this.customMessage});
                }
            },
        });
    }
);
