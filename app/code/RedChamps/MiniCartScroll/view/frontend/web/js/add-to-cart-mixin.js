define(['jquery'], function ($) {
   'use strict';
   
   return function (catalogAddToCart) {
       $.widget('mage.catalogAddToCart', catalogAddToCart, {
           ajaxSubmit: function (form) {
            var result = this._super(form);
            window.ajaxCartDone = true;
            return result;
        }
       });
       return $.mage.catalogAddToCart;
   };
});