var AEC = (function()
{
	return {
		add: function(context, dataLayer)
		{
			let element = context, qty = 1, variant = [], variant_attribute_option = [], items = [];

			document.querySelectorAll('input[name=qty]:checked, [name=qty]').forEach(element => 
			{
				qty = element.value;
			});
			
			qty = Math.abs(qty);
			
			if (isNaN(qty))
			{
				qty = 1;
			}
			
			if ('undefined' !== typeof jQuery)
			{
				var form = jQuery(context).closest('form');

				if (form.length)
				{
					if (!form.valid())
					{
						return true;
					}
				}
			}
			
			if (!AEC.gtm())
			{
				if (element.dataset.click)
				{
					AEC.Time.track(dataLayer, AEC.Const.TIMING_CATEGORY_ADD_TO_CART, element.dataset.name, element.dataset.category);
					
					eval(element.dataset.click);
				}
				
				return true;
			}
			
			if(element.dataset.configurable)
			{
				var variants = [];

				document.querySelectorAll('[name^="super_attribute"]').forEach(attribute => 
				{
					if (attribute.matches('select'))
					{
						var name = attribute.getAttribute('name'), id = name.substring(name.indexOf('[') + 1, name.lastIndexOf(']'));

						if (attribute.selectedIndex)
						{
							var option = attribute.options[attribute.selectedIndex];
							
							if (option)
							{
								variants.push(
								{
									id: 	id,
									option: option.label,
									text: 	option.text
								});
							}
						}
					}
					
					if (attribute.matches('input') && -1 != attribute.type.indexOf('radio'))
					{
						if (attribute.parentNode.classList.contains('swatch-option-selected') || attribute.checked)
						{
							Object.entries(AEC.SUPER).forEach(([key, super_attribute]) => 
							{
								if (-1 != attribute.name.indexOf("super_attribute[" + super_attribute.id + "]"))
								{
									let variant = {
										id: 	super_attribute.id,
										text:	super_attribute.label,
										option: attribute.value
									};
									
									variants.push(variant);
									
								}
							});
						}
					}
				});
				
				/**
				 * Colour Swatch support
				 */
				if (!variants.length)
				{
					Object.entries(AEC.SUPER).forEach(([key, attribute]) => 
					{
						var swatch = document.querySelectorAll('div[attribute-code="' + attribute.code + '"], div[data-attribute-code="' + attribute.code + '"]');
						
						swatch.forEach(element => 
						{
							let variant = 
							{
								id: 	attribute.id,
								text:	'',
								option: null
							};
							
							var select = element.querySelector('select');

							if (select)
							{
								if (select.selectedIndex)
								{
									var option = select.options[select.selectedIndex];
									
									if (option)
									{
										variant.text 	= option.text;
										variant.option 	= option.value;
									}
								}
							}
							else 
							{
								var span = element.querySelector('span.swatch-attribute-selected-option');

								if (span)
								{
									variant.text 	= span.innerHTML;
									variant.option 	= span.parentNode.dataset.optionSelected
								}
							}
							
							variants.push(variant);
						});
					});
				}
				
				if (!variants.length)
				{
					AEC.EventDispatcher.trigger('ec.variants', variants);
				}
				
				var SUPER_SELECTED = [];

				for (i = 0, l = variants.length; i < l; i++)
				{
					for (a = 0, b = AEC.SUPER.length; a < b; a++)
					{
						if (AEC.SUPER[a].id == variants[i].id)
						{
							var text = variants[i].text;

							if (AEC.useDefaultValues)
							{
								AEC.SUPER[a].options.forEach(option => 
								{
									if (parseInt(option.value_index) == parseInt(variants[i].option))
									{
										if (option.hasOwnProperty('admin_label'))
										{
											text = option.admin_label;
										}
										else if (option.hasOwnProperty('store_label'))
										{
											text = option.store_label;
										}
										
									}
								});
							}
							
							variant.push([AEC.SUPER[a].label,text].join(AEC.Const.VARIANT_DELIMITER_ATT));

							variant_attribute_option.push(
							{
								attribute: 	variants[i].id,
								option: 	variants[i].option
							})
						}
					}
				}
				
				if (!variant.length)
				{
					if (element.dataset.click)
					{
						AEC.Time.track(dataLayer, AEC.Const.TIMING_CATEGORY_ADD_TO_CART, element.dataset.name, element.dataset.category);
						
						eval(element.dataset.click);
					}
					
					return true;
				}
			}
			
			if (element.dataset.grouped)
			{
				for (u = 0, y = window.G.length; u < y; u++)
				{
					let field = document.querySelector('[name="super_group[' + window.G[u].id + ']"]');
					
					if (field)
					{
						(qty => 
						{
							if (qty)
							{
								let item = 
								{
									'item_name': 		window.G[u].name,
									'item_id': 		    window.G[u].sku,
									'price': 			window.G[u].price,
									'category': 		window.G[u].category,
									'item_brand':		window.G[u].brand,
									'quantity': 		qty
								};
								
								Object.assign(item, item, AEC.GA4.transformCategories(element.dataset.category));
								
								items.push(item);
							}
						})(Math.abs(field.value));
					}
				}
			}
			else
			{
				let item = 
				{
					'item_name': 		element.dataset.name,
					'item_id': 		    1 === parseInt(element.dataset.useSimple) ? element.dataset.simpleId : element.dataset.id,
					'price': 			element.dataset.price,
					'item_brand':		element.dataset.brand,
					'item_variant':		variant.join(AEC.Const.VARIANT_DELIMITER),
					'category':			element.dataset.category,
					'quantity': 		qty,
					'currency':			AEC.currencyCode,
					'index': 			0
				};
				
				Object.assign(item, item, AEC.GA4.transformCategories(element.dataset.category));
				
				items.push(item);
			}
			
			for (i = 0, l = items.length; i < l; i++)
			{
				(function(item)
				{
					Object.entries(AEC.parseJSON(element.dataset.attributes)).forEach(([key, value]) => 
					{
						item[key] = value;
					});
					
					let options = [];
					
					Object.entries(AEC.parseJSON(element.dataset.options)).forEach(([key, name]) => 
					{
						let option = document.querySelector('[name="options[' + key + ']"]'), value = null;
						
						switch(true)
						{
							case option instanceof HTMLInputElement: 
							case option instanceof HTMLTextAreaElement: value = option.value;
								break;
							case option instanceof HTMLSelectElement: value = option.options[option.selectedIndex].text;
								break;
						}
						
						if (value)
						{
							options.push({ name: name, value: value });
						}
						
					});
					
					if (options.length)
					{
						item['options'] = options;
					}

				})(items[i]);
			}
			
			var data = 
			{
				'event':'add_to_cart',
				'eventLabel': element.dataset.name,
				'ecommerce': 
				{
					'currency': 		AEC.currencyCode,
					'item_list_id': 	element.dataset.list,
					'item_list_name': 	element.dataset.list,
					'items': 			items,
					'options': 			variant_attribute_option
				},
				'currentStore': element.dataset.store
			};
			
			if (AEC.useDefaultValues)
			{
				data['currentStore'] = AEC.storeName;
			}
			
			/**
			 * Notify listeners
			 */
			this.EventDispatcher.trigger('ec.add.data', data);
			
			/**
			 * Track event
			 */
			AEC.Cookie.add(data).push(dataLayer);
			
			/**
			 * Save backreference
			 */
			if (AEC.localStorage)
			{
				(function(items)
				{
					for (var i = 0, l = items.length; i < l; i++)
					{
						AEC.Storage.reference().set(
						{
							id: 		items[i].item_id,
							category: 	items[i].category
						});
					}
				})(items);
			}
			
			/**
			 * Track time 
			 */
			AEC.Time.track(dataLayer, AEC.Const.TIMING_CATEGORY_ADD_TO_CART, element.dataset.name, element.dataset.category);

			if (AEC.facebook)
			{
				if ("undefined" !== typeof fbq)
				{
					(function(product, items, fbq)
					{
						var content_ids = [], price = 0;
						
						for (i = 0, l = items.length; i < l; i++)
						{
							content_ids.push(items[i].item_id);
			
							price += parseFloat(items[i].price);
						}
						
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
							(function(product, content_ids, price)
							{
								return function()
								{
									if ('undefined' === typeof variants)
									{
										variants = [];
									}
									
									fbq('track', 'AddToCart', 
									{
										content_name: 	product,
										content_ids: 	content_ids,
										content_type: 	!variants.length ? 'product' : 'product_group',
										value: 			price,
										currency: 		AEC.currencyCode
									}, 
									{ eventID: AEC.UUID.generate({ event: 'AddToCart'}) });
								}
							})(product, content_ids, price)
						);

					})(element.dataset.name, items, fbq);
				}
			}
			
			if (element.dataset.click)
			{
				eval(element.dataset.click);
			}
			
			return true;
		},
		addSwatch: function(context,dataLayer)
		{	
			var element = context;
			
			if (window.jQuery) 
			{  
				jQuery(document).on('ajax:addToCart', function()
				{
					var attributes = [];
					
					Object.entries(AEC.parseJSON(element.dataset.swatch)).forEach(([key, value]) => 
					{
						attributes.push(value);
					});
					
					var option = document.querySelector('.swatch-option.selected');
					
					if (!option)
					{
						return true;
					}
					
					var variant = [[attributes[0].attribute_label, option.getAttribute('aria-label')].join(AEC.Const.VARIANT_DELIMITER_ATT)].join(AEC.Const.VARIANT_DELIMITER);
					
					let items = [];
					
					let item =  
					{
						'item_name': 		element.dataset.name,
						'item_id': 		    element.dataset.id,
						'price': 			element.dataset.price,
						'category': 		element.dataset.category,
						'item_brand':		element.dataset.brand,
						'item_variant':		variant,
						'item_list_name': 	element.dataset.list,
						'item_list_id': 	element.dataset.list,
						'quantity': 		1,
						'index':			element.dataset.position
					};
					
					Object.assign(item, item, AEC.GA4.transformCategories(element.dataset.category));
					
					/**
					 * Track event
					 */
					AEC.Cookie.add(
					{
						'event': 			'add_to_cart',
						'currency':			AEC.currencyCode,
						'eventLabel': 		element.dataset.name,
						'item_list_id': 	element.dataset.list,
						'item_list_name': 	element.dataset.list,
						'ecommerce': 
						{
							'items': [item]
						},
						'currentStore': element.dataset.store
					}).push(dataLayer);
	
					/**
					 * Track time 
					 */
					AEC.Time.track(dataLayer, AEC.Const.TIMING_CATEGORY_ADD_TO_CART, element.dataset.name, element.dataset.category);
				});
			}
			
			return true;
		},
		click: function(context,dataLayer)
		{
			var element = context;
			
			if (!AEC.gtm())
			{
				AEC.Time.track(dataLayer, AEC.Const.TIMING_CATEGORY_PRODUCT_CLICK, element.dataset.name, element.dataset.category);

				return true;
			}

			var item = 
			{
				'item_name': 	element.dataset.name,
				'item_id': 		element.dataset.id,
				'price': 		element.dataset.price,
				'item_brand':	element.dataset.brand,
				'quantity': 	element.dataset.quantity,
				'index':		element.dataset.position,
				'category':		element.dataset.category,
				'currency':		AEC.currencyCode
			};
			
			Object.entries(AEC.parseJSON(element.dataset.attributes)).forEach(([key, value]) => 
			{
				item[key] = value;
			});
			
			Object.assign(item, item, AEC.GA4.augmentCategories(item));
			
			var data = 
			{
				'event': 			'select_item',
				'eventLabel': 		element.dataset.name,
				'item_list_id': 	element.dataset.list, 
				'item_list_name': 	element.dataset.list,
				'ecommerce': 
				{
					'items': 
					[
						item
					]
				},
    	     	'currentStore': element.dataset.store	
			};
			

			/**
			 * Push data
			 */
			AEC.Cookie.click(data).push(dataLayer);
			
			/**
			 * Track time 
			 */
			AEC.Time.track(dataLayer, AEC.Const.TIMING_CATEGORY_PRODUCT_CLICK, element.dataset.name, element.dataset.category);

			if (element.dataset.click)
			{
				eval(element.dataset.click);
			}

			return true;
		},
		remove: function(context, dataLayer)
		{
			var element = context;
			
			if (!AEC.gtm())
			{
				AEC.Time.track(dataLayer, AEC.Const.TIMING_CATEGORY_REMOVE_FROM_CART, element.dataset.name, element.dataset.category);
			}

			var item = 
			{
				'item_name': 	element.dataset.name,
				'item_id': 		element.dataset.id,
				'price': 		element.dataset.price,
				'category': 	element.dataset.category,
				'item_brand':	element.dataset.brand,
				'quantity': 	element.dataset.quantity,
				'currency': 	AEC.currencyCode
			};
			
			Object.entries(AEC.parseJSON(element.dataset.attributes)).forEach(([key, value]) => 
			{
				item[key] = value;
			});
			
			Object.assign(item, item, AEC.GA4.augmentCategories(item));
			
			var data = 
			{
				'event': 'remove_from_cart',
				'eventLabel': element.dataset.name,
				'ecommerce': 
				{
					'item_list_id':  element.dataset.list,
					'item_list_name':  element.dataset.list,
					'items': 
					[
						item
					],
				}
			};
			
			AEC.EventDispatcher.trigger('ec.remove.data', data);
			
			if (AEC.Message.confirm)
			{
				require(['Magento_Ui/js/modal/confirm'], function(confirmation) 
				{
				    confirmation(
				    {
				        title: AEC.Message.confirmRemoveTitle,
				        content: AEC.Message.confirmRemove,
				        actions: 
				        {
				            confirm: function()
				            {
				            	/**
								 * Track event
								 */
								AEC.Cookie.remove(data).push(dataLayer);

								/**
								 * Track time 
								 */
								AEC.Time.track(dataLayer, AEC.Const.TIMING_CATEGORY_REMOVE_FROM_CART, element.dataset.name);
								
								/**
								 * Execute standard data-post
								 */
								(params => 
								{
									let form = document.createElement("form");
									
					                form.setAttribute('method', 'post');
					                form.setAttribute('action', params.action);
					                
					                let formKey = jQuery.cookie('form_key');
					                
					                if (formKey)
					                {
					                	params.data['form_key'] = formKey;
					                }
					                
					                Object.entries(params.data).forEach(([name, value]) => 
									{
										let input = document.createElement('input');
								
										input.setAttribute('type', 'text');
										input.setAttribute('name',  name);
										input.setAttribute('value', value);
										
										form.append(input);
									});
					                
					                document.querySelector("body").appendChild(form);
					                
					                /**
					                 * Submit form
					                 */
					                form.submit();
									
								})(AEC.parseJSON(element.dataset.postAction))
				            },
				            cancel: function()
				            {
				            	return false;
				            },
				            always: function()
				            {
				            	return false;
				            }
				        }
				    });
				});
			}
			else 
			{
				/**
				 * Track event
				 */
				AEC.Cookie.remove(data).push(dataLayer);

				/**
				 * Track time 
				 */
				AEC.Time.track(dataLayer, AEC.Const.TIMING_CATEGORY_REMOVE_FROM_CART, element.dataset.name);
			}
			
			return false;
		},
		wishlist: function(context, dataLayer)
		{
			var element = context;

			if (!AEC.gtm())
			{
				/**
				 * Track time 
				 */
				AEC.Time.track(dataLayer, AEC.Const.TIMING_CATEGORY_PRODUCT_WISHLIST, element.dataset.name,'Wishlist');
				
				return true;
			}
			
			let attributes = JSON.parse(element.dataset.eventAttributes);
			
			/**
			 * Track event
			 */
			AEC.Cookie.wishlist(
			{
				event: 		element.dataset.event,
				eventLabel: element.dataset.eventLabel,
				ecommerce: 
				{
					items: attributes.items
				}
			}).push(dataLayer);

			return true;
		},
		compare: function(context, dataLayer)
		{
			var element = context;

			if (!AEC.gtm())
			{
				/**
				 * Track time 
				 */
				AEC.Time.track(dataLayer, AEC.Const.TIMING_CATEGORY_PRODUCT_COMPARE, element.dataset.name,'Compare');
				
				return true;
			}
			
			let attributes = JSON.parse(element.dataset.eventAttributes);

			AEC.Cookie.compare(
			{
				event: 		element.dataset.event,
				eventLabel: element.dataset.eventLabel,
				ecommerce: 
				{
					items: attributes.items
				}
			}).push(dataLayer);
			
			return true;
		},
		Bind: (function()
		{
			return {
				apply: function(parameters)
				{	
					/**
					 * Merge persistent storage
					 */
					AEC.Persist.merge();
					
					/**
					 * Push private data
					 */
					AEC.Cookie.pushPrivate();
					
					document.addEventListener('DOMContentLoaded',() => 
					{
						document.body.addEventListener('catalogCategoryAddToCartRedirect', () => 
						{
							dataLayer.push(
							{
								event: AEC.Const.CATALOG_CATEGORY_ADD_TO_CART_REDIRECT_EVENT
							});
						});
					});

					if (parameters)
					{
						if (parameters.performance)
						{
							if (window.performance)
							{
								window.onload = function()
								{
									setTimeout(function()
									{
									    var time = performance.timing.loadEventEnd - performance.timing.responseEnd;
									    
									    var timePayload = 
									    {
								    		'event':'performance',
							    			'performance':
							    			{
							    				'timingCategory':	'Load times',
							    				'timingVar':		'load',
							    				'timingValue': 		(time % 60000)
							    			}	
									    };
									    
									    switch(window.google_tag_params.ecomm_pagetype)
									    {
									    	case 'home':
									    		
									    		timePayload.performance.timingLabel = 'Home';
									    		
									    		AEC.CookieConsent.queue(() => 
									            {
									            	dataLayer.push(timePayload);
									            }).process();
									    		
									    		break;
									    	case 'product':
									    		
									    		timePayload.performance.timingLabel = 'Product';
									    		
									    		AEC.CookieConsent.queue(() => 
									            {
									            	dataLayer.push(timePayload);
									            }).process();
									    		
									    		break;
									    	
								    		case 'category':
									    		
									    		timePayload.performance.timingLabel = 'Category';
									    		
									    		AEC.CookieConsent.queue(() => 
									            {
									            	dataLayer.push(timePayload);
									            }).process();
									    		
									    		break;
									    }
									    
									}, 0);
								}	
							}
						}
					}
					
					return this;
				}
			}
		})(),
		Time: (function()
		{
			var T = 
			{
				event: 			'trackTime',
				timingCategory:	'',
				timingVar:		'',
				timingValue:	-1,
				timingLabel:	''
			};

			var time = new Date().getTime();
			
			return {
				track: function(dataLayer, category, variable, label)
				{
					T.timingValue = (new Date().getTime()) - time;
					
					if (category)
					{
						T.timingCategory = category;
					}

					if (variable)
					{
						T.timingVar = variable;
					}

					if (label)
					{
						T.timingLabel = label;
					}
					
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
						(function(dataLayer, T)
						{
							return function()
							{
								dataLayer.push(T);
							}
						})(dataLayer, T)
					);
				},
				trackContinue: function(dataLayer, category, variable, label)
				{
					this.track(dataLayer, category, variable, label);

					time = new Date().getTime();
				}
			}
		})(),
		Persist:(function()
		{
			var DATA_KEY = 'persist'; 

			var proto = 'undefined' != typeof Storage ? 
			{
				push: function(key, entity)
				{
					/**
					 * Get data
					 */
					var data = this.data();

					/**
					 * Push data
					 */
					data[key] = entity;

					/**
					 * Save to local storage
					 */
					localStorage.setItem(DATA_KEY, JSON.stringify(data));

					return this;
				},
				data: function()
				{
					var data = localStorage.getItem(DATA_KEY);
					
					if (null !== data)
					{
						return JSON.parse(data);
					}

					return {};
				},
				merge: function()
				{
					var data = this.data();
					var push = 
					{
						persist: {}
					};

					for (var i in data)
					{
						push.persist[i] = data[i];
					}

					dataLayer.push(push);

					return this;
				},
				clear: function()
				{
					/**
					 * Reset private local storage
					 */
					localStorage.setItem(DATA_KEY,JSON.stringify({}));

					return this;
				}
			} : {
				push: 	function(){}, 
				merge: 	function(){},
				clear: 	function(){}
			};

			/**
			 * Constants
			 */
			proto.CONST_KEY_PROMOTION = 'persist_promotion';

			return proto;
			
		})(),
		Checkout: (function()
		{
			return {
				init: false,
				data: {},
				tracked: {},
				getData: function()
				{
					return this.data;
				},
				getPayload: function()
				{
					if (this.data && this.data.hasOwnProperty('payload'))
					{
						return this.data.payload;
					}
					
					return {
						error: 'Missing checkout payload data'
					}
				},
				step: function(previous, current, currentCode)
				{
					if (!this.init)
					{
						return this.fail('Step tracking requires a checkout page.');
					}
					
					if (this.data && this.data.hasOwnProperty('ecommerce'))
					{	
						this.data.ecommerce['step'] = ++current;

						/**
						 * Notify listeners
						 */
						AEC.EventDispatcher.trigger('ec.checkout.step.data', this.data);
						
						/**
						 * Track checkout step
						 */
						AEC.Cookie.checkout(this.data).push(dataLayer);
					}
					
					return this;
				},
				stepOption: function(step, option)
				{
					if (!option)
					{
						return this;
					}
					
					if (!this.init)
					{
						return this.fail('Step option tracking requires a checkout page.');
					}
					
					
					if (!option.toString().length)
					{
						return this;
					}
					
					var data = 
					{
	    				'event': 'checkoutOption',
	    				'ecommerce': 
	    				{
	    					'checkout_option': 
	    					{
	    						'actionField': 
	    						{
	    							'step': step,
	    							'option': option
	    						}
	    					}
	    				}
	        		};
					
					/**
					 * Notify listeners
					 */
					AEC.EventDispatcher.trigger('ec.checkout.step.option.data', data);
					
					/**
					 * Track checkout option
					 */
					AEC.Cookie.checkoutOption(data).push(dataLayer);
					
					return this;
				},
				fail: function(message)
				{
					console.log(message);
					
					return this;
				}
				
			}
		})(),
		Cookie: (function()
		{
			return {
				data: null,
				privateData: null,
				reset: function()
				{
					if (AEC.reset)
					{
						dataLayer.push({ ecommerce: null });
					}
					
					return dataLayer;
				},
				push: function(dataLayer, consent)
				{
					consent = typeof consent !== 'undefined' ? consent : true;
					
					if (this.data)
					{
						if (consent)
						{
							if (AEC.Const.COOKIE_DIRECTIVE)
							{
								if (AEC.Const.COOKIE_DIRECTIVE_CONSENT_GRANTED)
								{
									this.reset().push(this.data);
								}
							}
							else 
							{
								this.reset().push(this.data);
							}
						}
						else 
						{
							this.reset().push(this.data);
						}
						
						/**
						 * Reset data to prevent further push
						 */
						this.data = null;
					}
					
					return this;
				},
				pushPrivate: function()
				{
					var data = this.getPrivateData();
					
					if (data)
					{
						dataLayer.push(
						{
							privateData: data
						});
					}
					
					return this;
				},
				augment: function(products)
				{
					/**
					 * Parse data & apply local reference
					 */
					var reference = AEC.Storage.reference().get();
					
					if (reference)
					{
						for (var i = 0, l = products.length; i < l; i++)
						{
							for (var a = 0, b = reference.length; a < b; a++)
							{
								if (products[i].item_id.toString().toLowerCase() === reference[a].id.toString().toLowerCase())
								{
									products[i].category = reference[a].category;
								}
							}
						}
					}
					
					return products;
				},
				click: function(data)
				{
					AEC.EventDispatcher.trigger('ec.cookie.click.data', data);
					
					this.data = data;
					
					return this;
				},
				add: function(data)
				{
					AEC.EventDispatcher.trigger('ec.cookie.add.data', data);
					
					this.data = data;

					return this;
				},
				remove: function(data)
				{
					AEC.EventDispatcher.trigger('ec.cookie.remove.item.data', data);
					
					this.data = data;
					
					if (AEC.localStorage)
					{
						this.data.ecommerce.items = this.augment(this.data.ecommerce.items);
					}

					return this;
				},
				compare: function(data)
				{
					AEC.EventDispatcher.trigger('ec.cookie.compare.data', data);
					
					this.data = data;
					
					return this;
				},
				wishlist: function(data)
				{
					AEC.EventDispatcher.trigger('ec.cookie.wishlist.data', data);
					
					this.data = data;
					
					return this;
				},
				update: function(data)
				{
					AEC.EventDispatcher.trigger('ec.cookie.update.item.data', data);
					
					this.data = data;
					
					return this;
				},
				visitor: function(data)
				{
					AEC.EventDispatcher.trigger('ec.cookie.visitor.data', data);
					
					this.data = (function(data, privateData)
					{
						if (privateData)
						{
							if (privateData.hasOwnProperty('visitor'))
							{
								data.visitorId 		   = privateData.visitor.visitorId;
								data.visitorLoginState = privateData.visitor.visitorLoginState;
							}
						}
						
						return data;
						
					})(data, AEC.Cookie.getPrivateData());
					
					return this;
				},
				detail: function(data)
				{
					AEC.EventDispatcher.trigger('ec.cookie.detail.data', data);
					
					this.data = data;
					
					return this;
				},
				purchase: function(data)
				{
					AEC.EventDispatcher.trigger('ec.cookie.purchase.data', data);
					
					this.data = data;

					if (AEC.localStorage)
					{
						this.data.ecommerce.purchase.items = this.augment(this.data.ecommerce.purchase.items);
					}
					
					return this;
				},
				impressions: function(data)
				{
					AEC.EventDispatcher.trigger('ec.cookie.impression.data', data);
					
					this.data = data;
					
					return this;
				},
				checkout: function(data)
				{
					AEC.EventDispatcher.trigger('ec.cookie.checkout.step.data', data);
					
					this.data = data;
					
					if (AEC.localStorage)
					{
						this.data.ecommerce.items = this.augment(this.data.ecommerce.items);
					}
					
					return this;
				},
				checkoutOption: function(data)
				{
					AEC.EventDispatcher.trigger('ec.cookie.checkout.step.option.data', data);
					
					this.data = data;
					
					return this;
				},
				promotion: function(data)
				{
					AEC.EventDispatcher.trigger('ec.cookie.promotion.data', data);
					
					this.data = data;
					
					return this;
				},
				promotionClick: function(data, element)
				{
					AEC.EventDispatcher.trigger('ec.cookie.promotion.click', data, 
				   {
						element: element
				   });
					
					this.data = data;
					
					return this;
				},
				remarketing: function(data)
				{
					AEC.EventDispatcher.trigger('ec.cookie.remarketing.data', data);
					
					this.data = data;
					
					return this;
				},
				getPrivateData: function()
				{
					if (!this.privateData)
					{
						var cookie = this.get('privateData');
						
						if (cookie)
						{
							this.privateData = this.parse(cookie);
						}
					}
					
					return this.privateData;
				},
				set: function(name, value, days)
				{
					if (!days)
					{
						days = 30;
					} 
				
					let date = new Date();
		            
		            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		            
			        document.cookie = name + "=" + value + "; expires=" + date.toGMTString() + "; path=/";
			        
			        return this;
				},
				get: function(name)
				{
					var start = document.cookie.indexOf(name + "="), len = start + name.length + 1;
					
					if ((!start) && (name != document.cookie.substring(0, name.length))) 
					{
					    return null;
					}
					
					if (start == -1) 
					{
						return null;
					}
										
					var end = document.cookie.indexOf(String.fromCharCode(59), len);
										
					if (end == -1) 
					{
						end = document.cookie.length;
					}
					
					return decodeURIComponent(document.cookie.substring(len, end));
				},
				unset: function(name) 
				{   
	                document.cookie = name + "=" + "; path=/; expires=" + (new Date(0)).toUTCString();
	                
	                return this;
	            },
				parse: function(json)
				{
					var json = decodeURIComponent(json.replace(/\+/g, ' '));
					
	                return JSON.parse(json);
				}
			}
		})(),
		CookieConsent: (function()
		{
			return {
				chain: {},
				queue: function(callback, event)
				{	
					event = typeof event !== 'undefined' ? event : AEC.Const.COOKIE_DIRECTIVE_CONSENT_GRANTED_EVENT;
					
					if (!this.chain.hasOwnProperty(event))
					{
						this.chain[event] = [];
					}
					
					this.chain[event].push(callback);
					
					return this;
				},
				dispatch: function(consent)
				{
					/**
					 * Essential cookies
					 */
					AEC.Const.COOKIE_DIRECTIVE_CONSENT_GRANTED = true;
					
					/**
					 * Push consent to dataLayer
					 */
					dataLayer.push(consent);
					
					return this.process(consent.event);
				},
				process: function(event)
				{
					event = typeof event !== 'undefined' ? event : AEC.Const.COOKIE_DIRECTIVE_CONSENT_GRANTED_EVENT;
					
					if (this.chain.hasOwnProperty(event) && this.getConsent(event))
					{
						for (a = 0, b = this.chain[event].length; a < b; a++)
						{
							this.chain[event][a].apply(this,[]);
						}
						
						this.chain[event] = [];
					}
				
					return this;
				},
				getConsent: function(event)
				{
					let consent = !AEC.Const.COOKIE_DIRECTIVE ? true : 1 == AEC.Cookie.get(event);
					
					/**
					 * Notify third parties for consent
					 */
					AEC.EventDispatcher.trigger('ec.consent', consent);
					
					return consent;
				},
				acceptGoogleConsent: function(segments)
				{
					if ('function' === typeof gtag)
					{
						const consentMode = true === AEC.Const.COOKIE_DIRECTIVE_SEGMENT_MODE ? 
						{
							ad_storage: 				-1 !== segments.indexOf('cookieConsentMarketingGranted')	? 'granted' : 'denied',
							security_storage:			-1 !== segments.indexOf('cookieConsentGranted')				? 'granted' : 'denied',
							functionality_storage:		-1 !== segments.indexOf('cookieConsentGranted')			   	? 'granted' : 'denied',
							personalization_storage:	-1 !== segments.indexOf('cookieConsentPreferencesGranted') 	? 'granted' : 'denied',
							analytics_storage:			-1 !== segments.indexOf('cookieConsentAnalyticsGranted')	? 'granted' : 'denied'
						} : 
						{
							ad_storage: 				'granted',
							security_storage:			'granted',
							functionality_storage:		'granted',
							personalization_storage:	'granted',
							analytics_storage:			'granted'
						}
						
						
						gtag('consent','updated',consentMode);
						
				        localStorage.setItem('consentMode', JSON.stringify(consentMode));
					}
					
					return this;
				},
				acceptConsent: function(event)
				{
					return this.dispatch({ event:event });
				},
				declineConsent: function(event)
				{
					return this.dispatch({ event:event });
				},
				declineGoogleConsent: function()
				{
					if ('function' === typeof gtag)
					{
						const consentMode = 
						{
							ad_storage: 				'denied',
							security_storage:			'denied',
							functionality_storage:		'denied',
							personalization_storage:	'denied',
							analytics_storage:			'denied'
						};
						
						gtag('consent','updated',consentMode);
						
				        localStorage.setItem('consentMode', JSON.stringify(consentMode));
					}
					
					return this;
				},
				getConsentDialog: function(dataLayer, endpoints)
				{
					if (1 == AEC.Cookie.get(AEC.Const.COOKIE_DIRECTIVE_CONSENT_DECLINE_EVENT))
					{
						return true;
					}
					
					if (1 != AEC.Cookie.get(AEC.Const.COOKIE_DIRECTIVE_CONSENT_GRANTED_EVENT))
					{
						AEC.Request.get(endpoints.cookieContent, {}, (response) => 
						{
							var directive = (body => 
							{
								body.insertAdjacentHTML('beforeend', response.cookieContent);
								
								return body.lastElementChild;
								
							})(document.body);
							
							directive.querySelectorAll('a.ec-gtm-cookie-directive-note-toggle').forEach(element => 
							{
								element.addEventListener('click', event => 
								{
									event.target.nextElementSibling.style.display = 'block' === event.target.nextElementSibling.style.display ? 'none' : 'block';
								});
							});
							
							directive.querySelectorAll('a.accept').forEach(element => 
							{
								element.addEventListener('click', event => 
								{
									event.target.text = event.target.dataset.confirm;
	
									var grant = [...directive.querySelectorAll('[name="cookie[]"]:checked')].map(element => { return element.value });

									grant.unshift('cookieConsentGranted');
									
									AEC.CookieConsent.acceptGoogleConsent(grant);

									AEC.Request.post(endpoints.cookie, { cookie: grant }, response => 
									{
										Object.keys(response).forEach(event => 
										{
											AEC.CookieConsent.acceptConsent(event);
										});

										directive.remove();
									});
								});
							});
							
							directive.querySelectorAll('a.accept-all').forEach(element => 
							{
								element.addEventListener('click', event => 
								{
									event.target.text = event.target.dataset.confirm;
	
									[...directive.querySelectorAll('[name="cookie[]"]')].forEach(element => 
									{
										element.checked = true;
									});
									
									element.parentNode.removeChild(element.previousElementSibling);

									var grant = [...directive.querySelectorAll('[name="cookie[]"]:checked')].map(element => { return element.value });
									
									grant.unshift('cookieConsentGranted');

									AEC.CookieConsent.acceptGoogleConsent(grant);
									
									AEC.Request.post(endpoints.cookie, { cookie: grant }, response => 
									{
										Object.keys(response).forEach(event => 
										{
											AEC.CookieConsent.acceptConsent(event);
										});

										directive.remove();
									});
								});
							});
							
							directive.querySelectorAll('a.decline').forEach(element => 
							{
								element.addEventListener('click', event => 
								{
									AEC.CookieConsent.declineGoogleConsent();
									
									AEC.Request.post(endpoints.cookie, { decline: true }, response => 
									{
										Object.keys(response).forEach(event => 
										{
											AEC.CookieConsent.declineConsent(event);
										});

										directive.remove();
									});
								});
							});
						});
					}
					else 
					{
						if (AEC.Const.COOKIE_DIRECTIVE_SEGMENT_MODE)
						{
							(segments => 
							{
								let grant = [];
								
								for (i = 0, l = segments.length; i < l;i++)
								{
									if (1 == AEC.Cookie.get(segments[i]))
									{
										AEC.CookieConsent.acceptConsent(segments[i]);	
										
										grant.push(segments[i]);
									}
								}
								
								AEC.CookieConsent.acceptGoogleConsent(grant);
								
							})(AEC.Const.COOKIE_DIRECTIVE_SEGMENT_MODE_EVENTS);
						}
						else 
						{
							AEC.CookieConsent.acceptConsent(AEC.Const.COOKIE_DIRECTIVE_CONSENT_GRANTED_EVENT);
						}
					}
				}
			}
		})(),
		Storage: (function(api)
		{
			return {
				set: function(property, value)
				{
					if ('undefined' !== typeof(Storage))
					{
						localStorage.setItem(property, JSON.stringify(value));
					}
					
					return this;
					
				},
				get: function(property)
				{
					if ('undefined' !== typeof(Storage))
					{
						return JSON.parse(localStorage.getItem(property));
					}
					
					return null;
				},
				reference: function()
				{
					return (function(storage)
					{
						return {
							set: function(reference)
							{
								var current = storage.get('category.add') || [];
								
								
								var exists = (function(current, reference)
								{
									for (i = 0, l = current.length; i < l; i++)
									{
										if (current[i].id.toString().toLowerCase() === reference.id.toString().toLowerCase())
										{
											/**
											 * Update category
											 */
											current[i].category = reference.category;
											
											return true;
										}
									}
									
									return false;
									
								})(current, reference);
								
								if (!exists)
								{
									current.push(reference);
								}
								
								storage.set('category.add', current);
								
								return this;
							},
							get: function()
							{
								return storage.get('category.add');
							}
						}
					})(this);
				}
			}
		})(),
		gtm: function()
		{
			if ("undefined" === typeof google_tag_manager)
			{
				/**
				 * Log error to console
				 */
				console.log('Unable to detect Google Tag Manager. Please verify if GTM install snippet is available.');
				
				return false;
			}

			return true;
		},
		parseJSON: function(content)
		{
			if ('object' === typeof content)
			{
				return content;
			}
			
			if ('string' === typeof content)
			{
				try 
				{
					return JSON.parse(content);
				}
				catch (e){}
			}
			
			return {};
		}, 
		getPayloadSize: function(object)
		{
			var objects = [object];
		    var size = 0;
		
		    for (var index = 0; index < objects.length; index++) 
		    {
		        switch (typeof objects[index]) 
		        {
		            case 'boolean':
		                size += 4;
		                break;
		            case 'number':
		                size += 8;
		                break;
		            case 'string':
		                size += 2 * objects[index].length;
		                break;
		            case 'object':
		                if (Object.prototype.toString.call(objects[index]) != '[object Array]') 
		                {
		                    for (var key in objects[index]) size += 2 * key.length;
		                }
		                for (var key in objects[index]) 
		                {
		                    var processed = false;
		                    
		                    for (var search = 0; search < objects.length; search++) 
		                    {
		                        if (objects[search] === objects[index][key]) {
		                            processed = true;
		                            break;
		                        }
		                    }
		                    if (!processed) objects.push(objects[index][key]);
		                }
		        }
		    }
		    return size;
		},
		getPayloadChunks: function(arr, len)
		{
			var chunks = [],i = 0, n = arr.length;
			
			while (i < n) 
			{
			    chunks.push(arr.slice(i, i += len));
			}
	
			return chunks;
		},
		url: function(url)
		{
			return [this.Const.URL, url].join('');
		},
		EventDispatcher: (function()
		{
			return {
				events: {},
			    on: function(event, callback) 
			    {
			        var handlers = this.events[event] || [];
			        
			        handlers.push(callback);
			        
			        this.events[event] = handlers;
			    },
			    trigger: function() 
			    {
			    	/**
			    	 * Cast arguments to array
			    	 */
			    	let args = [...arguments];
			    	
			    	/**
			    	 * Get event
			    	 */
			    	let event = args ? args.shift() : null;
			    	
			    	/**
			    	 * Get handlers
			    	 */
			    	let handlers = this.events[event] || [];
			    	
			    	/**
			    	 * Get data
			    	 */
			    	let data = args ? args.shift() : {};
			    	
			    	/**
			    	 * Get options
			    	 */
			    	let options = args ? args.shift() : {};

			    	/**
			    	 * Quit if no handler
			    	 */
			        if (!handlers || handlers.length < 1)
			        {
			            return;
			        }
			        
			        console.log(event + '(' + handlers.length + ' listeners)');
			        
			        handlers.forEach(function(handler)
			        {
			        	handler(data, options);
			        });
			    }
			}
		})(),
		Request: (function()
		{
			return {
				get: function(url, params, callback)
				{
					this.execute('GET', [url,this.serialize(params)].join('?'), callback).send(null);
				},
				post: function(url, params, callback) 
				{
					this.execute('POST', url, callback).send(this.serialize(params));
				},
				execute: function(method, url, callback)
				{
					try 
					{
						var request = new XMLHttpRequest();
	
						request.open(method, url, true);
	
						request.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
						request.setRequestHeader('X-Requested-With','XMLHttpRequest');
	
						request.addEventListener('load', () => 
						{
							let response;
							
							if ('application/json' === request.getResponseHeader("Content-Type"))
							{
								response = JSON.parse(request.responseText);
							}
							else
							{
								response = request.responseText;
							}
							
							if ('function' === typeof callback)
							{
								callback(response);
							}
						});
					}
					catch (e)
					{
						console.log(e.message);
						
						return null;
					}
					

					return request;
				},
				serialize: function(entity, prefix) 
				{
	                var query = [];

	                Object.keys(entity).map(key =>  
	                {
	                	var k = prefix ? prefix + "[" + key + "]" : key, value = entity[key];

	                	query.push((value !== null && typeof value === "object") ? this.serialize(value, k) : encodeURIComponent(k) + "=" + encodeURIComponent(value));
	              	});

	                return query.join("&");
	            }
			}
		})(),
		UUID: (() => 
		{
			return {
				generate: event => 
				{
					event = event || {};
					
					let uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) 
					{
					    var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
					    return v.toString(16);
					});
					
					if (-1 == ['AddToCart'].indexOf(event))
					{
						try 
						{
							let current = AEC.Cookie.get('facebook_latest_uuid');
							
							if (current)
							{
								current = JSON.parse(current);
								
								if (current.hasOwnProperty('uuid') && current.hasOwnProperty('event'))
								{
									if (event.event === current.event)
									{
										uuid = current.uuid;
									}
								}
							}
						}
						catch (e){}
					}
					
					event['uuid'] = uuid;
					
					/**
					 * Set facebook uuid cookie
					 */
					if (AEC.Const.COOKIE_DIRECTIVE)
					{
						if (AEC.Const.COOKIE_DIRECTIVE_CONSENT_GRANTED)
						{
							AEC.Cookie.set('facebook_latest_uuid', JSON.stringify(event));
						}
					}
					else 
					{
						AEC.Cookie.set('facebook_latest_uuid', JSON.stringify(event));
					}
					
					return uuid;
				}
			}
		})()
	}
})();