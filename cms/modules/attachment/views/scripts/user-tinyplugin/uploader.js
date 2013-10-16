$(function () {
    
    $('#fileupload').fileupload({
        dataType: 'json',
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#jProgress .bar').css(
                'width',
                progress + '%'
            );
        },
                
        add: function (e, data) {
            
            $('#jProgress').show();
            
            var jqXHR = data.submit()
                .success(function (result, textStatus, jqXHR) {
                    
                    attachPreview = '';
                    if(result.thumb) {
                        attachPreview = '<img src="'+result.thumb+'">';
                    } else {
                        attachPreview = '<a href="'+result.file+'" target="_blank">'+result.name+'</a>';
                    }
                    
                    if(!$('#jInsertAttachmentForm').is(":visible")) {

                        $('#jAttId').val(result.id);
                        
                        if(result.thumb2x) {
                            attachPreview = '<img src="'+result.thumb2x+'">';
                        }
                        $('#jFirstAttachment').html(attachPreview);
                        
                        $('.jAddFileButton').html('Stwórz galerię');
                        $('#jInsertAttachmentForm').show();
                        $('#jInsertAttachmentInit').hide();
                        
                    } else {

                        html = '<td><input name="att_item['+result.id+']" type="checkbox" checked="checked" > </td>';
                        
                        html += '<td>'; 
                        if(result.thumb) {
                            html += attachPreview;
                        } else {
                            html += attachPreview;
                        }
                        html += '</td>';
                        
                        html += '<td><textarea name="att_desc['+result.id+']" class="input-xxlarge"></textarea></td>';
                        
                        $('#jGallery > table > tbody').append('<tr>'+html+'</tr>');
                        
                        $('#jImgAlign').hide();
                        
                        $('.jAddFileButton').html('Dodaj kolejne zdjęcia');
                        
                        if(!$('#jGallery').is(":visible")) {
                            $('#jGallery').show();
                        }
                    }
                    
                    
                })
                .error(function (jqXHR, textStatus, errorThrown) {
                })
                .complete(function (result, textStatus, jqXHR) {
                });
                
        },
        done: function (e, data) {
            $.each(data.files, function (index, file) {
                //alert(file.name);
            });
            
            $('#jProgress').delay(3000).hide();
        }
    });
});