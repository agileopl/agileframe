$(document).ready(function() {
    
    $('#jCommuny').autocomplete({
        minLength: 0,
        source: '/location/service/search.communy/',
        select: function( event, ui ) {
            $( "#jCommuny" ).val( ui.item.name );
            $( "#jCommunyId" ).val( ui.item.id );
            $('#jCode1,#jCode2,#jCode').val('');
            return false;
        }
    }).data( "autocomplete" )._renderItem = function( ul, item ) {
      return $( "<li>" )
        .append( "<a>" + item.name + " (<em>" + item.district + "</em>)</a>" )
        .appendTo( ul );
    };
    
    if($('#jCode').val()) {
        $('#jCode1').val($('#jCode').val().substring(0,2));
        $('#jCode2').val($('#jCode').val().substring(3));
    }
    
    $('#jCode1,#jCode2').change(function() {
        $( "#jCommuny" ).val('');
    });
    
    $('#jCode1,#jCode2').keyup(function(event) {
        
        $('#jCode').val('');
        
        v = $(this).val().replace(/[^0-9]/,'');
        $(this).val(v);
        
        code = $('#jCode1').val()+'-'+$('#jCode2').val();
        if(code.length == 6) {
            
            $('#jCode').val(code);
            
            $.get('/location/service/get.communy.by.code', {code: code}, function(data) {
                if(data.result == 'true' && data.communies && data.communies[0]) {
                    $( "#jCommunyId" ).val(data.communies[0].id);
                    $( "#jCommuny" ).val(data.communies[0].name);
                } else {
                    alert('Nie istnieje kod: '+data.code);
                    $('#jCode').val('');
                }
            });
        }
        
        return false;

    });

});
