/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * https://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2023 Anowave (https://www.anowave.com/)
 * @license  	https://www.anowave.com/license-agreement/
 */

define(['jquery'], function ($) 
{
    'use strict';
    
    return function (widget) 
    {
    	$.widget('mage.configurable', widget, 
    	{
    		_setupChangeEvents: function()
    		{
    			var context = this;
    			
    			this._super();
    			
    			if ('undefined' === typeof AEC)
    			{
    				return this;
    			}
    			
    			if ('undefined' === typeof AEC.Const)
    			{
    				return this;
    			}
    			
    			$.each(this.options.settings, $.proxy(function (index, element) 
    			{
                    $(element).on('change.ec', $.proxy(function()
                    {
                    	(function(callback)
            			{
            				if (AEC.Const.COOKIE_DIRECTIVE)
            				{
            					AEC.CookieConsent.queue(callback).process();
            				}
            				else 
            				{
            					callback.apply(window,[]);
            				}
            			})
            			(
            				(function(context)
            				{
            					if (context && 'undefined' !== typeof context.simpleProduct)
            					{
	            					var simple = {}, key = context.simpleProduct.toString();
	            					
	            					if ('undefined' === typeof AEC.CONFIGURABLE_SIMPLES)
	            					{
	            						return function()
	            						{
	            							console.log('Skipping view_item event.');
	            						};
	            					}
	            					
	            					if (AEC.CONFIGURABLE_SIMPLES.hasOwnProperty(key))
	            					{
	            						simple = AEC.CONFIGURABLE_SIMPLES[key];
	            					}
	            					
	            					return function()
	            					{
	            						let list = 'Configurable variants', item = 
	            						{
	            							item_id: 			simple.id,
	            							item_name:  		simple.name,
	            							item_list_name: 	list,
	            							item_list_id: 		list,
	            							price: 				simple.price,
	            							quantity:			1
	            						};
	            						
	            						if (simple.hasOwnProperty('configurations'))
	            						{
	            							item['configurations'] = simple.configurations;
	            						}
	            						
	            					
	            						dataLayer.push(
	            						{
	            							event:'view_item',
	            							ecommerce:
	            							{
	            								currency: AEC.currencyCode,
	            								value: item.price,
            									items:
        										[
        											item
    											]
	            							}
	            						});
	            						
	            						/**
	            						 * Update data-simple attribute
	            						 */
	            						$('[data-event="add_to_cart"]').data('simple-id', simple.id).attr('data-simple-id', simple.id);
	            						
	            						/**
	            						 * Facebook Pixel tracking
	            						 */
	            						if ("undefined" !== typeof fbq)
	                            		{
	            							fbq("track", "CustomizeProduct", { eventID: AEC.UUID.generate({ event: 'CustomizeProduct'}) });
	                            		}
	            					}
            					}
            					else
            					{
            						return function()
            						{
            							dataLayer.push({ 'event':'resetConfigurableSelection' });
            						};
            					}
            					
            				})(this)
            			);
                    }, this));
                }, this));	
    		},
    		_changeProductImage: function()
    		{
    			this._super();
    			
    			if ('undefined' !== typeof dataLayer)
    			{
    				dataLayer.push(
					{
				        'event':'changeProductImage'
					});
    				
    				if ("undefined" !== typeof fbq)
            		{
            			(function(callback)
            			{
            				if (AEC.Const.COOKIE_DIRECTIVE)
            				{
            					AEC.CookieConsent.queue(callback).process();
            				}
            				else 
            				{
            					callback.apply(window,[]);
            				}
            			})
            			(
            				(function()
            				{
            					return function()
            					{
            						fbq("track", "ChangeProductImage");
            					}
            				})()
            			);
            		}
    			}
    		}
        });
    	
    	return $.mage.configurable;
    }
});