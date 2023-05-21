/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_CustomOptionImage
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    'jquery',
    'underscore',
    'mage/template',
    'priceUtils',
    'jquery/ui'
], function ($, _, mageTemplate) {
    'use strict';

    $.widget('bss.coiStaticImage', {
        _create: function () {
            var $widget = this;
            $widget.element = $widget.element.parent().parent();
            $widget.updateImage($widget);
            var gallery = $('[data-gallery-role=gallery-placeholder]', '.column.main');
            gallery.data('gallery') ?
                $widget._onGalleryLoaded($widget, gallery) :
                gallery.on('gallery:loaded', $widget._onGalleryLoaded.bind(this, $widget, gallery));
            if ($('#bundle-slide').length == 0 || $('#bundle-slide').length == 1) {
                $widget.updateStyle($widget, false);
            }
            $('#bundle-slide').click(function () {
                $widget.updateStyle($widget, true);
                console.log($('#bundle-slide').length);
            });
            $widget.element.on("click", ".Bss_image_radio", function() {
                var inputElement = $(this).find('.product-custom-option');
                if (inputElement.attr('type') == 'checkbox') {
                    if (inputElement.prop("checked")) {
                        inputElement.prop("checked", false);
                        $(this).find('img').css('border', '0px');
                        inputElement.trigger('change');
                    } else {
                        inputElement.prop("checked", true);
                        $(this).find('img').css('border', '1px solid red');
                        inputElement.trigger('change');
                    }
                } else if (inputElement.attr('type') == 'radio') {
                    if (inputElement.prop("checked")) {
                        $(this).find('img').css('border', '1px solid red');
                    } else {
                        inputElement.prop("checked", true);
                        $($widget.element).find('img').map(function (index, element) {
                            $(element).css('border', '0px');
                        });
                        //add swatch image
                        $widget.updateGalleryImage($widget, inputElement.val());
                        //end add swatch image
                        $(this).find('img').css('border', '1px solid red');
                        inputElement.trigger('change');
                    }
                }
            });
            $widget.element.on("click", "input", function() {
                var inputElement = $(this).closest('.Bss_image_radio').children('img');
                if ($(this).attr('type') == 'radio') {
                    $($widget.element).find('img').map(function (index, element) {
                        $(element).css('border', '0px');
                    });

                    if (!$(this).prop("checked")) {
                        inputElement.css('border', '0px');
                    } else {
                        inputElement.css('border', '1px solid red');
                    }
                    $widget.removeSwatchImage($widget);
                    var optionValueId = $(this).val();
                    $widget.updateGalleryImage($widget, optionValueId)
                }
            });
        },
        updateGalleryImage: function ($widget, optionValueId) {
            if ($widget.options.gallery) {
                var initialImages = $widget.options.gallery.returnCurrentImages();
                var images = $widget.options.swatchUrls[optionValueId];
                var imagesToUpdate = images ? $widget._setImageType($.extend(true, [], images)) : [];
                $.each( $widget.options.gallery.returnCurrentImages(), function( key, value ) {
                    if (value['option_id']) {
                        if (imagesToUpdate.length) {
                            if (value['option_id'] == imagesToUpdate[0]['option_id']) {
                                initialImages.splice(key , 1);
                            }
                        } else {
                            if (value['option_id'] == $widget.options.optionId) {
                                initialImages.splice(key , 1);
                            }
                        }
                    }
                });
                imagesToUpdate = imagesToUpdate.concat(initialImages);
                imagesToUpdate = $widget._setImageIndex(imagesToUpdate);
                $widget.options.gallery.updateData(imagesToUpdate);
                $widget.options.gallery.first();
            }
        },
        removeSwatchImage: function ($widget) {
            if ($widget.options.gallery) {
                var initialImages = $widget.options.gallery.returnCurrentImages(),
                    imagesToUpdate;
                //remove swatch image
                $.each($widget.options.gallery.returnCurrentImages(), function (key, value) {
                    if (value['option_id']) {
                        if (value['option_id'] == $widget.options.optionId) {
                            initialImages.splice(key, 1);
                        }
                    }
                });
                imagesToUpdate = $widget._setImageIndex(initialImages);
                $widget.options.gallery.updateData(imagesToUpdate);
                $widget.options.gallery.first();
                //end remove swatch image
            }
        },
        _setImageIndex: function (images) {
            var length = images.length,
                i;

            for (i = 0; length > i; i++) {
                images[i].i = i + 1;
            }

            return images;
        },
        _onGalleryLoaded: function ($widget, element) {
            var galleryObject = element.data('gallery');
            $widget.options.gallery = galleryObject;

            $widget.options.mediaGalleryInitial = galleryObject.returnCurrentImages();
        },
        _setImageType: function (images) {
            var initial = this.options.mediaGalleryInitial[0].img;

            if (images[0].img === initial) {
                images = $.extend(true, [], this.options.mediaGalleryInitial);
            } else {
                images.map(function (img) {
                    if (!img.type) {
                        img.type = 'image';
                    }
                });
            }

            return images;
        },
        updateImage: function ($widget) {
            $.each($widget.options.imageUrls, function (index, value) {
                $widget.element.find('input[value="' + value.id + '"]').parent('.field.choice')
                    .append(mageTemplate('<img src="<%- data.url %>" title="<%- data.title %>" alt="" />', {data: value}))
                    .children().wrapAll('<div class="Bss_image_radio"></div>');
            });
        },
        updateStyle: function ($widget, $isBundle) {
            var $element = $widget.element;
            $element.find('.Bss_image_radio img').height($widget.options.imageHeight).width($widget.options.imageWidth);
            $element.find('.Bss_image_radio label').css('width', 'calc(100% - 30px - '+$widget.options.imageWidth+'px - 40px)');
            $element.find('.Bss_image_radio').each(function () {
                if ($isBundle) {
                    if ($(this).width() - Number($widget.options.imageWidth) - 90 < $(this).find('.label').width()) {
                        $(this).find('.price-notice').css('display', 'block');
                    }
                }
                var height = Number($widget.options.imageHeight),
                    labelMargin = Number($widget.options.imageHeight)/2 - 4;
                if ($widget.options.priceDisplayType === 3) {
                    var marginDivision = Number($widget.options.imageHeight)/3;
                    labelMargin = marginDivision  - (marginDivision/10 + 4) ;
                }
                if ($(this).find('.label').height() >= Number($widget.options.imageHeight) - 6) {
                    height = $(this).find('label').height();
                    labelMargin = 5;
                    var imgMargin = (height - Number($widget.options.imageHeight))/2 + 5;
                    $(this).find('img').css('margin-top', imgMargin + 'px');
                }

                $(this).height(height + 10);
                $(this).find('input').css('margin-top', height/2 + 6 + 'px');
                $(this).find('.label').css('margin-left', '40px').css('margin-top', labelMargin + 'px');
            });
        }
    });
    return $.bss.coiStaticImage;
});
