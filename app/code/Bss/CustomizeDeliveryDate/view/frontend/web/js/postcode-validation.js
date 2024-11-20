require([
    'jquery',
    'mage/calendar',
    'Magento_Checkout/js/model/quote'
], function ($, calendar, quote) {
    'use strict';
    
    function updateDayOff(postcode) {
        updateCalendar();
    }
    
    function updateCalendar() {
        window.checkoutConfig.bss_delivery_day_off = "0,1";
        $("#ui-datepicker-div").datepicker('destroy').datepicker($.calendarConfig);
    }
    
    $(document).on('change', '#shipping-postcodenl-postcode', function () {
        var zipcode = $(this).val();
        if (zipcode == 123) {
            updateDayOff(zipcode);
        }
    });
});
