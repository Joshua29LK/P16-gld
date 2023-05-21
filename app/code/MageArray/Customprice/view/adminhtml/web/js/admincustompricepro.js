define([
    'jquery',
    'mage/template',
	'Magento_Catalog/js/price-utils',
	'jquery/validate'
], function ($, template, priceUtils) { 
	AdminCustomPricePro = Class.create();
	AdminCustomPricePro.prototype = {
		activeCsv: {},
		allOptions: {},
		csvElement: '',
		initialize: function(config){
			$.extend(this, config);
			$.extend(this.allOptions, config);
			
			this.csvElement = $('#product_composite_configure_form [name="options['+this.csvoption+']"].product-custom-option');
			if(this.csvElement.length)
			{
				var activeElement = $(this.csvElement[0]);
				activeElement.prop('checked', true);
				var csvId = activeElement.val();
				if(this.allOptions[csvId])
				{
					$.extend(this.activeCsv, this.allOptions[csvId]);
					$.extend(this, this.allOptions[csvId]);
					this.loadCsvDataProcess();
					this.loadDataProcess();
				}
			}
		},
		loadCsvDataProcess: function() {
			var MA = this;
			if(this.csvElement.length)
			{
				this.csvElement.each(function(){
					$(this).click(function(){
						if(MA[$(this).val()])
						{
							MA.activeCsv = MA[$(this).val()];
							MA.applyDataInThisObject();
						}
					});
				});
			}
		},
		applyDataInThisObject: function () {
			$.extend(this, this.activeCsv);
			this.loadDataProcess();
			this.resetValue();
		},
		resetValue: function(){
			$(this.getField(this.row.id)).val('').change();
			$(this.getField(this.col.id)).val('').change();
		},
		loadDataProcess: function(){
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
				if(MA.row.id == b || MA.col.id == b)
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
						else if(MA.col.id == b) 
						{
							MA.setValidationOnElement(option, 'col');
							MA.sizeValidation(option, 'col');
						}
					}
					$(option).on('change', function(){
						MA.changeBasePrice($(this));
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
							MA.changeBasePrice($(this));
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
							MA.changeBasePrice($(this));
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
							MA.changeBasePrice($(this));
						});
					}
				}
			});
		},
		getPriceMin: function(object, price) {
			price = parseFloat(price);
			var optionsConfig = this;
			if(!isNaN(price))
			{
				while (!object.hasOwnProperty(price)) 
				{
					if (optionsConfig.pricemin) {
						price = price - 1;
						if ((price < 1) || (price < optionsConfig.row.min)) {
							break
						}
					} else {
						price = price + 1;
						if (price > optionsConfig.row.max) {
							break
						}
					}
				}
			} else {
				return 0;
			}
			return price;
		}, 
		getPrice: function() { 
			var optionsConfig = this;
			
			if(parseInt(optionsConfig.select))
			{
				var rowElement = $('#product_composite_configure_form #customprice_csv_select_'+optionsConfig.row.id);
				var colElement = $('#product_composite_configure_form #customprice_csv_select_'+optionsConfig.col.id);
			} else {
				var rowElement = $(this.getField(optionsConfig.row.id));
				var colElement = $(this.getField(optionsConfig.col.id));
			}
			
			if(!this.validateElement(rowElement, 'row'))
			{
				//return 0;
			}
			if(!this.validateElement(colElement, 'col'))
			{
				return 0;
			}
			
			var rowValue = rowElement.val();
			if(!rowValue || isNaN(rowValue))
			{
				this.showErrorMsg($(rowElement), optionsConfig.alert.notfound);
				return 0;
			}
			
			var colValue = colElement.val();
			if(!colValue || isNaN(colValue))
			{
				this.showErrorMsg($(colElement), optionsConfig.alert.notfound);
				return 0;
			}
			
			var row = this.getMicoNumber(rowElement);
			var col = this.getMicoNumber(colElement);
			
			if ((row <= 0) || (row == 0)) 
			{
				return 0;
			}
			
			if ((col <= 0) || (col == 0)) 
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
			
			if(!optionsConfig.pricesheet[row].hasOwnProperty(col))
			{
				col = this.getPriceMin(optionsConfig.pricesheet[row], col);
				
				if(!optionsConfig.pricesheet[row].hasOwnProperty(col))
				{
					this.showErrorMsg($(colElement), optionsConfig.alert.notfound);
					return 0;
				}
			}
			
			var csvPrice = parseFloat(optionsConfig.pricesheet[row][col] * 1);
			if (isNaN(csvPrice) || csvPrice == 0) {
				this.showErrorMsg($(rowElement), optionsConfig.alert.notfound);
				return 0;
			}
			
			csvPrice += this.getMarkupPrice(csvPrice);
			
			csvPrice = parseFloat(csvPrice).toFixed(2);
			csvPrice = parseFloat(csvPrice);
			
			return csvPrice;
		},
		getMarkupPrice: function(csvPrice) {
			var markupPrice = 0;
			if(this.markuptype == 'percent' && parseFloat(this.markupvalue) > 0){
				markupPrice = ((csvPrice*parseFloat(this.markupvalue))/100);
			}
			
			if(this.markuptype=='fixed' && parseFloat(this.markupvalue) > 0){
				markupPrice = parseFloat(this.markupvalue);
			}
			return markupPrice;
		},
		getMicoNumber: function (c) {
			if (!c) {
				return 0
			}
			var b = 0;
			if (c.attr('type') == "select-one" || c.attr('type') == "select-multiple") {
				var a = c.selectedIndex;
				b = a >= 0 ? c.options[a].innerHTML : 0
			} else {
				b = c.attr('value')
			} if (!b) {
				return 0
			}
			b = parseFloat(b) * 1;
			if (isNaN(b)) {
				return 0
			}
			return b
		},
		getField: function(id)
		{
			return $('#product_composite_configure_form [id^="options_'+id+'"].product-custom-option');
		},
		showErrorMsg: function(element, msg, doNull){
			var id = $(element).attr('id').match(/\d+/);
			if($(element).parent().find('label.mage-error').length && msg != false)
			{
				
				$(element).parent().find('label.mage-error').html(msg).show();
				if(doNull){ $(element).val(''); }
				
			} else if(msg != false) {
				msg = '<label for="options_'+id+'_text" generated="true" class="custom mage-error" id="options_'+id+'_text-error">'+msg+'</label>';
				
				$(element).after(msg).show();
				if(doNull){ $(element).val(''); }
			}
			
			if(msg == false)
			{
				$(element).parent().find('label.mage-error').hide();
			}
		},
		setValidationOnElement: function(option, elmType) {
			var MA = this;
			var ERROR = MA.getErrorMsg();
			
			if(elmType == 'row')
			{
				$(option).addClass('validate-csvprice-range-row');
				$.validator.addMethod('validate-csvprice-range-row', function (value) {
					var rowmax = MA.row.max;
					var rowmin = MA.row.min;
					if (rowmin > value || rowmax < value) 
					{
						return false;
					} else {
						return true;
					}
				}, function () {
					return $.mage.__(ERROR['minRowErrorMsg']);
				});
			} else if(elmType == 'col') {
				$(option).addClass('validate-csvprice-range-col');
				$.validator.addMethod('validate-csvprice-range-col', function (value) 
				{
					var colmin = MA.col.min;
					var colmax = MA.col.max;
					if (colmin > value || colmax < value) {
						return false;
					} else {
						return true;
					}
				}, function () {
					return $.mage.__(ERROR['maxColErrorMsg']);
				});
			}
		},
		sizeValidation: function(option, elmType) {
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
						value = MA.getPriceMin(MA.pricesheet, value);
					}

					if(!MA.pricesheet.hasOwnProperty(value))
					{
						return false;
					} else {
						return true;
					}
				}, function () {
					return $.mage.__(MA.alert.notfound);
				});
			} 
			else if(elmType == 'col')
			{
			}
		},
		getErrorMsg: function() {
			var messages = [];
		
			var minRowValue = this.row.min;
			var maxRowValue = this.row.max;
			
			var minColValue = this.col.min;
			var maxColValue = this.col.max;
			
			var minErrorMsg = this.alert.min;
			var maxErrorMsg = this.alert.max;
			
			messages['notfound'] = this.alert.notfound;
			
			messages['minRowErrorMsg'] = minErrorMsg.replace(/{min}/g, minRowValue+this.unit).replace(/{max}/g, maxRowValue+this.unit);
			messages['maxColErrorMsg'] = minErrorMsg.replace(/{min}/g, minColValue+this.unit).replace(/{max}/g, maxColValue+this.unit);
			
			return messages;
		},
		validateElement: function (element, elmType){
			var MA = this;
			var ERROR = this.getErrorMsg();
			var colmin = this.col.min;
			var colmax = this.col.max;
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
			else if(elmType == 'col')
			{
				if (colmin > value || colmax < value) {
					MA.showErrorMsg($(element), ERROR['maxColErrorMsg']);
					return false;
				} else {
					MA.showErrorMsg($(element), false);
					return true;
				}
			}
		},
		changeBasePrice: function (option) {
			var csvPrice = this.changePercentPrice(option);
			
			var finalPrice = csvPrice;
			if(parseInt(this.allOptions.includebaseprice) == 0)
			{
				var basePrice = parseFloat(this.price);
				finalPrice = ((-basePrice)+csvPrice);
			}
			var priceElement = $(this.getField(this.row.id));
			if(!priceElement.length)
			{
				priceElement = $(this.getField(this.col.id));
			}
			
			priceElement.attr('price',finalPrice);
		},
		getFormattedPrice: function(price) {
			return priceUtils.formatPrice(price, this.priceFormat);
		},
		changePercentPrice: function(option) {
			var MA = this;
			var csvPrice = parseFloat(MA.getPrice());
			var originalCsvPrice = csvPrice;
			var percentOptionPrice = 0;
			
			
			/* add base price for calculate option percent price */
			if(parseInt(this.allOptions.includebaseprice) == 1)
			{
				if(parseFloat(this.allOptions.specialprice) > 0 )
				{
					csvPrice += parseFloat(this.allOptions.specialprice);
				} else {
					csvPrice += parseFloat(this.allOptions.price);
				}
			}
			
			if(_.size(this.allOptions.otheroptions) > 0 && csvPrice > 0)
			{
				$.each(this.allOptions.otheroptions, function(i, object){
					/* if(MA.row.id == i || MA.col.id == i){ return; } */
					
					if(object.type == 'area' || object.type == 'field')
					{
						var element = $('#product_composite_configure_form #options_'+i+'_text');
						if($.trim(element.val()) != '' && object.price_type == 'percent')
						{
							var optionPrice = parseFloat((csvPrice * object.price) / 100);
							if(optionPrice > 0)
							{
								element.attr('price', optionPrice);
								element.closest('.field').find('.price-wrapper').attr('data-price-amount', optionPrice).text(MA.getFormattedPrice(optionPrice));
							} else {
								 optionPrice = parseFloat(element.closest('.field').find('.price-wrapper').attr('data-price-amount'));
							}
							percentOptionPrice += optionPrice;
						}
					}
					else if(_.size(object) > 0)
					{
						$.each(object, function(index, arry){
							if(arry.price_type == 'percent')
							{
								var optionPrice = parseFloat((csvPrice * arry.price) / 100);
		
								var element = $('#product_composite_configure_form  .field [name*="options['+i+']"]')[0];							
								if(element.type == 'select-one' || element.type == 'select-multiple')
								{
									var values = $(element).val();
									if(values == index || $.inArray(index, values) !== -1)
									{
										if(optionPrice > 0)
										{
											$(element).find('[value="'+index+'"]').attr('price', optionPrice);
										} else {
											optionPrice = parseFloat($(element).find('[value="'+index+'"]').attr('price'));
										}
										
										if(element.type == 'select-multiple')
										{
											var name = 'options['+i+'][]##'+index;
										} else {
											var name = $(element).attr('name');
										}
										percentOptionPrice += optionPrice;
									}
								} else {
									element = $(element).closest('.options-list').find('[value="'+index+'"]');
									if($(element).is(':radio') || $(element).is(':checkbox'))
									{
										if($(element).is(':checked'))
										{
											if($(element).is(':checkbox'))
											{
												var name = 'options['+i+'][]##'+index;
											} else {
												var name = $(element).attr('name');
											}
											
											if(optionPrice > 0)
											{
												$(element).attr('price', optionPrice);
											} else {
												optionPrice = parseFloat($(element).attr('price'));
											}
											percentOptionPrice += optionPrice;
										}
									}
								}
							}
						});
					}
				});
			}
			return originalCsvPrice;
		},
		changeInputToSelect: function(option) {
			var MA = this;
			var value = option.value;
			var optionId = option.name.replace(/[^0-9]/g,'');	
			$('#customprice_csv_select_'+optionId).remove();
			
			if (MA.row.id == optionId)
			{
				var selectBox = this.createSelectBox(this.vals.row, option);
				$(selectBox).addClass('required-entry admin__control-select');
				$(selectBox).val(value);
				this.sizeValidation($(selectBox), 'row');
				$(selectBox).change(function(){
					MA.changeBasePrice($(this));
					option.value = $(this).val();
				});
			} else if(MA.col.id == optionId) {
				var selectBox = this.createSelectBox(this.vals.col, option);
				$(selectBox).addClass('required-entry admin__control-select');
				$(selectBox).val(value);
				this.sizeValidation($(selectBox), 'col');
				$(selectBox).change(function(){
					MA.changeBasePrice($(this));
					option.value = $(this).val();
				});
			}
		},
		createSelectBox: function (arry, element){
			$(element).addClass('no-display');
			$(element).removeClass('required-entry');
			
			var id = element.id.match(/\d+/);
			var selectBox = document.createElement("select");
			selectBox.id = 'customprice_csv_select_'+id;
			selectBox.name = 'customprice_options['+id+']';
			document.getElementById(element.id).parentElement.appendChild(selectBox);
			
			var option = document.createElement("option");
			option.value = '';
			option.text = '-- Please Select --';
			selectBox.appendChild(option);
		
			if(_.size(arry) > 0)
			{
				var first = parseInt(arry[0]);
				var last = parseInt(arry[(arry.length-1)]);
				for(var i=0; i <= last; i++)
				{
					if(i >= first)
					{
						var option = document.createElement("option");
						option.value = i;
						option.text = i;
						selectBox.appendChild(option);
					}
				}
			}
			return selectBox;
		},
	};
});