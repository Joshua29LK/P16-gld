require([
    'jquery',
    'Magento_Ui/js/modal/alert'
], function ($, alert) {
    'use strict';

    $("[name='file']").on('change', function() {
        var self = this;
        var maxFileSize = $("[name='max_file_size']").val();
        var fileSize = this.files[0].size;

        if (fileSize > maxFileSize) {
            var sizeInMB = Math.round(maxFileSize / (1024*1024)) + ' MB';
            var checkLink = '<a href="https://mageprince.com/blog/how-to-increase-file-upload-size" target="_blank">How to increase file upload size</a>'
            var message = 'Maximum allowed file size is ' + sizeInMB +
                ' Check this article to increase file size limit ' + checkLink;
            alert({
                title: 'Big File Size',
                content: message,
                actions: {
                    always: function(){
                        $(self).val('');
                    }
                }
            });
        } else {
            var fileName = this.files[0].name;
            var uploadedFileName = fileName.split('.').shift();
            var uploadedFileType = fileName.split('.').pop().toLowerCase();
            var uploadedFileSize = formatFileSize(this.files[0].size);
            var fileUploadedHtml = '<p><b>File Name: </b>'+ uploadedFileName + '</p>' +
                '<p><b>Size: </b>' + uploadedFileSize + '</p>' +
                '<p><b>Type:</b> ' + uploadedFileType + '</p>';
            $('#uploaded-file-content').html(fileUploadedHtml);
            $('#uploaded-file-content').css('margin-bottom', '30px');

        }
    });

    $('#deletefile').click(function () {
        if(confirm("Are you sure you want to delete file?")){
            return true;
        } else{
            return false;
        }
    });

    $('#productattach_main_file_type').on('change', function() {
        if(this.value == 2) {
            $('.field-file').hide();
        } else {
            $('.field-file').show();
        }
    });

    function formatFileSize(bytes,decimalPoint) {
        if(bytes == 0) return '0 Bytes';
        var k = 1000,
            dm = decimalPoint || 2,
            sizes = ['Bytes', 'KB', 'MB', 'GB'],
            i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
});
