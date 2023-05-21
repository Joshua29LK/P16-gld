define([
    'jquery',
    'mage/translate',
    'mageUtils'
], function(
    $,
    $t,
    Utils
) {
    'use strict';

    return function(value, component) {

        if(typeof component !== 'object' || !component.vatFieldFunctionalityEnabled) {
            return true;
        }

        if(component.debug) {
            console.log('valid-vat-required validation', component);
        }

        var errMsg = component.error();
        var country = component.getCountry();
        if (typeof errMsg === 'undefined' || errMsg === false) {
            errMsg = '';
        }

        if(!component.visible()) {
            if(component.debug) {
                console.log('valid-vat-required not visible');
            }
            return true;
        }
        if(Utils.isEmpty(component.value())) {
            if(component.debug) {
                console.log('valid-vat-required is empty');
            }
            return false;
        }

        //Accept non-EU numbers as valid (valid in means of form validation), as they can't be validated at VIES
        if($.isArray(component.euCountries) && $.inArray(country, component.euCountries) === -1
            && component.getVatNumberCountry() === country
        ) {
            if(component.debug) {
                console.log('valid-vat-required is non-EU');
            }
            return true;
        }
        if (component.offlineValidationEnabled && component.passedRegex()) {
            if(component.debug) {
                console.log('valid-vat-required disabled AJAX but pass Regex');
            }
            return true;
        }

        if(component.debug) {
            console.log('valid-vat-required conditions', [
                value,
                component.isValidated(),
                component.isValidVatNumber(),
                (errMsg.length <= 0)
            ]);
        }
        return component.isValidated() && component.isValidVatNumber() && (errMsg.length <= 0);

    };
});


