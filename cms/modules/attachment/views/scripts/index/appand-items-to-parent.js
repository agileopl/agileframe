$(document).ready(function(){


    $('#jJoinAttForm').submit(function() { 
        $(this).ajaxSubmit({
            success: function (responseData, statusText, xhr, $form) {
                parent.objectAttachmentsJoinerCallback(responseData);
            }
           }
        ); 
        return false; 
    }); 
    
    $('#jJoinAttItems .pagination a').live('click', function (){
        $('#jJoinAttItems').load($(this).attr('href'), function() {
            refreshSelected ();
        });
        return false;
    });

    $('#jJoinAttFilterForm').submit(function() { 

        $(this).ajaxSubmit({ 
            target: '#jJoinAttItems',
            success: function (responseText, statusText, xhr, $form) {
                refreshSelected ();
            }
           }   
        ); 

        return false; 
    }); 
    
    function refreshSelected () {
        $('#jJoinAttChoosed > div').each(function (i) {
            $('#jJoinAttItems .images-list > li[data-id='+$(this).data('id')+']').addClass('selected');
        });
    }

    function prepareJChoosed () {
        
        if($('#jJoinAttChoosed > div').length > 0) {
            $('#jJoinAttForm').show();
            $('#jJoinAttChoosedFix').css('height', '20px');
        } else {
            $('#jJoinAttForm').hide();
            $('#jJoinAttChoosedFix').css('height', '0');
        }
    }

    $('#jJoinAttChoosed a.remove').live('click', function (){
        id = $(this).parent().data('id');
        $('#jJoinAttChoosed > div[data-id='+id+']').remove();
        $('#jJoinAttItems .images-list > li[data-id='+id+']').removeClass('selected');
        prepareJChoosed ();
        return false;
    });    
    $('.images-list > li').live('click', function (){
        if(!$(this).hasClass('selected')) {
            $(this).addClass('selected');
            if($('#jJoinAttChoosed > div[data-id='+$(this).data('id')+']').length == 0) {
                html = $(this).find('img').length > 0 ? '<img src="'+$(this).find('img').first().attr('src')+'" />' : $(this).find('.info').first().html(); 
                $('#jJoinAttChoosed').append('<div data-id="'+$(this).data('id')+'"><input type="hidden" name="attId[]" value="'+$(this).data('id')+'" />'+html+'<a class="remove"><i class="icon-remove"></i></a></div>');
            }
        } else {
            $(this).removeClass('selected');
            if($('#jJoinAttChoosed > div[data-id='+$(this).data('id')+']').length > 0) {
                $('#jJoinAttChoosed > div[data-id='+$(this).data('id')+']').remove();
            }
        }
        
        prepareJChoosed ();
        
        return false;
    });
    
        

});
    