define([
    'abstractStep',
    'jquery',
    'uiRegistry',
    'owlCarousel'
], function (AbstractStep, $, uiRegistry) {
    'use strict';

    return AbstractStep.extend({
        defaults: {
            tracks: {
                hasProducts: true
            }
        },

        updateProducts: function (productId) {
            var self = this;

            $.ajax({
                url: this.action,
                type: 'POST',
                data : {'product': productId},
                showLoader: true,
                success: function (data) {
                    self.updateContent(data);
                }
            });
        },

        updateContent: function (data) {
            if (this.isCarouselInitialized) {
                this.destroyCarousel(this.selector);
                this.isCarouselInitialized = false;
            }

            this.products = data;

            if (Array.isArray(this.products) && this.products.length === 0) {
                return this.handleEmptyProductsArray();
            }

            this.hasProducts = true;
            this.initCarousel();
            this.selectDefaultProduct();
        },

        destroyCarousel: function (selector) {
            $(selector).owlCarousel('destroy');
            $(selector).find('li').remove();
        },
        
        handleEmptyProductsArray: function () {
            this.hasProducts = false;
            uiRegistry.get('configurator').handleStepProductChange(this.name, this.products);
        }
    });
});