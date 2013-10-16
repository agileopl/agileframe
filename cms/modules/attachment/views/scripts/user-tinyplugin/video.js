
$('#jInsertAttachmentForm').ajaxForm({
    dataType: 'json',
    success: function (res, statusText, xhr, $form)  {

        if(res.result == 'ok') {
            
            //$('#jVideoPreview').html(res.embed);
            var h = {};
            if(res.youtube) {
                h = {
                    'href': res.youtube ? res.youtube : '',
                    'data-type': 'uatt-video',
                    'data-id': res.id,
                    'class': 'att-youtube'
                };
            } else if(res.veneo) {
                h = {
                    'href': '#'+res.id,
                    'data-type': 'uatt-video',
                    'data-id': res.id,
                    'class': 'att-vv'
                };
            }
            
            html = res.imageThumb ? '<img src="'+ res.imageThumb +'" style="height: 180px" />' : 'W trakcie przetwarzania';
            
            insHtml = '<a class="'+h.class+'" href="'+h.href+'" data-type="'+h['data-type']+'" data-id="'+h['data-id']+'">'+html+'</a>'; 
        
            ed = tinyMCEPopup.editor;
            node = ed.selection.getNode();
            
            if (ed.dom.getAttrib(node, 'data-type')
                        && ed.dom.getAttrib(node, 'data-type').indexOf('uatt-video') !== -1) {
                
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

