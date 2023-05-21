define(['jquery'], function ($) {
    'use strict';

    return function (Component) {
        var miniCart = $('[data-block=\'minicart\']');
        return Component.extend({
            update: function (updatedCart) {
                var result = this._super(updatedCart);
                if(window.ajaxCartDone) {
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                    miniCart.find('[data-role="dropdownDialog"]').dropdownDialog('open');
                    window.ajaxCartDone = false;
                }
               return result;
            }
        });
    }
});