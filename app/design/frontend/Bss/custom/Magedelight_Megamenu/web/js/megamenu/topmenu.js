/*global $ */
define(['jquery','domReady!'], function($) {

    'use strict';
    return function(config) {
        var animation_time = config.animation_time;
        var nav = $('.horizontal-menu');

        if (nav.length) {
            var stickyHeaderTop = nav.offset().top;
            var $window = $(window);
            var $stickyMenu = $('.horizontal-menu .stickymenu');
            var $horizontalMenu = $('.section-item-content .menu-container.horizontal-menu');
            var $stickyAlias = $('.stickyalias');
            var $dropdowns = $('.section-item-content .menu-container.horizontal-menu .menu > ul li.dropdown');
            var displayOverlay = config.display_overlay;

            $window.scroll(function() {
                if ($window.width() >= 768) {
                    if ($window.scrollTop() > stickyHeaderTop) {
                        if ($stickyMenu.hasClass('vertical-right')) {
                            var outerWidth = $('.section-items.nav-sections-items').width();
                            var innerWidth = $horizontalMenu.width();
                            var rightMargin = ((outerWidth - innerWidth) / 2) + 'px';
                            $stickyMenu.css({ position: 'fixed', top: '0px', right: rightMargin });
                        } else {
                            $stickyMenu.css({ position: 'fixed', top: '0px' });
                        }
                        $stickyAlias.css('display', 'block');
                    } else {
                        $stickyMenu.css({ position: 'static', top: '0px' });
                        $stickyAlias.css('display', 'none');
                    }
                }
            });

            $dropdowns.children('a').after('<span class="marker"><i class="icon-caret-down"></i></span>');

            $dropdowns.find('span.marker').click(function() {
                $(this).parent('li').addBack().toggleClass('active');
                $(this).toggleClass('active').siblings('ul').slideToggle('500');
                var icon = $(this).find('i');
                if (icon.hasClass('icon-caret-down')) {
                    icon.removeClass('icon-caret-down').addClass('icon-caret-up');
                } else {
                    icon.removeClass('icon-caret-up').addClass('icon-caret-down');
                }
            });

            if (displayOverlay) {
                var $menuItems = $('.horizontal-menu ul li');
                $menuItems.mouseover(function() {
                    $('body').addClass('md-overlay-bg');
                });
                $menuItems.mouseout(function() {
                    $('body').removeClass('md-overlay-bg');
                });
            }
        }
    }
});
