define([
    'jquery',
    'underscore',
	'Magento_Catalog/js/price-utils',
	'jquery/validate'
], function ($, _, priceUtils) {
    'use strict';

	$.widget('mage.customPricePro',{
		allOptions: {},
		activeCsv: {},
		csvElement: '',
		_create: function() {
			$.extend(this, this.options.optionConfig);
			$.extend(this.allOptions, this.options.optionConfig);
			this.csvElement = $('[name="options['+this.csvoption+']"].product-custom-option');
			if(this.csvElement.length)
			{
				var activeElement = $(this.csvElement[0]);

				if(activeElement.is('select'))
				{
					if(activeElement.find('option:eq(0)').val() == "")
					{
						activeElement.find('option:eq(0)').remove();
					}
					//activeElement.find('option:eq(0)').prop('selected', true).trigger('change');
				} else if(activeElement.is(':radio')) {
					activeElement.prop('checked', true).trigger('click');
				}

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
					$(this).change(function(){
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
			this.updatePrice(0,false,false);
			this.loadDataProcess();
			this.productConfigureMode();
		},
		loadDataProcess: function () {
			var MA = this;
			var error = this.getErrorMsg();

			if(MA.maxMinError == 1){
				MA.showErrorMsg($('#options_'+MA.row.id+'_text'), error.minRowErrorMsg);
				MA.showErrorMsg($('#options_'+MA.col.id+'_text'), error.maxColErrorMsg);
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
						if(MA.otheroptions[b][option.value] && MA.otheroptions[b][option.value].price_type == 'percent')
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
			this.productConfigureMode();
		},
		changeInputToSelect: function(option) {
			var MA = this;
			var optionId = option.name.replace(/[^0-9]/g,'');
			$('#customprice_csv_select_'+optionId).remove();

			if (MA.row.id == optionId)
			{
				setTimeout(function(){
					$(option).trigger('keyup');

					var selectBox = MA.createSelectBox(MA.vals.row, option);
					if(window.productConfigureMode == "configure")
					{
						$(selectBox).val(option.value);
					}
					$(selectBox).addClass('required-entry');
					MA.sizeValidation($(selectBox), 'row');
					$(selectBox).change(function(){
						MA.changeBasePrice();
						option.value = $(this).val();
						$(option).trigger('keyup').change();
					});
				}, 2000);
			} else if(MA.col.id == optionId) {
				setTimeout(function(){
					$(option).trigger('keyup');

					var selectBox = MA.createSelectBox(MA.vals.col, option);
					if(window.productConfigureMode == "configure")
					{
						$(selectBox).val(option.value);
					}
					$(selectBox).addClass('required-entry');
					MA.sizeValidation($(selectBox), 'col');
					$(selectBox).change(function(){
						MA.changeBasePrice();
						option.value = $(this).val();
						$(option).trigger('keyup').change();
					});
				}, 2000);
			}
		},
		getUnit: function (name){
			if($.trim(this.unit) != '')
			{
				name = name +' '+ this.unit;
			}
			return name;
		},
		createSelectBox: function (arry, element){
			var MA = this;
			var unit = MA.unit;

			$(element).addClass('no-display');
			$(element).removeClass('required-entry');

			var id = element.id.match(/\d+/);
			var selectBox = document.createElement("select");
			selectBox.id = 'customprice_csv_select_'+id;
			selectBox.name = 'customprice_options['+id+']';
			var parentElm = document.getElementById(element.id).parentElement;
			//$(parentElm).prepend(selectBox);
			$(parentElm).append(selectBox);

			var option = document.createElement("option");
			option.value = '';
			option.text = '-- Please Select --';
			selectBox.appendChild(option);
			if(_.size(arry) > 0)
			{
				if (!MA.alldropdown || MA.alldropdown == '0'){
					$.each(arry, function( index, value ) {
					  if(arry[i] !='undefined'){
							var option = document.createElement("option");
							option.value = value;
							if(typeof unit !== 'undefined' && unit != null) {
								option.text = value +' '+ unit;
							}else{
								option.text = value;
							}
							selectBox.appendChild(option);
						}
					});
				}else{
					var first = parseInt(arry[0]);
					var last = parseInt(arry[(arry.length-1)]);
					for(var i=0; i <= last; i++)
					{
						if(i >= first)
						{
							var option = document.createElement("option");
							option.value = i;
							option.text = this.getUnit(i);
							selectBox.appendChild(option);
						}
					}
				}
			}
			return selectBox;
		},
		setValidationOnElement: function(option, elmType) {
			var MA = this;
			var error = MA.getErrorMsg();

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
					return $.mage.__(error.minRowErrorMsg);
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
					return $.mage.__(error.maxColErrorMsg);
				});
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
						return true;
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
		getPriceMin: function(object, price, row) {
			price = parseFloat(price);
			var optionsConfig = this;
            if(this.options.csvType == 'dimensional'){
                var max = optionsConfig.row.max;
                var min = optionsConfig.row.min;
            }else{
                if(row == 'row')
                {
                    var max = optionsConfig.row.max;
                    var min = optionsConfig.row.min;
                } else {
                    var max = optionsConfig.col.max;
                    var min = optionsConfig.col.min;
                }
            }

			if(!isNaN(price))
			{
				while (!object.hasOwnProperty(price))
				{
					if (optionsConfig.pricemin) {
						price = price - 1;
						if(object.hasOwnProperty(price))
						{
							break;
						} else if ((price < 1) || (price < min)) {
							break
						}
					} else {
						price = price + 1;
						if(object.hasOwnProperty(price))
						{
							break;
						} else if(price > max) {
							break;
						}
					}
				}
			} else {
				return 0;
			}
			return price;
		},
		getErrorMsg: function() {
			var messages = new Object();

			var minRowValue = this.row.min;
			var maxRowValue = this.row.max;

			var minColValue = this.col.min;
			var maxColValue = this.col.max;

			var minErrorMsg = this.alert.min;
			var maxErrorMsg = this.alert.max;

			messages.notfound = this.alert.notfound;
			messages.minRowErrorMsg = minErrorMsg.replace(/{min}/g, minRowValue+this.unit).replace(/{max}/g, maxRowValue+this.unit);
			messages.maxColErrorMsg = minErrorMsg.replace(/{min}/g, minColValue+this.unit).replace(/{max}/g, maxColValue+this.unit);

			return messages;
		},
		changeBasePrice: function ()
		{
			if(this.row && !this.col)
			{
				var rowElm = this.getField(this.row.id);
				if(!rowElm.val())
				{
					this.updatePrice(0,false,false);
					return false;
				}
			} else if(!this.row && this.col) {
				var colElm = this.getField(this.col.id);
				if(!colElm.val())
				{
					this.updatePrice(0,false,false);
					return false;
				}
			} else if(this.row && this.col) {
				var rowElm = this.getField(this.row.id);
				var colElm = this.getField(this.col.id);
				if(!rowElm.val() || !colElm.val())
				{
					this.updatePrice(0,false,false);
					return false;
				}
			} else {
				this.updatePrice(0,false,false);
				return false;
			}

			var csvPrice = this.changePercentPrice();
			/* Claculate taxrate and update to csvprice */
			if(parseFloat(this.allOptions.taxrate) > 0)
			{
				csvPrice = csvPrice+(csvPrice*parseFloat(this.allOptions.taxrate)/100);
			}
			this.updatePrice(csvPrice,false,true);
		},
		updatePrice: function (csvPrice,optionName,removeBasePrice)
		{
			var finalPrice = csvPrice;
			var priceExcludingTax = this.changePercentPrice();
			var oldPrice = this.getOldPrice(csvPrice,removeBasePrice);
			if(parseInt(this.allOptions.includebaseprice) == 0)
			{
				if(removeBasePrice)
				{
					var specialPrice = parseFloat(this.specialprice);
					var basePrice = parseFloat(this.price);
					finalPrice = ((-specialPrice) + csvPrice);

				}
			}
			optionName = optionName ? optionName : 'customCsvPrice';
			var changes = $.parseJSON('{"'+optionName+'":{"finalPrice":{"amount":'+finalPrice+'},"basePrice":{"amount":'+priceExcludingTax+'},"oldPrice":{"amount":'+oldPrice+'}}}');

			/* Magento add there option price after we add our custom price */
			setTimeout(function(){
				$('[data-role="priceBox"]').trigger('updatePrice', changes);
			}, 150);
		},
		getOldPrice: function(csvPrice,removeBasePrice) {
			if(csvPrice == 0)
			{
				return 0;
			}

			var basePrice = parseFloat(this.price);
			var specialPrice = parseFloat(this.specialprice);
			var diffPrice = parseFloat(basePrice - specialPrice);
			var oldPrice = csvPrice;
			if(diffPrice > 0)
			{
				var discountPrice = parseFloat((diffPrice/basePrice)*100);

				if(parseInt(this.allOptions.includebaseprice) == 1)
				{
					csvPrice = parseFloat(csvPrice)+specialPrice;
				}
				oldPrice = ((csvPrice/discountPrice)*100);
				oldPrice = oldPrice - csvPrice;
			}

			if(removeBasePrice)
			{
				oldPrice =  ((-basePrice) + oldPrice);
			}
			return oldPrice;
		},
		changePercentPrice: function() {
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
					//if(MA.row.id == i || MA.col.id == i){ return; }

					if(object.type == 'area' || object.type == 'field')
					{
						var element = $('#options_'+i+'_text');
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
							MA.updatePrice(optionPrice, element.attr('name'), false);
							percentOptionPrice += optionPrice;
						}
					}
					else if(_.size(object) > 0)
					{
						$.each(object, function(index, arry){
							if(arry.price_type == 'percent')
							{
								var optionPrice = parseFloat((csvPrice * arry.price) / 100);

								var element = $('.field [name*="options['+i+']"]')[0];
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
										MA.updatePrice(optionPrice, name, false);
										percentOptionPrice += optionPrice;

										/* if(element.type == 'select-multiple')
										{
											$(element).find('[value="'+index+'"]').text(arry.title +' + '+MA.getFormattedPrice(optionPrice));
										} */
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
											MA.updatePrice(optionPrice, name, false);
											percentOptionPrice += optionPrice;
											//$(element).attr('price', optionPrice);
											//element.closest('.field').find('.price-wrapper').attr('data-price-amount', optionPrice).text(MA.getFormattedPrice(optionPrice));
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
		getPrice: function() {
			var optionsConfig = this;

			if(parseInt(optionsConfig.select))
			{
				var rowElement = $('#customprice_csv_select_'+optionsConfig.row.id);
				var colElement = $('#customprice_csv_select_'+optionsConfig.col.id);
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
				this.showErrorMsg($(rowElement), optionsConfig.alert.notfound);
				return 0;
			}

			if ((col <= 0) || (col == 0))
			{
				this.showErrorMsg($(colElement), optionsConfig.alert.notfound);
				return 0;
			}

			if(!optionsConfig.pricesheet.hasOwnProperty(row))
			{
				row = this.getPriceMin(optionsConfig.pricesheet, row, 'row');

				if(!optionsConfig.pricesheet.hasOwnProperty(row))
				{
					this.showErrorMsg($(rowElement), optionsConfig.alert.notfound);
					return 0;
				}
			}

			if(!optionsConfig.pricesheet[row].hasOwnProperty(col))
			{
				col = this.getPriceMin(optionsConfig.pricesheet[row], col , 'col');

				if(!optionsConfig.pricesheet[row].hasOwnProperty(col))
				{
					this.showErrorMsg($(colElement), optionsConfig.alert.notfound);
					return 0;
				}
			}

			var csvPrice = parseFloat(optionsConfig.pricesheet[row][col] * 1);
			if (isNaN(csvPrice) || csvPrice == 0) {
				this.showErrorMsg($(rowElement), optionsConfig.alert.notfound);
				this.showErrorMsg($(colElement), optionsConfig.alert.notfound);
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
		getMicoNumber: function(c) {
			if (!c) {
				return 0
			}
			var b = 0;
			if (c.attr('type') == "select-one" || c.attr('type') == "select-multiple") {
				var a = c.selectedIndex;
				b = a >= 0 ? c.options[a].innerHTML : 0
			} else {
				b = c.val();
			} if (!b) {
				return 0
			}
			b = parseFloat(b) * 1;
			if (isNaN(b)) {
				return 0
			}
			return b
		},
		validateElement: function (element, elmType){
			var MA = this;
			var error = this.getErrorMsg();
			var colmin = this.col.min;
			var colmax = this.col.max;
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
			else if(elmType == 'col')
			{
				if (colmin > value || colmax < value) {
					MA.showErrorMsg($(element), error.maxColErrorMsg);
					return false;
				} else {
					MA.showErrorMsg($(element), false);
					return true;
				}
			}
		},
		showErrorMsg: function(element, msg, doNull){

			var id = $(element).attr('id').match(/\d+/);
			if($(element).parent().find('div.mage-error').length && msg != false)
			{

				$(element).parent().find('div.mage-error').html(msg).show();
				if(doNull){ $(element).val(''); }

			} else if(msg != false) {
				msg = '<div for="options_'+id+'_text" generated="true" class="custom mage-error" id="options_'+id+'_text-error">'+msg+'</div>';

				$(element).after(msg).show();
				if(doNull){ $(element).val(''); }
			}

			if(msg == false)
			{
				$(element).parent().find('div.mage-error').hide();
			}
		},
		getField: function(id)
		{
			return $('[id^="options_'+id+'"].product-custom-option');
		},
		getFormattedPrice: function(price) {
			return priceUtils.formatPrice(price, this.productPriceFormat);
		},
		productConfigureMode: function() {
			if(window.productConfigureMode == "configure")
			{
				var MA = this;
				setTimeout(function(){
					if(parseInt(MA.select))
					{
						var rowElement = $('#customprice_csv_select_'+MA.row.id);
						var colElement = $('#customprice_csv_select_'+MA.col.id);
					} else {
						var rowElement = $(MA.getField(MA.row.id));
						var colElement = $(MA.getField(MA.col.id));
					}
					rowElement.change();
					colElement.change();

					if(parseInt($('#qty').val()) >1 || $('#qty').val() == "")
					{
						$('#qty').val(parseInt(window.itemQty));
					}

				}, 2000);
			}
		},
		onlyNumberValueValidation: function() {
			$("input.custom-validate-digits").keydown(function (event)
			{
				if (event.shiftKey == true) {
					event.preventDefault();
				}
				if ((event.keyCode >= 48 && event.keyCode <= 57) ||
					(event.keyCode >= 96 && event.keyCode <= 105) ||
					event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
					event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190 || event.keyCode == 190) {
				} else {
					event.preventDefault();
				}
			});
		},
	});
	return $.mage.customPricePro;
});
