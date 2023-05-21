define([
    'jquery',
    'MageArray_Customprice/js/admincustompricepro',
], function ($) { 
	OneDimensional = Class.create();
	OneDimensional.prototype = Object.extend(new AdminCustomPricePro(), {
		allOptions: {},
		initialize: function(config){
			$.extend(this, config);
			$.extend(this.allOptions, config);
			this.loadDataProcess();
		},
		loadDataProcess: function () {
			var MA = this;		
			var options = $("#product_composite_configure_form .product-custom-option");
			$.each(options, function(key, option){
				
				if(MA.csvElement)
				{
					var csvElement = false;
					MA.csvElement.each(function(){
						if($(this).attr('id') == option.id)
						{
							csvElement = true;
						}
					});
					if(csvElement){	return;	}
				}
				
				var percent = false;
				var b = option.name.replace(/[^0-9]/g,'');
				if(MA.row.id == b)
				{
					if (MA.select == 1) 
					{
						MA.changeInputToSelect(option);
					} else {
						
						if(MA.row.id == b)
						{
							MA.setValidationOnElement(option, 'row');
							MA.sizeValidation(option, 'row');
						}
					}
					$(option).on('change', function(){
						MA.changeBasePrice();
					});
				} else {
					
					if (option.type == "select-one" || option.type == 'select-multiple') {
						$(option).find('option').each(function(){
							if(MA.otheroptions[b][$(this).val()] && MA.otheroptions[b][$(this).val()].price_type == 'percent')
							{
								var price = parseFloat(MA.otheroptions[b][$(this).val()].price);
								var title = MA.otheroptions[b][$(this).val()].title;
								$(this).text(title+' + '+price+'%');
								percent = true;
							}
						});
						
						$(option).on('change', function(){
							MA.changeBasePrice();
						});
					}
					
					if (option.type == "radio" || option.type == "checkbox") 
					{
						if(MA.otheroptions[b][option.value].price_type == 'percent')
						{
							var price = parseFloat(MA.otheroptions[b][option.value].price);
							$(option).closest('.field').find('.price-wrapper').text(price+'%');
							percent = true;
						}
						
						$(option).on('click', function(){
							MA.changeBasePrice();
						});
					}
					
					if (option.type == "text" || option.type == "textarea") 
					{
						if(MA.otheroptions[b].price_type == 'percent')
						{
							var price = parseFloat(MA.otheroptions[b].price);
							$(option).closest('.field').find('.price-wrapper').text(price+'%');
							percent = true;
						}
						
						$(option).on('change', function(){
							MA.changeBasePrice();
						});
					}
				}
			});
		},
		getPrice: function() {
			var optionsConfig = this;
			
			if(parseInt(optionsConfig.select))
			{
				var rowElement = $('#product_composite_configure_form #customprice_csv_select_'+optionsConfig.row.id);
			} else {
				var rowElement = $(this.getField(optionsConfig.row.id));
			}
			
			if(!this.validateElement(rowElement, 'row'))
			{
				return 0;
			}
			
			var rowValue = rowElement.val();
			if(!rowValue || isNaN(rowValue))
			{
				this.showErrorMsg($(rowElement), optionsConfig.alert.notfound);
				return 0;
			}
		
			var row = this.getMicoNumber(rowElement);
			
			if ((row <= 0) || (row == 0)) 
			{
				return 0;
			}
			
			if(!optionsConfig.pricesheet.hasOwnProperty(row))
			{
				row = this.getPriceMin(optionsConfig.pricesheet, row);
				
				if(!optionsConfig.pricesheet.hasOwnProperty(row))
				{
					this.showErrorMsg($(rowElement), optionsConfig.alert.notfound);
					return 0;
				}
			}
			
			
			var csvPrice = parseFloat(optionsConfig.pricesheet[row] * 1);
			if (isNaN(csvPrice)) {
				this.showErrorMsg($(rowElement), optionsConfig.alert.notfound);
				return 0;
			}
			
			csvPrice += this.getMarkupPrice(csvPrice);
			
			csvPrice = parseFloat(csvPrice).toFixed(2);
			csvPrice = parseFloat(csvPrice);
			
			return csvPrice;
		},
		getErrorMsg: function() {
			var messages = [];
		
			var minRowValue = this.row.min;
			var maxRowValue = this.row.max;
		
			var minErrorMsg = this.alert.min;
			var maxErrorMsg = this.alert.max;
			
			messages['notfound'] = this.alert.notfound;
			
			messages['minRowErrorMsg'] = minErrorMsg.replace(/{min}/g, minRowValue+this.unit).replace(/{max}/g, maxRowValue+this.unit);
			return messages;
		},
		validateElement: function (element, elmType){
			var MA = this;
			var ERROR = this.getErrorMsg();
			var rowmax = this.row.max;
			var rowmin = this.row.min;
			var value = $(element).val();
			if(isNaN(value))
			{
				MA.showErrorMsg($(element), ERROR['notfound']); 
				return false;
			}
			
			if(elmType == 'row')
			{
				if (rowmin > value || rowmax < value) {
					MA.showErrorMsg($(element), ERROR['minRowErrorMsg']);
					return false;
				} else {
					MA.showErrorMsg($(element), false);
					return true;
				}
			}
		},
	});
});