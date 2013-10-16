<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Filter_Seo implements Zend_Filter_Interface
{

    public function filter($value)
    {

        $value = T::normalize($value);

        $value = strtr($value, array (
            "'" => '',
            '"' => '',
            ',' => '-',
            '?' => '',
            '!' => ''
        ));

        $value = preg_replace("/[^a-zA-Z0-9]/i", "-", $value);
        $value = preg_replace("/([-])+/i", "-", $value);
        $value = trim(mb_strtolower($value));

        return $value; 

    }

}