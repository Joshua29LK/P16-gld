/*global $ */
require(['jquery'], function($) {
    $(document).ready(function() {
        jQuery('.md-menu-close-btn').on('click',function(){
           jQuery('html').removeClass('nav-open');

        });

        /* Fix issue for Mobile when click on the menu it is close every time. */
        /*$('body').click(function(e) {
            if ($('html').hasClass('nav-open')){
                jQuery('html').removeClass('nav-open');
            }
        });*/
    });
});
