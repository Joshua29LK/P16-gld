define([
    'uiComponent',
    'jquery',
    'uiRegistry',
    'priceFormatter',
    'owlCarousel'
], function (Component, $, uiRegistry, priceFormatter) {
    'use strict';

    return Component.extend({
        defaults: {
            products: [],
            configuratorComponentName: 'configurator',
            selectedProductOptions: [],
            carouselProductCount: 0,
            selectedProductHasOptions: false,
            selectedProduct: false,
            isCarouselInitialized: false,
            tracks: {
                products: true,
                selectedProductOptions: true,
                selectedProductHasOptions: true,
                selectedProduct: true
            }
        },

        initialize: function () {
            var self = this;

            this._super();
            this.products = this.productsData;
            this.carouselProductCount = this.stepItemCount;
            this.mobileCarouselProductCount = this.mobileStepItemCount;

            uiRegistry.get('configurator', function (configurator) {
                configurator.handleStepInitialization(self.name);
            });
        },

        selectDefaultProduct: function() {
            var firstItem = $(this.selector).find('li')[0];

            this.changeSelectedProduct(firstItem);
        },

        selectProduct: function (data, event) {
            var selectedElement = event.currentTarget;

            if ($(selectedElement).hasClass('selected') === false) {
                this.changeSelectedProduct(selectedElement);
            }
        },

        changeSelectedProduct: function (element) {
            this.changeSelectedProductClass(element, this.selector);
            this.updateSelectedProductVariables(element);
            this.notifyConfiguratorAboutProductChange([ this.selectedProduct ]);
        },

        changeSelectedProductClass: function (selectedElement, listSelector) {
            this.removeSelectedClass(listSelector);
            this.addSelectedClass(selectedElement);
        },

        removeSelectedClass: function (listSelector) {
            $('li', $(listSelector)).each(function () {
                var currentItem = $(this);

                if (currentItem.hasClass('selected')) {
                    currentItem.removeClass('selected');
                }
            });
        },

        addSelectedClass: function (element) {
            $(element).addClass('selected');
        },

        updateSelectedProductVariables: function (element) {
            var self = this;
            var firstInput = $(element).find('input')[0];
            var productId = $(firstInput).val();

            _.each(this.products, function (element) {
                if (element.hasOwnProperty('product_id') && element['product_id'] === productId) {
                    self.selectedProduct = element;
                    self.updateSelectedProductOptionsVariables();
                }
            });
        },

        updateSelectedProductOptionsVariables: function () {
            if (!this.selectedProduct.hasOwnProperty('options')) {
                this.selectedProductHasOptions = false;
                this.selectedProductOptions = [];

                return;
            }

            this.selectedProductHasOptions = true;
            this.selectedProductOptions = this.selectedProduct['options'];
            this.setDefaultSelectedProductOptionValues();
        },

        initCarousel: function () {
            $(this.selector).owlCarousel({
                "nav": true,
                "dots": false,
                "responsive" : {
                    0 : {
                        "items" : 1
                    },
                    480: {
                        "items" : this.mobileCarouselProductCount
                    },
                    768 : {
                        "items" : this.carouselProductCount
                    }
                },
                navText : [
                    '<span class="carousel-arrow-left"></span>',
                    '<span class="carousel-arrow-right"></span>'
                ]
            });

            this.isCarouselInitialized = true;
        },

        setDefaultSelectedProductOptionValues: function () {
            if (!this.selectedProduct.hasOwnProperty('options')) {
                return;
            }

            var self = this;

            _.each(this.selectedProduct['options'], function (element) {
                if (self.checkForOptionFields(element) && typeof element['values'][0] !== 'undefined') {
                    self.setSelectedOption(element, element['values'][0]);
                }
            });
        },

        setSelectedOption: function (option, optionValue) {
            var selectedOption = {
                option_id: option['option_id'],
                title: option['title'],
                value: optionValue
            };
            
            if (!this.selectedProduct.hasOwnProperty('selected_options')) {
                this.selectedProduct['selected_options'] = [];
            }
            
            var self = this;
            var matchFound = false;

            this.selectedProduct['selected_options'].forEach(function (arrayOption, index) {
                if (arrayOption.hasOwnProperty('option_id') && arrayOption['option_id'] === option['option_id']) {
                    self.selectedProduct['selected_options'][index] = selectedOption;
                    matchFound = true;
                }
            });

            if (matchFound === false) {
                this.selectedProduct['selected_options'].push(selectedOption);
            }
        },

        optionChanged: function (data, event) {
            if (!this.checkForOptionFields(data)) {
                return;
            }

            var self = this;
            var selectedValueId = $(event.currentTarget).val();

            _.each(data['values'], function (element) {
                if (element.hasOwnProperty('option_type_id') && element['option_type_id'] === selectedValueId) {
                    self.setSelectedOption(data, element);
                }
            });

            uiRegistry.get('configurator').updateSelectedProduct(this.name, [ this.selectedProduct ]);
        },

        checkForOptionFields: function (value) {
            return value.hasOwnProperty('values') && value.hasOwnProperty('option_id') && value.hasOwnProperty('title');
        },

        notifyConfiguratorAboutProductChange: function (data) {
            uiRegistry.get('configurator').handleStepProductChange(this.name, data);
        },

        openProductViewModal: function (productId) {
            uiRegistry.get('configurator').openProductViewModal(this.products, productId);
        },

        getFormattedPrice: function (price) {
            return priceFormatter().getFormattedPrice(price);
        },

        getFormattedOptionValueLabel: function (option) {
            var floatPrice = parseFloat(option.price);
            var optionValueLabel = option.title;

            if (isNaN(floatPrice) || floatPrice <= 0) {
                return optionValueLabel
            }

            return optionValueLabel + ' + ' + this.currencySymbol + ' ' + this.getFormattedPrice(floatPrice);
        },

        getSortOrder: function () {
            return this.stepSortOrder;
        }
    });
});