<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_View_Helper_PrettySize extends Zend_View_Helper_Abstract
{

    public function prettySize($value, $unit = 'B', $giga = 'G', $mega = 'M', $kilo = 'k')
    {
        
        if($value > 99999999) {
            // giga
            $value = $value/1000/1000/1000;
            
            return number_format($value, 1, '.', '').$giga.$unit;
            
        } elseif ($value > 99999) {
            //mega
            $value = $value/1000/1000;
            return number_format($value, 1, '.', '').$mega.$unit;
            
        } elseif ($value > 99) {
            // kilo
            $value = $value/1000;
            return number_format($value, 1, '.', '').$kilo.$unit;
        } else {
            
            return number_format($value, 1, '.', '').$unit;
             
        }

    }

}