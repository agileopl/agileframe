<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

abstract class Agileo_Bootstrap_Core extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initConfig()
    {
        Zend_Registry::set('config', new Zend_Config($this->getOptions()));
    }

    protected function _initConst()
    {
        if(isset(Zend_Registry::get('config')->globals->mds->{APPLICATION_NAME})) {
            define('STATIC_PATH', Zend_Registry::get('config')->globals->host->mds . Zend_Registry::get('config')->globals->mds->{APPLICATION_NAME});
        } else {
            define('STATIC_PATH', '');
        }
    }
    
    protected function _initLog()
    {
        if ($this->hasPluginResource('Log')) {
            $log = $this->getPluginResource('Log')->getLog();
            Zend_Registry::set('log', $log);
        }
    }

}