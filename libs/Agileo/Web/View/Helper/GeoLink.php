<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Web_View_Helper_GeoLink extends Zend_View_Helper_Abstract
{

    public function geoLink(Agileo_Object $object)
    {
        return '<a href="#" data-lat="'.$object->geo_lat.'" data-lng="'.$object->geo_lng.'" data-zoom="'.$object->geo_zoom.'"><i class="icon-map-marker"></i> Lokalizacja</a>'; 
    }
    

}