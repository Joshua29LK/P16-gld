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
    'underscore'
], function ($) {
    'use strict';

    $.widget('bss.dcoControl', {
        options: {
            optionWrapper: '#product-options-wrapper',
            optionClass: '.product-custom-option'
        },
        hasDependValue: false,
        _create: function () {
            var $widget = this;
            if (window.dcoStatus === undefined) {
                window.dcoStatus = {};
            }
            if ($widget.options.dcoData.dependent_id && $widget.options.dcoData.dependent_id !== 0) {
                $widget.updateDependentId($widget);
                $widget.updateEventListener($widget);
                $widget.collectState($widget, null);
            }
        },
        updateDependentId: function ($widget) {
            var optionType = $widget.options.optionType,
                dcoData = $widget.options.dcoData.child,
                productId = $widget.element.closest('.bss-gpo-custom-option').data('product-id'),
                dependentId = $widget.options.dcoData.dependent_id;

            $widget.element.addClass('dco-uncommon')
                .attr('depend-id', $widget.createSeparatelyDependId(dependentId, productId));
            if (!window.dcoStatus.hasOwnProperty($widget.options.dcoData.dependent_id)) {
                $widget.element.addClass('dco-enabled');
            }
            if (optionType === 'drop_down' || optionType === 'multiple') {
                if ($widget.options.dcoRequire > 0) {
                    $widget.element.addClass('has-required').addClass('required');
                    $widget.element.find('select').addClass('required');
                }
                $widget.element.find($widget.options.optionClass).find('option').each(function () {
                    var value = $(this).attr('value'),
                        selected = $(this).prop('selected');
                    if (dcoData.hasOwnProperty(value)) {
                        dcoData[value].currentValue = false;
                        if (dcoData[value].depend_value) {
                            $widget.hasDependValue = true;
                        }
                        $(this).attr('depend-id', $widget.createSeparatelyDependId(dcoData[value].dependent_id, productId));
                        try {
                            if (dcoData[value].depend_value !== '0') {
                                $.each(dcoData[value].depend_value.split(','), function (dk, dvl) {
                                    if (window.dcoStatus[$widget.createSeparatelyDependId(dvl, productId)] === undefined) {
                                        window.dcoStatus[$widget.createSeparatelyDependId(dvl, productId)] = {};
                                    }
                                    window.dcoStatus[$widget.createSeparatelyDependId(dvl, productId)][value] = selected;
                                });
                            }
                        } catch (e) {
                            console.log('error when updateDependentId: ' + value);
                        }
                    }
                });
            } else if (optionType === 'radio' || optionType === 'checkbox') {
                if ($widget.options.dcoRequire > 0) {
                    $widget.element.addClass('has-required').addClass('required');
                    $widget.element.find('input').addClass('required');
                }
                $widget.element.find($widget.options.optionClass).each(function () {
                    var value = $(this).val(),
                        checked = $(this).prop('checked');
                    if (dcoData.hasOwnProperty(value)) {
                        dcoData[value].currentValue = $(this).prop('checked');
                        if (dcoData[value].depend_value) {
                            $widget.hasDependValue = true;
                        }
                        $(this).parents('div.choice').attr('depend-id', $widget.createSeparatelyDependId(dcoData[value].dependent_id, productId));
                        try {
                            if (dcoData[value].depend_value != '0') {
                                $.each(dcoData[value].depend_value.split(','), function (dk, dvl) {
                                    if (window.dcoStatus[$widget.createSeparatelyDependId(dvl, productId)] === undefined) {
                                        window.dcoStatus[$widget.createSeparatelyDependId(dvl, productId)] = {};
                                    }
                                    window.dcoStatus[$widget.createSeparatelyDependId(dvl, productId)][value] = checked;
                                });
                            }

                        } catch (e) {
                            console.log('error when updateDependentId: ' + value);
                        }
                    } else {
                        if ($widget.options.dcoRequire > 0 && !($(this).hasClass('bss-qty-box'))) {
                            $(this).parent().remove();
                        }
                    }
                });
            }
            $.each(window.dcoStatus, function (dk, dvl) {
                var value = $('.bss-gpo-custom-option[data-product-id="'+$widget.options.productId+'"]') || $('[depend-id="'+dk+'"]');
                if (!value.hasClass('field') || value.hasClass('admin__field-option')) {
                    value.addClass('dco_value');
                }
            });
        },
        // compatible with feature Is Default of CustomOption Template
        setIsDefault: function (currentElement, checkUnwrap) {
            var arrBodyClass = $('body').attr('class').split(' ');
            if (!$.isEmptyObject(window.optionsIsDefaultJson) && $.inArray('checkout-cart-configure', arrBodyClass) < 0 && currentElement) {
                var defaultData = window.optionsIsDefaultJson;
                $.each(window.optionsIsDefaultJson, function(optionId, optionSelectedData) {
                    if (defaultData[optionId].selected) {
                        if ($.inArray(defaultData[optionId].type, ['drop_down', 'multiple']) !== -1) {
                            var elementSelect = $('#select_'+optionId);
                            var selectedValues = elementSelect.val() ? elementSelect.val() : [];
                            var val = elementSelect.val();
                            var checkSameElement = true;
                            if (currentElement && currentElement.attr('id')) {
                                var currentElementOptionId = currentElement.attr('id').split('_');
                                if ($.isArray(currentElementOptionId) && $.inArray(optionId, currentElementOptionId) === -1) {
                                    checkSameElement = false;
                                }
                            }
                            if (currentElement.length > 0 && currentElement.find('input').attr('id')) {
                                var currentElementOptionId = currentElement.find('input').attr('id').split('_');
                                if ($.isArray(currentElementOptionId) && currentElementOptionId[1] && currentElementOptionId[1]!= optionId) {
                                    checkSameElement = false;
                                }
                            }
                            if (!checkSameElement) {
                                if ($.inArray(defaultData[optionId].type, ['multiple']) !== -1) {
                                    selectedValues =selectedValues.concat(_.values(defaultData[optionId].selected));
                                    elementSelect.val(selectedValues).change();
                                } else {
                                    if (val === "") {
                                        elementSelect.val(defaultData[optionId].selected[0]).change();
                                    }
                                }
                            }
                        } else {
                            $.each(defaultData[optionId].selected, function (index, value) {
                                var elementInput = $('[data-selector="options['+optionId+']['+value+']"]');
                                if (defaultData[optionId].type === 'radio') {
                                    elementInput = $('[data-selector="options['+optionId+']"]').filter(function(){return this.value==value});
                                }
                                if (elementInput.length > 0) {
                                    var nameElement = jQuery(elementInput).attr('name');
                                    nameElement = nameElement.replace("[]", "");
                                    var valElement = jQuery('[name="'+nameElement+'"]:checked').val();
                                    if (!valElement && elementInput.prop('checked') === false) {
                                        if (elementInput.closest('.Bss_image_radio').length > 0) {
                                            elementInput.closest('.Bss_image_radio').find('img').css('border', '0px');
                                        }
                                        // with radio and check box not unwrap
                                        if (elementInput.attr('disabled') === 'disabled') {
                                            if (elementInput.closest('.Bss_image_radio').length > 0) {
                                                elementInput.closest('.Bss_image_radio').trigger('click')
                                            } else {
                                                elementInput.prop('checked', true);
                                            }
                                        }
                                        //with checkbox radio and check box unwrap
                                        if (checkUnwrap) {
                                            if (elementInput.closest('.Bss_image_radio').length > 0) {
                                                elementInput.closest('.Bss_image_radio').trigger('click')
                                            } else {
                                                elementInput.prop('checked', true);
                                            }
                                        }
                                    }
                                }
                            });

                        }
                    }
                });
            }
        },
        updateEventListener: function ($widget) {
            var optionType = $widget.options.optionType;

            $widget.element.on('dco-reset', function () {
                $widget.resetValue($widget, $(this));
            });
            $widget.element.on('dco-enable', function (e, flag) {
                $widget.enableOption($widget, $(this), flag);
            });
            $widget.element.on('dco-statecommit', function (e, flag) {
                $widget.stateCommit($widget, $(this), flag);
            });
            $widget.element.parents('#product-options-wrapper').on('scan-depend', function () {
                $widget.collectState($widget, null);
            });
            if ($widget.hasDependValue) {
                if (optionType === 'drop_down' || optionType === 'multiple') {
                    $widget.element.find($widget.options.optionClass).change(function () {
                        $widget.onOptionChange($widget, $(this));
                        $widget.collectState($widget, $(this));
                    });
                } else if (optionType === 'radio' || optionType === 'checkbox') {
                    $widget.element.find($widget.options.optionClass).click(function () {
                        $widget.onOptionClick($widget, $(this));
                        $widget.collectState($widget, $(this));
                    });
                    $('body').on('change', '#bss_options_'+$widget.options.optionId+' .options-list .product-custom-option', function () {
                        $widget.onOptionClick($widget, $(this));
                        $widget.collectState($widget, $(this));
                    });
                }
            }
        },
        onOptionChange: function ($widget, element) {
            var values = element.find('option');
            values.each(function () {
                var value = $(this).val();
                if ($(this).prop('selected')) {
                    $.each(window.dcoStatus, function (sk, svl) {
                        if (svl.hasOwnProperty(value)) {
                            svl[value] = true;
                        }
                    });
                } else {
                    $.each(window.dcoStatus, function (sk, svl) {
                        if (svl.hasOwnProperty(value)) {
                            svl[value] = false;
                        }
                    });
                }
            });
        },
        onOptionClick: function ($widget, element) {
            var values = element.parents('.dco-uncommon').find('input');
            values.each(function () {
                var value = $(this).val();
                if ($(this).prop('checked')) {
                    $.each(window.dcoStatus, function (sk, svl) {
                        if (svl.hasOwnProperty(value)) {
                            svl[value] = true;
                        }
                    });
                } else {
                    $.each(window.dcoStatus, function (sk, svl) {
                        if (svl.hasOwnProperty(value)) {
                            svl[value] = false;
                        }
                    });
                }
            });
        },
        collectState: function ($widget, currentElement) {
            var multipleParentValue = $widget.options.multipleParentValue,
                flag;
            $.each(window.dcoStatus, function (sk, svl) {
                var parent = $('[depend-id="' + sk + '"]').parents('.dco-uncommon'),
                    parentId = parent.attr('depend-id');
                if (multipleParentValue === 'atleast_one') {
                    flag = parent.hasClass('dco-enabled') && parent.length > 0 && window.dcoStatus.hasOwnProperty(sk) && window.dcoStatus.hasOwnProperty(parentId);
                } else if (multipleParentValue === 'all') {
                    flag = parent.length == 0 || parent.hasClass('dco-enabled') || !window.dcoStatus.hasOwnProperty(sk) || !window.dcoStatus.hasOwnProperty(parentId);
                }
                $.each(svl, function (sk2, svl2) {
                    if (multipleParentValue === 'atleast_one') {
                        flag = flag || svl2;
                    } else if (multipleParentValue === 'all') {
                        flag = flag && svl2;
                    }
                });
                $widget.triggerDepend(sk, flag, currentElement);
            });

            $widget.triggerOption();
        },
        triggerOption: function () {
            $('.dco-uncommon').each(function () {
                if ($(this).find('[depend-id]:not(.dco-hide)').length == 0) {
                    $(this).trigger('dco-statecommit', false);
                } else {
                    $(this).trigger('dco-statecommit', true);
                }
            });
        },
        triggerDepend: function ($dependId, flag, currentElement) {
            var childrenDisplay = this.options.childrenDisplay,
                element = $('[depend-id="' + $dependId + '"]'),
                $widget = this;
            if (childrenDisplay === 'hide') {
                if (flag) {
                    if (element.hasClass('dco-option') || element.hasClass('dco-uncommon')) {
                        element.trigger('dco-enable', flag);
                        // compatible with feature Is Default of CustomOption Template
                        this.setIsDefault(currentElement, false);
                        return;
                    } else {
                        element.fadeIn();
                        element.removeClass('dco-hide');
                        $('#image_preview_' + element.val()).show();
                        // fix for safari
                        if( (element.parent().is('span')) ) {
                            element.unwrap();
                            // compatible with feature Is Default of CustomOption Template
                            this.setIsDefault(currentElement, true);
                        }
                    }
                } else {
                    if (element.hasClass('dco-option') || element.hasClass('dco-uncommon')) {
                        element.trigger('dco-enable', flag);
                        // element.trigger('dco-reset');
                        return;
                    } else if (element.prop('tagName') === 'OPTION') {
                        if (element.prop("selected")) {
                            element.prop("selected", false);
                            element.parents('select').trigger('change');
                        }
                    } else {
                        if (element.find('input').attr('type') === 'radio') {
                            var option = element.find('input').attr('id').split('_')[1],
                                changes = {},
                                currentState = element.find('input').prop("checked");
                            changes['options[' + option + ']'] = {
                                finalPrice: {amount: 0},
                                basePrice: {amount: 0}
                            };
                            if (currentState) {
                                element.find('input').prop("checked", false);
                                $('[data-role="priceBox"]').trigger('updatePrice', changes);
                                $widget.onOptionClick($widget, element.find('input'));
                                $widget.collectState($widget, null);
                            }
                        } else {
                            if (element.find('input').prop("checked")) {
                                if (element.find('.Bss_image_radio').length > 0) {
                                    element.find('.Bss_image_radio').trigger('click');
                                } else {
                                    element.find('input').trigger('click');
                                }

                            }
                        }
                    }
                    $('#image_preview_' + element.val()).hide();
                    element.fadeOut();
                    element.addClass('dco-hide');
                    // fix for safari
                    if( !(element.parent().is('span')) ) element.wrap('<span>');
                }
            } else if (childrenDisplay === 'display') {
                if (element.hasClass('dco-option') || element.hasClass('dco-uncommon')) {
                    element.trigger('dco-enable', flag);
                    if (flag) {
                        // compatible with feature Is Default of CustomOption Template
                        $widget.setIsDefault(currentElement, false);
                    }
                    if (!flag) {
                        element.trigger('dco-reset');
                    }
                } else if (element.prop('tagName') === 'OPTION') {
                    element.prop('disabled', !flag);
                    if (!flag && element.prop("selected")) {
                        element.prop("selected", false);
                        element.parents('select').trigger('change');
                    }
                    this.setIsDefault(currentElement, true);
                } else {
                    element.find('input').prop('disabled', !flag);
                    if (element.find('input').attr('type') === 'radio') {
                        var option = element.find('input').attr('id').split('_')[1],
                            changes = {},
                            currentState = element.find('input').prop("checked");
                        changes['options[' + option + ']'] = {
                            finalPrice: {amount: 0},
                            basePrice: {amount: 0}
                        };
                        if (!flag && currentState) {
                            element.find('input').prop("checked", false);
                            $('[data-role="priceBox"]').trigger('updatePrice', changes);
                            $widget.onOptionClick($widget, element.find('input'));
                            $widget.collectState($widget, null);
                        }
                    } else {
                        if (!flag && element.find('input').prop("checked")) {
                            element.find('input').prop("checked", false).trigger('change');
                            $widget.onOptionClick($widget, element.find('input'));
                            $widget.collectState($widget, null);
                        }
                    }
                }
            }
        },

        resetValue: function ($widget, $this) {
            var changes = {};
            changes['options[' + $widget.options.optionId + ']'] = {
                finalPrice: {amount: 0},
                basePrice: {amount: 0}
            };
            switch ($widget.options.optionType) {
                case 'radio':
                    if ($this.find('input:checked').length > 0) {
                        $this.find('input:checked').each(function () {
                            $(this).prop('checked', false);
                            $widget.onOptionClick($widget, $(this));
                            $widget.collectState($widget, null);
                        });
                        $('[data-role="priceBox"]').trigger('updatePrice', changes);
                    }
                    break;
                case 'checkbox':
                    if ($this.find('input:checked').length > 0) {
                        $this.find('input:checked').each(function () {
                            $(this).prop('checked', false).trigger('change');
                            $widget.onOptionClick($widget, $(this));
                            $widget.collectState($widget, null);
                        });
                    }
                    break;
                case 'drop_down':
                    if (($this.find('.product-custom-option').val() !== '') ) {
                        $this.find('option').prop('selected', false);
                        $this.find('select').trigger('change');
                    }
                    break;
                case 'multiple':
                    if ($this.find('option:selected').length > 0) {
                        $this.find('option').prop('selected', false);
                        $this.find('select').trigger('change');
                    }
                    break;
            }
        },
        enableOption: function ($widget, $this, flag) {
            var dcoRequire = $widget.options.dcoRequire;
            if (!flag) {
                $this.removeClass('dco-enabled');
                if ($widget.options.childrenDisplay === 'hide') {
                    if ($this.is(':visible')) {
                        $this.fadeOut();
                        $this.find('[depend-id]:not(.dco_value)').fadeOut();
                    }
                }
            } else {
                $this.addClass('dco-enabled');
                if ($widget.options.childrenDisplay === 'hide') {
                    if ($this.is(':hidden')) {
                        $this.fadeIn();
                        $this.find('[depend-id]:not(.dco_value)').fadeIn();
                    }

                }
            }
            switch ($widget.options.optionType) {
                case 'radio':
                case 'checkbox':
                    if (dcoRequire > 0) {
                        if (flag && !$this.find('input').hasClass('required')) {
                            $this.find('input').addClass('required');
                        } else if (!flag && $this.find('input').hasClass('required')) {
                            $this.find('input').removeClass('required');
                        }
                    }
                    break;
                case 'drop_down':
                case 'multiple':
                    if (dcoRequire > 0) {
                        if (flag && !$this.find('select').hasClass('required')) {
                            $this.find('select').addClass('required');
                        } else if (!flag && $this.find('select').hasClass('required')) {
                            $this.find('select').removeClass('required');
                        }
                    }
                    break;
            }
        },
        stateCommit: function ($widget, $this, flag) {
            var dcoRequire = $widget.options.dcoRequire;
            if ($this.find('.dco_value').length !== $this.find('[depend-id]').length) {
                flag = $this.hasClass('dco-enabled');
            }
            if ($widget.options.childrenDisplay === 'hide') {
                if (flag || $this.find('.dco_value:not(.dco-hide)').length > 0) {
                    $this.fadeIn();
                } else {
                    $this.fadeOut();
                }
            }
            switch ($widget.options.optionType) {
                case 'radio':
                case 'checkbox':
                    $this.find('[depend-id]:not(.dco_value)').find('input').prop('disabled', !flag);
                    if (dcoRequire > 0) {
                        if (flag && !$this.find('input').hasClass('required')) {
                            $this.find('input').addClass('required');
                        } else if (!flag && $this.find('input').hasClass('required')) {
                            $this.find('input').removeClass('required');
                        }
                    }
                    break;
                case 'drop_down':
                case 'multiple':
                    $this.find('[depend-id]:not(.dco_value)').prop('disabled', !flag);
                    if (dcoRequire > 0) {
                        if (flag && !$this.find('select').hasClass('required')) {
                            $this.find('select').addClass('required');
                        } else if (!flag && $this.find('select').hasClass('required')) {
                            $this.find('select').removeClass('required');
                        }
                    }
                    break;
            }
        },

        /**
         * Create separately dependent id for product
         *
         * @param dependId
         * @param productId
         * @returns {string}
         */
        createSeparatelyDependId: function(dependId, productId) {
            if (productId) {
                dependId = dependId + '_' + productId;
            }
            return dependId;
        }
    });
    return $.bss.dcoControl;
});
