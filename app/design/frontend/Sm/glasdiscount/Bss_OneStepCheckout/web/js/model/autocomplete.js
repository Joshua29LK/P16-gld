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
 * @package   Bss_OneStepCheckout
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

define([
    'ko',
    'underscore',
    'uiComponent',
    'jquery',
    'uiRegistry',
    'Magento_Customer/js/model/address-list',
    'Magento_Ui/js/lib/view/utils/async'
], function (ko, _, Component, $, registry, addressList) {
    'use strict';

    var componentFields = [
        'country_id',
        'postcode',
        'region_id',
        'region',
        'city',
        'street'
    ];
    var streetIds = [],
        askLocation = false;
    window.geoAddress = [];

    return Component.extend({
        /** @inheritdoc */
        initialize: function () {
            this._super();
            var self = this,
                mapUrl;
            if (window.checkoutConfig.bssOsc.googleApiAutoComplete == false) {
                if (window.checkoutConfig.bssOsc.googleApiCustomerCountry) {
                    $.async('[name="street[0]"]', function (element) {
                        var id = $(element).attr('id');
                        self.fillCountry(id, window.checkoutConfig.bssOsc.googleApiCustomerCountry);
                    });
                }
                return;
            }
            if (!_.isUndefined(window.checkoutConfig.bssOsc.googleApi)) {
                mapUrl = 'https://maps.googleapis.com/maps/api/js?key=' + window.checkoutConfig.bssOsc.googleApi + '&libraries=places';
                $.getScript(mapUrl);
            }
            $.async('[name="street[0]"]', function (element) {
                var id = $(element).attr('id');
                streetIds.push(id);
                self.initAutocomplete(id);
                $(element).attr('placeholder', '');
            });
        },

        /**
         * @param {Object} autocomplete
         */
        geolocate: function (id) {
            var self = this;
            if (window.geoAddress.length !== 0) {
                $.each(streetIds, function( index, id ) {
                    self.fillInAddress(window.geoAddress, id, 'autofill');
                });
                return;
            }
            if (navigator.geolocation && askLocation == false) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var long = position.coords.longitude;
                    var lat = position.coords.latitude;
                    var geocoder = new google.maps.Geocoder();
                    //long = '-73.977330';
                    //lat = '40.680590';
                    var latlng = new google.maps.LatLng(lat, long);
                    if (geocoder) {
                        geocoder.geocode({'latLng': latlng}, function (results, status) {
                            if (status == google.maps.GeocoderStatus.OK) {
                                if (results[0]) {
                                    var address = self.getAddressFromGoogleApi(results, 'autofill');
                                    window.geoAddress = address;
                                    $.each(streetIds, function( index, id ) {
                                        self.fillInAddress(address, id, 'autofill');
                                    });
                                }
                            }
                        })
                    }
                }, function (error) {
                    if (window.checkoutConfig.bssOsc.googleApiCustomerCountry) {
                        $.each(streetIds, function( index, id ) {
                            self.fillCountry(id, window.checkoutConfig.bssOsc.googleApiCustomerCountry);
                        });
                    }
                });
            }
            askLocation = true;
        },

        getAddressFromGoogleApi: function (addressGoogleApi, type) {
            if (type == 'autofill') {
                var addressGoogleApiOne = addressGoogleApi[0];
                if (addressGoogleApi[1]) {
                    var addressGoogleApiTwo = addressGoogleApi[1];
                }
            } else {
                var addressGoogleApiOne = addressGoogleApi;
            }
            var self = this,
                address = [],
                a = 1,
                issetCountry = false,
                issetCity = false,
                regionId = false;
            for (var i = 0; i < addressGoogleApiOne.address_components.length; i++) {
                for (var b = 0; b < addressGoogleApiOne.address_components[i].types.length; b++) {
                    if (addressGoogleApiOne.address_components[i].types[b] == "country" && issetCountry == false) {
                        address['country_id'] = addressGoogleApiOne.address_components[i].short_name;
                        issetCountry = true;
                    }
                    if (addressGoogleApiOne.address_components[i].types[b] == "locality") {
                        address['city'] = addressGoogleApiOne.address_components[i].long_name;
                        issetCity = true;
                    }
                    if (addressGoogleApiOne.address_components[i].types[b] == "administrative_area_level_1") {
                        address['region_id'] = addressGoogleApiOne.address_components[i].long_name;
                        regionId = addressGoogleApiOne.address_components[i].long_name;
                    }
                    if (addressGoogleApiOne.address_components[i].types[b] == "postal_code") {
                        address['postcode'] = addressGoogleApiOne.address_components[i].long_name;
                    }
                }
            }
            if (addressGoogleApiTwo) {
                address['street'] = addressGoogleApiTwo.formatted_address;
            } else {
                address['street'] = addressGoogleApiOne.formatted_address;
            }
            if (issetCity == false && regionId != false) {
                address['city'] = regionId;
            }
            return address;
        },

        fillInAddress: function (address, id, type) {
            if (addressList().length !== 0) {
                return;
            }
            var street = $('#' + id).val();
            if (typeof street !== "undefined" && street != '' && type == 'autofill'){
                return;
            }
            var component,
                country = false,
                countryList = window.checkoutConfig.bssOsc.googleApiListCountries,
                useRegionId = false,
                countryElement = false,
                regionIdElement = false,
                billing = 'checkout.steps.shipping-step.shippingAddress.billing-address-form-shared.form-fields',
                shipping = 'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset';
            if (registry.get(shipping).getChild('street').getChild(0).uid === id) {
                component = shipping;
            } else {
                component = billing;
            }
            setTimeout(function(){
                registry.get(component, function (formComponent) {
                    $.each(componentFields, function (index, field) {
                        var element = formComponent.getChild(field);
                        if (field === 'region') {
                            element = formComponent.getChild('region_id_input');
                        }

                        if (field == 'country_id' && field in address) {
                            $('#' + element.uid).find('option').each(function () {
                                if ($(this).attr('value') == address[field]) {
                                    var currentCountry = element.value();
                                    element.value(address[field]);
                                    country = address[field];
                                    countryElement = element;
                                    if (($.inArray(currentCountry, countryList) === -1 && $.inArray(address[field], countryList) !== -1) ||
                                        ($.inArray(currentCountry, countryList) !== -1 && $.inArray(address[field], countryList) !== -1 && currentCountry != address[field])
                                    ) {
                                        element.trigger('change');
                                    }
                                    return false;
                                }
                            });
                        }
                        if (field == 'region_id' && field in address && country != false && $.inArray(country, countryList) !== -1) {
                            $('#' + element.uid).find('option').each(function () {
                                if ($(this).attr('data-title') == address[field]) {
                                    element.value($(this).attr('value'));
                                    regionIdElement = element;
                                    return false;
                                }
                            });
                            useRegionId = true;
                        }
                        if (field == 'region' && country != false && useRegionId == false) {
                            if ('region_id' in address) {
                                element.value(address['region_id']);
                            } else {
                                element.value('');
                            }
                        }
                        if (field == 'street' && field in address) {
                            element = formComponent.getChild(field).getChild(0);
                            element.value(address[field]);
                        }
                        if ((field == 'postcode' || field == 'city')) {
                            if (field in address) {
                                element.value(address[field]);
                            } else {
                                element.value('');
                            }
                        }
                    });
                });

                if (country != '' && component == shipping) {
                    if (useRegionId == true && regionIdElement != false) {
                        regionIdElement.trigger('change');
                    } else {
                        if (countryElement != false) {
                            countryElement.trigger('change');
                        }
                    }
                }
            }, 500);
        },

        fillCountry: function (id, countryCode) {
            if (addressList().length !== 0) {
                return;
            }
            if ($('#' + id).val() != ''){
                return;
            }
            var component,
                billing = 'checkout.steps.shipping-step.shippingAddress.billing-address-form-shared.form-fields',
                shipping = 'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset';
            if (registry.get(shipping).getChild('street').getChild(0).uid === id) {
                component = shipping;
            } else {
                component = billing;
            }
            setTimeout(function(){
                registry.get(component, function (formComponent) {
                    var element = formComponent.getChild('country_id');
                    $('#' + element.uid).find('option').each(function () {
                        if ($(this).attr('value') == countryCode) {
                            var currentCountry = element.value();
                            element.value(countryCode);
                            if (currentCountry != countryCode) {
                                element.trigger('change');
                            }
                            return false;
                        }
                    });
                });
            }, 500);
        },

        /**
         * @param {String} id
         */
        initAutocomplete: function (id) {
            var self = this,
                options = {types: ['address']},
                googleAddress = false;

            if (!_.isUndefined(window.checkoutConfig.bssOsc.specificcountry)) {
                var countries = window.checkoutConfig.bssOsc.specificcountry;
                options.componentRestrictions = {country: countries};
            }

            var autocomplete = new google.maps.places.Autocomplete(
                (document.getElementById(id)),
                options
            );
            autocomplete.addListener('place_changed', function () {
                var results = autocomplete.getPlace();
                if (results) {
                    var address = self.getAddressFromGoogleApi(results, 'suggest');
                    self.fillInAddress(address, id, 'suggest');
                }
            });
            this.geolocate(id);
        }
    });
});
