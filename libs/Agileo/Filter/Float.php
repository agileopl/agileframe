<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Filter_Float implements Zend_Filter_Interface
{

    public function filter($value)
    {

        $value = str_replace(',', '.', $value);
        return floatval($value); 

    }

}