# PostcodeNL Magento 2 integration extension
###MANUAL INSTALLATION >= 2.1.*
1. Unzip to app/code/Hoofdfabriek/PostcodeNL
2. Enable the extension
    >php bin/magento module:enable Hoofdfabriek_PostcodeNL      
    php bin/magento setup:upgrad       
    php bin/magento setup:di:compile       
    php bin/magento cache:flush        
3. Log in to adminpanel and go to Stores -> Configuration -> Hoofdfabriek Extensions -> PostcodeNL and enable PostcodeNL extension

###COMPOSER INSTALLATION >= 2.1.*
1. Make a directory /path/to/zipfiles/;
2. Drop Hoofdfabriek-{packageversion}.zip to that folder
3. Go to magento root folder and run next command:
    >composer config repositories.hoofdfabriek_postcodeNL artifact /path/to/zipfiles/
4. Install the package:
    >composer require "hoofdfabriek/magento2-postcodenl:{packageversion}"
5. Enable the extension
     >php bin/magento module:enable Hoofdfabriek_PostcodeNL     
    php bin/magento setup:upgrade       
    php bin/magento setup:di:compile        
    php bin/magento cache:flush     
6. Log in to adminpanel and go to Stores -> Configuration -> Hoofdfabriek Extensions -> PostcodeNL and enable PostcodeNL extension

###UNINSTALL INSTRUCTIONS
Run next command to remove all related data from the database:      
run in commend line: php -d memory_limit=2048M bin/magento module:uninstall Hoofdfabriek_PostcodeNL  --clear-static-content     
   run in mysql: DELETE FROM `core_config_data` WHERE `path` LIKE '%postcodenl/%';     

If installed via composer:      
rm -rf vendor/hoofdfabriek/magento2-postcodenl      

If installed manually:      
rm -rf app/code/Hoofdfabriek/PostcodeNL     

###KNOWN ISSUES
1. If MyParcelNL_Magento version 2.3.5 extension is used make next changes:       
    In [project root]/vendor/myparcelnl/magento/view/frontend/web/js/checkout/shipping_method/show-myparcel-shipping-method.js replace:
    ```
    define(
    [
        'mage/url',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/checkout-data',
        'jquery',
        'text!MyParcelNL_Magento/template/checkout/options.html',
        'text!MyParcelNL_Magento/css/checkout/options-dynamic.min.css',
        'MyParcelNL_Magento/js/lib/moment.min',
        'MyParcelNL_Magento/js/lib/myparcel'
    ],
    function(mageUrl, uiComponent, quote, customer, checkoutData,jQuery, optionsHtml, cssDynamic, moment) {
        'use strict';
    ```
    to
    ```
    define(
        [
            'mage/url',
            'uiComponent',
            'Magento_Checkout/js/model/quote',
            'Magento_Customer/js/model/customer',
            'Magento_Checkout/js/checkout-data',
            'jquery',
            'text!MyParcelNL_Magento/template/checkout/options.html',
            'text!MyParcelNL_Magento/css/checkout/options-dynamic.min.css',
            'MyParcelNL_Magento/js/lib/moment.min',
            'uiRegistry',
            'MyParcelNL_Magento/js/lib/myparcel'
        ],
        function(mageUrl, uiComponent, quote, customer, checkoutData,jQuery, optionsHtml, cssDynamic, moment, registry) {
            'use strict';
    ```
    replace _setAddress() function:
    ```javascript
    function _setAddress() {
                if (customer.isLoggedIn() &&
                    typeof quote !== 'undefined' &&
                    typeof quote.shippingAddress !== 'undefined' &&
                    typeof quote.shippingAddress._latestValue !== 'undefined' &&
                    typeof quote.shippingAddress._latestValue.street !== 'undefined' &&
                    typeof quote.shippingAddress._latestValue.street[0] !== 'undefined'
                ) {
                    var street0 = quote.shippingAddress._latestValue.street[0];
                    if (typeof street0 === 'undefined') street0 = '';
                    var street1 = quote.shippingAddress._latestValue.street[1];
                    if (typeof street1 === 'undefined') street1 = '';
                    var street2 = quote.shippingAddress._latestValue.street[2];
                    if (typeof street2 === 'undefined') street2 = '';
                    var country = quote.shippingAddress._latestValue.countryId;
                    if (typeof country === 'undefined') country = '';
                    var postcode = quote.shippingAddress._latestValue.postcode;
                    if (typeof postcode === 'undefined') postcode = '';
                } else {
                    var street0 = jQuery("input[name='street[0]']").val();
                    if (typeof street0 === 'undefined') street0 = '';
                    var street1 = jQuery("input[name='street[1]']").val();
                    if (typeof street1 === 'undefined') street1 = '';
                    var street2 = jQuery("input[name='street[2]']").val();
                    if (typeof street2 === 'undefined') street2 = '';
                    var country = jQuery("select[name='country_id']").val();
                    if (typeof country === 'undefined') country = '';
                    var postcode = jQuery("input[name='postcode']").val();
                    if (typeof postcode === 'undefined') postcode = '';
                }
    
                window.mypa.address = [];
                window.mypa.address.street0 = street0.replace(/[<>=]/g,'');
                window.mypa.address.street1 = street1.replace(/[<>=]/g,'');
                window.mypa.address.street2 = street2.replace(/[<>=]/g,'');
                window.mypa.address.cc = country.replace(/[<>=]/g,'');
                window.mypa.address.postcode = postcode.replace(/[\s<>=]/g,'');
            }
    ```
    with
    ```javascript
    function _setAddress()
    {
        if (customer.isLoggedIn() &&
            typeof quote !== 'undefined' &&
            typeof quote.shippingAddress !== 'undefined' &&
            typeof quote.shippingAddress._latestValue !== 'undefined' &&
            typeof quote.shippingAddress._latestValue.street !== 'undefined' &&
            typeof quote.shippingAddress._latestValue.street[0] !== 'undefined'
        ) {
            var street0 = quote.shippingAddress._latestValue.street[0];
            if (typeof street0 === 'undefined') street0 = '';
            var street1 = quote.shippingAddress._latestValue.street[1];
            if (typeof street1 === 'undefined') street1 = '';
            var street2 = quote.shippingAddress._latestValue.street[2];
            if (typeof street2 === 'undefined') street2 = '';
            var country = quote.shippingAddress._latestValue.countryId;
            if (typeof country === 'undefined') country = '';
            var postcode = quote.shippingAddress._latestValue.postcode;
            if (typeof postcode === 'undefined') postcode = '';
        } else {
            var street0 = registry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.street.0').get('value');
            if (typeof street0 === 'undefined') street0 = '';
            var street1 = registry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.street.1').get('value');
            if (typeof street1 === 'undefined') street1 = '';
            if (registry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.street.2')) {
                var street2 = registry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.street.2').get('value');

            } else {
                var street2 = '';
            }
            var country = registry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.country_id').get('value');
            if (typeof country === 'undefined') country = '';
            var postcode = registry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode').get('value');
            if (typeof postcode === 'undefined') postcode = '';
        }

        window.mypa.address = [];
        window.mypa.address.street0 = street0.replace(/[<>=]/g,'');
        window.mypa.address.street1 = street1.replace(/[<>=]/g,'');
        window.mypa.address.street2 = street2.replace(/[<>=]/g,'');
        window.mypa.address.cc = country.replace(/[<>=]/g,'');
        window.mypa.address.postcode = postcode.replace(/[\s<>=]/g,'');
    }
    ```
