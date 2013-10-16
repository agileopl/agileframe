<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_View_Helper_PhoneCall extends Zend_View_Helper_Abstract
{

    public function phoneCall($string)
    {
        $ret = preg_replace("/[^0-9]/i", "", $string);
        if(!empty($ret)) {
            $ret = 'tel:'.$ret; 
        }
        
        return $ret;
    }

}