/**
 * Copyright © 2016 ITORIS INC. All rights reserved.
 * See license agreement for details
 */
 
if (!Itoris) {
    var Itoris = {};
}

Itoris.PriceFormula = {
    customerData: {id: 0},
    variables: {},
    initialize: function(conditions, optionsData, specialPrice, dataBySku, productId, conversionRate, taxInfo, tierPrices) {
		if (!jQuery.mage || (!jQuery.mage.priceOptions && optionsData.length > 0) || !this.getPriceBox().priceBox) {
			var _this = this;
			//wait until priceOptions widget is loaded and initialized
			setTimeout(function(){_this.initialize(conditions, optionsData, specialPrice, dataBySku, productId, conversionRate, taxInfo, tierPrices);}, 200);
			return;
		}
        this.conditions = conditions;
        this.optionsData = optionsData;
		this.specialPrice = specialPrice;
		this.dataBySku = dataBySku;
        this.tierPrices = tierPrices;
		this.productId = productId;
		this.priceFormulaCurrencyConversionRate = conversionRate;
        this.priceFormulaTaxInfo = taxInfo;
		this.configurableConfig = {};
        this.optionsConfig = jQuery('#product_addtocart_form').data('magePriceOptions');
	
		if (!this.conditions.length /*|| !window['optionsPrice' + productId] || !window['optionsPrice' + productId].reload*/) return this;
		
		//if (!window['optionsPrice' + productId].productId) window['optionsPrice' + productId] = new Product.OptionsPrice(window.priceFormulaDefaultProductJsonConfig);
		
		for(var i=0; i<this.optionsData.length; i++) {
			if (this.optionsData[i].values) {
				for(var key in this.optionsData[i].values) {
					var option = this.optionsData[i].values[key];
					if (option.sku) this.dataBySku['{'+option.sku+'}'] = {type: this.optionsData[i].type, id: option.id};
				}
			} else if (this.optionsData[i].sku) {
				this.dataBySku['{'+this.optionsData[i].sku+'}'] = {type: this.optionsData[i].type, id: this.optionsData[i].id};
			}
		}
		
        this.getConfigurableOptionsConfig();

        var curObj = this;
        if ($('qty')) Event.observe($('qty'), 'change', function(){curObj.getPriceBox().trigger('updatePrice');});
        if ($('itoris_dynamicoptions_qty')) Event.observe($('itoris_dynamicoptions_qty'), 'change', function(){
			$('qty').value = $('itoris_dynamicoptions_qty').value;
			curObj.getPriceBox().trigger('updatePrice');
		});

		this.getPriceBox().on('reloadPrice', function(ev){
            if (ev.target && ev.target.className.indexOf('tier') >-1 ) return;
            
            //updating old price first if special price exists
            var oldPriceObj = jQuery('[data-role="priceBox"][data-product-id="'+curObj.productId+'"] [data-price-type="oldPrice"] .price');
            if (oldPriceObj[0]) curObj.updatePriceInPriceBoxObject(oldPriceObj, oldPriceObj);
            
            //updating regular price then
			var basePriceObj = jQuery('#itoris_dynamicproductoptions_popup_price [data-price-type="basePrice"] .price');
            if (!basePriceObj[0]) basePriceObj = jQuery('[data-role="priceBox"][data-product-id="'+curObj.productId+'"] [data-price-type="basePrice"] .price');
			var finalPriceObj = jQuery('#itoris_dynamicproductoptions_popup_price [data-price-type="finalPrice"] .price');
			if (!finalPriceObj[0]) finalPriceObj = jQuery('[data-role="priceBox"][data-product-id="'+curObj.productId+'"] [data-price-type="finalPrice"] .price');
            
			if (!basePriceObj[0]) basePriceObj[0] = finalPriceObj[0];
			
            curObj.updatePriceInPriceBoxObject(basePriceObj, finalPriceObj);

		});
		this.getPriceBox().trigger('updatePrice');
		
		jQuery('#product-addtocart-button').on('click', function(event){
			var isError = false;
			if (window.itorisPriceFormulaErrors) {
				window.itorisPriceFormulaErrors.each(function(criteria){
					if (isError) return;
					var formula = curObj.parseCondition(criteria.formula, 0, curObj.configuredPrice / curObj.priceFormulaCurrencyConversionRate, curObj.initialPrice, curObj.finalPrice / curObj.priceFormulaCurrencyConversionRate);

					eval("if ("+formula+") {isError = true}");
					if (isError) {
						event.preventDefault();
                        jQuery.each(Itoris.PriceFormula.variables, function(key, val){
                            criteria.message = criteria.message.replace('{'+key+'}', val);
                        });
						alert(criteria.message);
					}
				});
			}
		});
		
		return this;
    },
    updatePriceInPriceBoxObject: function(basePriceObj, finalPriceObj){
        var curObj = this;
        
        var baseInitialAmount = basePriceObj[0] ? jQuery(basePriceObj[0]).closest('[data-price-type="basePrice"]').attr('data-price-amount') : 0;
        var finalInitialAmount = finalPriceObj[0] ? jQuery(finalPriceObj[0]).closest('[data-price-type="finalPrice"]').attr('data-price-amount') : 0;

        var taxRate = curObj.priceFormulaTaxInfo.priceAlreadyIncludesTax ? 1 / curObj.priceFormulaTaxInfo.taxRate : curObj.priceFormulaTaxInfo.taxRate;
    
        var decimalSymbol = curObj.getPriceBox().data('magePriceBox').options.priceConfig.priceFormat.decimalSymbol;
        var price = (baseInitialAmount ? basePriceObj[0] : finalPriceObj[0]).innerHTML;
        price = price.replace(/[^0-9]+/g,"") / (price.indexOf(decimalSymbol) > -1 ? 100 : 1);

        curObj.initialPrice = baseInitialAmount ? baseInitialAmount : finalInitialAmount;
        
        if (!curObj.priceFormulaTaxInfo.priceAlreadyIncludesTax && curObj.priceFormulaTaxInfo.displayPriceMode == 2) {
            price /= taxRate;
            curObj.initialPrice /= taxRate;
        }
        
        curObj.tierPrice = curObj.initialPrice;
        if ($('qty').value - 0 > 0) {
            for(var i=0; i<curObj.tierPrices.length; i++) {
                if (curObj.tierPrices[i].qty <= $('qty').value - 0) curObj.tierPrice = curObj.tierPrices[i].price;
            }
        }

        var productCurrentPrice = price;
        curObj.configuredPrice = price;
        curObj.variables = {};
        curObj.selectorCache = {};

        var priceForCompare = 0, multiplyByQty = false;
        for (var i = 0; i < this.conditions.length; i++) {
            curObj.parseVariables(this.conditions[i].variables, 0, price / curObj.priceFormulaCurrencyConversionRate, curObj.initialPrice, productCurrentPrice / curObj.priceFormulaCurrencyConversionRate);
            var conditionData = this.conditions[i].conditions;
            this.isRightCondition = false;
            for (var j = 0; j < conditionData.length; j++) {
                if (!this.isRightCondition) {
                    var condition = curObj.parseCondition(conditionData[j].condition, 0, price / curObj.priceFormulaCurrencyConversionRate, curObj.initialPrice, productCurrentPrice / curObj.priceFormulaCurrencyConversionRate);
                    var priceCondition = curObj.parseCondition(conditionData[j].price, 0, price / curObj.priceFormulaCurrencyConversionRate, curObj.initialPrice, productCurrentPrice / curObj.priceFormulaCurrencyConversionRate);								

                    eval("if ("+condition+") {this.isRightCondition = true; priceForCompare ="+ priceCondition +";}");
                    
                    if (priceForCompare > 0) productCurrentPrice = priceForCompare * curObj.priceFormulaCurrencyConversionRate;
                    multiplyByQty = !!parseInt(conditionData[j].apply_to_total) && !parseInt(conditionData[j].frontend_total);
                } else {
                    continue;
                }
            }
        }

        curObj.finalPrice = priceForCompare > 0 ? priceForCompare * curObj.priceFormulaCurrencyConversionRate / (multiplyByQty && parseFloat($('qty').value) > 0 ? $('qty').value : 1) : price;
        //curObj.finalPrice = curObj.finalPrice.toFixed(2);
        
        //var tierObj = jQuery('.product-info-main .prices-tier');
        //if (tierObj[0]) tierObj.css({display: priceForCompare > 0 ? 'none' : 'block'});

		var bssPrice = curObj.finalPrice.toFixed(2);
		var bssTotalTax = (bssPrice * (taxRate - 1)).toFixed(2);
		var bssPriceIncl = (parseFloat(bssPrice) + parseFloat(bssTotalTax)).toFixed(2);
		jQuery('.total_excl_btw-value').text('€ ' + bssPrice);
		jQuery('.product_btw-value').text('€ ' + bssTotalTax);
		jQuery('.total_incl_btw-value').text('€ ' + bssPriceIncl);
        
        if (priceForCompare > 0) {
            finalPriceObj.text(window._priceUtils.formatPrice(curObj.finalPrice * (!curObj.priceFormulaTaxInfo.priceAlreadyIncludesTax && curObj.priceFormulaTaxInfo.displayPriceMode > 1 ? taxRate : 1)));
            if (basePriceObj[0] && basePriceObj[0] !== finalPriceObj[0]) basePriceObj.text(window._priceUtils.formatPrice(curObj.finalPrice * (!curObj.priceFormulaTaxInfo.priceAlreadyIncludesTax && curObj.priceFormulaTaxInfo.displayPriceMode != 3 || curObj.priceFormulaTaxInfo.priceAlreadyIncludesTax && curObj.priceFormulaTaxInfo.displayPriceMode != 2 ? taxRate : 1)));
        }
    },
    getConfigurableOptionsConfig: function(){
        if (this.configurableConfig.length) return this.configurableConfig;
        
		if (window.spConfig && spConfig.config && spConfig.config.attributes) this.configurableConfig = spConfig.config.attributes;
        else try {
            this.configurableConfig = jQuery('[data-role=swatch-options]').data('mageSwatchRenderer').options.jsonConfig.attributes;
        } catch(e){
            try {
                this.configurableConfig = jQuery('#product_addtocart_form').data('mageConfigurable').options.spConfig.attributes;
            } catch (e) { }
        }        
    },
    parseVariables: function(string, def_value, confPrice, initialPrice, productPrice) {
        if (!string || string === null) return;
        var _vars = string.replace(/\n|\t|\r/g, '').split('var ');
        var objRegex = new RegExp(/\{.*\}/g);
        for(var i=0; i<_vars.length; i++) {
            var _var = jQuery.trim(_vars[i]);
            if (!_var) continue; else var pos = _var.indexOf('=');
            if (pos == -1) continue; else var varName = jQuery.trim(_var.substr(0, pos));
            if (!varName) continue; else var varStr = _varStr = jQuery.trim(_var.substr(pos + 1));
            while(res = objRegex.exec(_varStr)) {
                if (res[0].indexOf(':') == -1) continue;
                try {
                    var obj = JSON.parse(res[0]);
                } catch (e) {
                    continue;
                }
                var rnd = Math.random();
                this.variables[rnd] = obj;
                varStr = varStr.replace(res[0], 'Itoris.PriceFormula.variables["'+rnd+'"]');
            }
            this.variables[varName] = eval(this.parseCondition(varStr, def_value, confPrice, initialPrice, productPrice));
        }        
    },
	parseCondition: function(string, def_value, confPrice, initialPrice, productPrice) {
        var that = this;
        this.getConfigurableOptionsConfig();
		if (!string.replace) return string;
        for (key in this.variables) {
			try {
                string = string.replace(new RegExp('{'+key+'}', "gi"), 'Itoris.PriceFormula.variables["'+key+'"]');
			} catch(e) {}
        }
        jQuery('.swatch-attribute').each(function(){
            var attributeCode = jQuery.trim(jQuery(this).attr('attribute-code'));
            var attributeValue = jQuery.trim(jQuery(this).find('.swatch-option.selected').attr('option-label'));
            that.dataBySku['{'+attributeCode+'}'] = {value: attributeValue};
        });
		for (key in this.dataBySku) {
            if (key == '{tier_price}') continue;
			var value = def_value, oqty = 0, oprice = 0;
			if (this.dataBySku[key].value) value = this.dataBySku[key].value;
            for (var i = 0; i < this.optionsData.length; i++) {
                if (this.optionsData[i].type == 'field' || this.optionsData[i].type == 'area') {
                    if (this.dataBySku[key] && this.optionsData[i].id == this.dataBySku[key].id) {
                        if (this.dataBySku[key].type && this.optionsData[i].type == this.dataBySku[key].type) {
                            var textOptionId = 'options_' + this.optionsData[i].id + '_text';
                            if (!this.selectorCache[textOptionId]) this.selectorCache[textOptionId] = $(textOptionId);
                            var input = this.selectorCache[textOptionId]; 
                            if (input && input.value) value = input.value;
                        }
                    }
                }
				if (this.optionsData[i].type == 'checkbox' || this.optionsData[i].type == 'radio') {
					if (this.dataBySku[key] && this.dataBySku[key].type && this.optionsData[i].type == this.dataBySku[key].type) {
						var optionId = 'options-' + this.optionsData[i].id + '-list';
                        if (!this.selectorCache[optionId]) this.selectorCache[optionId] = $(optionId).select('input:checked');
                        var input = this.selectorCache[optionId]; 
						for (var j = 0; j < input.length; j++) {
							if (input[j].checked) {
								var subOptionId = input[j].value;
								var values = this.optionsData[i].values;
								var subOptionById = values[subOptionId];
								var skuSubOption = subOptionById ? subOptionById.sku : '';
								var skuSubOptionStr = '{' + skuSubOption + '}';
								if (key == skuSubOptionStr) {
                                    if (this.optionsConfig && this.optionsConfig.options && this.optionsConfig.options.optionConfig
                                        && this.optionsConfig.options.optionConfig[this.optionsData[i].id] && this.optionsConfig.options.optionConfig[this.optionsData[i].id][subOptionById.id]) {
                                            value = this.optionsConfig.options.optionConfig[this.optionsData[i].id][subOptionById.id].name;
                                    } else value = jQuery.trim($(optionId).select('label[for='+input[j].id+']')[0].innerText);
                                    if (!this.selectorCache[input[j].id+'_qty']) {
                                        this.selectorCache[input[j].id+'_qty'] = input[j].up().select('.option-qty')[0];
                                        if (!this.selectorCache[input[j].id+'_qty']) this.selectorCache[input[j].id+'_qty'] = {value: 0};
                                    }
                                    _oqty = this.selectorCache[input[j].id+'_qty'];
                                    if (_oqty && _oqty.value - 0 >= 1) oqty = _oqty.value - 0;
                                    if (subOptionById && subOptionById.price - 0) oprice = subOptionById.price - 0;
								}
							}
						}
					}
				}
				if (this.optionsData[i].type == 'drop_down') {
					if (this.dataBySku[key] && this.dataBySku[key].type && this.optionsData[i].type == this.dataBySku[key].type) {
						var selectId = 'select_' + this.optionsData[i].id;
                        if (!this.selectorCache[selectId]) this.selectorCache[selectId] = $(selectId);
                        var select = this.selectorCache[selectId];
						var selectValue = select.value;
						if (selectValue) {
							var values = this.optionsData[i].values;
							var subOptionById = values[selectValue];
							var skuSubOption = subOptionById ? subOptionById.sku : '';
							var skuSubOptionStr = '{' + skuSubOption + '}';
							if (key == skuSubOptionStr) {								
                                if (this.optionsConfig && this.optionsConfig.options && this.optionsConfig.options.optionConfig
                                    && this.optionsConfig.options.optionConfig[this.optionsData[i].id] && this.optionsConfig.options.optionConfig[this.optionsData[i].id][subOptionById.id]) {
                                        value = this.optionsConfig.options.optionConfig[this.optionsData[i].id][subOptionById.id].name;
                                } else value = jQuery.trim(select.options[select.selectedIndex].text);
                                if (!this.selectorCache[select.id+'_qty']) {
                                    this.selectorCache[select.id+'_qty'] = select.up().select('.option-qty')[0];
                                    if (!this.selectorCache[select.id+'_qty']) this.selectorCache[select.id+'_qty'] = {value: 0};
                                }
								_oqty = this.selectorCache[select.id+'_qty'];
								if (_oqty && _oqty.value - 0 >= 1) oqty = _oqty.value - 0;
								if (subOptionById && subOptionById.price - 0) oprice = subOptionById.price - 0;
							}
						}
					}
				}
				if (this.optionsData[i].type == 'multiple') {
					if (this.dataBySku[key] && this.dataBySku[key].type && this.optionsData[i].type == this.dataBySku[key].type) {
						var selectId = 'select_' + this.optionsData[i].id;
                        if (!this.selectorCache[selectId]) this.selectorCache[selectId] = $(selectId).select('option:selected');
                        var options = this.selectorCache[selectId];
						for (var j = 0; j < options.length; j++) {
							if (options[j].selected) {
								var subOptionId = options[j].value;
								var values = this.optionsData[i].values;
								var subOptionById = values[subOptionId];
								var skuSubOption = subOptionById ? subOptionById.sku : '';
								var skuSubOptionStr = '{' + skuSubOption + '}';
								if (key == skuSubOptionStr) {
									value = options[j].innerHTML;
									if (subOptionById && subOptionById.price - 0) oprice = subOptionById.price - 0;
								}
							}
						}
					}
				}
            }
            value = _value = (value.replace ? value.replace(/^\s+|\s+$/g,"") : value);
			if (!isNaN(parseFloat(value)) && isFinite(value)) {} else value = "'"+value+"'";
			try {
                key = key.replace('$', '\\$');
				string = string.replace(new RegExp(key, "gi"), value);
				string = string.replace(new RegExp(key.replace('}','.qty}'), "gi"), oqty);
				string = string.replace(new RegExp(key.replace('}','.price}'), "gi"), oprice);
				string = string.replace(new RegExp(key.replace('}','.length}'), "gi"), _value.length ? _value.length : 0);
			} catch(e) {}
		}
		string = string.replace(new RegExp('{configured_price}', "gi"), confPrice);
		string = string.replace(new RegExp('{initial_price}', "gi"), initialPrice);
		string = string.replace(new RegExp('{special_price}', "gi"), this.specialPrice / this.priceFormulaCurrencyConversionRate);
		string = string.replace(new RegExp('{price}', "gi"), productPrice);
		string = string.replace(new RegExp('{tier_price}', "gi"), this.tierPrice);
        string = string.replace(new RegExp('{customer_id}', "gi"), this.customerData.id ? this.customerData.id : 0);
		
		var crossChecks = [], configurablePid = 0;
		for (key in this.configurableConfig) {
			var data = this.configurableConfig[key];
            if (typeof this.configurableConfig[key] != 'object') continue;
            var value = "''", dd = $$('#attribute'+key+', [name="super_attribute['+data.id+']"]')[0];
			var _crossChecks = [];
			if (dd) {
				for(var i=0; i<data.options.length; i++) {
					var option = data.options[i];
					if (dd.value == option.id) {
						value = "'" + option.label.replace(/\'/gi, '\\\'') + "'";
						_crossChecks = option.allowedProducts ? option.allowedProducts : option.products;
						break;
					}
				}
			}
			crossChecks[crossChecks.length] = _crossChecks;
			string = string.replace(new RegExp('{'+data.code+'}', "gi"), value);
		}
		if (crossChecks.length > 0) {
			for(var o = 0; o < crossChecks[0].length; o ++) {
				var pid = crossChecks[0][o], isPidValid = true;
				for(var i=1; i<crossChecks.length; i++) {
					isPidValid = crossChecks[i].indexOf(pid) > -1;
					if (!isPidValid) break;
				}
				if (isPidValid && pid) {
					configurablePid = pid;
					break;
				}
			}
		}
		string = string.replace(new RegExp('{configurable_pid}', "gi"), configurablePid);
		
        if (!this.selectorCache['qty']) this.selectorCache['qty'] = $('qty');        
		if (this.selectorCache['qty']) {
			var qty = this.selectorCache['qty'].value == 0 ? 1 : this.selectorCache['qty'].value;
			string = string.replace(new RegExp('{qty}', "gi"), qty);
		}
        
        var ignoreText = [], ignoreTextRegex = new RegExp(/[\"\'](.*?)[\"\']/g);
        while(res = ignoreTextRegex.exec(string)) ignoreText.push(res);
        ignoreText.each(function(match, i){string = string.replace(match[0], '###'+i+'###'); });         
		Object.getOwnPropertyNames(Math).each(function(key){ string = string.replace(new RegExp(key, "g"), 'Math.'+key); });
        ignoreText.each(function(match, i){string = string.replace('###'+i+'###', match[0]); });
		string = string.replace(/(\r\n|\n|\r)/gm,"").replace(/({([^}:]+)})/g, 0);

		return string;
	},
	getPriceBox: function(){
		return jQuery('#itoris_dynamicproductoptions_popup_price [data-role="priceBox"], [data-role="priceBox"][data-product-id="'+this.productId+'"]');
	}
}