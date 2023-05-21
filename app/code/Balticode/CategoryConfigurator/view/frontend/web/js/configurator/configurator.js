define([
    'uiComponent',
    'jquery',
    'ko',
    'uiRegistry'
], function (Component, $, ko, uiRegistry) {
    'use strict';

    return Component.extend({
        productViewModalName: 'product-view-modal',
        configuratorCartComponentName: 'configurator-cart',
        stepsInitialized: false,
        isProductsDataValid: false,
        stepsData: [],

        initialize: function () {
            this._super();

            this.stepsData = this.defaultData;
        },

        manageStepsInitializationProcess: function () {
            var allStepsInitialized = true;

            _.each(this.stepsData, function (stepData) {
                if (stepData.init === false) {
                    allStepsInitialized = false;
                }
            });

            if (allStepsInitialized === false) {
                return;
            }

            this.stepsInitialized = true;
            this.selectDefaults();
        },

        handleStepInitialization: function (stepIdentifier) {
            this.stepsData[stepIdentifier].init = true;
            this.manageStepsInitializationProcess();
        },

        handleStepProductChange: function (stepIdentifier, productsData) {
            this.updateSelectedProduct(stepIdentifier, productsData);

            if (!this.stepsData.hasOwnProperty(stepIdentifier) || !this.isSingleProductArray(productsData)) {
                return;
            }

            var stepParent = this.stepsData[stepIdentifier].parent;

            if (stepParent !== stepIdentifier && productsData[0].hasOwnProperty('product_id')) {
                this.updateRelatedSteps(stepIdentifier, productsData[0]['product_id']);
            }
        },

        updateSelectedProduct: function (stepIdentifier, productsData) {
            this.stepsData[stepIdentifier].value = productsData;
            this.updateCart(stepIdentifier, productsData);
        },

        updateRelatedSteps: function (parentIdentifier, productId) {
            _.each(this.stepsData, function (stepData, stepIdentifier) {
                if (stepData.parent && stepData.parent === parentIdentifier) {
                    uiRegistry.get(stepIdentifier, function (step) {
                        step.updateProducts(productId);
                    });
                }
            });
        },

        selectDefaults: function () {
            _.each(this.stepsData, function (stepData, stepIdentifier) {
                if (!stepData.parent) {
                    uiRegistry.get(stepIdentifier, function (step) {
                        step.selectDefaultProduct();
                    });
                }
            });
        },

        updateCart: function (stepIdentifier, productsData) {
            uiRegistry.get(this.configuratorCartComponentName, function (cartComponent) {
                cartComponent.updateCart(stepIdentifier, productsData);
            });
        },

        prepareSelectedValues: function () {
            var selectedValues = {};
            var self = this;

            _.each(this.stepsData, function (stepData, stepIdentifier) {
                if (!stepData.hasOwnProperty('value')) {
                    return;
                }

                if (Array.isArray(stepData['value']) && stepData['value'].length === 0) {
                    return;
                }

                var processedStepData = self.processStepDataValue(stepData['value']);

                if (processedStepData !== false) {
                    selectedValues[stepIdentifier] = processedStepData;
                }
            });

            return selectedValues;
        },

        processStepDataValue: function (value) {
            var processedValue = {};
            var self = this;

            _.each(value, function (productData, index) {
                var processedProductData = self.processProductData(productData);

                if (processedProductData !== false) {
                    processedValue[index] = processedProductData;
                }
            });

            return processedValue;
        },

        processProductData: function (productData) {
            var processedProductData = {};

            if (!productData.hasOwnProperty('product_id')) {
                return false;
            }

            processedProductData['product_id'] = productData['product_id'];

            if (productData.hasOwnProperty('selected_options')) {
                processedProductData['selected_options'] = this.prepareSelectedOptions(productData['selected_options']);
            }

            if (productData.hasOwnProperty('selected_configuration') &&
                productData['selected_configuration'].hasOwnProperty('product_id') &&
                this.checkProductDimensionFields(productData)
            ) {
                processedProductData['selected_configuration'] = productData['selected_configuration']['product_id'];
                processedProductData = this.appendProductDimensionFields(processedProductData, productData);
            }

            return processedProductData;
        },

        prepareSelectedOptions: function (selectedProductOptions) {
            var selectedOptions = [];
            var self = this;

            _.each(selectedProductOptions, function (selectedOption, index) {
                var optionValue = self.processOptionValues(selectedOption);

                if (optionValue !== false) {
                    selectedOptions[index] = optionValue;
                }
            });

            return selectedOptions;
        },

        processOptionValues: function (option) {
            if (!option.hasOwnProperty('option_id') ||
                !option.hasOwnProperty('value') ||
                !option['value'].hasOwnProperty('option_type_id')
            ) {
                return false;
            }

            return {
                option_id: option['option_id'],
                option_type_id: option['value']['option_type_id']
            };
        },

        checkProductDimensionFields: function (productData) {
            return productData.hasOwnProperty('height') &&
                productData.hasOwnProperty('width') &&
                productData.hasOwnProperty('top_width') &&
                productData.hasOwnProperty('bottom_width') &&
                productData.hasOwnProperty('uses_custom_width');
        },

        appendProductDimensionFields: function (processedProductData, productData) {
            processedProductData['height'] = productData['height'];
            processedProductData['width'] = productData['width'];
            processedProductData['uses_custom_width'] = this.processCustomWidthFlag(productData['uses_custom_width']);

            if (productData['uses_custom_width'] === false) {
                return processedProductData;
            }

            processedProductData['top_width'] = productData['top_width'];
            processedProductData['bottom_width'] = productData['bottom_width'];

            return processedProductData;
        },

        isSingleProductArray: function (productArray) {
            return productArray instanceof Array && productArray.length === 1 && productArray.hasOwnProperty('0');
        },

        processCustomWidthFlag: function(customWidthFlag) {
            if (customWidthFlag === true) {
                return 1;
            }

            return 0;
        },

        openProductViewModal: function (productList, productId) {
            this.getChild(this.productViewModalName).open(productList, productId);
        },

        isSelectedProductsDataValid: function () {
            return this.isProductsDataValid;
        },

        setSelectedProductsDataValidityFlag: function (isValid) {
            this.isProductsDataValid = isValid;
        }
    });
});