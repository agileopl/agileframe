<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_View_Helper_NumberLabel extends Zend_View_Helper_Abstract
{

    public function numberLabel($number, $wordNo1, $wordNo2, $wordNoMore)
    {

        if ($number == 1) {
            return $wordNo1;
        } else if (strlen($number) >= 2 && ( (int) substr($number, -2) >= 11 && (int) substr($number, -2) <= 19)) {
            return $wordNoMore;
        } else {
            $last = substr($number, -1);
            if ($last >= 2 && $last <= 4) {
                return $wordNo2;
            } else {
                return $wordNoMore;
            }
        }
    }

}
