<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Filter_Escape implements Zend_Filter_Interface
{

    private $_type = 'html';
    private $_charset = 'utf-8';

    public function __construct($options = array())
    {
        if (!empty($options['type'])) {
            $this->_type = $options['type'];
        }
        if (!empty($options['charset'])) {
            $this->_charset = $options['charset'];
        }
    }

    public function filter($value)
    {

        switch ($this->_type) {
            case 'html':
                return htmlspecialchars($value, ENT_QUOTES, $this->_charset);

            case 'htmlall':
                return htmlentities($value, ENT_QUOTES, $this->_charset);

            case 'url':
                return urlencode($value);

            case 'quotes':
                // escape unescaped single quotes
                return preg_replace("%(?<!\\\\)'%", "\\'", $value);

            default:
                return $value;
        }
    }

}
