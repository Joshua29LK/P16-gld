define([
    'jquery',
    'underscore',
	'Magento_Catalog/js/price-utils',
	'jquery/validate',
	'MageArray_Customprice/js/custompricepro',
], function ($, _, priceUtils) {
    'use strict';
		
	$.widget('mage.customprice', $.mage.customPricePro, {
		allOptions: {},
		_create: function() {
			$.extend(this, this.options.optionConfig);
			$.extend(this.allOptions, this.options.optionConfig);
			this.loadDataProcess();
        }
	});
	return $.mage.customprice;
	
});