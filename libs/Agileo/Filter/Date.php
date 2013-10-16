<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Filter_Date implements Zend_Filter_Interface
{

    public function filter($value)
    {
        if(empty($value) || $value == '0000-00-00') {
            return null;
        }
        return !empty($value) ? date('Y-m-d', strtotime($value)) : '';
    }

}
