/**
 * Copyright © 2016 ITORIS INC. All rights reserved.
 * See license agreement for details
 */
 
if (!Itoris) {
    var Itoris = {};
}

Itoris.ProductPriceFormula = {
    templates : {},
    initialize : function(templates, dateFormat, lastFormulaIdDb, formulaSettings, lastConditionIdDb, conditionData) {
        this.templates = templates;
        this.dateFormat = dateFormat;
        this.formulaId = lastFormulaIdDb;
        this.conditionId = lastConditionIdDb;
        this.addEvents();
        if (formulaSettings.length) {
            for (var i = 0; i < formulaSettings.length; i++) {
                this.createFormula(formulaSettings[i], conditionData);
            }
        }
        Event.observe($$('.itoris_productpriceformula_mask')[0], 'click', this.hideHelpCondition.bind(this));
        Event.observe($$('.itoris_productpriceformula_help_box')[0].select('.close')[0], 'click', this.hideHelpCondition.bind(this));
        this.saveProductButton();
		
		return this;
    },
    addEvents : function() {
        Event.observe($$('.itoris_productpriceformula_add_new_formula')[0], 'click', this.createFormula.bind(this));
    },
    createFormula : function(formulaSettings, conditionData) {
        if (formulaSettings.formula_id) {
            var formulaId = formulaSettings.formula_id;
        } else {
            this.formulaId++;
            var formulaId = this.formulaId;
        }
        if (!conditionData) {
            this.conditionId++;
            var conditionId = this.conditionId;
        }
        var checkedRunAlways = parseInt(formulaSettings.run_always) ? 'checked="checked"' : '';
		var checkedOverrideWeight = '';
        if (conditionData) {
            if (formulaId && conditionData[formulaId]) {
                var condition = conditionData[formulaId];
                for (var i = 0; i < condition.length; i++) {
                    if (condition[i].position == 1) {
                        var conditionId = condition[i].condition_id;
                        var conditionValue = condition[i].condition ? condition[i].condition : null;
                        var price = condition[i].price ? condition[i].price : null;
						var checkedOverrideWeight = parseInt(condition[i].override_weight) ? 'checked="checked"' : '';
						var weight = condition[i].weight ? condition[i].weight : null;
                    }
                }
            }
        }
        var templateData = {
            name               : formulaSettings.name ? formulaSettings.name : null,
            formula_id         : formulaId,
            condition_id       : conditionId,
            group_id           : formulaSettings.group_id ? formulaSettings.group_id : '',
            store_ids           : formulaSettings.store_ids ? formulaSettings.store_ids : '',
            position           : formulaSettings.position ? formulaSettings.position : 0,
            variables          : formulaSettings.variables ? formulaSettings.variables : '',
            condition          : conditionValue ? conditionValue.escapeHTML().replace(/\"/g, '&quot;') : null,
            price              : price,
            checked_run_always : checkedRunAlways,
			checked_override_weight : checkedOverrideWeight,
			weight				: weight
        };
        var formulaBlock = document.createElement('div');
        Element.extend(formulaBlock);
        var t = new Template(this.templates.settings);
        formulaBlock.position_condition = 1;
        formulaBlock.formula_id = formulaId;
        formulaBlock.addClassName('itoris_productpriceformula_formula_box');
        formulaBlock.update(t.evaluate(templateData));
		formulaBlock.select('.itoris_productpriceformula_weight')[0].setStyle({display: checkedOverrideWeight == '' ? 'none' : 'block'});
        if ($$('.itoris_productpriceformula_formula_box').length && $$('.itoris_productpriceformula_formula_box')[0]) {
            $$('.itoris_productpriceformula_content')[0].insertBefore(formulaBlock, $$('.itoris_productpriceformula_formula_box')[0]);
        } else {
            $$('.itoris_productpriceformula_content')[0].appendChild(formulaBlock);
        }
        Event.observe(formulaBlock.select('.itoris_productpriceformula_button_delete')[0], 'click', this.deleteRule.bind(this, formulaBlock.select('.itoris_productpriceformula_button_delete')[0]));
        $$('.itoris_productpriceformula_no_formulas')[0].hide();
        this.displayFormulaSettings(formulaSettings, formulaBlock);
        this.actionFromToCalendar(formulaId);
        Event.observe(formulaBlock.select('.itoris_productpriceformula_button_else')[0], 'click', this.addCondition.bind(this, formulaBlock));
        if (conditionData) {
            if (formulaId && conditionData[formulaId]) {
                var conditionConf = conditionData[formulaId];
                for (var i = 0; i < conditionConf.length; i++) {
                    if (conditionConf[i].position != 1) {
                        this.addCondition(formulaBlock, conditionConf[i]);
                    }
                }
            }
        }
		if (formulaSettings.disallow_criteria) {
			for(var i=0; i<formulaSettings.disallow_criteria.length; i++) {
				this.addDisallowCriteria(formulaBlock, formulaSettings.disallow_criteria[i]['formula'], formulaSettings.disallow_criteria[i]['message']);
			}
		}
        Event.observe(formulaBlock.select('.itoris_productpriceformula_button_else')[0], 'click', this.moveCheckboxRunAlways.bind(this, formulaBlock));
        this.moveCheckboxRunAlways(formulaBlock);
        var checkboxRunAlways = formulaBlock.select('.itoris_productpriceformula_run_always')[0].select('input')[0];
        Event.observe(checkboxRunAlways, 'click', this.disabledFieldCondition.bind(this, formulaBlock, checkboxRunAlways));
        this.disabledFieldCondition(formulaBlock, checkboxRunAlways);
        Event.observe(formulaBlock.select('.itoris_productpriceformula_condition_help')[0], 'click', function() {
            window.scrollTo(0, 0);
            $$('.itoris_productpriceformula_mask')[0].show();
            $$('.itoris_productpriceformula_help_box')[0].show();
            $$('.itoris_productpriceformula_help_box')[0].select('.operator')[0].show();
            $$('.itoris_productpriceformula_help_box')[0].select('.operator_for_price')[0].hide();
            $$('.itoris_productpriceformula_help_box')[0].setStyle({height: '1810px'});
        });
        Event.observe(formulaBlock.select('.itoris_productpriceformula_price_help')[0], 'click', function() {
            window.scrollTo(0, 0);
            $$('.itoris_productpriceformula_mask')[0].show();
            $$('.itoris_productpriceformula_help_box')[0].select('.operator')[0].hide();
            $$('.itoris_productpriceformula_help_box')[0].select('.operator_for_price')[0].show();
            $$('.itoris_productpriceformula_help_box')[0].show();
            $$('.itoris_productpriceformula_help_box')[0].setStyle({height: '1600px'});
        });
		var obj = this;
		Event.observe(formulaBlock.select('.itoris_productpriceformula_button_add_disallow')[0], 'click', function(){obj.addDisallowCriteria(formulaBlock,'','')} );
		Event.observe(formulaBlock.select('#customer_groups')[0], 'click', function(){
            var groups = [];
            this.select('option').each(function(option){if (option.selected && option.value) groups[groups.length] = option.value; });
            formulaBlock.select('#group_serialized')[0].value = groups.join();
            formulaBlock.select('#group_serialized')[0].setAttribute('value', groups.join());
        } );
		Event.observe(formulaBlock.select('#stores')[0], 'click', function(){
            var groups = [];
            this.select('option').each(function(option){if (option.selected && option.value) groups[groups.length] = option.value; });
            formulaBlock.select('#stores_serialized')[0].value = groups.join();
            formulaBlock.select('#stores_serialized')[0].setAttribute('value', groups.join());
        } );
        formulaBlock.select('.itoris_productpriceformula_add_variables')[0].checked = templateData.variables ? true : false;
        formulaBlock.select('.itoris_productpriceformula_variables')[0].style.display = templateData.variables ? 'inline' : 'none';
        formulaBlock.select('.itoris_productpriceformula_variables_example')[0].style.display = templateData.variables ? 'block' : 'none';
		Event.observe(formulaBlock.select('.itoris_productpriceformula_add_variables')[0], 'click', function(){
            if (formulaBlock.select('.itoris_productpriceformula_variables')[0].value) {
                if (confirm('Are you sure want to empty constants and variables?')) {
                    formulaBlock.select('.itoris_productpriceformula_variables')[0].value = '';
                    formulaBlock.select('.itoris_productpriceformula_variables')[0].style.display = 'none';
                    formulaBlock.select('.itoris_productpriceformula_variables_example')[0].style.display = 'none';
                } else {
                    formulaBlock.select('.itoris_productpriceformula_add_variables')[0].checked = true;
                }
            }
            formulaBlock.select('.itoris_productpriceformula_variables')[0].style.display = this.checked ? 'inline' : 'none';
            formulaBlock.select('.itoris_productpriceformula_variables_example')[0].style.display = this.checked ? 'block' : 'none';
        } );
    },
	addDisallowCriteria : function(formulaBlock, formula, msg) {
		var formulaId = formulaBlock.select('.itoris_productpriceformula_disallow_criteria .formula_id')[0].value;
		var tbody = formulaBlock.select('.itoris_productpriceformula_disallow_criteria_table tbody')[0];
        var maxId = 0;
        tbody.select('input').each(function(input){
            var name = input.name, id = name.substr(name.lastIndexOf('[')).replace('[', '').replace(']', '') - 0;
            if (id > maxId) maxId = id;
        });
		var criteria = new Element('tr');
		criteria.update('<td style="padding-bottom:5px"><input type="text" class="required-entry input-text formula admin__control-text formula-validate" data-form-part="product_form" name="itoris_productpriceformula_settings['+formulaId+'][disallow_formula]['+(maxId+1)+']" value="" data-validate="{\'formula-required\':true, \'formula-validate\':true}" /></td><td style="padding-bottom:5px"><input type="text" class="required-entry input-text admin__control-text" data-form-part="product_form" name="itoris_productpriceformula_settings['+formulaId+'][disallow_message]['+(maxId+1)+']" value="" /></td><td><a href="javascript://" onclick="if (confirm(\'Are you sure want to remove this criteria\')) Element.extend(this).up(\'tr\').remove()">Remove</a></td>');
		criteria.select('input')[0].value = formula ? formula : '';
		criteria.select('input')[1].value = msg ? msg : '';
		tbody.insert({bottom: criteria});
	},
    saveProductButton : function(t) {
		var _this = this;
        this.mathStr = '';
        Object.getOwnPropertyNames(Math).each(function(key){_this.mathStr += 'var '+key+' = Math.'+key+';';});
		jQuery.validator.addMethod(
			'formula-required', function (value, elm) {
				return !jQuery(elm).is(":visible") || jQuery(elm).is(":disabled") || value.replace(/\s+/g, '') != '';
			}, jQuery.mage.__('Formula is required')
		);
		jQuery.validator.addMethod(
			'formula-validate', function (value, elm) {
				try {
                    if (value != "") {
                        value = value.replace(/{((?:[^}{\"\'])*)\.qty}/g, 1).replace(/{((?:[^}{])*)}/g, 1).replace(/\[((?:[^\"\'\,\:])*)\]/g, 1);                     
                        eval(_this.mathStr + "if (" + value + ") {} ");
                        if (value.replace(/[\=|\>|\<|\!]\=/g,'').indexOf('=') > -1) return false;
                    }
				} catch (e) {
					return false;
				}
				return true;
			}, jQuery.mage.__('Formula is incorrect')
		);
		jQuery.validator.addMethod(
			'constants-validate', function (value, elm) {
				try {
                    if (value != "") {
                        value = value.replace(/{((?:[^}{\"\'])*)\.qty}/g, 1).replace(/{((?:[^}{\"\'\:])*)}/g, 1).replace(/\[((?:[^\"\'\,\:])*)\]/g, 1);
                        eval(_this.mathStr+value);
                    }
				} catch (e) {
					return false;
				}
				return true;
			}, jQuery.mage.__('Expression is incorrect')
		);		
    },
    addCondition : function(formulaBlock, conditionData) {
        var formulaBox = formulaBlock;
        var head = new Template(this.templates.head);
        var tr = document.createElement('tr');
        Element.extend(tr);
        tr.addClassName('itoris_productpriceformula_condition_line');
        tr.update(head.evaluate({}));
        formulaBox.select('.itoris_productpriceformula_condition_table')[0].appendChild(tr);
        var input = new Template(this.templates.input);
        if (conditionData.condition_id) {
            var conditionId = conditionData.condition_id;
        } else {
            this.conditionId++;
            var conditionId = this.conditionId;
        }
        if (conditionData.position) {
            var position = conditionData.position;
        } else {
            formulaBox.position_condition++;
            var position = formulaBox.position_condition;
        }
        var templateData = {
            condition_id       : conditionId,
            formula_id         : formulaBox.formula_id,
            position_condition : position,
            condition          : conditionData.condition ? conditionData.condition.escapeHTML().replace(/\"/g, '&quot;') : null,
            price              : conditionData.price ? conditionData.price : null,
			checked_override_weight : parseInt(conditionData.override_weight) ? 'checked="checked"' : '',
            weight              : conditionData.weight ? conditionData.weight : null
        };
        var trForInput = document.createElement('tr');
        trForInput.addClassName('itoris_productpriceformula_condition_input_line');
        Element.extend(trForInput);
        trForInput.update(input.evaluate(templateData));
		trForInput.select('.itoris_productpriceformula_weight')[0].setStyle({display: !conditionData.override_weight ? 'none' : 'block'});
        formulaBox.select('.itoris_productpriceformula_condition_table')[0].appendChild(trForInput);
        var removeLink = tr.select('.itoris_productpriceformula_condition_remove')[0];
        Event.observe(removeLink, 'click', this.removeCondition.bind(this, removeLink));
    },
    moveCheckboxRunAlways : function(formulaBlock) {
        var checkbox = formulaBlock.select('.itoris_productpriceformula_run_always')[0];
        var conditionLine = formulaBlock.select('.itoris_productpriceformula_condition_line');
        conditionLine[conditionLine.length - 1].select('td')[0].appendChild(checkbox);
    },
    disabledFieldCondition : function(formulaBlock, checkbox) {
        var tableTr = formulaBlock.select('.itoris_productpriceformula_condition_table')[0].select('tr');
        var lastInput = tableTr[tableTr.length - 1].select('.itoris_productpriceformula_condition')[0];
        if (checkbox.checked) {
            lastInput.disabled = 'disabled';
            formulaBlock.select('.itoris_productpriceformula_button_else')[0].hide();
        } else {
            lastInput.disabled = '';
            formulaBlock.select('.itoris_productpriceformula_button_else')[0].show();
        }
    },
    deleteRule : function(deleteButton) {
        if (confirm('Do you really want to remove the rule?')) {
            var isDelete = deleteButton.up('div').select('input[type="hidden"]')[0];
            isDelete.name = isDelete.name.replace('formula_id_db', 'formula_id_to_delete');
            deleteButton.up('div.itoris_productpriceformula_content').insert(isDelete);
            deleteButton.up('div.itoris_productpriceformula_formula_box').remove();
        }
    },
    removeCondition : function(removeLink) {
        if (confirm('Do you really want to remove the condition?')) {
            var tr = removeLink.up('tr');
            var trChange = removeLink.up('tr').previous();
            if (tr.select('.itoris_productpriceformula_run_always')[0]) {
                if (trChange.previous()) {
                    trChange.previous().select('td')[0].appendChild(tr.select('.itoris_productpriceformula_run_always')[0]);
                } else {
                    trChange.select('td')[0].appendChild(tr.select('.itoris_productpriceformula_run_always')[0]);
                }
            }
            if (tr && tr.next()) {
                tr.next().remove();
            }
            if (tr) {
                tr.remove();
            }
        }
    },
    displayFormulaSettings : function(formulaSettings, formulaBlock) {
        if (formulaSettings.status) {
            var productStatus = formulaSettings.status;
            formulaBlock.select('.itoris_productpriceformula_status')[0].value = productStatus;
        }
        if (formulaSettings.apply_to_total) {
            formulaBlock.select('.itoris_productpriceformula_apply_to_total')[0].value = formulaSettings.apply_to_total;
        }
		if (formulaBlock.select('.itoris_productpriceformula_frontend_total')[0]) {
			formulaBlock.select('.itoris_productpriceformula_apply_to_total')[0].updateFrontendTotal = function(){
				if (parseInt(formulaBlock.select('.itoris_productpriceformula_apply_to_total')[0].value) == 1) {
					formulaBlock.select('.itoris_productpriceformula_frontend_total')[0].up('div').setStyle({display: 'block'});
				} else {
					formulaBlock.select('.itoris_productpriceformula_frontend_total')[0].up('div').setStyle({display: 'none'});
					formulaSettings.frontend_total = 0;
				}
				formulaBlock.select('.itoris_productpriceformula_frontend_total')[0].value = formulaSettings.frontend_total;
			}
			Event.observe(formulaBlock.select('.itoris_productpriceformula_apply_to_total')[0], 'change', function() {
				this.updateFrontendTotal();
			});
			formulaBlock.select('.itoris_productpriceformula_apply_to_total')[0].updateFrontendTotal();
		}
        if (formulaSettings.active_from) {
            var prepareStartPublish = formulaSettings.active_from.split('-');
            var startPublish = prepareStartPublish[1] + '/' + prepareStartPublish[2] + '/' + prepareStartPublish[0];
            formulaBlock.select('.itoris_productpriceformula_active_from')[0].value = startPublish;
        }
        if (formulaSettings.active_to) {
            var prepareActiveTo = formulaSettings.active_to.split('-');
            var activeTo = prepareActiveTo[1] + '/' + prepareActiveTo[2] + '/' + prepareActiveTo[0];
            formulaBlock.select('.itoris_productpriceformula_active_to')[0].value = activeTo;
        }
        if (formulaSettings.group_id) {
            var groupSelected = formulaSettings.group_id.split(',');
            var allValueGroup = formulaBlock.select('.itoris_productpriceformula_group option');
			allValueGroup[0].selected = false;
			allValueGroup[0].removeAttribute('selected');
            for (var  i = 0; i < groupSelected.length; i++) {
                for (var j = 0; j < allValueGroup.length; j++) {
                    if (groupSelected[i] == allValueGroup[j].value) {
                        allValueGroup[j].selected = true;
                    }
                }
            }
        }
        if (formulaSettings.store_ids) {
            var storesSelected = formulaSettings.store_ids.split(',');
            var allValueStores = formulaBlock.select('.itoris_productpriceformula_stores option');
			allValueStores[0].selected = false;
			allValueStores[0].removeAttribute('selected');
            for (var  i = 0; i < storesSelected.length; i++) {
                for (var j = 0; j < allValueStores.length; j++) {
                    if (storesSelected[i] == allValueStores[j].value) {
                        allValueStores[j].selected = true;
                    }
                }
            }
        }
    },
    actionFromToCalendar: function(formula_id) {
		jQuery('#itoris_productpriceformula_active_from_'+ formula_id).calendar({dateFormat: this.dateFormat});
		jQuery('#itoris_productpriceformula_active_to_'+ formula_id).calendar({dateFormat: this.dateFormat});
    },
    hideHelpCondition : function() {
        $$('.itoris_productpriceformula_mask')[0].hide();
        $$('.itoris_productpriceformula_help_box')[0].hide();
    }
}