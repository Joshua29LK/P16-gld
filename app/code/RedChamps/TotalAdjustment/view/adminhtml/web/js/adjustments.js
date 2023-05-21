require(
    [
        'jquery'
    ], function ($) {
        $(document).ready(function (){
            var bodyElem = $("body");
            bodyElem.on("click",  "#add-adjustments", function () {
                $("body").removeClass('no-adjustments');
                $(this).parent().next('tr').show();
                $(this).parent().hide();
                $(".add-more-adjustments-container").show();

            });

            bodyElem.on("click", "#add-more-adjustments", function () {
                prevAdjRow = jQuery(this).parent().prev('tr');
                newAdjRow  = prevAdjRow.clone().insertBefore(jQuery(this).parent());
                newAdjRow.find('input').val('');
                newAdjRow.find('.amount-value').remove();
                newAdjRow.find('.adjustment-type').val("flat");
            });

            $(document).on('click', ".remove-adjustment", function () {
                tr = jQuery(this).parent().parent();
                title = tr.find('.adjustment-title').val();
                if($(".adjustment-wrapper").length == 1) {
                    jQuery("body").addClass('no-adjustments');
                    tr.find('input').val('');
                    tr.hide();
                    jQuery(".add-adjustments-container").show();
                    jQuery(".add-more-adjustments-container").hide();
                } else {
                    tr.remove();
                }
                if(title) {
                    updateAdjustments();
                }
            });

            bodyElem.on("click", "#save-adjustments", function () {
                updateAdjustments();
            });
        });
    });
function updateAdjustments() {
    jsonObj = [];
    var fail = false;
    var data = {};
    if(!jQuery("body").hasClass('no-adjustments')){
        jQuery(".adjustment-wrapper").each(function() {
            title = jQuery(this).find('.adjustment-title').val();
            amount = jQuery(this).find('.adjustment-amount').val();
            type = jQuery(this).find('.adjustment-type').val();
            if(!title || !amount) {
                alert('One of title or amount field is empty. Please correct and retry.');
                fail = true;
                return false;
            } else if (!jQuery.isNumeric(amount)) {
                alert("One of amount field contains non-numeric value. Please correct and retry.");
                fail = true;
                return false;
            }
            item = {};
            item ["title"] = title;
            item ["amount"] = amount;
            item ["type"] = type;
            jsonObj.push(item);
        });
        if(fail) {
            return false;
        }
        //if order view screen submit form
        if(jQuery("#add-adjustments-form").length) {
            jQuery("#add-adjustments-form").submit();
            return true;
        }
        data['adjustments'] = JSON.stringify(jsonObj);
    } else {
        data['adjustments'] = 'remove-all';
    }
    order.loadArea(['totals'], true, data);
    return true;
}
