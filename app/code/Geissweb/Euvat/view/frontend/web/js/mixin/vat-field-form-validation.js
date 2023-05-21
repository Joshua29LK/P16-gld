define([
    'jquery',
    'mage/translate',
    'mageUtils',
    'uiRegistry',
    '../form/validation/valid-vat-required',
    '../form/validation/valid-vat-if-company',
    '../form/validation/valid-vat-if-specified'
], function(
    $,
    $t,
    Utils,
    uiRegistry,
    validVatRequiredValidation,
    validVatRequiredIfCompanyValidation,
    validVatRequiredIfSpecifiedValidation
) {
    'use strict';

    return function(widget) {
        /**
         * Checks if the VAT number is validated successfully
         */
        $.validator.addMethod(
            'valid-vat-required',
            function (value, selector) {
                var component = uiRegistry.get('vat-id-input.vat_id');
                return validVatRequiredValidation(value, component);
            },
            $t('Please type a valid VAT number in this field. Example: CC123456789'),
            true
        );

        /**
         * Requires a validated VAT number when the company field is filled
         */
        $.validator.addMethod(
            'valid-vat-if-company-specified',
            function (value, selector) {
                var component = uiRegistry.get('vat-id-input.vat_id');
                return validVatRequiredIfCompanyValidation(value, component);
            },
            $t('Please type a valid VAT number in this field, or leave the company field empty.'),
            true
        );

        /**
         * If there is a value in the VAT field, require the number to be valid
         */
        $.validator.addMethod(
            'valid-vat-if-specified',
            function (value, selector) {
                var component = uiRegistry.get('vat-id-input.vat_id');
                return validVatRequiredIfSpecifiedValidation(value, component);
            },
            $t('Please type a valid VAT number in this field, or leave it empty.'),
            true
        );

        return widget;
    };
});
