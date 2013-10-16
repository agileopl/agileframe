<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_View_Helper_Truncate extends Zend_View_Helper_Abstract
{

    public function truncate($string, $length = 80, $etc = '...', $breakWords = false, $middle = false)
    {
        $filter = new Agileo_Filter_Truncate(array('length' => $length, 'etc' => $etc, 'breakWords' => $breakWords, 'middle' => $middle));
        return $filter->filter($string);
    }

}