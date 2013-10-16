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
                    
                    html = '<td><input name="add['+result.id+']" type="checkbox" checked="checked" > </td>';
                    
                    html += '<td>'; 
                    if(result.thumb) {
                        html += '<img src="'+result.thumb+'">';
                    } else {
                        html += '<a href="'+result.file+'" target="_blank">'+result.name+'</a>';
                    }
                    html += '</td>';
                    
                    html += '<td><input name="desc['+result.id+']" class="input-xxlarge" type="text"></td>';
                    
                    $('#jResults > table > tbody').append('<tr>'+html+'</tr>');
                    
                    $('#jResults').show();
                    
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