define([
    'jquery',
    'mage/translate',
    'mageUtils',
    '../form/validation/valid-vat-required',
    '../form/validation/valid-vat-if-company',
    '../form/validation/valid-vat-if-specified'
], function(
    $,
    $t,
    Utils,
    validVatRequiredValidation,
    validVatRequiredIfCompanyValidation,
    validVatRequiredIfSpecifiedValidation
) {
    'use strict';

    return function(validator) {

        /**
         * Checks if the VAT number is validated successfully
         */
        validator.addRule(
            'valid-vat-required',
            function (value, param, component) { return validVatRequiredValidation(value, component) },
            $t('Please type a valid VAT number in this field. Example: CC123456789')
        );

        /**
         * Requires a validated VAT number when the company field is filled
         */
        validator.addRule(
            'valid-vat-if-company-specified',
            function (value, param, component) { return validVatRequiredIfCompanyValidation(value, component) },
            $t('Please type a valid VAT number in this field, or leave the company field empty.')
        );

        /**
         * If there is a value in the VAT field, require the number to be valid
         */
        validator.addRule(
            'valid-vat-if-specified',
            function (value, param, component) { return validVatRequiredIfSpecifiedValidation(value, component) },
            $t('Please type a valid VAT number in this field, or leave it empty.')
        );

        return validator;

    };
});
