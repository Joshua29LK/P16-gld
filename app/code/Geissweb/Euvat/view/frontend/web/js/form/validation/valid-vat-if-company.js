define([
    'jquery',
    'mage/translate',
    'mageUtils',
    './valid-vat-required'
], function(
    $,
    $t,
    Utils,
    validVatRequiredValidation
) {
    'use strict';

    return function(value, component) {

        if(typeof component !== 'object' || !component.vatFieldFunctionalityEnabled) {
            return true;
        }

        if(component.debug) {
            console.log('valid-vat-if-company validation', component);
        }

        if(!component.visible()) {
            return true;
        }

        var company = component.getCompany();
        if(!Utils.isEmpty(company)) {
            return validVatRequiredValidation(value, component);
        }

        return true;
    };

});


