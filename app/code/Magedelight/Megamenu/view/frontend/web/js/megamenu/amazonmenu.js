/*global $ */
define(['jquery','domReady!'], function($) {
    
    'use strict';
    return function(config) {
        // Cache frequently used elements
        var $mdAmazonMenu = $('.md-amazon-menu');
        var $mdAmazonMenuUl = $mdAmazonMenu.find('ul');
        var $mdAmazonMenuFirstUl = $mdAmazonMenuUl.first();
        var $mdMenuBackBtn = $('.md-menu-back-btn');
        var $mdMenuCloseBtn = $('.md-menu-close-btn');

        $('.md-amazon-menu ul:first li').on('click', function(e){
            e.preventDefault();
            var $this = $(this);
            var dataMenuId = $this.find('a').attr('data-menu-id');
            var $dataMenuIdUl = $mdAmazonMenu.find('ul[data-menu-id="'+dataMenuId+'"]');

            $mdAmazonMenuUl.removeClass('hmenu-visible');

            if ($dataMenuIdUl.length) {
                $dataMenuIdUl.addClass('hmenu-visible');
                $this.parent().removeClass('md-translateX').addClass('md-translateX-left');
                $dataMenuIdUl.removeClass('md-translateX-right').addClass('md-translateX');
            } else {
                var $a = $this.find('a');

                if ($a.attr('target')) {
                    $mdAmazonMenuFirstUl.addClass('hmenu-visible');
                    window.open($a.attr('href'), '_blank');
                } else {
                    window.location.href = $a.attr('href');
                }
            }
        });

        $('.amazon-menu-btn > a').on('click', function(e){
            e.preventDefault();
            $('html').addClass('amazon-nav-open amz-nav-open');
            $mdAmazonMenuFirstUl.addClass('hmenu-visible');
        });

        $('.hmenu-back-button').on('click', function(e){
            $mdAmazonMenuFirstUl.addClass('hmenu-visible');
        });

        $mdMenuBackBtn.on('click', function(e){
            e.preventDefault();
            $mdAmazonMenuUl.removeClass('hmenu-visible');
            $mdAmazonMenuFirstUl.addClass('hmenu-visible');
            $(this).parents('ul').removeClass('md-translateX').addClass('md-translateX-right');
            $(this).parents('div.md-amazon-menu-content').find('ul:first').removeClass('md-translateX-left').addClass('md-translateX');
            //$('html').removeClass('amazon-nav-open');
        });

        $mdMenuCloseBtn.on('click', function(e){
            $('html').removeClass('amz-nav-open');
            $mdAmazonMenuUl.removeClass('hmenu-visible');
            $mdAmazonMenuFirstUl.removeClass('md-translateX-left');
        });

    }
});
