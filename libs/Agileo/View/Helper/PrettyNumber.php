<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_View_Helper_PrettyNumber extends Zend_View_Helper_Abstract
{

    public function prettyNumber($value, $variant1, $variant2, $variant3)
    {
        if($value == 1) {
            return $value.' '.$variant1;
        } elseif($value > 1 && $value < 5) {
            return $value.' '.$variant2;
        } else {
            return $value.' '.$variant3;
        }
    }

}