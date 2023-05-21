define([
    'jquery',
    'Hoofdfabriek_PostcodeNL/js/view/customer/postcode'
], function ($) {
    'use strict';

    $.widget('hoofdfabriek.postcodenl', {
        _create: function () {
            $('#form-validate').addClass('form-postcodenl');

            var street = $('.street');
            $('.country').insertBefore(street);
            $('.zip').insertBefore(street);
            $('#postcodenl').insertAfter('.country');
        },
    });

    return $.hoofdfabriek.postcodenl;
});
