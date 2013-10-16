<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Globals
{

    protected static $_instance = null;

    protected $_lg_is_multi = false;
    protected $_lg_current = null;
    protected $_lg_supported = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if(!self::$_instance) {
            $object = new Agileo_Globals();
            if(count(Zend_Registry::get('config')->globals->lg->suported) > 1) {
                $object->_lg_is_multi = true;
                $object->_lg_supported = Zend_Registry::get('config')->globals->lg->suported->toArray();
            }
            $object->_lg_current = Zend_Registry::get('config')->globals->lg->default;
            self::$_instance = $object;
        }
        return self::$_instance;
    }

    public function setCurrentLg($lg) {
        if(!empty($this->_lg_supported[$lg])) {
            $this->_lg_current = $lg;
        } else {
            throw new Agileo_Exception('Nieobsługiwany język: '.$lg);
        }
    }

    public function getCurrentLg() {
        return $this->_lg_current;
    }

    public function isMultiLg() {
        return $this->_lg_is_multi;
    }

    public function getSupportedLanguages() {
        return $this->_lg_supported;
    }

}