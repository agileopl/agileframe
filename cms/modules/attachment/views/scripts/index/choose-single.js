$(document).ready(function(){

    $('.images-list > li').live('click', function (){
        
        parent.attachFieldCallback("<?= $this->target;?>", {id: $(this).data('id'), url: $(this).data('url'), thumb: $(this).data('thumb')});
        
        return false;
    });
    
        

});
    