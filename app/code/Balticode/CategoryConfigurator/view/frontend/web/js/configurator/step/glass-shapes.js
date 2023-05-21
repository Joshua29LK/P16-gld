define([
    'dynamicStep',
    'jquery',
    'uiRegistry',
    'ko'
], function (DynamicStep, $, uiRegistry, ko) {
    'use strict';

    return DynamicStep.extend({
        dimensionErrorText: ko.observable(),
        defaults: {
            glassShapeTypeCarouselInitialized: false,
            errorFieldSelector: '#glass-shape-dimension-error',
            glassShapeTypes: [],
            tracks: {
                glassShapeTypes: true,
                errorText: true
            }
        },

        changeSelectedProduct: function (element) {
            this.changeSelectedProductClass(element, this.selector);
            this.selectDefaultWidthOptions();
            this.selectDefaultGlassShapeTypes();
            this.updateSelectedProductVariables(element);
            this.updateGlassShapeTypes();
        },

        updateGlassShapeTypes: function () {
            if (this.glassShapeTypeCarouselInitialized) {
                this.destroyCarousel(this.glassShapeTypesSelector);
                this.glassShapeTypeCarouselInitialized = false;
            }

            if (!this.selectedProduct.hasOwnProperty('product_configurations')) {
                this.glassShapeTypes = [];
                
                return false;
            }

            this.glassShapeTypes = this.selectedProduct['product_configurations'];

            this.initGlassShapeTypeCarousel();
            this.addSelectedClassToSelectedGlassShapeType();
        },

        initGlassShapeTypeCarousel: function () {
            $(this.glassShapeTypesSelector).owlCarousel({
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

            this.glassShapeTypeCarouselInitialized = true;
        },

        selectDefaultGlassShapeTypes: function () {
            var self = this;

            _.each(this.products, function (element) {
                if (!element.hasOwnProperty('selected_configuration') &&
                    element.hasOwnProperty('product_configurations') &&
                    element['product_configurations'].hasOwnProperty('0')
                ) {
                    element['selected_configuration'] = element['product_configurations'][0];
                    self.handlePriceChange(element);
                }
            });
        },

        selectDefaultWidthOptions: function () {
            _.each(this.products, function (element) {
                if (!element.hasOwnProperty('uses_custom_width')) {
                    element['uses_custom_width'] = false;
                }
            });
        },

        selectGlassShapeType: function (data, event) {
            var selectedElement = event.currentTarget;

            if ($(selectedElement).hasClass('selected') === true) {
                return;
            }

            this.selectedProduct['selected_configuration'] = data;
            this.changeSelectedProductClass(selectedElement, this.glassShapeTypesSelector);
            this.handlePriceChange(this.selectedProduct);
        },

        addSelectedClassToSelectedGlassShapeType: function () {
            if (!this.selectedProduct.hasOwnProperty('selected_configuration') ||
                !this.selectedProduct['selected_configuration'].hasOwnProperty('product_id')
            ) {
                return false;
            }

            var selectedProductId = this.selectedProduct['selected_configuration']['product_id'];
            var self = this;

            $('li', $(this.glassShapeTypesSelector)).each(function () {
                var firstInput = $(this).find('input')[0];
                var productId = $(firstInput).val();

                if (productId === selectedProductId) {
                    self.changeSelectedProductClass(this, this.glassShapeTypesSelector)
                }
            });
        },

        calculateGlassShapePrice: function (glassShape) {
            if (!glassShape.hasOwnProperty('selected_configuration')) {
                return false;
            }

            var glassShapeType = glassShape['selected_configuration'];

            if (!glassShapeType.hasOwnProperty('price') || !this.validateGlassShapeDimensionFields(glassShape)) {
                return false;
            }

            var conversionRate = 1000 * 1000;
            var squareMeterPrice = glassShapeType['price'];
            var width = this.determineGlassShapeWidth(glassShape);
            var height = this.parseDimensionValue(glassShape['height']);

            return (squareMeterPrice * height * width) / conversionRate;
        },

        toggleWidthFieldsVisibility: function (data, event) {
            if (!data.hasOwnProperty('uses_custom_width')) {
                return false;
            }

            $(event.currentTarget).parent().parent().children('.width').toggleClass('custom-width');
            data['uses_custom_width'] = !data['uses_custom_width'];

            this.handlePriceChange(data);
        },

        updateGlassShapeDimensions: function (data, event) {
            var modifiedInput = $(event.currentTarget);
            var modifiedFieldName = modifiedInput.attr('name');

            if (typeof modifiedFieldName === 'undefined') {
                return false;
            }

            data[modifiedFieldName] = modifiedInput.val();

            this.handlePriceChange(data);
        },

        determineGlassShapeWidth: function (glassShape) {
            if (!glassShape.hasOwnProperty('width')) {
                return false;
            }

            if (!glassShape.hasOwnProperty('uses_custom_width') || glassShape['uses_custom_width'] === false) {
                return this.parseDimensionValue(glassShape['width']);
            }

            if (!glassShape.hasOwnProperty('top_width') || !glassShape.hasOwnProperty('bottom_width')) {
                return this.parseDimensionValue(glassShape['width']);
            }

            var topWidth = this.parseDimensionValue(glassShape['top_width']);
            var bottomWidth = this.parseDimensionValue(glassShape['bottom_width']);

            if (topWidth > bottomWidth) {
                return topWidth;
            }

            return bottomWidth;
        },

        handlePriceChange: function (glassShape) {
            glassShape['price'] = this.calculateGlassShapePrice(glassShape);
            this.notifyConfiguratorAboutProductChange(this.products);
        },

        validateGlassShapeDimensionFields: function (glassShape) {
            var isValid = true;

            if (!uiRegistry.get(this.configuratorComponentName).checkProductDimensionFields(glassShape)) {
                this.setMissingDimensionFieldValuesMessage();
                isValid = false;
            }

            if (isValid === true && !this.validateGlassShapeHeightValue(this.parseDimensionValue(glassShape['height']), glassShape['glassShapesMinimumHeight'],glassShape['glassShapesMaximumHeight'])) {
                this.setIncorrectHeightMessage(glassShape['glassShapesMinimumHeight'],glassShape['glassShapesMaximumHeight']);
                isValid = false;
            }

            if (isValid === true &&
                !this.usesCustomWidth(glassShape) &&
                !this.validateGlassShapeWidthValue(this.parseDimensionValue(glassShape['width']), glassShape['glassShapesMinimumWidth'],glassShape['glassShapesMaximumWidth'])
            ) {
                this.setIncorrectWidthMessage(glassShape['glassShapesMinimumWidth'],glassShape['glassShapesMaximumWidth']);
                isValid = false;
            }

            if (isValid === true &&
                this.usesCustomWidth(glassShape) &&
                (!this.validateGlassShapeWidthValue(this.parseDimensionValue(glassShape['top_width']), glassShape['glassShapesMinimumWidth'],glassShape['glassShapesMaximumWidth']) ||
                !this.validateGlassShapeWidthValue(this.parseDimensionValue(glassShape['bottom_width']), glassShape['glassShapesMinimumWidth'],glassShape['glassShapesMaximumWidth']))
            ) {
                this.setIncorrectWidthMessage(glassShape['glassShapesMinimumWidth'],glassShape['glassShapesMaximumWidth']);
                isValid = false;
            }

            if (isValid === true) {
                this.hideErrorMessage();
            } else {
                this.showErrorMessage();
            }

            this.setProductValidityFlag(isValid);

            return isValid;
        },

        validateGlassShapeHeightValue: function (value, minHeight, maxHeight) {
            if (minHeight === '') {
                minHeight = this.glassShapesMinimumHeight;
            }
            if (maxHeight === '') {
                maxHeight = this.glassShapesMaximumHeight;
            }
            return !(value === false || value < minHeight || value > maxHeight);
        },

        validateGlassShapeWidthValue: function (value, minWidth, maxWidth) {
            if (minWidth === '') {
                minWidth = this.glassShapesMinimumWidth;
            }
            if (maxWidth === '') {
                maxWidth = this.glassShapesMaximumWidth;
            }
            return !(value === false || value < minWidth || value > maxWidth);
        },

        usesCustomWidth: function (glassShape) {
            return glassShape.hasOwnProperty('uses_custom_width') && glassShape['uses_custom_width'] === true;
        },

        parseDimensionValue: function (value) {
            var parsedValue = parseInt(value);

            if (isNaN(parsedValue)) {
                return false;
            }

            return parsedValue;
        },
        
        showErrorMessage: function () {
            $(this.errorFieldSelector).show();
        },
        
        hideErrorMessage: function () {
            $(this.errorFieldSelector).hide();
        },

        setProductValidityFlag: function (isValid) {
            !uiRegistry.get(this.configuratorComponentName).setSelectedProductsDataValidityFlag(isValid);
        },

        setIncorrectWidthMessage: function (minWidth, maxWidth) {
            if (minWidth === '') {
                minWidth = this.glassShapesMinimumWidth;
            }
            if (maxWidth === '') {
                maxWidth = this.glassShapesMaximumWidth;
            }
            var errorString = 'Voer a.u.b. een breedte in van minimaal ' + minWidth + ' en maximaal ' + maxWidth + ' millimeters.';

            this.dimensionErrorText(errorString);
        },

        setIncorrectHeightMessage: function (minHeight, maxHeight) {
            if (minHeight === '') {
                minHeight = this.glassShapesMinimumHeight;
            }
            if (maxHeight === '') {
                maxHeight = this.glassShapesMaximumHeight;
            }
            var errorString = 'Voer a.u.b. een hoogte in van minimaal ' + minHeight + ' en maximaal ' + maxHeight + ' millimeters.';

            this.dimensionErrorText(errorString);
        },

        setMissingDimensionFieldValuesMessage: function () {
            this.dimensionErrorText('Benodigde afmetingen missen.');
        },

        openGlassShapeTypeModal: function (productId) {
            !uiRegistry.get(this.configuratorComponentName).openProductViewModal(this.glassShapeTypes, productId);
        },

        resetGlassShapeDimensionFieldValue: function (data, event) {
            var modifiedInput = $(event.currentTarget);
            modifiedInput.val('');
        },

        restoreValueIfNotChanged: function (data, event) {
            var modifiedInput = $(event.currentTarget);
            var modifiedFieldName = modifiedInput.attr('name');

            if (modifiedInput.val() !== '' ||
                typeof modifiedFieldName === 'undefined' ||
                !data.hasOwnProperty(modifiedFieldName)
            ) {
                return;
            }

            modifiedInput.val(data[modifiedFieldName]);
        }
    });
});