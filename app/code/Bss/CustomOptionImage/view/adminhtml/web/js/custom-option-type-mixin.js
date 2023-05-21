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
 * @package    Bss_CustomOptionImage
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    'jquery'
    ], function ($) {
        'use strict';

        var mixin = {
            /**
             * Callback that fires when 'value' property is updated.
             * Check if 'value' in drop_down,radio then will display swatch image
             *
             * @param {String} currentValue
             * @returns {*}
             */
            onUpdate: function (currentValue) {
                this.updateComponents(currentValue);
                var checkExist = $('#'+this.uid).closest('td').find('.image-customoption-container .swatch-image').parent();
                if ( checkExist.length > 0 && currentValue !== 'drop_down' && currentValue !== 'radio') {
                    checkExist.parent().parent().parent().hide();
                    checkExist.closest('.admin__fieldset-wrapper-content.admin__collapsible-content._show').find('table thead tr th span').each(function () {
                        if ($(this).text() == $.mage.__('Swatch Image')) {
                            $(this).parent().hide();
                        }
                    });
                }
                if (checkExist.length > 0 && currentValue === 'drop_down' || currentValue === 'radio') {
                    checkExist.parent().parent().parent().show();
                    checkExist.closest('.admin__fieldset-wrapper-content.admin__collapsible-content._show').find('table thead tr th span').each(function () {
                        if ($(this).text() == $.mage.__('Swatch Image')) {
                            $(this).parent().show();
                        }
                    });
                }
                return this._super();
            },
        };

        return function (target) {
            return target.extend(mixin);
        };
    });
