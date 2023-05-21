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
            bss_coi_image: '',
            src: '',
            bss_span: '',
            bss_span_class: '',
            bss_option_so: '',
            bss_value_so: '',
            tempCoiImage: ''
        },
        initConfig: function () {
            this._super();
            var key1 = this.dataScope.split('.')[3];
            var key2 = this.dataScope.split('.')[5];
            this.bss_coi_image = 'bss_coi_image' + key1 + '_' + key2;
            this.bss_span = 'bss_span_' + key1 + '_' + key2;
            this.bss_option_so = this.dataScope.split('.')[3];
            this.bss_value_so = this.dataScope.split('.')[5];
            this.uploadFieldId = 'Bss_CustomOptionImage' + this.bss_option_so + '_' + this.bss_value_so;
            this.eventCheck();
            return this;
        },
        eventCheck: function () {
            $("body ").on('mouseover', '.image-customoption-container .coi-image',function(){
                if ($(this).find('img').attr('src') !='') {
                    $(this).find('.action-delete').show();
                }
                $(this).find('.action-edit').show();
            });
            $("body ").on('mouseleave', '.image-customoption-container .coi-image',function(){
                $(this).find('.action-delete').hide();
            });
        },
        delCoiImage: function () {
            $('#'+this.bss_coi_image).attr('src','');
            var uploadField = $('#'+this.bss_coi_image).parents('.image-customoption-container').find('#'+this.uploadFieldId);
            var name = uploadField.attr('name');
            var nameHidden = name.replace('image_url','bss_image_button');
            uploadField.val('');
            $('input[name ="'+nameHidden+'"]').val('delcoi');
            $('input[name ="'+nameHidden+'"]').trigger('change');
        },
        editCoiImage: function () {
            document.getElementById(this.uid).click();
        },
        setSrc: function () {
            if (this.src.length == 0) {
                this.src = this.getPreview();
            }
        },
        getSrc: function () {
            var imageElement = $('label[for="' + this.uid + '"]').parent().parent().find('.image-customoption-container .coi-image img').parent();
            if (this.value()) {
                if (imageElement.length > 0) {
                    imageElement.parents('.coi-image').show();
                    imageElement.parents('.image-customoption-container').find('.image.image-placeholder').hide();
                }
                return this.mediaUrl + this.value();
            } else {
                if (imageElement.length > 0) {
                    imageElement.parents('.coi-image').hide();
                    imageElement.parents('.image-customoption-container').find('.image.image-placeholder').show();
                }
                return '';
            }
        },
        clickUpload: function () {
            document.getElementById(this.uid).click();
        },
        readUrlImage: function () {
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
                    $('#' + upFieldId).parents('.image-customoption-container').find('.coi-image').show();
                    $('#' + upFieldId).parents('.image-customoption-container').find('.image.image-placeholder').hide();
                    var name = $('#' + upFieldId).attr('name');
                    var nameHidden = name.replace('image_url','bss_image_button');
                    $('input[name ="'+nameHidden+'"]').val(data);
                    $('input[name ="'+nameHidden+'"]').trigger('change');
                    $widget.storageImage(data);
                }
            });
        },
        storageImage: function (data) {
            $('#' + this.bss_coi_image).attr('src', this.mediaUrl + data);
            $('#' + this.bss_span).attr('class', 'bss-checkbox-del');
            this.tempCoiImage = this.mediaUrl + data;
        },
        getClass: function () {
            if (this.bss_coi_image == '') {
                return 'bss-checkbox-null';
            } else {
                return 'bss-checkbox-del'
            }
        }
    });
});
