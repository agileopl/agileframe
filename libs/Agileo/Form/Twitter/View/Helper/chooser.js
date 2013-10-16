

var crop = {};

function initCrop (elName) {

    if($('#jContainer'+elName+' > .jThumb > img').length > 0) {
        $('#jContainer'+elName+' > .jCropRatio').show();

        $('#jContainer'+elName+' > .jThumb > img').Jcrop({
          onChange: function (c) {
              showCoords(c, elName);
          },
          onSelect: function (c) {
              //showCoords(c, elName);
          }
        },function() {
            
            crop[elName] = this;
            
            if($('#'+elName+'_crop').val()) {
                xyxy = $('#'+elName+'_crop').val().split(",");
                
                w = $('#jContainer'+elName+' > .jThumb > img').width();
                h = $('#jContainer'+elName+' > .jThumb > img').height();
                
                //przepisz na px
                xyxy[0] = Math.round(xyxy[0]*w/100);
                xyxy[1] = Math.round(xyxy[1]*h/100);
                xyxy[2] = Math.round(xyxy[2]*w/100);
                xyxy[3] = Math.round(xyxy[3]*h/100);
                crop[elName].setSelect(xyxy);
        
                // ratio
                //ratio = (xyxy[2]-xyxy[0])/(xyxy[3]-xyxy[1]); 
                
            }
        });

    }
    
}

function showCoords(c, elName) {
    w = $('#jContainer'+elName+' > .jThumb > img').width();
    h = $('#jContainer'+elName+' > .jThumb > img').height();
    $('#'+elName+'_crop').val(Math.round(c.x/w*100)+','+Math.round(c.y/h*100)+','+Math.round(c.x2/w*100)+','+Math.round(c.y2/h*100));
};

function addAttachmentForElementImageCallback(elName, data) {

    $('#jContainer'+elName+' > .jThumb').html('<img src="'+data.thumb+'" class="ico-element" />');
    
    $('#'+elName+'UnsetSingle').show();
    
    $('#jContainer'+elName+' > .jCropRatios').show();
    
    initCrop (elName);

}

function addAttachmentForElementAttachmentCallback(elName, data) {
    if($('#jContainer'+elName+' > a.attach-element').length == 0) {
        $('#jContainer'+elName).prepend('<a class="attach-element"/>');
    }
    $('#jContainer'+elName+' > a.attach-element').attr('href',MDS_ATTACHMENT_PATH+$('#'+elName).val()).addClass("ico-element").html($('#'+elName).val());
}

function attachFieldCallback(elName, data) {

    $('#'+elName).val(data.url);
    $('#'+elName+'_id').val(data.id);
    
    type = $('#'+elName).data('type');
    
    if(type == 'image') {
        addAttachmentForElementImageCallback(elName, data);
    } else {
        addAttachmentForElementAttachmentCallback(elName, data);
    }
    
    agiIframePopoverHide();
    
}

$(document).ready(function() {
    
    $('.jCropRatio').click(function () {
        elName = $(this).data('rel');
        $('#jContainer'+elName+' .jCropRatio').removeClass('btn-danger');
        $('#jContainer'+elName+' .jCropRatio').addClass('btn-info');
        $(this).removeClass('btn-info');
        $(this).addClass('btn-danger');
        if($(this).data('ratio') == 'cancel') {
            crop[elName].setOptions({ aspectRatio: 0 });
        } else {
            crop[elName].setOptions({ aspectRatio: $(this).data('ratio') });
        }
        return false;
    });

});