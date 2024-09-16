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
 * @package    Bss_Faqs
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

define([
    'jquery',
    'mage/adminhtml/grid'
], function ($, _) {
    'use strict';
    $.widget('bss.grid_control', {
        _create: function () {
            var fieldId = this.options.fieldId,
                sourceId = this.options.sourceId,
                dependenceId = this.options.dependence,
                gridJsObject = window[this.options.jsObjectName],
                tableId = this.options.tableId,
                valueList;
            if (dependenceId.length > 0) {
                if ($('#' + dependenceId).val() === '1') {
                    $('#' + tableId).hide();
                }
                $('#' + dependenceId).change(function () {
                    if ($(this).val() === '1') {
                        $('#' + tableId).hide();
                    } else {
                        $('#' + tableId).show();
                    }
                });
            }

            function initEvent(grid, row)
            {
                var value = $('#' + sourceId).val();
                if (value != '') {
                    valueList = value.split(';');
                } else {
                    valueList = [];
                }
                $('#' + fieldId).val($('#' + sourceId).val());
                var $row = $(row).find('input.checkbox');
                $row.attr('id', fieldId + $row.val());
                $row.next().attr('for', fieldId + $row.val());
                $row.parent('label').attr('for', fieldId + $row.val());
                $row.change(function () {
                    if ($(this).is(':checked')) {
                        valueList.push($(this).val());
                    } else {
                        valueList.splice(valueList.indexOf($(this).val()), 1);
                    }
                    $('#' + fieldId).val(valueList.join(';'));
                });
            }
            gridJsObject.initRowCallback = initEvent;
            if (gridJsObject.rows) {
                gridJsObject.rows.each(function (row) {
                    initEvent(gridJsObject, row);
                });
            }
        }
    });
    return $.bss.grid_control;
});
