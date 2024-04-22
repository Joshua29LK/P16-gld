/*global $ */
define(['jquery','domReady!'], function($) {
    
    'use strict';
    return function(config) {
        var $mdMegamenuBurger = $('#md-megamenu-burger');
        var html = $('html');
        var $horizontalMenuUlLi = $('.horizontal-menu ul li');

        $mdMegamenuBurger.css('display', 'block');

        /* Check burger menu is enabled */
        var burgerStatus = config.burger_status;
        html.toggleClass('md-burger-menu', burgerStatus);

        var displayOverlay = config.display_overlay;
        if (displayOverlay) {
            $horizontalMenuUlLi.on({
                mouseover: function() {
                    $('body').addClass('md-overlay-bg');
                },
                mouseout: function() {
                    $('body').removeClass('md-overlay-bg');
                }
            });
        }
    }
});
