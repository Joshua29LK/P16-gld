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
            var videoSettings;

            videoSettings = this.options.videoSettings[0];
            if (typeof Cookiebot != 'undefined') {
                if ($image.find('.cookieconsent-optout-marketing').length == 0) {
                    $image.append('<div style="background-color: rgb(238, 238, 238); position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; display: block;" class=" cookieconsent-optout-marketing">\n' +
                        '<p>De video is niet beschikbaar omdat je niet de juiste cookies daarvoor hebt geselecteerd.</p>\n' +
                        '<a class="btn btn-lg info-btn p-3" style="color:white !important" href="javascript:Cookiebot.renew()"><span>Voorkeuren aanpassen</span></a>\n' +
                        '\n' +
                        '</div>');
                    $image.find('.cookieconsent-optout-marketing').css('padding', "30% 40% 30% 40%");
                    $image.find('.cookieconsent-optout-marketing').css('z-index', "99");
                }
                if (!Cookiebot.consent.marketing) {
                    $('.fotorama-video-container').toggleClass('hide-after');
                }
            }
            $image.find('.' + this.PV).remove();
            $image.append(
                '<div class="' +
                this.PV +
                '" data-related="' +
                videoSettings.showRelated +
                '" data-loop="' +
                videoSettings.videoAutoRestart +
                '" data-type="' +
                videoData.provider +
                '" data-code="' +
                videoData.id +
                '"  data-youtubenocookie="' +
                videoData.useYoutubeNocookie +
                '" data-width="100%" data-height="100%"></div>'
            );
        },
    };

    return function (targetWidget) {
        $.widget('mage.bssCustomMixin', targetWidget, bssCustomMixin);

        return $.mage.bssCustomMixin;
    };
});
