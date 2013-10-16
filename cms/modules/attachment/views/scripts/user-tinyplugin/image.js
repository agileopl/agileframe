
function objectAttachmentsJoinerCallback(responseData) 
{
    $.each(responseData.data, function (i, item){
        addImageToHtml(item);
    });
    agiIframePopoverHide();

}

function addImageToHtml(result)
{
    if(result.thumb) {
        attachPreview = '<img src="'+result.thumb+'">';
    } else {
        attachPreview = '<a href="'+result.file+'" target="_blank">'+result.name+'</a>';
    }
    
    if(!$('#jInsertAttachmentForm').is(":visible")) {

        $('#jAttId').val(result.id);

        if(result.description !== undefined) {
            $('#jFirstDescription').val(result.description);
        }
        
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
        
        html += '<td><textarea name="att_desc['+result.id+']" class="input-xxlarge">'+(result.description !== undefined ? result.description : '')+'</textarea></td>';
        
        $('#jGallery > table > tbody').append('<tr>'+html+'</tr>');
        
        $('#jImgAlign').hide();
        
        $('.jAddFileButton').html('Dodaj kolejne zdjęcia');
        
        if(!$('#jGallery').is(":visible")) {
            $('#jGallery').show();
        }
    }
}

$('#jInsertAttachmentForm').ajaxForm({
    dataType: 'json',
    success: function (res, statusText, xhr, $form)  { 
        if(res.result == 'ok') {
            
            isGallery = $('#jGallery > table > tbody > tr').length > 0;
            h = {
                'href': res.imageBig,
                'data-type': 'uatt-image',
                'data-id': res.id,
                'data-align': isGallery ? 'gallery' : $('#jImgAlign').val(),
                'class': 'att-'+(isGallery ? 'gallery' : $('#jImgAlign').val())
            };
            
            html = '<img src="'+(isGallery || $('#jImgAlign').val() == 'center' ? res.imageThumbCenter : res.imageThumb)+'" />';
            
            insHtml = '<a class="'+h.class+'" href="'+h.href+'" data-type="'+h['data-type']+'" data-id="'+h['data-id']+'" data-align="'+h['data-align']+'">'+html+'</a>'; 
        
            ed = tinyMCEPopup.editor;
            node = ed.selection.getNode();
            
            if (ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-image') !== -1) {
                
                ed.dom.setAttribs(node,h);
                ed.dom.setHTML(node,html);
                
            } else {
                //Isert to edditor
                tinyMCEPopup.editor.execCommand('mceInsertContent', false, insHtml);
            }
            
            //Close
            tinyMCEPopup.close();
            
        }
   
    } 
    
}); 

