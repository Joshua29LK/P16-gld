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
 * @package    Bss_OrderDeliveryDate
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
*/
define(
    [
        'jquery',
        'mage/utils/wrapper',
        'Magento_Checkout/js/model/quote'
    ],
    function ($, wrapper, quote) {
        'use strict';

        return function (placeOrderAction) {
            return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, redirectOnSuccess, messageContainer) {
                var billingAddress = quote.billingAddress();
                if (billingAddress['extension_attributes'] === undefined) {
                    billingAddress['extension_attributes'] = {};
                }
                if ($('.checkout-payment-method #shipping_arrival_date').length > 0) {
                    var order_comments = '',
                        order_date = '',
                        order_time_slot = '';
                    if ($('#shipping_arrival_comments').val()) {
                        billingAddress['extension_attributes']['shipping_arrival_comments'] = $('#shipping_arrival_comments').val();
                    }
                    if ($('#shipping_arrival_date').val()) {
                        billingAddress['extension_attributes']['shipping_arrival_date'] = $('#shipping_arrival_date').val();
                    }
                    if ($('#delivery_time_slot').length > 0 && $('#delivery_time_slot').val()) {
                        billingAddress['extension_attributes']['delivery_time_slot'] = $('#delivery_time_slot').val();
                    }
                }
                return originalAction(paymentData, redirectOnSuccess, messageContainer);
            });
        };
    }
);
