var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/form/element/email': {
                'Bss_CheckoutValidation/js/view/form/element/email-mixin': true
            },
            'Magento_Ui/js/view/messages': {
                'Bss_CheckoutValidation/js/view/message-mixin': true
            }
        }
    }
};