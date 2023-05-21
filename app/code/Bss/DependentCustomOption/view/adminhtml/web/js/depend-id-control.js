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
 * @package    Bss_DependentCustomOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/abstract',
    'mage/translate'
], function ($, _, abstract) {
    'use strict';

    return abstract.extend({
        defaults: {
            bssOptionsKey: '',
            bssValueKey: '',
            bssTempDependId: ''
        },
        initConfig: function () {
            this._super();
            var namePart = this.name.split('.');
            this.bssOptionsKey = namePart[4];
            if (window.bss_depend_id === undefined) {
                window.bss_depend_id = {};
            }
            if (window.bss_depend_option === undefined) {
                window.bss_depend_option = {};
            }
            if (window.bss_depend_option[this.bssOptionsKey] === undefined) {
                window.bss_depend_option[this.bssOptionsKey] = {};
            }
            return this;
        },
        getDependValue: function () {
            if (this.value() == '0' || this.value() == '') {
                if (this.bssTempDependId == '') {
                    var jsonUrl = this.jsonUrl,
                        $this = this;
                    var val = parseInt($('#depend-last-increment-id').val()) + 1;
                    $('#depend-last-increment-id').val(val);
                    $this.bssTempDependId = val;
                    window.bss_depend_id[val] = jQuery('input.bss-dependent-id[value="'+val+'"]').closest('tr').find('input.admin__control-text[name*="title]"]').val()
                        || jQuery('input.bss-dependent-id[value="'+val+'"]').closest('fieldset').find('input.admin__control-text[name*="title]"]').val()
                        || true;
                    window.bss_depend_option[$this.bssOptionsKey][val] = true;
                    $this.value(val);
                    setTimeout(function () {
                        $('#bss-dco-span-' + $this.uid).html(val);
                    },10)
                }
            } else {
                if (window.bss_depend_id[this.value()] === undefined) {
                    window.bss_depend_id[this.value()] = jQuery('input.bss-dependent-id[value="'+this.value()+'"]').closest('tr').find('input.admin__control-text[name*="title]"]').val()
                        || jQuery('input.bss-dependent-id[value="'+this.value()+'"]').closest('fieldset').find('input.admin__control-text[name*="title]"]').val()
                        || true;
                }
                window.bss_depend_option[this.bssOptionsKey][this.value()] = true;
                return this.value();
            }
            return this.value();
        },
        getSpanId: function () {
            window.dcoChanged = true;
            return 'bss-dco-span-' + this.uid;
        },
        getDependClass: function () {
            return 'dependent-id option-' + this.bssOptionsKey;
        }
    });
});
