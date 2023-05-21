define([
    'jquery',
    'uiComponent',
    'ko',
    'underscore',
    'Magento_Checkout/js/checkout-data',
    'uiRegistry',
    'mage/translate',
    'mage/validation',
    'Hoofdfabriek_PostcodeNL/js/view/form/postcode',
    'mage/menu'
], function ($, Component, ko, _, checkoutData, registry, $t) {
    'use strict';

//resolve issue with autocomplete on checkout if there is a top menu
    $.widget("postcode.menu", $.mage.menu, {
        _create: function() {
            $(this.element).data('ui-menu', this);
            this._super();
        }
    });

    ko.bindingHandlers.autoCompleteArea = {
        init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
            $(element).autocomplete({
                autoFocus: true,
                messages: {
                    noResults: '',
                    results: function() {}
                },
                select: function (event, ui) {
                    viewModel.selectPostalArea(ui);
                    viewModel.validateSingle(ui, event);
                },
                change: function (event, ui) {
                   viewModel.changePostalArea(event, ui);
                   viewModel.validateSingle(element, event);
                },
                source: function (request, responseCallback) {
                    viewModel.completePostalArea(request, responseCallback, this);
                }
            }).focus(function () {
                if (viewModel.beAddress.municipalityNisCode() === null && $(element).val() !== '') {
                    $(element).autocomplete('search');
                }
            });
        }
    };

    ko.bindingHandlers.autoCompleteStreet = {
        init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
            $(element).autocomplete({
                autoFocus: true,
                messages: {
                    noResults: '',
                    results: function() {}
                },
                select: function (event, ui) {
                    viewModel.selectStreetName(ui);
                    viewModel.validateSingle(element, event);
                },
                change: function (event, ui) {
                    viewModel.changeStreetName(event, ui);
                    viewModel.validateSingle(element, event);
                },
                source: function (request, responseCallback) {
                    viewModel.completeStreetName(request, responseCallback, this);
                }
            }).focus(function () {
                if (viewModel.beAddress.streetId() === null && $(element).val() !== '') {
                    $(element).autocomplete('search');
                }
            }).data('ui-autocomplete')._renderItem = function( ul, item ) {
                var div = $('<div>', {'text': item.streetName, 'class': 'ui-menu-item-wrapper complete-street'}).append('<a>');

                if (viewModel.beAddress.postcode() === null) {
                    div.append($('<span>', {'text': item.postcode}));
                }
                return $('<li class="ui-menu-item">').append(div).appendTo(ul);
            };
        }
    };

    ko.bindingHandlers.autoCompleteHouse = {
        init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
            $(element).autocomplete({
                autoFocus: true,
                messages: {
                    noResults: '',
                    results: function() {}
                },
                select: function (event, ui) {
                    viewModel.selectHouseNumber(ui);
                    viewModel.validateSingle(element, event);
                },
                change: function (event, ui) {
                    viewModel.validateSingle(element, event);
                },
                source: function (request, responseCallback) {
                    viewModel.completeHouseNumber(request, responseCallback, this);
                }
            }).data('ui-autocomplete')._renderItem = function( ul, item ) {
                var div = $('<div>', {'text': item.houseNumber, 'class': 'ui-menu-item-wrapper complete-house-number'}).append('<a>');
                if (item.status === 'incomplete') {
                    div.append($('<span>', {'text': $t('select bus number…'), 'class' : 'remark'}));
                } else if (item.status === 'unknown') {
                    div.append($('<span>', {'text': $t('(unknown house number)'), 'class' : 'remark'}));
                }

                return $('<li class="ui-menu-item">').append(div).appendTo(ul);
            };
        }
    };

    return Component.extend({
        defaults: {
            template: 'Hoofdfabriek_BePostcodeNL/form/autocomplete',
            addressType: 'shipping',
            houseNumber: ko.observable(''),
            municipalityName: ko.observable(''),
            streetName: ko.observable(''),
            init: false,
            beAddress: {
                'postcode': ko.observable(null),
                'municipalityNisCode': ko.observable(null),
                'municipalityName': ko.observable(null),
                'streetName': ko.observable(null),
                'houseNumber': ko.observable(null),
                'streetId': ko.observable(null)
            },
            showManual: ko.observable(''),
            cityManual: ko.observable(''),
            postcodeManual: null,
            isManual: ko.observable(false),
            settings: {},
            isVisible: ko.observable(false),
            imports: {
                observeCountry: '${ $.parentName }.country_id:value'
            },
            listens: {
                cityManual: 'observeManualCity',
                postcodeManual: 'observeManualPostcode',
                houseNumber: 'observeHouseNumber',
                '${ $.provider }:${ $.customScope ? $.customScope + "." : ""}data.validate': 'validate'
            }
        },

        /**
         * Initializes observable properties of instance
         *
         * @returns {Object} Chainable.
         */
        initialize: function () {
            this._super();
            var self = this;
            registry.async(this.provider)(function () {
                self.initModules();
            });

            if (!!window.checkoutConfig) {
                this.settings = window.checkoutConfig.be_autocomplete.settings;
            }

            this.showManual = ko.computed(function() {
                if (this.isManual()) {
                    return true;
                }
                return (!!this.beAddress.streetName() && this.beAddress.municipalityName() === null ||
                    this.beAddress.postcode() === null) && !!this.streetName() && !!this.municipalityName();
            }, this);


            return this;
        },
        /** @inheritdoc */
        initConfig: function () {
            this._super();
            this.isVisible(this.resolveFieldsetVisibility());

            return this;
        },
        initObservable: function () {
            this._super().observe('houseNumber cityManual postcodeManual');
            return this;
        },
        resolveFieldsetVisibility: function () {
            var address = this.getAddressData();
            if (!!address && address.country_id == 'BE') {
                return true;
            }

            return false;
        },
        getAddressData: function () {
            if (this.addressType === 'shipping' && typeof checkoutData.getShippingAddressFromData() !== 'undefined' && checkoutData.getShippingAddressFromData()) {
                return checkoutData.getShippingAddressFromData();
            } else if (this.addressType === 'billing' && typeof checkoutData.getBillingAddressFromData() !== 'undefined' && checkoutData.getBillingAddressFromData()) {
                return checkoutData.getBillingAddressFromData();
            } else if (this.source) {
                return this.source.get(this.customScope);
            }
        },
        getAutocompleteOptions: function (requestData, responseCallback, sourceScope) {
            var self = this;
            $.getJSON(
                this.settings.url,
                requestData,
                function (items) {
                    if (typeof items.message !== 'undefined') {
                        return false;//console.error(items.exception);
                    }
                    _.isFunction(self[responseCallback]) ? self[responseCallback](items, requestData) : responseCallback(items);
                }
            ).fail(function (jqXHR) {
                    if (jqXHR.status !== 200) {
                        // Server might have (capacity) issues, stop sending requests to be safe.
                        sourceScope.close();
                        sourceScope.disable();
                    }
                }
            );

        },
        completePostalArea: function (request, responseCallback, sourceScope) {
            var requestData = {
                t: request.term,
                type: 'postal-area'
            };
            this.getAutocompleteOptions(requestData, responseCallback, sourceScope);
        },
        completeStreetName: function(request, responseCallback, sourceScope) {
            if (this.beAddress.municipalityNisCode() === null) {
                return; // Can't continue without municipalityNisCode value.
            }

            var requestData = {
                municipalityNisCode: this.beAddress.municipalityNisCode(),
                postcode: this.beAddress.postcode() || '',
                street: request.term,
                type: 'street'
            };
            this.getAutocompleteOptions(requestData, responseCallback, sourceScope);
        },
        completeHouseNumber: function (request, responseCallback, sourceScope) {
            if (this.beAddress.streetId() === null || this.beAddress.postcode() === null) {
                return;
            }
            var requestData = {
                streetId: this.beAddress.streetId(),
                postcode: this.beAddress.postcode() || '',
                houseNumber: request.term,
                type: 'house'
            };
            this.getAutocompleteOptions(requestData, responseCallback, sourceScope);
        },
        selectPostalArea: function (uiElement) {
            this.isManual(false);
            // Clear streetName if municipalityNisCode or postcode has changed.
            if (this.beAddress.municipalityNisCode() !== uiElement.item.municipalityNisCode
                || (uiElement.item.postcode !== undefined && this.beAddress.postcode() !== uiElement.item.postcode)
            ) {
                this.beAddress.streetId(null);
            }

            if (typeof(uiElement.item.postcode) !== 'undefined') {
                this.beAddress.postcode(uiElement.item.postcode);
            } else {
                this.beAddress.postcode(null);
            }
            this.beAddress.municipalityName(uiElement.item.municipalityName);
            this.beAddress.municipalityNisCode(uiElement.item.municipalityNisCode);
            this.updateAddress();

        },
        selectStreetName: function (uiElement) {
            this.isManual(false);
            if (this.beAddress.postcode() === null) {
                this.municipalityName(uiElement.item.postcode + ' ' + uiElement.item.municipalityName);
            }
            this.beAddress.postcode(uiElement.item.postcode);
            this.beAddress.municipalityName(uiElement.item.municipalityName);
            this.beAddress.streetName(uiElement.item.streetName);
            this.beAddress.streetId(uiElement.item.streetId);
            this.updateAddress();
        },
        selectHouseNumber: function (uiElement) {
            this.isManual(false);
            // Postcode output from house number selection must be used as the definitive postcode
            this.beAddress.houseNumber(uiElement.item.houseNumber);
            this.beAddress.postcode(uiElement.item.postcode);

            if (uiElement.item.status === 'incomplete') {
                // Trigger a new search to complete missing house number parts.
                window.setTimeout(function () {
                    $('#' + this.addressType + '-be-house').autocomplete('search', uiElement.item.houseNumber); }, 200);
            }
            this.updateAddress();
        },
        changePostalArea: function (event, uiElement) {
            // Postal area has changed, but isn't one of the autocomplete items.
            if (uiElement.item === null) {
                //this.beAddress.streetName(null);
                //this.beAddress.houseNumber(null);
                this.beAddress.streetId(null);
                this.beAddress.postcode(null);
                this.beAddress.municipalityNisCode(null);
                this.beAddress.municipalityName(null);
                //try to resend if address is entered too fast
                var requestData = {
                    t: $(event.target).val(),
                    type: 'postal-area'
                };
                this.getAutocompleteOptions(requestData, 'resabmitPostalArea', event.target);
            }
        },
        resabmitPostalArea: function (items, request) {
            var index;
            for (index = 0; index < items.length; ++index) {
                if (items[index].municipalityName == request.t || items[index].postcode == request.t) {
                    if (typeof(items[index].postcode) !== 'undefined') {
                        this.beAddress.postcode(items[index].postcode);
                    }
                    this.beAddress.municipalityName(items[index].municipalityName);
                    this.beAddress.municipalityNisCode(items[index].municipalityNisCode);
                    this.updateAddress();
                }
            }
        },
        changeStreetName: function (event, uiElement) {
            // Street name has changed, but isn't one of the autocomplete items.
            if (uiElement.item === null) {
                this.beAddress.streetName($(event.target).val());
                this.beAddress.postcode(null);
                this.beAddress.streetId(null);
            }
        },
        updateAddress: function () {
            var city = this.beAddress.municipalityName() !== null ? this.beAddress.municipalityName() : '',
                postcode = this.beAddress.postcode() !== null ? this.beAddress.postcode() : '',
                street_0 = this.beAddress.streetName() != null ? this.beAddress.streetName() : '';

            registry.get(this.parentName + '.city').set('value', city);
            registry.get(this.parentName + '.postcode').set('value', postcode);
            registry.get(this.parentName + '.street.0').set('value', street_0);
            if (registry.get(this.parentName + '.region_id_input')) {
                registry.get(this.parentName + '.region_id_input').set('value', '');
            }

            var houseNumber = this.beAddress.houseNumber() !== null ? this.beAddress.houseNumber() : '';

            if (this.settings.useStreet2AsHouseNumber) {
                registry.get(this.parentName + '.street.1').set('value', houseNumber.trim());
            } else {
                registry.get(this.parentName + '.street.0').set('value', this.beAddress.streetName() + ' ' +
                    houseNumber.trim());
            }
        },
        observeCountry: function (value) {
            if (value) {
                var address = this.getAddressData();

                if (address && address.country_id === 'BE') {
                    this.toggleFields(['street', 'street.0', 'street.1', 'street.2', 'city', 'postcode'], false);
                    this.isVisible(true);

                    if (!this.init) {
                        if (address && typeof address.be_postcodenl !== 'undefined') {
                            this.beAddress = address.be_postcodenl;
                            this.houseNumber(address.be_postcodenl.houseNumber ? address.be_postcodenl.houseNumber : '');
                            this.municipalityName(address.be_postcodenl.municipalityName ? address.be_postcodenl.municipalityName : '');
                            this.streetName(address.be_postcodenl.streetName ? address.be_postcodenl.streetName : '');
                            this.init = true;
                        }
                    }
                    this.updateAddress();
                } else {
                    if (address.country_id !== 'NL') {
                        this.toggleFields(['street', 'street.0', 'street.1', 'street.2'], true); //city and postcode is toggled by postcodenl extension
                    }
                    this.isVisible(false);
                }
            }
        },
        toggleFields: function (fields, isOn) {
            var self = this;
            $.each(fields, function (key, fieldName) {
                registry.async(self.parentName + '.' + fieldName)(function () {
                    var element = registry.get(self.parentName + '.' + fieldName);
                    if (element) {
                        if (isOn) {
                            element.set('visible', true).set('labelVisible', true);
                        } else {
                            element.set('visible', false).set('labelVisible', false);
                        }
                    }
                });
            });

        },
        validate: function () {
            var beAutocompleteFormSelector = '#' + this.addressType + '-be-postcodenl';

            if (!$(beAutocompleteFormSelector).validation() || !$(beAutocompleteFormSelector).validation('isValid')) {
                this.source.set('params.invalid', true);
                return false;
            }
            return true;
        },
        validateSingle: function (element, event) {
            var beAutocompleteForm = $('#' + this.addressType + '-be-postcodenl'), validator;
            beAutocompleteForm.validation();
            validator = beAutocompleteForm.validate();
            var field = $(event.target);
            validator.element('#' + field.attr('id'));
        },
        observeManualCity: function (value, event) {
            var newValue = !!value ? value : null;
                this.isManual(true);
                this.beAddress.municipalityName(newValue);
                this.updateAddress();

        },
        observeManualPostcode: function (value) {
            this.isManual(true);
            this.beAddress.postcode(value);
            this.updateAddress();
        },
        observeHouseNumber: function (value) {
            this.beAddress.houseNumber(value);
            this.updateAddress();
        }
    });
});
