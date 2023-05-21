/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking GA4
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec4
 * @copyright 	Copyright (c) 2022 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

if ('undefined' !== typeof AEC && 'undefined' !== typeof AEC.EventDispatcher)
{	
	AEC.GA4 = (() => 
	{
		return {
			enabled: false,
			transformCategories: function(category)
			{
				let map = {}, categories = category.split('/');
				
				if (categories)
				{
					map['item_category'] = categories.shift();
					
					if (categories.length)
					{
						let index = 1;
						
						categories.forEach((category) => 
						{
							map['item_category_' + (++index)] = category;
						});
					}
				}
				
				return map;
			},
			augmentCategories: function(product) 
			{
				let category = null;
				
				if (product.hasOwnProperty('category'))
				{
					category = product.category;
					
					if (AEC.localStorage)
		            {
		                let reference = AEC.Storage.reference().get();
		                
		                if (reference)
		                {
			                for (var a = 0, b = reference.length; a < b; a++)
		    				{
		    					if (product.id.toString().toLowerCase() === reference[a].id.toString().toLowerCase())
		    					{
		    						category = reference[a].category;
		    					}
		    				}
		                }
		            }
				}
				
				return this.transformCategories(category);
			}
		}
	})();
	
	/**
	 * Modify product impressions payload
	 */
	AEC.EventDispatcher.on('ec.cookie.impression.data', (data) => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		var items = [];
		
		data.ecommerce.impressions.forEach((product) => 
		{
			let item = 
			{	
				item_id: 		product.id,
				item_name: 		product.name,
				item_list_name: data.ecommerce.actionField.list,
				item_list_id:	data.ecommerce.actionField.list,
				item_brand: 	product.brand,
				price: 			product.price,
				quantity: 		product.quantity ? product.quantity : 1,
				index: 			product.position,
				currency:		AEC.GA4.currency
			};
			
			if (product.hasOwnProperty('affiliation'))
			{
				item['affiliation'] = product.affiliation;
			}
			
			Object.assign(item, item, AEC.GA4.augmentCategories(product));

			items.push(item);
		});
		
		data['event'] = 'view_item_list';
		data.ecommerce['items'] = items;
	});
	
	AEC.EventDispatcher.on('ec.widget.view.data', (data) => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		var items = [];
		
		data.ecommerce.impressions.forEach((product) => 
		{
			let item = 
			{
					
				item_id: 		product.id,
				item_name: 		product.name,
				item_list_name: data.ecommerce.actionField.list,
				item_list_id:	data.ecommerce.actionField.list,
				item_brand: 	product.brand,
				price: 			product.price,
				quantity: 		product.quantity ? impression.quantity : 1,
				index: 			product.position,
				currency:		AEC.GA4.currency
			};
			
			Object.assign(item, item, AEC.GA4.augmentCategories(product));

			items.push(item);
		});
		
		data['event'] = 'view_item_list';
		data.ecommerce['items'] = items;
	});
	
	/**
	 * Modify product click payload
	 */
	AEC.EventDispatcher.on('ec.cookie.click.data', (data) => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		let items = [];
		
		data.ecommerce.click.products.forEach((product) => 
		{
			let item = 
			{
				item_id: 		product.id,
				item_name: 		product.name,
		        item_brand: 	product.brand,
		        item_list_name: data.ecommerce.click.actionField.list,
		        quantity: 		product.quantity,
		        index: 			product.position,
		        price: 			product.price,
		        currency:		AEC.GA4.currency
			};
		
			Object.assign(item, item, AEC.GA4.augmentCategories(product));
			
			items.push(item);
		});

		data['event'] = 'select_item';
		data.ecommerce['items'] = items;
	});
	
	/**
	 * Modify product detail payload
	 */
	AEC.EventDispatcher.on('ec.cookie.detail.data', (data) => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		let items = [];
		
		data.ecommerce.detail.products.forEach((product) => 
		{
			let item = 
			{
				item_name: 		product.name,
				item_id: 		product.id,
		        item_brand: 	product.brand,
		        item_list_name: data.ecommerce.detail.actionField.list,
				item_list_id:	data.ecommerce.detail.actionField.list,
		        quantity: 		product.quantity,
		        price: 			product.price,
		        currency:		AEC.GA4.currency
			};
			
			if (product.hasOwnProperty('affiliation'))
			{
				item['affiliation'] = product.affiliation;
			}
		
			Object.assign(item, item, AEC.GA4.augmentCategories(product));
			
			items.push(item);
		});
		
		data['event'] = 'view_item';
		
		data.ecommerce['items'] = items;
		data.ecommerce['value'] = items[0].price;
	});
	
	
	/**
	 * Modify add to cart payload
	 */
	AEC.EventDispatcher.on('ec.cookie.add.data', (data) => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		let items = [];
		
		data.ecommerce.add.products.forEach((product) => 
		{
			let item = 
			{
				item_id: 		product.id,
				item_name: 		product.name,
		        item_brand: 	product.brand,
		        item_list_id:   data.ecommerce.add.actionField.list,
		        item_list_name: data.ecommerce.add.actionField.list,
		        quantity: 		product.quantity,
		        price: 			product.price,
		        currency:		AEC.GA4.currency
			};
			
			if (product.hasOwnProperty('variant'))
			{
				item['item_variant'] = product.variant;
			}
			
			if (product.hasOwnProperty('affiliation'))
			{
				item['affiliation'] = product.affiliation;
			}
		
			Object.assign(item, item, AEC.GA4.augmentCategories(product));
			
			items.push(item);
		});
		
		data['event'] = 'add_to_cart';
		
		data.ecommerce['items'] = items;
	});
	
	/**
	 * Modify remove from cart payload
	 */
	AEC.EventDispatcher.on('ec.cookie.remove.item.data', (data) => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		let items = [];
		
		data.ecommerce.remove.products.forEach((product) => 
		{
			var item = 
			{
				item_id: 		product.id,
				item_name: 		product.name,
		        item_brand: 	product.brand,
		        item_list_id: 	data.ecommerce.remove.actionField.list,
		        item_list_name: data.ecommerce.remove.actionField.list,
		        quantity: 		product.quantity,
		        price: 			product.price,
		        currency:		AEC.GA4.currency
			};
			
			if (product.hasOwnProperty('variant'))
			{
				item['item_variant'] = product.variant;
			}
		
			Object.assign(item, item, AEC.GA4.augmentCategories(product));
			
			items.push(item);
		});
		
		data['event'] = 'remove_from_cart';
		
		data.ecommerce['items'] = items;
	});
	
	/**
	 * Modify remove from cart payload
	 */
	AEC.EventDispatcher.on('ec.cookie.update.item.data', (data) => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		let items = [];
		
		if ('addToCart' === data.event)
		{
			data.ecommerce.add.products.forEach((product) => 
			{
				var item = 
				{
					item_id: 		product.id,
					item_name: 		product.name,
			        item_brand: 	product.brand,
			        item_list_id:   data.ecommerce.add.actionField.list,
			        item_list_name: data.ecommerce.add.actionField.list,
			        quantity: 		product.quantity,
			        price: 			product.price,
			        currency:		AEC.GA4.currency
				};
				
				if (product.hasOwnProperty('variant'))
				{
					item['item_variant'] = product.variant;
				}
			
				Object.assign(item, item, AEC.GA4.augmentCategories(product));
				
				items.push(item);
			});
			
			data['event'] = 'add_to_cart';
		}
		else 
		{
			data.ecommerce.remove.products.forEach((product) => 
			{
				var item = 
				{
					item_id: 		product.id,
					item_name: 		product.name,
			        item_brand: 	product.brand,
			        item_list_id:   data.ecommerce.remove.actionField.list,
			        item_list_name: data.ecommerce.remove.actionField.list,
			        quantity: 		product.quantity,
			        price: 			product.price,
			        currency:		AEC.GA4.currency
				};
				
				if (product.hasOwnProperty('variant'))
				{
					item['item_variant'] = product.variant;
				}
			
				Object.assign(item, item, AEC.GA4.augmentCategories(product));
				
				items.push(item);
			});
			
			data['event'] = 'remove_from_cart';
		}
		
		data.ecommerce['items'] = items;
	});
	
	
	
	/**
	 * Modify checkout step payload
	 */
	

	AEC.EventDispatcher.on('ec.checkout.step.data', (data) => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
	});
	
	AEC.EventDispatcher.on('ec.cookie.checkout.step.data', (data) => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		let items = [];
		
		data.ecommerce.checkout.products.forEach((product) => 
		{
			var item = 
			{
				item_id: 			product.id,
				item_name: 			product.name,
		        item_brand: 		product.brand,
		        item_list_id:       product.list,
		        item_list_name:     product.list,
		        quantity: 			product.quantity,
		        price: 				product.price,
		        currency:			AEC.GA4.currency
			};
			
			if (product.hasOwnProperty('variant'))
			{
				item['item_variant'] = product.variant;
			}
			
			if (product.hasOwnProperty('affiliation'))
			{
				item['affiliation'] = product.affiliation;
			}
		
			Object.assign(item, item, AEC.GA4.augmentCategories(product));
			
			items.push(item);
		});
		
		if (1 == Number(data.ecommerce.checkout.actionField.step))
		{
			data['event'] = 'begin_checkout';
		}
		else
		{
			data['event'] = 'checkout';
		}
		
		data.ecommerce['items'] = items;
	});
	
	/**
	 * Modify checkout step option payloasd
	 */
	AEC.EventDispatcher.on('ec.checkout.step.option.data', (data) => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}

		switch(parseInt(data.ecommerce.checkout_option.actionField.step))
		{
			case AEC.Const.CHECKOUT_STEP_SHIPPING:
				
				data['event'] = 'add_shipping_info';
				
				data.ecommerce['items'] = AEC.Checkout.data.ecommerce.items;
				
				data.ecommerce['shipping_tier'] = data.ecommerce.checkout_option.actionField.option;
				
				delete data.ecommerce.checkout_option;
				
				break;
				
			case AEC.Const.CHECKOUT_STEP_PAYMENT:
				
				data['event'] = 'add_payment_info';
				
				data.ecommerce['items'] = AEC.Checkout.data.ecommerce.items;
				
				data.ecommerce['payment_type'] = data.ecommerce.checkout_option.actionField.option;
				
				delete data.ecommerce.checkout_option;
				
				break;
		}
	});
	
	/**
	 * Modify purchase payload
	 */
	AEC.EventDispatcher.on('ec.cookie.purchase.data', (data) => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		let items = [];
		
		data.ecommerce.purchase.products.forEach((product) => 
		{
			let item = 
			{
				item_id: 		product.id,
				item_name: 		product.name,
		        item_brand: 	product.brand,
		        quantity: 		product.quantity,
		        price: 			product.price,
		        currency:		AEC.GA4.currency
			};
			
			if (product.hasOwnProperty('variant'))
			{
				item['item_variant'] = product.variant;
			}
			
			if (product.hasOwnProperty('affiliation'))
			{
				item['affiliation'] = product.affiliation;
			}
		
			Object.assign(item, item, AEC.GA4.augmentCategories(product));
			
			items.push(item);
		});
		
		
		data['event'] = AEC.GA4.conversion_event;
		
		data.ecommerce.purchase['items'] 			= items;
		data.ecommerce.purchase['transaction_id'] 	= data.ecommerce.purchase.actionField.id;
		data.ecommerce.purchase['affiliation'] 		= data.ecommerce.purchase.actionField.id;
		data.ecommerce.purchase['value'] 			= data.ecommerce.purchase.actionField.revenue;
		data.ecommerce.purchase['tax'] 				= data.ecommerce.purchase.actionField.tax;
		data.ecommerce.purchase['shipping'] 		= data.ecommerce.purchase.actionField.shipping;
		data.ecommerce.purchase['currency'] 		= data.ecommerce.currencyCode;
		data.ecommerce.purchase['coupon'] 			= data.ecommerce.purchase.actionField.coupon;
	});
	
	/**
	 * Add to wishlist (Google Analytics 4)
	 */
	AEC.EventDispatcher.on('ec.add.wishlist', (data, options) => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		let attributes = JSON.parse(options.attributes);
		
		data['event']     = 'add_to_wishlist';
		data['ecommerce'] = 
		{
			items: attributes.items
		};
	});
	
	AEC.EventDispatcher.on('ec.add.compare', (data, options) => 
	{
		if (!AEC.GA4.enabled)
		{
			return true;
		}
		
		let attributes = JSON.parse(options.attributes);
		
		data['event']     = 'add_to_compare';
		data['ecommerce'] = 
		{
			items: attributes.items
		};
	});
}