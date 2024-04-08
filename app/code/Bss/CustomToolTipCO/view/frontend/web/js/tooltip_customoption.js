define([
    'jquery',
    "mage/translate",
    'mage/mage',
    "domReady!",
    "Magento_Ui/js/modal/modal"
], function ($, modal) {
    'use strict';

    var $modalOption = 0;
    
    $.widget('mage.bsstooltipCustomOption', {
        options: {
            jsonTooltip:[]
            
        },

        _init: function () {
            if (this.options.jsonTooltip !== '') {
                this._RenderToolTip();
            } else {
                console.log('Bss fail');
            }
        },

        _create: function () {
            var $widget = this;
        },

        _RenderToolTip: function () {
            var jsonTooltip = this.options.jsonTooltip;
            var $widget = this;
            var tooltipRadio = 0;
            $('.product-custom-option').each(function () {
                var optionid = $(this).attr('name').match(/\d+/)[0];
                $modalOption = optionid
                var tooltip_content = jsonTooltip[optionid];
                if (tooltip_content && tooltip_content.trim() != '') {
                    var bssOption = $(this).closest(".field");
                    if (this.type == 'radio') {
                        bssOption = $(this).closest(`#bss_options_${optionid}`);
                        if (tooltipRadio !== optionid) {
                            bssOption.children('label').after(`<span class="bss-tooltip-content"><span class="bss-tooltip-customoption" id="`+optionid+`"><i class="far fa-question-circle"></i></span></span>`);
                            tooltipRadio = optionid;
                        }
                    } else {
                        bssOption.children('label').after(`<span class="bss-tooltip-content"><span class="bss-tooltip-customoption" id="`+optionid+`"><i class="far fa-question-circle"></i></span></span>`);
                    }
                }
                $(this).append(`<div id="modal-content-`+optionid+`">
                    <div class="modal-inner-content">
                        `+tooltip_content+`
                    </div>
                    </div>`);
            });

            var jsonTooltip = this.options.jsonTooltip;
            var $widget = this;

            var options = 
            {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    slideshowSpeed: 8000,  
            };

            $(document).ready(function() {
                // event.preventDefault();
                //$('.bss-custom-tooltip').remove();
                
                $(".bss-tooltip-customoption").on('click',function(){
                    var contentPanelId = jQuery(this).attr("id");
                    modal($(`#modal-content-`+contentPanelId+``));
                    $(`#modal-content-`+contentPanelId+``).modal(options).modal("openModal");
                });
                
            })

        }
    });
    return $.mage.bsstooltipCustomOption;
});
