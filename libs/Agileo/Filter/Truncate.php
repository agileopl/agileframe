<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Filter_Truncate implements Zend_Filter_Interface
{

    private $_length = 80;
    private $_etc = '...';
    private $_breakWords = false;
    private $_middle = false;

    public function __construct($options = array())
    {
        if (!empty($options['length'])) {
            $this->_length = $options['length'];
        }
        if (!empty($options['etc'])) {
            $this->_etc = $options['etc'];
        }
        if (!empty($options['breakWords'])) {
            $this->_breakWords = $options['breakWords'];
        }
        if (!empty($options['middle'])) {
            $this->_middle = $options['middle'];
        }
    }

    public function filter($string)
    {
        if ($this->_length == 0) {
            return '';
        }

        if (mb_strlen($string) > $this->_length) {
            $length = $this->_length;
            $length -= min($length, mb_strlen($this->_etc));
            if (!$this->_breakWords && !$this->_middle) {
                $string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length + 1));
            }
            if (!$this->_middle) {
                return mb_substr($string, 0, $length) . $this->_etc;
            } else {
                return mb_substr($string, 0, $length / 2) . $this->_etc . mb_substr($string, -$length / 2);
            }
        }

        return $string;
    }

}
