require(['jquery'], function ($) {
    $(document).ready(function () {
        $('#zip_is_range').change(function () {
            if ($(this).is(':checked')) {
                $('.admin__field.field.field-zip_code').hide();
                $('.admin__field.field.field-range_to').show();
                $('.admin__field.field.field-range_from').show();
            } else {
                $('.admin__field.field.field-zip_code').show();
                $('.admin__field.field.field-range_to').hide();
                $('.admin__field.field.field-range_from').hide();
            }
        });
        
        if ($('#zip_is_range').is(':checked')) {
            $('.admin__field.field.field-zip_code').hide();
            $('.admin__field.field.field-range_to').show();
            $('.admin__field.field.field-range_from').show();
        } else {
            $('.admin__field.field.field-zip_code').show();
            $('.admin__field.field.field-range_to').hide();
            $('.admin__field.field.field-range_from').hide();
        }
    });
});
