define([
    "uiComponent",
    "jquery"
], function (Component,$) {
    "use strict";

    return Component.extend({
        defaults: {
            cartElement: {},
            cartIdentifier: '#configurator-cart-container',
            cartClass: 'fixed-cart',
            extremum: 'max',
            breakPoint: 768
        },

        initialize: function () {
            this._super();
            this.cartElement = $(this.cartIdentifier);

            $(window).resize(function() {
                this.toggleStickyClass();
            }.bind(this));

            $(window).scroll(function () {
                this.toggleStickyClass();
            }.bind(this));

            this.toggleStickyClass();

            return this;
        },

        toggleStickyClass: function () {
            var topOffset = this.cartElement.parent().offset().top;

            if ($(window).scrollTop() > topOffset && !this.isMobile()) {
                this.makeCartSticky();
            } else {
                this.removeStickyCartAttributes();
            }
        },

        setCartWidth: function(width) {
            this.cartElement.width(width);
        },

        getCartParentWidth: function() {
            return this.cartElement.parent().width();
        },

        makeCartSticky: function () {
            this.cartElement.addClass(this.cartClass);
            this.setCartWidth(this.getCartParentWidth());
        },

        removeStickyCartAttributes: function () {
            this.cartElement.removeClass(this.cartClass);
            this.setCartWidth('');
        },

        isMobile: function () {
            return window.matchMedia('(' + this.extremum + '-width: ' + this.breakPoint + 'px)').matches;
        }
    });
});
