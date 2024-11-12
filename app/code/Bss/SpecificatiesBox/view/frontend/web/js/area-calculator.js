define([
    'jquery'
], function($) {
    'use strict';

    return function(config) {
        var widthSelector = config.widthSelector;
        var heightSelector = config.heightSelector;
        var weightFormula = config.weightFormula;

        function calculateArea() {
            var width = parseFloat($(widthSelector).val()) || 0;
            var height = parseFloat($(heightSelector).val()) || 0;
            var area = (width * height / 1000000).toFixed(2);
            $('#oppervlakte-value').text(area);
            var weight = calculateWeight(area);
            $('#weight-value').text(weight.toFixed(2));
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
        
        $(widthSelector + ',' + heightSelector).on('input', calculateArea);
    };
});
