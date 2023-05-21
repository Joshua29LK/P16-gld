define([
    'jquery',
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/postcode-validator',
    'Magento_Checkout/js/checkout-data',
    'uiRegistry',
    'mage/translate',
    'mage/validation'
], function ($, Component, ko, postcodeValidator, checkoutData, registry, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Hoofdfabriek_PostcodeNL/form/postcodenl',
            addressRegex: /^([0-9]+)(?:[^0-9a-zA-Z]+([0-9a-zA-Z ]+)|([a-zA-Z](?:[0-9a-zA-Z ]*)))?$/,
            isLoading: false,
            addressType: 'shipping',
            postcodeNL: null,
            houseNumber: null,
            houseAddition: null,
            lastRequestResult: null,
            validationMessage: null,
            isVisible: false,
            showAdditions: ko.observable(false),
            showAddressDetails: ko.observable(false),
            streetManual: null,
            cityManual: null,
            resultBlock: null,
            imports: {
                observeCountry: '${ $.parentName }.country_id:value',
            },
            listens: {
                postcodeNL: 'observePostcodeField',
                houseNumber: 'observeHousenumberField',
                houseAddition: 'observeAddition',
                cityManual: 'observeManualCity',
                streetManual: 'observeStreetManual',
                '${ $.provider }:${ $.customScope ? $.customScope + "." : ""}data.validate': 'validate',
            }
        },

        checkDelay: 1000,
        postcodeCheckTimeout: 0,
        currentRequest: null,
        sendRequest: false,
        isManual: ko.observable(false),

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

            this.houseAdditionalOptions = ko.observableArray([]);
            this.canEnterManually = ko.computed(function() {
                return this.showAdditions() || this.showAddressDetails() && !this.isManual();
            },this);

            this.resultBlock = '#' + this.addressType + '-nl-result>.postcode-valid-address';


            $.validator.addMethod(
                'validate-house-number-nl', function (value) {
                    return !self.showAdditions();
                }, $.mage.__('Select a proper house number or enter address manually'));

            return this;
        },

        initObservable: function () {
            this._super().observe('isLoading cityManual streetManual postcodeNL validate houseNumber houseAddition isVisible validationMessage');

            return this;
        },

        /** @inheritdoc */
        initConfig: function () {
            this._super();

            this.isVisible = this.resolveFieldsetVisibility();

            return this;
        },
        resolveFieldsetVisibility: function () {
            var address = this.getAddressData();
            if (!!address && address.country_id == 'NL') {
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

        observeCountry: function (value) {
            if (value) {
                var address = this.getAddressData();
                if (address && address.country_id === 'NL') {
                    this.toggleFields(['street', 'street.0', 'street.1', 'street.2', 'city', 'postcode', 'region_id_input'], false);
                    this.isVisible(true);
                    this.updatePostcodeNL();
                } else {
                    this.toggleFields(['street', 'street.0', 'street.1', 'street.2', 'city', 'postcode', 'region_id_input'], true);
                    this.isVisible(false);
                    this.postcodeValidate();
                }
            }
        },
        observePostcodeField: function (value) {
            if (value) {
                this.updatePostcodeNL();
            }
        },
        observeHousenumberField: function (value) {
            if (value && !this.sendRequest) {
                this.updatePostcodeNL();
            }

            var addition = this.getAdditionFromHouseNumber();
            if (addition != this.houseAddition()) {
                this.houseAddition(addition);
                //this.showAddressDetails(false);
            }
            if (this.isManual) {
                var parentPartentName = this.parentName;

                if (this.getSettings().useStreet3AsHouseNumberAddition && registry.get(parentPartentName + '.street.2')) {
                    registry.get(parentPartentName + '.street.2').set('value', addition);
                    registry.get(parentPartentName + '.street.1').set('value', this.getNumberFromHouse());
                } else if (this.getSettings().useStreet2AsHouseNumber && registry.get(parentPartentName + '.street.1')) {
                    registry.get(parentPartentName + '.street.1').set('value', value);
                } else if (registry.get(parentPartentName + '.street.0')) {
                    registry.get(parentPartentName + '.street.0').set('value', this.streetManual + ' ' + value);
                }
            }

        },
        observeAddition: function (newValue) {
            if (newValue == undefined || !this.lastRequestResult) {
                return;
            }
             var sendRequest = this.sendRequest;
             this.sendRequest = true;

            var parentPartentName = this.parentName;

            if (this.getSettings().useStreet3AsHouseNumberAddition && registry.get(parentPartentName + '.street.2')) {
                registry.get(parentPartentName + '.street.2').set('value', newValue);
            } else if (this.getSettings().useStreet2AsHouseNumber && registry.get(parentPartentName + '.street.1') && this.lastRequestResult) {
                registry.get(parentPartentName + '.street.1').set('value', this.lastRequestResult.houseNumber.toString() + newValue);
            } else if (registry.get(parentPartentName + '.street.0') && this.lastRequestResult) {
                registry.get(parentPartentName + '.street.0').set('value', this.lastRequestResult.street + ' ' + this.lastRequestResult.houseNumber.toString() + ' ' + newValue);
            }

            this.houseNumber(this.lastRequestResult.houseNumber.toString() + ' ' + newValue);
            this.showAdditions(false);
            this.updateResultBlock(newValue);
            this.showAddressDetails(true);
            this.validate();
            this.sendRequest = sendRequest;
        },
        observeManualCity: function (value) {
            registry.get(this.parentName + '.city').set('value', value).set('error', false);
        },
        observeStreetManual: function (value) {
            if (!this.getSettings().useStreet2AsHouseNumber) {
                value = value + ' ' + this.houseNumber();
            }

            if (!this.getSettings().useStreet3AsHouseNumberAddition) {
                value = value + ' ' + this.houseAddition();
            }

            registry.get(this.parentName + '.street.0').set('value', value).set('error', false);
        },
        updateResultBlock: function (houseAddition) {
            var resultBlock = $(this.resultBlock);
            if (resultBlock.length) {
                resultBlock.empty();
                if (this.lastRequestResult) {
                    resultBlock.append(this.lastRequestResult.street);
                    resultBlock.append(' ', this.lastRequestResult.houseNumber.toString());
                    resultBlock.append(' ', houseAddition);
                    resultBlock.append($('<br />'), this.lastRequestResult.postcode, ' ', this.lastRequestResult.city);
                }
            }
        },
        validate: function () {
            var postcodeForm = $('#' + this.addressType + '-postcodenl');
            postcodeForm.mage('validation', {
                ignore: ':hidden'
            });
            if (!postcodeForm.validation() || !postcodeForm.validation('isValid')) {
                this.source.set('params.invalid', true);
                return false;
            }
            return true;
        },
        postcodeValidate: function () {
            var self = this;
            var address = this.getAddressData();
            $.each(['.street.0', '.street.1', '.street.2', '.city', '.region_id_input'], function (key, fieldName) {
                var element = registry.get(self.parentName + fieldName);
                if (!!element) {
                    element.set('value', '').set('error', false);
                }
            });

            var validationResult = postcodeValidator.validate(address.postcode, address.country_id);

            if (!validationResult) {
                self.validationMessage(self.getValidationMessage());
                self.isManual(true);
                return false;
            }
            registry.get(this.parentName + '.postcode').set('warn', '');
            return true;
        },
        updatePostcodeNL: function () {
            var address = this.getAddressData();
            this.validationMessage(false);
            this.showAddressDetails(false);
            this.hideManualFields();
            if (!!address) {
                    var self = this;
                    clearTimeout(this.postcodeCheckTimeout);

                    this.postcodeCheckTimeout = setTimeout(function () {
                        if (address.country_id == 'NL') {
                            var validationResult = postcodeValidator.validate(self.postcodeNL(), address.country_id);
                            if (validationResult && self.houseNumber()) {
                                self.getPostcodeInformation();
                                self.validate();//if we want to show error on empty street + city
                            } else if (!validationResult && self.postcodeNL()){
                                self.validationMessage(self.getValidationMessage());
                            }
                        }
                    }, self.checkDelay);
                }
        },

        getValidationMessage: function () {
            var warnMessage = $t('Provided Zip/Postal Code seems to be invalid.');

            if (postcodeValidator.validatedPostCodeExample.length) {
                warnMessage += $t(' Example: ') + postcodeValidator.validatedPostCodeExample.join('; ') + '. ';
            }
            warnMessage += $t('If you think it is correct you can ignore this notice.');

            return warnMessage;
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
        getSettings: function () {
            var settings = window.checkoutConfig.hoofdfabriek_postcode.settings;
            return settings;
        },
        clearSteerts: function () {
            var self = this;
            $.each(['.street.0', '.street.1', '.street.2'], function (key, fieldName) {

                var element = registry.get(self.parentName + fieldName);
                if (element) {
                    element.value('');
                }
            });
        },
        getAdditionFromHouseNumber: function() {
            var addition = '';
            if (this.houseNumber()) {
                var match = (this.addressRegex).exec(this.houseNumber());
                if (match && match[3]) {
                    addition = match[3];
                } else if (match && match[2]){
                    addition = match[2];
                }
            }
            return addition;
        },
        getNumberFromHouse: function() {
            var number = '';
            if (this.houseNumber()) {
                var match = (this.addressRegex).exec(this.houseNumber());
                if (match && match[1]) {
                    number = match[1];
                }
            }
            return number;
        },
        getPostcodeInformation: function () {
            if (!this.source || this.sendRequest) {
                return;
            }
            var self = this;
            var response = false;
            var formData = this.source.get(this.customScope);
            this.validateRequest();
            this.isLoading(true);
            this.showAdditions(false);
            this.hideManualFields();
            this.currentRequest = $.ajax({
                method: "POST",
                async: false,
                url: self.getSettings().url,
                data: { postcode: self.postcodeNL(), houseNumber: self.houseNumber(), houseAddition: ''},
                success: function (response) {
                    self.validationMessage(null);
                    self.houseAdditionalOptions.removeAll();
                    if (response.street) {
                       self.clearSteerts();
                        self.lastRequestResult = response;

                        registry.get(self.parentName + '.postcode').set('warn', '');

                        if (self.getSettings().useStreet2AsHouseNumber) {
                            registry.get(self.parentName + '.street.0').set('value', response.street).set('error', false);
                            registry.get(self.parentName + '.street.1').set('value', response.houseNumber.toString()).set('error', false);
                        } else {
                            registry.get(self.parentName + '.street.0').set('value', response.street + ' ' + response.houseNumber).set('error', false);
                        }
                        self.setHouseNumberAdditions(response.houseNumberAdditions);

                        var addressAddition = self.getAdditionFromHouseNumber();
                        if (response.houseNumberAddition) {
                            var responseAddition = response.houseNumberAddition;
                        } else {
                            var responseAddition = '';
                        }

                        if (addressAddition.toUpperCase() != responseAddition.toUpperCase() || (!response.houseNumberAddition && response.houseNumberAdditions.indexOf('') === -1)) {
                            self.showAdditions(true);
                        } else {
                            if (self.getSettings().useStreet3AsHouseNumberAddition) {
                                registry.get(self.parentName + '.street.2').set('value', addressAddition.toUpperCase());
                            } else if (self.getSettings().useStreet2AsHouseNumber) {
                                registry.get(self.parentName + '.street.1').set('value', registry.get(self.parentName + '.street.1').value() + ' ' + responseAddition.toUpperCase());
                            } else if (registry.get(self.parentName + '.street.0')) {
                                registry.get(self.parentName + '.street.0').set('value', registry.get(self.parentName + '.street.0').value() + ' ' + responseAddition.toUpperCase());
                            }
                        }

                        //registry.get(self.parentName + '.country_id').set('value', 'NL').set('error', false);
                        registry.get(self.parentName + '.city').set('value', response.city).set('error', false);
                        registry.get(self.parentName + '.postcode').set('value', response.postcode).set('error', false);


                        self.updateResultBlock(responseAddition);
                        self.streetManual(response.street);
                        self.cityManual(response.city);
                        self.showAddressDetails(true);
                        self.toggleFields(['street', 'street.0', 'street.1', 'street.2', 'city', 'postcode', 'region_id_input'], false);
                    } else {
                        self.lastRequestResult = null;
                        self.updateResultBlock('');
                        if (response.message) {
                            var warnMessage = response.message + ' ';
                        } else {
                            var warnMessage = $t('We cannot find a validated adres of the combination from your zipcode and housenumber.');
                        }

                        warnMessage += $t('Are the details correct? Then you can fill in the streetname and city manually below');
                        self.validationMessage(warnMessage);

                        if (self.getSettings().useStreet2AsHouseNumber) {
                            registry.get(self.parentName + '.street.0').set('value', '');
                            registry.get(self.parentName + '.street.1').set('value', '');
                        } else {
                            registry.get(self.parentName + '.street.0').set('value', '');
                        }

                        registry.get(self.parentName + '.city').set('value', '');
                        if (registry.get(self.parentName + '.region_id_input')) {
                            registry.get(self.parentName + '.region_id_input').set('value', '');
                        }
                        registry.get(self.parentName + '.postcode').set('value', self.postcodeNL());
                        self.showManualFields();
                    }

                    self.isLoading(false);
                },

            });

        },
        setHouseNumberAdditions: function (additions) {
            var self = this;
            var options = [];
            $.each(additions, function (key, addition) {
                    var additionStripped = addition.replace(" ", "");
                    self.houseAdditionalOptions.push(additionStripped);
            });
        },
        /**
         * If request has been sent -> abort it.
         * ReadyStates for request aborting:
         * 1 - The request has been set up
         * 2 - The request has been sent
         * 3 - The request is in process
         */
        validateRequest: function () {
            if (this.currentRequest != null && $.inArray(this.currentRequest.readyState, [1, 2, 3])) {
                this.currentRequest.abort();
                this.currentRequest = null;
            }
        },
        toggleManual: function() {
            if (this.isManual()) {
                this.hideManualFields();
            } else {
                this.showManualFields();
                this.showAdditions(false);
                this.validate();
            }
        },
        showManualFields: function () {
            this.showAddressDetails(false);
            //this.cityManual('');
            //this.streetManual('');
            this.isManual(true);
        },
        hideManualFields: function () {
            this.isManual(false);
        }
    });
});
