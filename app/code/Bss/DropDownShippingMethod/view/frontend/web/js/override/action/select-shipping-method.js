
define([
    'jquery',
    'Magento_Checkout/js/model/quote'
], function ($, quote) {
    'use strict';

    return function (shippingMethod) {
        quote.shippingMethod(shippingMethod);
        if (!$('.bss-custom-dropdown-shipping').length) {
            var startTime = 1;
            var interval = setInterval(function(){
                startTime += 1;
                if ($('.table-checkout-shipping-method tbody .row').length > 1) {
                    var row = '<tr class="row bss-custom-dropdown-shipping"><td class="col col-method"><input type="radio" class="radio" id="bss_matrixrate_matrixrate" value="matrixrate_matrixrate" name="ko_unique"></td><td class="col col-price"><span class="price"></span></td><td class="col col-method" id="label_method_matrixrate_matrixrate" style="max-width: 180px;">Afhalen</td><td class="col col-carrier" id="label_carrier_matrixrate_matrixrate"><div id="custom_afhalen"></div></td></tr>';
                    // var row = '<tr class="row bss-custom-dropdown-shipping"><td class="col col-method"><input type="radio" class="radio" id="bss_matrixrate_matrixrate" value="matrixrate_matrixrate" name="ko_unique"></td><td class="col col-price"><span class="price"></span></td><td class="col col-method" id="label_method_matrixrate_matrixrate" style="max-width: 180px;">Afhalen</td><td class="col col-carrier" id="label_carrier_matrixrate_matrixrate"><select id="custom_afhalen"></select></td></tr>';
                    $('.table-checkout-shipping-method tbody .row').each(function(){
                        if ($(this).find('input').val() != 'flatrate_flatrate') {
                            $(this).css('display','none')
                            var o = new Option($(this).find('.col-method').text(), $(this).find('input').val());
                            $('.bss-custom-dropdown-shipping').find('#custom_afhalen').append(o);
                        } else {
                            $(row).insertAfter($(this)).css('display','flex')
                            $(this).css('display','flex')
                        }
                    })
                    setTimeout(function(){
                        $('.table-checkout-shipping-method tbody .row').each(function(){
                            if ($(this).find('input').val() != 'flatrate_flatrate') {
                                if ($('.bss-custom-dropdown-shipping .price').text() == '') {
                                    $('.bss-custom-dropdown-shipping .price').text($(this).find('.col-price .price .price').text())
                                }
                            }
                        })
                        if (shippingMethod && shippingMethod['carrier_code'] == 'matrixrate') {
                            $('#custom_afhalen').val(shippingMethod['carrier_code'] + '_' + shippingMethod['method_code'])
                            $('#bss_matrixrate_matrixrate').prop('checked', true)
                        }
                    },100)
                    clearInterval(interval);
                }
                if(startTime > 50){
                    clearInterval(interval);
                }
            }, 200);

            // $('body').on('click', '#bss_matrixrate_matrixrate', function(){
            //     if ($('.bss-custom-dropdown-shipping #custom_afhalen').val() =='') {
            //         $('.bss-custom-dropdown-shipping #custom_afhalen').val($("#custom_afhalen option:first").val())
            //         $('input[value='+ $("#custom_afhalen option:first").val() +']').click()
            //     } else {
            //         $('#custom_afhalen').trigger('change')
            //     }
            // })
            // $('body').on('change', '#custom_afhalen', function(){
            //     $('input[value='+ $(this).val() +']').click()
            // })
        }
        setTimeout(function(){
            if (shippingMethod) {
                if (shippingMethod['carrier_code'] != 'matrixrate') {
                    $('#bss_matrixrate_matrixrate').prop('checked', false)
                } else {
                    $('#bss_matrixrate_matrixrate').prop('checked', true)
                }
            }

        },10)
    };
});
