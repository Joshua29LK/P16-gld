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
], function ($) {
    'use strict';
    $('.Bss_image_multiselect img').css('border', 'solid 2px #ddd');
    $.widget('bss.bss_preview_multiple', {
        _create: function () {
            var $widget = this;
            $widget.element = $widget.element.parent().parent();
            var url = $widget.updateImage($widget);
            $widget.cartOptionUpdate($widget, url);
            $widget.updateEventListener($widget, url);
        },
        updateEventListener: function ($widget, url) {
            var viewType = this.options.viewType,
                width = this.options.imageWidth,
                height = this.options.imageHeight;

            $widget.element.find('.product-custom-option.multiselect').change(function () {
                var values = [];
                var html = '';
                if (viewType == 0) {
                    $(this).each(function () {
                        values.push($(this).val());
                    });
                    $.each(values[0],function (index, vl) {
                        if(typeof url[vl] !== "undefined") {
                            if (url[vl].length > 0) {
                                html += '<img id ="image_preview_'+vl+'" alt="" src="' +
                                    url[vl] +
                                    '" title="' +
                                    $("option[value=" + vl + "]").html() +
                                    '" style="height: ' +
                                    height +
                                    'px; width: ' +
                                    width +
                                    'px; border: solid 1px #ddd;" />';
                            }
                        }
                    });
                    if (html.length > 0) {
                        $widget.element.find('.Bss_image_multiselect').html(html);
                        $widget.element.find('.Bss_image_multiselect').fadeIn();
                    } else {
                        $widget.element.find('.Bss_image_multiselect').fadeOut();
                    }
                } else if (viewType == 1) {
                    $(this).each(function () {
                        values.push($(this).val());
                    });
                    $widget.element.find('.Bss_image_multiselect img').css('border', 'solid 2px #ddd');
                    $.each(values[0],function (index, vl) {
                        $('#image_preview_' + vl).css('border', 'solid 2px #d33');
                    });
                }
            });
            $widget.element.on("click", "img", function() {
                var imageId = $(this).attr('id').split('_'),
                    optionId = imageId[2],
                    multifield = $widget.element.find('.product-custom-option'),
                    multifieldVal = multifield.val();
                if (multifieldVal == null) {
                    multifieldVal = [];
                }
                if (multifieldVal.indexOf(optionId) >= 0) {
                    multifieldVal.splice(multifieldVal.indexOf(optionId), 1);
                    multifield.val(multifieldVal).trigger('change');
                } else {
                    multifieldVal.push(optionId);
                    multifield.val(multifieldVal).trigger('change');
                }
            });
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

            if (this.options.viewType == 1) {
                $('.Bss_image_multiselect img').css('width', width + 'px')
                    .css('height', height + 'px');
                $('.Bss_image_multiselect').fadeIn();
            }

            var multiCartOptions = $widget.element.find('.product-custom-option.multiselect');

            var values = [];
            var html = '';
            if (viewType == 0) {
                $(multiCartOptions).each(function () {
                    values.push($(multiCartOptions).val());
                });
                $.each(values[0],function (index, vl) {
                    if (url[vl].length > 0) {
                        html += '<img alt="" src="' +
                            url[vl] +
                            '" title="' +
                            $("option[value=" + vl + "]").html() +
                            '" style="height: ' +
                            height +
                            'px; width: ' +
                            width +
                            'px; border: solid 1px #ddd;" />';
                    }
                });
                if (html.length > 0) {
                    $widget.element.find('.Bss_image_multiselect').html(html);
                    $widget.element.find('.Bss_image_multiselect').fadeIn();
                } else {
                    $widget.element.find('.Bss_image_multiselect').fadeOut();
                }
            } else if (viewType == 1) {
                $(multiCartOptions).each(function () {
                    values.push($(multiCartOptions).val());
                });
                $widget.element.find('.Bss_image_multiselect img').css('border', 'solid 2px #ddd');
                $.each(values[0],function (index, vl) {
                    $('#image_preview_' + vl).css('border', 'solid 2px #d33');
                });
            }
        }
    });
    return $.bss.bss_preview_multiple;
});
