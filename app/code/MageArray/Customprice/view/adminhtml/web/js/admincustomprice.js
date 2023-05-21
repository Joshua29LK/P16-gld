define([
    'jquery',
	'MageArray_Customprice/js/admincustompricepro',
], function ($) {
	AdminCustomPrice = Class.create();
	AdminCustomPrice.prototype = Object.extend(new AdminCustomPricePro(), {
		allOptions: {},
		initialize: function(config){
			$.extend(this, config);
			$.extend(this.allOptions, config);
			this.loadDataProcess();
		}
	});
});