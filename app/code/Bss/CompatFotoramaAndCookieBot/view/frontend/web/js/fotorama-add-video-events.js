define(['jquery'], function ($) {
    'use strict';

    var bssCustomMixin = {
        /**
         * Create video container
         *
         * @param {Object} videoData
         * @param {jQuery} $image
         * @private
         */
        _createVideoContainer: function (videoData, $image) {
            if (typeof Cookiebot != 'undefined') {
                if ($image.find('.cookieconsent-optout-marketing').length == 0) {
                    $image.append('<div style="background-color: rgb(238, 238, 238); position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; display: block;" class="cookieconsent-optout-marketing">\n' +
                        '<p>De video is niet beschikbaar omdat je niet de juiste cookies daarvoor hebt geselecteerd.</p>\n' +
                        '<a class="btn btn-lg info-btn p-4 bss-index" onclick="event.stopPropagation()" style="color: white !important;" href="javascript:Cookiebot.renew()"><span>Voorkeuren aanpassen</span></a>\n' +
                        '\n' +
                        '</div>');
                    $image.find('.cookieconsent-optout-marketing').css('padding', "30% 40% 30% 40%");
                    $image.find('.cookieconsent-optout-marketing').css('z-index', "999");
                }
                if (!Cookiebot.consent.marketing) {
                    $('.cookieconsent-optout-marketing').show();
                    $('.fotorama-video-container').addClass('hide-after');
                } else {
                    $('.cookieconsent-optout-marketing').hide();
                    $('.fotorama-video-container').removeClass('hide-after');
                }
            }
            return this._super(videoData, $image);
        },
    };

    return function (targetWidget) {
        $.widget('mage.AddFotoramaVideoEvents', targetWidget, bssCustomMixin);

        return $.mage.AddFotoramaVideoEvents;
    };
});
