
function attachFieldCallback(result) 
{

    $('#jAttId').val(result.id);

    attachPreview = '<a href="'+result.file+'" target="_blank">'+result.name+'</a>';
    $('#jFirstAttachment').html(attachPreview);

    if(!$('#jInsertAttachmentForm').is(":visible")) {

        $('#jInsertAttachmentForm').show();
        $('#jInsertAttachmentInit').hide();
        
    }
        
    agiIframePopoverHide();

}

$('#jInsertAttachmentForm').ajaxForm({
    dataType: 'json',
    success: function (res, statusText, xhr, $form)  { 
        if(res.result == 'ok') {
            
            h = {
                'href': res.file,
                'data-type': 'uatt-file',
                'data-id': res.id,
                'data-align': 'file',
                'class': 'att-file'
            };
            
            ed = tinyMCEPopup.editor;
            node = ed.selection.getNode();
            
            if (ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-file') !== -1) {
                
                ed.dom.setAttribs(node,h);
                
            } else {
                //Isert to edditor
                insHtml = '<a class="'+h.class+'" href="'+h.href+'" data-type="'+h['data-type']+'" data-id="'+h['data-id']+'" data-align="'+h['data-align']+'">'+res.description+'</a>'; 
                tinyMCEPopup.editor.execCommand('mceInsertContent', false, insHtml);
            }
            
            //Close
            tinyMCEPopup.close();
            
        }
   
    } 
    
}); 

