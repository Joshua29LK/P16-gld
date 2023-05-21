var config = {
    map: {
        '*': {
            'Magento_Checkout/template/billing-address/form.html':
                'Bss_OneStepCheckout/template/billing-address/form.html',
            'Magento_Checkout/js/model/shipping-rate-service':
                'Bss_OneStepCheckout/js/model/shipping-rate-service',
            'Magento_Checkout/js/action/get-payment-information':
                'Bss_OneStepCheckout/js/action/get-payment-information',
            'ui/template/form/field.html':
                'Bss_OneStepCheckout/templates/form/field.html',
            'ui/template/form/element/input.html':
                'Bss_OneStepCheckout/templates/form/element/input.html',
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Bss_OneStepCheckout/js/model/place-order-mixin': true,
                'Magento_CheckoutAgreements/js/model/place-order-mixin': false
            },
            'Magento_Checkout/js/model/step-navigator': {
                'Bss_OneStepCheckout/js/model/step-navigator-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Magento_CheckoutAgreements/js/model/set-payment-information-mixin': false,
                'Bss_OneStepCheckout/js/model/set-payment-information-mixin': true
            },
            'Magento_Checkout/js/model/shipping-rates-validation-rules': {
                'Bss_OneStepCheckout/js/model/shipping-rates-validation-rules-mixin': true
            },
            'Magento_Paypal/js/in-context/express-checkout-wrapper': {
                'Bss_OneStepCheckout/js/in-context/express-checkout-wrapper-mixin': true
            },
            'Magento_Paypal/js/view/payment/method-renderer/in-context/checkout-express': {
                'Bss_OneStepCheckout/js/view/payment/method-renderer/in-context/checkout-express-mixin': true
            },
            'Amazon_Payment/js/view/payment/list': {
                'Bss_OneStepCheckout/js/view/amazon-pay/payment-list-mixin': true
            },
            'Amazon_Payment/js/view/checkout-revert': {
                'Bss_OneStepCheckout/js/view/amazon-pay/checkout-revert-rewrite': true
            }
        }
    }
};
