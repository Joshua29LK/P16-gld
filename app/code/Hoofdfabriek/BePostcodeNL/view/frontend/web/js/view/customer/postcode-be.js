define([
    'jquery',
    'Hoofdfabriek_BePostcodeNL/js/view/form/autocomplete',
    'underscore',
    'mage/validation',
    'Hoofdfabriek_PostcodeNL/js/view/customer/postcode'
], function ($, Component, _) {
    'use strict';

    return Component.extend({
        defaults: {
            settings: {}
        },
        initialize: function () {
            this._super();
            var countryField = $('#country');
            _.bindAll(this,'_countryChanged');
            countryField.on('change', this._countryChanged);

            if (countryField.val() === 'BE') {
                countryField.change();
            }

            var postcode = this.settings.postcode;
            var house = this.settings.house_number;
            var city = this.settings.city;
            var street = this.settings.street;
            if (postcode && house && city && street) {
                this.municipalityName(postcode + ' ' + city);
                this.beAddress.municipalityName(city);
                this.beAddress.postcode(postcode);
                this.streetName(street);
                this.beAddress.streetName(street);

                this.houseNumber(house);
                this.beAddress.houseNumber(house);
            }
            $('#postcod-be').insertAfter('.country');
        },
        _countryChanged: function (event) {
            if ($(event.target).val() === 'BE') {
                $('input[name="postcode"]').attr('readonly', 'readonly').parents('.field').hide();
                $('input[name="city"]').attr('readonly', 'readonly').parents('.field').hide();
                $('input[name="region"]').attr('readonly', 'readonly').parents('.field').hide();
                $('#region_id').attr('readonly', 'readonly').prop("disabled", true).parents('.field').hide();
                $('input[name^="street[').attr('readonly', 'readonly').parents('.field').hide();
                this.isVisible(true);
                this.updateAddress();
            } else {
                if ($(event.target).val() !== 'NL'){
                    $('input[name^="street[').removeAttr('readonly').parents('.field').show();
                    $('input[name="city"]').removeAttr('readonly').parents('.field').show();
                    $('input[name="region"]').removeAttr('readonly').parents('.field').show();
                    $('#region_id').removeAttr('readonly').prop("disabled", false).parents('.field').show();
                    $('input[name="postcode"]').removeAttr('readonly').parents('.field').show();
                }
                this.isVisible(false);
            }
        },
        updateAddress: function () {
            var city = this.beAddress.municipalityName() !== null ? this.beAddress.municipalityName() : '',
                postcode = this.beAddress.postcode() !== null ? this.beAddress.postcode() : '',
                street_0 = this.beAddress.streetName() != null ? this.beAddress.streetName() : '';

            $('#city').val(city);
            $('#zip').val(postcode);
            $('#street_1').val(street_0);
            $('#region').val('');

            var houseNumber = this.beAddress.houseNumber() !== null ? this.beAddress.houseNumber() : '';

            if (this.settings.useStreet2AsHouseNumber) {
                $('#street_2').val(houseNumber.trim());
            } else {
                $('#street_1').val(street_0 + ' ' + houseNumber.trim());
            }
        },
        validateSingle: function (element, event) {
            var beAutocompleteForm = $('#form-validate'), validator;
            beAutocompleteForm.validation();
            validator = beAutocompleteForm.validate();
            var field = $(event.target);
            validator.element('#' + field.attr('id'));
        },
        getAddressData: function () {
            return $('#form-validate').serializeArray().reduce(function(obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});
        },
    });
});
