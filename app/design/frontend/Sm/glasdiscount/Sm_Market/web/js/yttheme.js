define(["jquery"], function ($) {
    $(document).ready(function () {
        // Megamenu
        $('.sm_megamenu_menu > li > div').parent().addClass('parent-item');

        // Box full width
        var full_width = $('body').innerWidth();
        $('.full-content').css({'width': full_width});

        $(window).resize(function () {
            var full_width = $('body').innerWidth();
            $('.full-content').css({'width': full_width});
        });

        // Fix hover on IOS
        $('body').bind('touchstart', function () {
        });

        // Go to top
        $('#yt-totop').click(function () {
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
		
		$("body").on('click', '#btn-filter, .filter-overlay, #close-btn', function () {
            $('html').toggleClass('show-filter-sidebar');
        });

        $("#yt-totop-fix").hide();
        $(function () {
            var wh = $(window).height();
            var whtml = $(document).height();
            $(window).scroll(function () {
                if ($(this).scrollTop() > whtml / 10) {
                    $('#yt-totop-fix').fadeIn();
                } else {
                    $('#yt-totop-fix').fadeOut();
                }
            });
            $('#yt-totop-fix').click(function () {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                return false;
            });
        });

        // FIX LOI IE INDEX 10

        function fixWidth() {
            if ($('.header-style-10').length && $('.home-page-10').length && full_width > 768) {
                var header_width = $('.header-wrapper > .container').width();
                $('#maincontent').css({'width': header_width + 60, 'display': 'block'});
                $('.header-wrapper').css({'width': header_width + 60, 'display': 'block'});
            }
        }

        fixWidth();

        $(window).resize(function () {
            fixWidth();
        });

    });

});

