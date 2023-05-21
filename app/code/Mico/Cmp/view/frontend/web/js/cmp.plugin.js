/**
 * Mico Solutions EULA
 * http://www.micosolutions.com/
 * Read the license at http://www.micosolutions.com/license.txt
 *
 * Do not edit or add to this file, please refer to http://www.micosolutions.com for more information.
 *
 * @author		Pham Tri Cong <phtcong@micosolutions.com>
 * @copyright   Copyright (c) 2011 Mico Solutions (http://www.micosolutions.com)
 * @license     http://www.micosolutions.com/license.txt
 *
 */
define(["jquery","underscore","mage/template","priceUtils","priceBox"],function(c,h,f,g){var e={productId:null,priceHolderSelector:".price-box",optionsSelector:".product-custom-option",optionConfig:{},optionHandlers:{},optionTemplate:"<%- data.label %><% if (data.finalPrice.value) { %> +<%- data.finalPrice.formatted %><% } %>",controlContainer:"dd"};c.widget("mage.cmp",{options:e,priceConfig:0,priceBoxOption:0,magePriceBox:0,finalPrices:0,_init:function i(){},_create:function d(){var m="";var k="";var l=c(this.options.priceHolderSelector,c(this.options.optionsSelector).element);if(l.data("magePriceBox")&&l.priceBox("option")&&l.priceBox("option").priceConfig){this.magePriceBox=l.data("magePriceBox");this.priceBoxOption=l.priceBox("option");this.priceConfig=this.priceBoxOption.priceConfig;this.finalPrices={finalPrice:this.priceBoxOption.prices.finalPrice};this.bindEvent("cmpreload",function(n){this.cmpreload(n)}.bind(this))}},cmpreload:function j(k){this.reloadPrice(k.price)},reloadPrice:function a(k){this.finalPrices.finalPrice.amount=k;this.magePriceBox.setDefault(this.finalPrices);c(this.options.priceHolderSelector).trigger("updatePrice",0)},bindEvent:function(k,l){if(!l){return 0}jQuery("body").on(k,function(n,m){l(m)})},fireEvent:function(k,l){jQuery("body").trigger(k,l)},_log:function b(k){jQuery("#cmp-log").append(k+"<br/>")}});return c.mage.cmp});