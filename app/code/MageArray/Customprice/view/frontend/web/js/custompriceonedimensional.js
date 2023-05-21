define([
    'jquery',
    'underscore',
	'Magento_Catalog/js/price-utils',
	'jquery/validate',
	'MageArray_Customprice/js/custompricepro'
], function ($, _, priceUtils) {
    'use strict';

	$.widget('mage.customPriceOneDimensional', $.mage.customPricePro, {
		allOptions:{},
		_create: function() {
			$.extend(this, this.options.optionConfig);
			$.extend(this.allOptions, this.options.optionConfig);
			this.loadDataProcess();
		},
		loadDataProcess: function () {
			var MA = this;	
			var error = this.getErrorMsg();
			
			if(MA.maxMinError == 1){
				MA.showErrorMsg($('#options_'+MA.row.id+'_text'), error.minRowErrorMsg);				
			}
			
			var options = $(".product-custom-option");
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
				var rowElement = $('#customprice_csv_select_'+optionsConfig.row.id);
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
			if (isNaN(csvPrice)  || csvPrice == 0) {
				this.showErrorMsg($(rowElement), optionsConfig.alert.notfound);
				return 0;
			}
			
			csvPrice += this.getMarkupPrice(csvPrice);
			
			csvPrice = parseFloat(csvPrice).toFixed(2);
			csvPrice = parseFloat(csvPrice);
			
			return csvPrice;
		},
		getErrorMsg: function() {
			var messages = new Object();
		
			var minRowValue = this.row.min;
			var maxRowValue = this.row.max;
		
			var minErrorMsg = this.alert.min;
			var maxErrorMsg = this.alert.max;
			
			messages.notfound = this.alert.notfound;
			messages.minRowErrorMsg = minErrorMsg.replace(/{min}/g, minRowValue+this.unit).replace(/{max}/g, maxRowValue+this.unit);
			return messages;
		},
		validateElement: function (element, elmType){
			var MA = this;
			var error = this.getErrorMsg();
			var rowmax = this.row.max;
			var rowmin = this.row.min;
			var value = $(element).val();
			if(isNaN(value))
			{
				MA.showErrorMsg($(element), error.notfound); 
				return false;
			}
			
			if(elmType == 'row')
			{
				if (rowmin > value || rowmax < value) {
					MA.showErrorMsg($(element), error.minRowErrorMsg);
					return false;
				} else {
					MA.showErrorMsg($(element), false);
					return true;
				}
			}
		},
		sizeValidation: function(option, elmType) {

			$(option).attr('type', 'number');
			$(option).addClass('custom-validate-digits');
			/* this.onlyNumberValueValidation(); */

			var MA = this;
			if(elmType == 'row')
			{
				$(option).addClass('notfound-row');
				$.validator.addMethod('notfound-row', function (value)
				{
					if(isNaN(value))
					{
						return false;
					}
					if(!MA.pricesheet.hasOwnProperty(value))
					{
						value = MA.getPriceMin(MA.pricesheet, value, 'row');
					}
					if(!MA.pricesheet.hasOwnProperty(value))
					{
						return false;
					} else {
						if((MA.pricesheet[value]* 1) > 0){
							return true;
						}else{
							return false;
						}
					}
				}, function () {
					return $.mage.__(MA.alert.notfound);
				});
			}
			else if(elmType == 'col')
			{
				$(option).addClass('notfound-col');
				$.validator.addMethod('notfound-col', function (value) {

					if(isNaN(value))
					{

						return false;
					}


					var rowEle = MA.getMicoNumber(MA.getField(MA.row.id));
					var colEle = MA.getMicoNumber(MA.getField(MA.col.id));

					colEle = value;

					if(!MA.pricesheet.hasOwnProperty(rowEle))
					{
						rowEle = MA.getPriceMin(MA.pricesheet, rowEle, 'row');

					}

					if(MA.pricesheet.hasOwnProperty(rowEle) && !MA.pricesheet[rowEle].hasOwnProperty(colEle))
					{
						colEle = MA.getPriceMin(MA.pricesheet[rowEle], colEle, 'col');

					}


					if(MA.pricesheet.hasOwnProperty(rowEle) && MA.pricesheet[rowEle].hasOwnProperty(colEle))
					{
						if((MA.pricesheet[rowEle][colEle]* 1) > 0)
						{

							return true;
						} else {

							return false;
						}
					} else {

						return false;
					}

				}, function () {
					return $.mage.__(MA.alert.notfound);
				});
			}
		},
	});
	return $.mage.customPriceOneDimensional;
});