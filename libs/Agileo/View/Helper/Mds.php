<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

abstract class Agileo_View_Helper_Mds extends Zend_View_Helper_Abstract
{

    protected $_defOptions = array(
        'content' => NULL,
        'attribs' => array()
    );

    protected $_options = array();

    protected function _prepareOptions($options)
    {
    
        $config = Zend_Registry::get('config');
        $this->_options = array_merge($this->_defOptions, $options);
    }

    protected function _prepareAttribs()
    {
        $htmlAttrs = '';
        foreach ($this->_options['attribs'] as $attrName => $val) {
            $htmlAttrs .= ' '.$attrName.'="'.(is_array($val) ? join(' '.$val) : $val).'"';
        }
        return $htmlAttrs;
    }

}
