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
 * @package    Bss_Faqs
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

define([
    'jquery',
    'underscore',
    'jquery/ui',
    'jquery/jquery.parsequery'
], function ($, _) {
    'use strict';

    $.widget('bss.faq_image', {
        _create: function () {
            var $widget = this;
            $('.category_label').css('background-color', $('#color').val())
            $widget.element.on('change', '#faq_image', function () {
                return $widget._OnChange($(this), $widget);
            });
            $widget.element.on('click', '#del_image', function () {
                return $widget._OnClickDel($(this), $widget);
            });
            $widget.element.on('click', '#reset_upload', function () {
                return $widget._OnClickReset($(this), $widget);
            });
            $widget.element.on('change', '#color', function () {
                return $widget._ColorChange($(this), $widget);
            });
            $widget.element.on('change', '#faq_id', function () {
                return $widget._UpdateFaqIdShow($(this).val(), $widget);
            });
        },
        _OnChange: function ($this, $widget) {
            if (document.getElementById($this.attr('id')).files && document.getElementById($this.attr('id')).files[0]) {
                var file = document.getElementById($this.attr('id')).files[0];
                var extension = file.name.substring(file.name.lastIndexOf('.'));
                var validFileType = ".jpg , .png , .bmp";
                if (validFileType.indexOf(extension.toLowerCase()) < 0) {
                    alert("Please select valid file type. The supported file types are .jpg , .png , .bmp");
                    return false;
                }
                var reader = new FileReader();
                var imgId = 'faq_category_image';
                reader.onload = function (event) {
                    document.getElementById(imgId).src = event.target.result;
                }
                reader.readAsDataURL(document.getElementById($this.attr('id')).files[0]);
            }
        },
        _OnClickDel: function ($this, $widget) {
            $('#faq_category_image').attr('src', $widget.options.emptyImg);
            $('#delimage').val('1');
        },
        _OnClickReset: function ($this, $widget) {
            $('.faq_image').val('');
            $('#faq_category_image').attr('src', $widget.options.srcImage);
            $('#delimage').val('0');
        },
        _ColorChange: function ($this, $widget) {
            $('.category_label').css('background-color', $this.val());
        },
        _UpdateFaqIdShow: function ($newValue, $widget) {
            var $oldValue = $('#old_faq_id_to_show').val().split(';'), result = [];
            $('#faq_id_to_show').html('');
            $.each($newValue, function (index, value) {
                $('#faq_id_to_show').append('<option value="' + value + '">Question ID: ' + value + '</option>');
                if ($oldValue.indexOf(value) >= 0) {
                    result.push(value);
                }
            });
            $('#faq_id_to_show').val(result);
        }
    });
    return $.bss.faq_image;
});
