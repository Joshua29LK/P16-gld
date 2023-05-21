define([
    'jquery',
    'Hoofdfabriek_PostcodeNL/js/view/form/postcode',
    'ko',
    'mageUtils',
    'mage/translate',
    'underscore'
], function ($, Component, ko, utils, $t, _) {
    'use strict';
    return Component.extend({
        defaults: {
            settings: {}
        },

        initialize: function () {
            this._super();
            this.resultBlock = '.postcode-valid-address';

            var postcode = this.getSettings().postcode;
            var house = this.getSettings().house_number;
            var city = this.getSettings().city;
            var street = this.getSettings().street;
            this.postcodeNL(postcode);
            this.houseNumber(house);
            this.streetManual(street);
            this.cityManual(city);

            var countryField = $('#country');

            _.bindAll(this,'countryChanged');

            countryField.on('change', this.countryChanged);
            if (countryField.val() === 'NL') {
                countryField.change();
            }

            if ((utils.isEmpty(postcode) || utils.isEmpty(house)) && !utils.isEmpty(city) && !utils.isEmpty(street)) {
                this.showManualFields(true);
            }
        },
        countryChanged: function (event) {
            if ($(event.target).val() === 'NL') {
                $('input[name="postcode"]').attr('readonly', 'readonly').parents('.field').hide();
                $('input[name="city"]').attr('readonly', 'readonly').parents('.field').hide();
                $('input[name="region"]').attr('readonly', 'readonly').parents('.field').hide();
                $('#region_id').attr('readonly', 'readonly').prop("disabled", true).parents('.field').hide();
                $('input[name^="street[').attr('readonly', 'readonly').parents('.field').hide();
                this.isVisible(true);
                var postcode = this.getSettings().postcode;
                var house = this.getSettings().house_number;
                var city = this.getSettings().city;
                var street = this.getSettings().street;
                if ((utils.isEmpty(postcode) || utils.isEmpty(house)) && !utils.isEmpty(city) && !utils.isEmpty(street)) {
                    this.showManualFields(true);
                } else {
                    this.updatePostcodeNL();
                }
            } else {
                $('input[name^="street[').removeAttr('readonly').parents('.field').show();
                $('input[name="city"]').removeAttr('readonly').parents('.field').show();
                $('input[name="region"]').removeAttr('readonly').parents('.field').show();
                $('#region_id').removeAttr('readonly').prop("disabled", false).parents('.field').show();
                $('input[name="postcode"]').removeAttr('readonly').parents('.field').show();
                this.isVisible(false);
                this.postcodeValidate();
            }
        },
        getSettings: function () {
            return this.settings;
        },
        getAddressData: function () {
            return $('#form-validate').serializeArray().reduce(function(obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});
        },
        getPostcodeData: function () {
            return $('#postcode-validation').serializeArray().reduce(function(obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});
        },
        postcodeValidate: function () {
            var self = this;
            var address = this.getPostcodeData();

            var patterns = [], pattern, regex;
            patterns['pattern_1'] = {
                pattern: '^[1-9][0-9]{3}\\s?[a-zA-Z]{2}$',
                example: '1234 AB/1234AB'
            }

            this.validatedPostCodeExample = [];

            if (!utils.isEmpty(address.postcodenl_postcode) && !utils.isEmpty(patterns)) {
                for (pattern in patterns) {
                    if (patterns.hasOwnProperty(pattern)) { //eslint-disable-line max-depth
                        this.validatedPostCodeExample.push(patterns[pattern].example);
                        regex = new RegExp(patterns[pattern].pattern);

                        if (regex.test(address.postcodenl_postcode)) { //eslint-disable-line max-depth
                            return true;
                        }
                    }
                }
                self.validationMessage(self.getValidationMessage());
                self.isManual(true);
                return false;
            }

            return true;
        },
        updatePostcodeNL: function () {
            var address = this.getPostcodeData();
            this.validationMessage(false);
            this.showAddressDetails(false);
            this.hideManualFields();
            if (!!address) {
                var self = this;
                clearTimeout(this.postcodeCheckTimeout);

                this.postcodeCheckTimeout = setTimeout(function () {
                    var validationResult = self.postcodeValidate();
                    if (validationResult && self.houseNumber()) {
                        self.getPostcodeInformation();
                        self.validate();//if we want to show error on empty street + city
                    } else if (!validationResult && self.postcodeNL()){
                        self.validationMessage(self.getValidationMessage());
                        $('#zip').val(self.postcodeNL());
                    }
                }, self.checkDelay);
            }
        },
        clearSteerts: function () {
            $('input[name^="street[').val('');
        },
        getPostcodeInformation: function () {
            if (this.sendRequest) {
                return;
            }
            var self = this;
            var response = false;
            this.validateRequest();
            this.isLoading(true);
            this.showAdditions(false);
            this.hideManualFields();
            this.currentRequest = $.ajax({
                method: "POST",
                async: false,
                url: self.getSettings().url,
                data: { postcode: self.postcodeNL(), houseNumber: self.houseNumber(), houseAddition: ''},
                beforeSend: function(){
                    $("#postcode-loader").show();
                },
                complete:function(data){
                    $("#postcode-loader").hide();
                },
                success: function (response) {
                    self.validationMessage(null);
                    self.houseAdditionalOptions.removeAll();
                    self.cityManual('');
                    self.streetManual('');
                    $('#region').val('');
                    if (response.street) {
                        self.clearSteerts();
                        self.lastRequestResult = response;
                        if (self.getSettings().useStreet2AsHouseNumber) {
                            $('#street_1').val(response.street);
                            $('#street_2').val(response.houseNumber.toString());
                        } else {
                            $('#street_1').val(response.street + ' ' + response.houseNumber.toString());
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
                                $('#street_3').val(addressAddition.toUpperCase());
                                } else if (self.getSettings().useStreet2AsHouseNumber) {
                                $('#street_2').val($('#street_2').val() + ' ' + responseAddition.toUpperCase());
                                } else if ($('#street_1').val()) {
                                $('#street_1').val($('#street_1').val() + ' ' + responseAddition.toUpperCase());
                            }
                        }

                        $('#city').val(response.city);
                        $('#zip').val(response.postcode);


                        self.updateResultBlock(responseAddition);
                        self.streetManual(response.street);
                        self.cityManual(response.city);
                        self.showAddressDetails(true);
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
                        $('#zip').val(self.postcodeNL());
                        self.cityManual(self.getSettings().city);
                        self.streetManual(self.getSettings().street);
                        self.showManualFields();
                    }

                    self.isLoading(false);
                },

            });

        },
        observeManualCity: function (value) {
            $('#city').val(value);
        },
        observeStreetManual: function (value) {
            if (this.getSettings().useStreet2AsHouseNumber) {
                $('#street_1').val(value);
            } else {
                $('#street_1').val(value + ' ' + this.houseNumber());
            }
        },
        observeAddition: function (newValue) {
            if (newValue == undefined || !this.lastRequestResult) {
                return;
            }
            var sendRequest = this.sendRequest;
            this.sendRequest = true;

            var parentPartentName = this.parentName;

            if (this.getSettings().useStreet3AsHouseNumberAddition && $('#street_3').length) {
                $('#street_3').val(newValue);
            } else if (this.getSettings().useStreet2AsHouseNumber && $('#street_2').length) {
                $('#street_2').val(this.lastRequestResult.houseNumber.toString() + newValue);
            } else {
                $('#street_1').val(this.lastRequestResult.street + ' ' + this.lastRequestResult.houseNumber.toString() + ' ' + newValue);
            }

            this.houseNumber(this.lastRequestResult.houseNumber.toString() + ' ' + newValue);
            this.showAdditions(false);
            this.updateResultBlock(newValue);
            this.showAddressDetails(true);
            this.validate();
            this.sendRequest = sendRequest;
        },
    });
});
