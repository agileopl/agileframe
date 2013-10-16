<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_View_Helper_MapInit extends Zend_View_Helper_Abstract
{

    public static $isInit = false;

    public function mapInit()
    {
        if(!self::$isInit) {
            $this->view->headScript()->appendFile('//maps.googleapis.com/maps/api/js?key='.Zend_Registry::get('config')->globals->googleApi->mapKey.'&sensor=true');
            $this->view->headScript()->appendFile(STATIC_PATH.'/js/google.map.infobox.js');
            
            self::$isInit = true;
        }
        return '<!-- map init //-->';
    }

}