define([
    'Magento_Ui/js/form/element/abstract',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-rate-registry'
], function(Abstract, $, quote, rateRegistry) {
    'use strict';

    return Abstract.extend({
        initialize: function () {
            this._super();
            this.triggerCheck();
            return this;
        },

        /**
         * trigger click on firstload
         */
        triggerCheck: function() {
            $('#radio-consumer').trigger('click');
        },

        /**
         * Handle radio click, return true to check te radio
         */
        click: function(data, event) {
            this.change(event.target.value);

            return true;
        },

        /**
         * Change value of radio
         */
        change: function(value) {
            if (value === 'company') {
                $('#co-shipping-form [name="shippingAddress.vat_id"]').show();
                $('#co-shipping-form [name="shippingAddress.company"]').show();
                var inputVat = $('#co-shipping-form [name="shippingAddress.vat_id"] :input');
                inputVat.attr('aria-required', 'true');
                inputVat.attr('aria-invalid', 'true');
                inputVat.attr('data-validate', '{required:true}');
            } else if (value === 'consumer') {
                $('#co-shipping-form [name="shippingAddress.vat_id"]').find(':input').val('').change();
                var address = quote.shippingAddress();
                address.vatId = "";
                //address.trigger_reload = new Date().getTime();
                rateRegistry.set(address.getKey(), null);
                rateRegistry.set(address.getCacheKey(), null);
                quote.shippingAddress(address);
                $('#co-shipping-form [name="shippingAddress.vat_id"]').hide();
                $('#co-shipping-form [name="shippingAddress.company"]').hide();
            }
        }
    });
});
