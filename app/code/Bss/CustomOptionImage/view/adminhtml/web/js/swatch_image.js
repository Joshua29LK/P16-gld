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
    'jquery/ui',
    'Magento_Catalog/js/form/element/checkbox',
    'Magento_Ui/js/form/element/single-checkbox'
], function ($, ui, checkbox,singleCheckbox) {
    'use strict';

    return singleCheckbox.extend({
        defaults: {
            bss_swatch_image: '',
            src: '',
            bss_span: '',
            bss_span_class: '',
            bss_option_so: '',
            bss_value_so: '',
            tempImgSwatch: ''
        },
        initConfig: function () {
            this._super();
            var key1 = this.dataScope.split('.')[3];
            var key2 = this.dataScope.split('.')[5];
            this.bss_swatch_image = 'bss_swatch_image' + key1 + '_' + key2;
            this.bss_span = 'bss_span_' + key1 + '_' + key2;
            this.bss_option_so = this.dataScope.split('.')[3];
            this.bss_value_so = this.dataScope.split('.')[5];
            this.uploadFieldId = 'Bss_Customoptionimage_swatch' + this.bss_option_so + '_' + this.bss_value_so;
            this.eventCheck();
            return this;
        },
        eventCheck: function () {
            $("body ").on('mouseover', '.image-customoption-container .swatch-image',function(){
                if ($(this).find('img').attr('src') !='') {
                    $(this).find('.action-delete').show();
                }
                $(this).find('.action-edit').show();
            });
            $("body ").on('mouseleave', '.image-customoption-container .swatch-image',function(){
                $(this).find('.action-delete').hide();
            });
        },
        delSwatch: function () {
            $('#'+this.bss_swatch_image).attr('src','');
            var uploadField = $('#'+this.bss_swatch_image).parents('.image-customoption-container').find('#'+this.uploadFieldId);
            var name = uploadField.attr('name');
            var nameHidden = name.replace('swatch_image_url','swatch_image_url_hidden');
            uploadField.val('');
            $('input[name ="'+nameHidden+'"]').val('delswatch');
            $('input[name ="'+nameHidden+'"]').trigger('change');
        },
        editSwatch: function () {
            document.getElementById(this.uid).click();
        },
        setSrc: function () {
            if (this.src.length == 0) {
                this.src = this.getPreview();
            }
        },
        getSrc: function () {
            var checkExist = $('label[for="' + this.uid + '"]').parent().parent().find('.image-customoption-container .swatch-image').parent();
            var text = $('label[for="' + this.uid + '"]').closest(
                '.admin__fieldset-wrapper-content.admin__collapsible-content._show'
            ).find('.admin__action-multiselect .admin__action-multiselect-text').text();
            if ( checkExist.length && text != 'Drop-down' &&  text != 'Radio Buttons') {
                checkExist.parent().parent().parent().hide();
                checkExist.parentsUntil('.admin__fieldset-wrapper-content.admin__collapsible-content._show').find('table thead tr th span').each(function() {
                    if ($( this ).text() == $.mage.__('Swatch Image')) {
                        $(this).parent().hide();
                    }
                });
            }
            if (checkExist.length && text == 'Drop-down' ||  text == 'Radio Buttons') {
                checkExist.parent().parent().parent().show();
                checkExist.parentsUntil('.admin__fieldset-wrapper-content.admin__collapsible-content._show').find('table thead tr th span').each(function() {
                    if ($( this ).text() == $.mage.__('Swatch Image')) {
                        $(this).parent().show();
                    }
                });
            }
            var imageElement = $('label[for="' + this.uid + '"]').parent().parent().find('.image-customoption-container .swatch-image img').parent();
            if (this.value()) {
                if (imageElement.length > 0) {
                    imageElement.parents('.swatch-image').show();
                    imageElement.parents('.image-customoption-container').find('.image.image-placeholder').hide();
                }
                return this.mediaUrl + this.value();
            } else {
                if (imageElement.length > 0) {
                    imageElement.parents('.swatch-image').hide();
                    imageElement.parents('.image-customoption-container').find('.image.image-placeholder').show();
                }
                return '';
            }
        },
        clickUpload: function () {
            document.getElementById(this.uid).click();
        },
        readUrlSwatch: function () {
            var formData = new FormData(),
                baseUrl = this.baseUrl,
                upFieldId = this.uploadFieldId,
                $widget = this;
            formData.append('temporary_image', $('#' + this.uid)[0].files[0]);
            formData.append('option_sortorder', this.bss_option_so);
            formData.append('value_sortorder', this.bss_value_so);
            var form_key = $("input[name=form_key]").val();
            formData.append('form_key', form_key);
            if (document.getElementById(this.uid).files && document.getElementById(this.uid).files[0]) {
                var file = document.getElementById(this.uid).files[0];
                var extension = file.name.substring(file.name.lastIndexOf('.'));
                var validFileType = ".jpg , .png , .gif";
                if (validFileType.indexOf(extension.toLowerCase()) < 0) {
                    alert("Please select valid file type. The supported file types are .jpg , .png , .gif");
                    return false;
                }
            }
            $.ajax({
                url : baseUrl,
                type : 'POST',
                data : formData,
                processData: false,
                contentType: false,
                success : function (data) {
                    $('#' + upFieldId).val(data);
                    $('#' + upFieldId).parents('.image-customoption-container').find('.swatch-image').show();
                    $('#' + upFieldId).parents('.image-customoption-container').find('.image.image-placeholder').hide();
                    var name = $('#' + upFieldId).attr('name');
                    var nameHidden = name.replace('swatch_image_url','swatch_image_url_hidden');
                    $('input[name ="'+nameHidden+'"]').val(data);
                    $('input[name ="'+nameHidden+'"]').trigger('change');
                    $widget.storageImage(data);
                }
            });
        },
        storageImage: function (data) {
            $('#' + this.bss_swatch_image).attr('src', this.mediaUrl + data);
            $('#' + this.bss_span).attr('class', 'bss-checkbox-del');
            this.tempImgSwatch = this.mediaUrl + data;
        },
        getClass: function () {
            if (this.tempImgSwatch == '') {
                return 'bss-checkbox-null';
            } else {
                return 'bss-checkbox-del'
            }
        }
    });
});
