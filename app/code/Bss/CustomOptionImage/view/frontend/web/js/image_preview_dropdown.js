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
], function ($, _) {
    'use strict';

    $.widget('bss.bss_preview', {
        _create: function () {
            var $widget = this;
            $widget.element = $widget.element.parent().parent();
            var url = $widget.updateImage($widget);
            $widget.cartOptionUpdate($widget, url);
            $widget.updateEventListener($widget, url);
            var gallery = $('[data-gallery-role=gallery-placeholder]', '.column.main');
            gallery.data('gallery') ?
                $widget._onGalleryLoaded($widget, gallery) :
                gallery.on('gallery:loaded', $widget._onGalleryLoaded.bind(this, $widget, gallery));
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
        _setImageIndex: function (images) {
            var length = images.length,
                i;

            for (i = 0; length > i; i++) {
                images[i].i = i + 1;
            }

            return images;
        },
        updateEventListener: function ($widget, url) {
            var viewType = this.options.viewType;

            $widget.element.find('select.product-custom-option:not(.multiselect)').change(function () {
                var initialImages = $widget.options.gallery ? $widget.options.gallery.returnCurrentImages() : [];
                if (viewType == 0) {
                    var element = $widget.element.find('.Bss_image_preview img');
                    //add swatch image
                    $widget.updateGalleryImage($widget, $(this).val());
                    //end add swatch image
                    if (typeof url[$(this).val()] == 'string' && url[$(this).val()].length > 0) {
                        element.attr('src', url[$(this).val()]);
                        element.attr('title', $("option[value=" + $(this).val() + "]").html());
                        $widget.element.find('.Bss_image_preview').fadeIn();
                    } else {
                        if (initialImages.length > 0 && $(this).val() === "") {
                            //remove swatch image
                            $widget.removeSwatchImage($widget);
                            //end remove swatch image
                        }
                        $widget.element.find('.Bss_image_preview').fadeOut();
                    }
                } else if (viewType == 1) {
                    //add swatch image
                    var optionValueId = $(this).val();
                    $widget.updateGalleryImage($widget, optionValueId);
                    // end add swatch image
                    if (typeof url[$(this).val()] == 'string') {
                        var element = $widget.element.find('#image_preview_' + $(this).val());
                        $widget.element.find('.Bss_image_preview img').css('border','solid 2px #ddd');
                        element.css('border','solid 2px #d33');
                    } else {
                        if (initialImages.length > 0 && $(this).val() === "") {
                            //remove swatch image
                            $widget.removeSwatchImage($widget);
                            //end remove swatch image
                        }
                        $widget.element.find('.Bss_image_preview img').css('border','solid 2px #ddd');
                    }
                }
            });
            $widget.element.on("click", "img", function() {
                var imageId = $(this).attr('id').split('_'),
                    optionId = imageId[2],
                    dropdown = $widget.element.find('.product-custom-option');
                if ($widget.options.viewType == 1) {
                    if (dropdown.val() == optionId) {
                        dropdown.val('').trigger('change');
                    } else {
                        dropdown.val(optionId).trigger('change');
                    }
                } else {
                    dropdown.val('').trigger('change');
                }
            });
        },
        updateGalleryImage: function ($widget, optionValueId) {
            var initialImages = $widget.options.gallery ? $widget.options.gallery.returnCurrentImages() : [];
            var images = $widget.options.swatchUrls[optionValueId];
            var imagesToUpdate = images ? $widget._setImageType($.extend(true, [], images)) : [];
            if ($widget.options.gallery) {
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
        },
        updateImage: function ($widget) {
            var result = [];
            $.each($widget.options.imageUrls, function (index, image) {
                result[image.id] = image.url;
            });
            return result;
        },
        cartOptionUpdate: function ($widget, url) {
            var viewType = this.options.viewType,
                width = this.options.imageWidth,
                height = this.options.imageHeight;

            $('.Bss_image_preview img').css('width', width + 'px')
                .css('height', height + 'px');
            if (this.options.viewType == 1) {
                $('.Bss_image_preview').fadeIn();
            }

            var dropdownCartOptions = $widget.element.find('select.product-custom-option:not(.multiselect)');

            if (viewType == 0) {
                var element = $widget.element.find('.Bss_image_preview img');
                if (typeof url[$(dropdownCartOptions).val()] == 'string' && url[$(dropdownCartOptions).val()].length > 0) {
                    element.attr('src', url[$(dropdownCartOptions).val()]);
                    element.attr('title', $("option[value=" + $(dropdownCartOptions).val() + "]").html());
                    $widget.element.find('.Bss_image_preview').fadeIn();
                } else {
                    $widget.element.find('.Bss_image_preview').fadeOut();
                }
            } else if (viewType == 1) {
                if (typeof url[$(dropdownCartOptions).val()] == 'string') {
                    var element = $widget.element.find('#image_preview_' + $(dropdownCartOptions).val());
                    $widget.element.find('.Bss_image_preview img').css('border','solid 2px #ddd');
                    element.css('border','solid 2px #d33');
                } else {
                    $widget.element.find('.Bss_image_preview img').css('border','solid 2px #ddd');
                }
            }
        }
    });
    return $.bss.bss_preview;
});
