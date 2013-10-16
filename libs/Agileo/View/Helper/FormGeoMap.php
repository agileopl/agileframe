<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_View_Helper_FormGeoMap extends Zend_View_Helper_Abstract
{

    public function formGeoMap(Zend_Form $form, $prefix)
    {
            
        $this->view->mapInit();
        
        $html = '
        
        <div class="control-group">
            <label for="bbi_thumb" class="control-label">'. $this->view->translate('form_geo_map_set_marker').'</label>
            <div class="controls">
                
                <div style="position: absolute; top: -10000px">
                <input id="jAddressSemafor" type="text" value="1" class="required"/>
                </div>
                
                '. $this->view->translate('form_geo_map_address').'
                <input id="jAddress" type="text" name="'.$prefix.'_geo_address" value="'.$form->{$prefix.'_geo_address'}->getValue().'" autocomplete="off"/>
                
                <input class="btn btn-info" type="button" value="'.$this->view->translate('form_geo_map_search').'" id="jGeocode"/>
                
                <div id="map_canvas" style="margin-top: 5px; width:100%; height:400px"></div>
            
                '.$form->{$prefix.'_geo_lat'}->render().'
                '.$form->{$prefix.'_geo_lng'}->render().'
                '.$form->{$prefix.'_geo_zoom'}->render().'
                                
            </div>
        </div>
        
        ';
        
        $js = "

$(document).ready(function() {
    
    var currentPoint = null;
    
    zoom = parseInt($('#".$prefix."_geo_zoom').val());
    if(zoom) {
        currentPoint = {
            lat : $('#".$prefix."_geo_lat').val(), 
            lng: $('#".$prefix."_geo_lng').val(),
            zoom : zoom
        };
    }
    
    var mapOptions = map = geocoder = marker = null;
    if(google != undefined) {
        mapOptions = {
            center : new google.maps.LatLng( (currentPoint ? currentPoint.lat : '52.00029624201451'), (currentPoint ? currentPoint.lng : '19.122580262500037')),
            zoom : (currentPoint ? currentPoint.zoom : 11),
            mapTypeId : google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
    
        geocoder = new google.maps.Geocoder();
        
        if(currentPoint) {
            marker = new google.maps.Marker({
                            position : new google.maps.LatLng( currentPoint.lat, currentPoint.lng),
                            map : map,
                            draggable : true
                        });
            google.maps.event.addListener(marker, 'dragend', function() {
                setPositionToForm(marker.getPosition());
            });
        }
        
        google.maps.event.addListener(map, 'zoom_changed', function() {
            $('#".$prefix."_geo_zoom').val(map.zoom);
        });
        
    }
    
    $('#jAddress').keyup(function(event) {

        $('#jAddressSemafor').val('');

        keycode = event.keyCode;
        if (keycode == 13) {
            $('#jGeocode').click();
        }

        return false;

    });

    $('#jAddress').focus(function(event) {
        $('#jAddressSemafor').val('');
        return false;
    });
    $('#jAddress').blur(function(event) {
        $('#jAddressSemafor').val(1);
        return false;
    });


    function setPositionToForm(location) {
        $('#".$prefix."_geo_lat').val(location.lat());
        $('#".$prefix."_geo_lng').val(location.lng());
        $('#".$prefix."_geo_zoom').val(map.zoom);
    }
    

    $('#jGeocode').click(function() {

        $('#jAddressSemafor').val(1);

        var address = $('#jAddress').val();
        geocode(address);
    });

    
    function geocode(address) {
        geocoder.geocode({
            'address' : address
        }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);

                setPositionToForm(results[0].geometry.location);

                if (marker) {
                    marker.setPosition(results[0].geometry.location);
                } else {
                    marker = new google.maps.Marker({
                        position : results[0].geometry.location,
                        map : map,
                        draggable : true
                    });
                    google.maps.event.addListener(marker, 'dragend', function() {
                        setPositionToForm(marker.getPosition());
                    });
                }

            } else {
                alert('".$this->view->translate('form_geo_map_search_error')."');
            }
        });
    }

}); 

        ";
        $this->view->inlineScript()->appendScript($js);
        
        return $html;
    }

}