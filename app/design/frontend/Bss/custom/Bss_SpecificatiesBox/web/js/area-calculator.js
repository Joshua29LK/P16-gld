define([
    'jquery'
], function($) {
    'use strict';

    return function(config) {
        var widthSelector = config.widthSelector;
        var heightSelector = config.heightSelector;
        var weightFormula = config.weightFormula;
        var fixedBoxSelector = '.fixed-product-box';
        var footerSelector = '.page-footer';

        function calculateArea() {
            var width = parseFloat($(widthSelector).val()) || 0;
            var height = parseFloat($(heightSelector).val()) || 0;
            var area = (width * height / 1000000).toFixed(2);
            $('.oppervlakte-value').text(area);
            var weight = calculateWeight(area);
            $('.weight-value').text(weight.toFixed(2));
        }

        function calculateWeight(area) {
            var formula = weightFormula.replace(/{sq}/g, area);
            try {
                var weight = eval(formula);
                return weight;
            } catch (e) {
                console.error('Error calculating weight:', e);
                return 0;
            }
        }

        function handleScroll() {
            var $fixedBox = $(fixedBoxSelector);
            var $footer = $(footerSelector);
            var footerOffset = $footer.offset().top;
            var fixedBoxHeight = $fixedBox.outerHeight();
            var fixedBoxOffset = $fixedBox.offset().top;
            var scrollTop = $(window).scrollTop();
            var viewportHeight = $(window).height();
            var initialTop = 280;
            if ($(window).width() < 992) {
                initialTop = 140;
            }

            var fixedBoxBottom = scrollTop + initialTop + fixedBoxHeight;
            if (fixedBoxBottom >= footerOffset) {
            $fixedBox.css({
                position: 'absolute',
                top: `${footerOffset - fixedBoxHeight}px`
            });
            } else {
                $fixedBox.css({
                    position: 'fixed',
                    top: `${initialTop}px`
                });
            }
        }

        $(window).on('scroll', handleScroll);
        $(window).trigger('scroll');
        $(widthSelector + ',' + heightSelector).on('input', calculateArea);
    };
});
