$(function () {
    $('#jUploadedForm').submit(function() { 
        // submit the form 
        $(this).ajaxSubmit({
            success: function (res, status, xhr, $form) {
                parent.objectAttachmentsUploaderCallback();
            }
        }); 
        // return false to prevent normal browser submit and page navigation 
        return false; 
    });

});