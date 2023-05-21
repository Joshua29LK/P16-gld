define([

    'jquery',

    'owlcarousel'

], function ($) {

    'use strict';

    

    $.widget('custom.sliderProduct', {
        options: {
            type: '',
            data: {},

        },
        _create: function () {
            var owl = $("."+ this.options.type +" .slider-products");
            var data = this.options.data;
            if(Object.keys(data).length === 0) {
                data = {
                    responsive: {
                        0: {
                            items: 1
                        },
                        480: {
                            items: 2
                        },
                        768: {
                            items: 3
                        },
                        992: {
                            items: 3
                        },
                        1200: {
                            items: 5
                        }
                    },
                    autoplay: false,
                    loop: false,
                    nav: true, // Show next and prev buttons
                    dots: false,
                    autoplaySpeed: 500,
                    navSpeed: 500,
                    dotsSpeed: 500,
                    autoplayHoverPause: true,
                    margin: 30,
                };
            }
            owl.owlCarousel(data);

        },

    });



    return $.custom.sliderProduct;

});