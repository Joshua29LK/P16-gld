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
 * @copyright  Copyright (c) 2017-2023 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    'ko',
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/abstract',
    'Magento_Ui/js/lib/validation/validator',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function (ko, $, _, abstract, validator, alert) {
    'use strict';

    return abstract.extend({
        defaults: {
            bssOptionsKey: '',
            multiselectId: '',
            multiselectListArray: ko.observableArray(),
            multiselectValue: ko.observableArray(),
            bssOptionId: null
        },

        initConfig: function () {
            this._super();
            if (window.dcoChanged === undefined) {
                window.dcoChanged = false;
            }
            var namePart = this.name.split('.');
            this.bssOptionsKey = namePart[4];
            this.bssOptionId = namePart[4];
            this.multiselectId = 'multiselect-' + this.uid;
            this.multiselectListArray = ko.observableArray([]);
            validator.addRule('dependent-id-exist', this.validateDependentExist, $.mage.__("Please add the valid IDs"));
            validator.addRule('dependent-id-option', this.validateDependentOption, $.mage.__("Parent and children values can't be in the same custom option"));
            this.validation = {'dependent-id-exist': true, 'dependent-id-option': true};
            this.validationParams = {'dependent-option-key': this.bssOptionsKey, 'this': this};
            var selft = this;
            $('body').on('change','.admin__control-text[name$="[title]"]', function (event) {
                let $bssDependentId = $(this).closest('.admin__field-group-columns').find('.bss-dependent-id'),
                    id,
                    uid;

                if ($(this).closest('tr').find('.bss-depend-option-id').val() === selft.bssOptionId) {
                    return false;
                }
                if ($bssDependentId.length === 0) {
                    $bssDependentId = $(this).closest('tr').find('.bss-dependent-id');
                }
                id = $bssDependentId.val();
                uid = $bssDependentId.attr('id');
                selft.multiselectList(uid, $(this).val());
                window.bss_depend_id[id] = $(this).val();
                $('.multiselect-dco option[value="'+id+'"]').text($(this).val());
            });
            return this;
        },
        multiselectOption: function () {
            var $this = this,
                currentIds = window.bss_depend_option[this.bssOptionsKey];
            $this.multiselectList.removeAll();
            $.each(window.bss_depend_id, function (k, id) {
                if (currentIds[k] === undefined) {
                    var a = {};
                    a.value = k;
                    a.label = jQuery('input.bss-dependent-id[value="'+k+'"]').closest('tr').find('input.admin__control-text[name*="title]"]').val()
                        || jQuery('input.bss-dependent-id[value="'+k+'"]').closest('fieldset').find('input.admin__control-text[name*="title]"]').val()
                        || id
                    ;
                    $this.multiselectList.push(a);
                }
            });
            $this.multiselectValue.removeAll();
            $.each(this.value().split(','), function (k, vl) {
                $this.multiselectValue.push(vl);
            });
        },
        multiselectedValues: function () {
            var selectedValues = [];
            $.each(this.value().split(','), function (k, vl) {
                selectedValues.push(vl);
            });
            return selectedValues;
        },

        /**
         * Update depend options title
         *
         * @param {string} uid
         * @param {string} newTitle
         * @returns {boolean}
         */
        multiselectList: function (uid, newTitle) {
            var currentIds,
                $this = this,
                removeElements = [];

            // when update title of options
            if (uid) {
                const optionListByUid = $this.multiselectListArray;
                let affectOption, currentUid, newOptIndex;

                if (optionListByUid === undefined) {
                    return false;
                }

                affectOption = optionListByUid().find(item => item.uid === uid);

                if (affectOption) {
                    affectOption.label = newTitle;
                } else {
                    newOptIndex = parseInt($('#depend-last-increment-id').val());

                    // If new value in same Option with dependent options
                    if (window.bss_depend_option[this.bssOptionsKey][newOptIndex] !== undefined) {
                        return false;
                    }

                    // New options
                    optionListByUid.push({
                        label: newTitle,
                        uid,
                        value: newOptIndex
                    });
                }
            } else { // when initialization
                currentIds = window.bss_depend_option[this.bssOptionsKey];
                $this.multiselectListArray([]);
                $.each(window.bss_depend_id, function (k, id) {
                    if (currentIds && currentIds[k] === undefined) {
                        let $optionSelector = jQuery('input.bss-dependent-id[value="'+k+'"]'),
                            $uid = $optionSelector.closest('.admin__field-group-columns').find('.bss-dependent-id'),
                            a = {};
                        a.value = k;
                        a.label = $optionSelector.closest('tr').find('input.admin__control-text[name*="title]"]').val()
                            || $optionSelector.closest('fieldset').find('input.admin__control-text[name*="title]"]').val()
                            || id;

                        if ($uid.length === 0) {
                            $uid = $optionSelector.closest('tr').find('.bss-dependent-id')
                        }
                        a.uid = $uid.attr('id');
                        if (a.label && a.label !== true) {
                            $this.multiselectListArray.push(a);
                        } else {
                            removeElements.push(a.value);
                        }
                    }
                });
            }

            if (this.value()) {
                let value = this.value().split(','),
                    $dependMultiselect = $(`#${this.multiselectId}`),
                    intersections;

                if (removeElements.length > 0 && $dependMultiselect.length > 0) {
                    intersections = value .filter(e => removeElements .indexOf(e) === -1);
                    this.value(intersections.join(','));
                }
                $dependMultiselect.val(value);
            }
        },
        // Function check when change option select
        updateDependChild: function () {
            var valueSelected = $('#' + this.multiselectId).val();
            if (valueSelected && (typeof(valueSelected)  === "object" && valueSelected.length)) {
                var checkNotSelectSameParent = true;
                var elementRow = $('#' + this.multiselectId).closest('tr.data-row');
                var cloneSelectData = $('#' + this.multiselectId).val();
                var dependId = elementRow.find('.bss-dependent-id').val();
                var elementDependIdParent = elementRow.find('.check-depend-is-parent-option .bss-dependent-id');
                var dependIdParent = elementDependIdParent.length > 0 ? elementDependIdParent.val() : elementRow.parentsUntil('tr.data-row').find('.check-depend-is-parent-option .bss-dependent-id').val();
                $.each($('#' + this.multiselectId).val(), function (index, value) {
                    if (!checkNotSelectSameParent) {
                        return false;
                    }
                    var optionKey = $('.bss-dependent-id[value="'+value+'"]').attr('option_key');
                    if (window.bss_depend_option[optionKey]) {
                        $.each(window.bss_depend_option[optionKey], function (indexDepend, valueDepend) {
                            if (!checkNotSelectSameParent) {
                                return false;
                            }
                            var element = $('.bss-dependent-id[value="'+indexDepend+'"]');
                            element.closest('tr.data-row:not(".check-depend-is-parent-option .bss-dependent-id")').find('.multiselect-dco').each(function() {
                                var same = $(this).closest('tr.data-row').find('.bss-dependent-id').val();
                                var sameParent = element.closest('tr.data-row:has(".check-depend-is-parent-option")').find('.bss-dependent-id').val();
                                if (($(this).val() && (value === same || value === sameParent) && (($.inArray(dependId, $(this).val()) > -1 ) || $.inArray(dependIdParent, $(this).val()) > -1)) === true) {
                                    checkNotSelectSameParent = false;
                                    cloneSelectData.splice(index, 1);
                                    alert({
                                        content: $.mage.__("You can't not select its parent custom option as dependent custom option ")
                                    });
                                    return false;
                                }
                            });
                        })
                    }
                });
                $('#' + this.multiselectId).val(cloneSelectData);
                if (checkNotSelectSameParent) {
                    this.value($('#' + this.multiselectId).val().join(','));
                }
            }
            if ($('#' + this.multiselectId+ ' option').length > 0 && !$('#' + this.multiselectId).val().length) {
                this.value('');
            }
        },
        expandMultiselect: function () {
            if ($('#' + this.multiselectId).hasClass('show')) {
                $('#' + this.multiselectId).fadeOut().removeClass('show');
                $('#' + this.uid).fadeIn();
            } else {
                this.multiselectOption();
                $('#' + this.uid).fadeOut();
                $('#' + this.multiselectId).fadeIn().addClass('show');
            }
        },
        validateDependentExist: function (value, params, additionalParams) {
            if (value === '' || !window.dcoChanged) {
                return true;
            }
            var ids = value.split(','),
                result = true;
            $.each(ids, function (k, val) {
                if (window.bss_depend_id[val] === undefined) {
                    result = false;
                }
            });
            return result;
        },
        validateDependentOption: function (value, params, additionalParams) {
            if (!window.dcoChanged) {
                return true;
            }
            var ids = value.split(','),
                result = true,
                currentIds = window.bss_depend_option[additionalParams['dependent-option-key']];
            $.each(currentIds, function (k, id) {
                if (ids.indexOf(k) >= 0) {
                    result = false;
                }
            });
            return result;
        }
    });
});
