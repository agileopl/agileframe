$(document).ready(function() {
    
    var currentPoint = null;
    
    var mapOptions = {
        center : new google.maps.LatLng( 0, 0),
        zoom : 2,
        mapTypeId : google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById('users_map'), mapOptions);
    
    var ibOptions = {
         disableAutoPan: false
        ,maxWidth: 0
        ,pixelOffset: new google.maps.Size(-71, -171)
        ,zIndex: null
        ,boxClass: 'gmIBoxThumbnail'
        ,closeBoxMargin: "2px 2px 2px 2px"
        ,closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif"
        ,pane: "floatPane"
        
    };
    var ib = new InfoBox(ibOptions);

    if(_MARKERS) {
        

        marker = {};      
        $.each(_MARKERS, function (i, item) {
            
            var marker = new google.maps.Marker({
                        position : new google.maps.LatLng( item.lat, item.lng),
                        map : map,
                        title: item.title
                    });
            var markerInfo = item;
            
            google.maps.event.addListener(marker, 'mouseover', function() {

                ib.setContent('<div class="gmIBoxContent"><a href="'+item.publicUrl+'">'+(item.thumb ? '<img src="'+item.thumb+'">' : '')+'<em>'+markerInfo.title+'</em></a></div>');
                ib.open(map, this);
                
            });            
            
        });
        
    }


}); 