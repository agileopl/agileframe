<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
require_once 'Agileo/Bootstrap/Core.php';

class Bootstrap extends Agileo_Bootstrap_Core
{

    public function run()
    {

    }

    protected function _initAutoloader()
    {
        $loader = Zend_Loader_Autoloader::getInstance();
        $loader->setFallbackAutoloader(true);
    }

    /**
     * Zapisanie konfiga do rejestru
     * @return void
     */
    protected function _initConfig()
    {
        Zend_Registry::set('config', new Zend_Config($this->getOptions(), true));
    }

    protected function _initMultiDatabase()
    {
        $options = $this->getOptions();
        if (isset($options['resources']['multidb'])) {
            Zend_Registry::set('multidb', $this->bootstrap('multidb')->getPluginResource('multidb'));
        }
    }

    protected function _initManagerCache()
    {
        $this->bootstrap('cachemanager');
        Zend_Registry::set('cachemanager', $this->getResource('cachemanager'));
    }

    protected function _initCache()
    {
        $this->bootstrap('cachemanager');
        Zend_Registry::set('cache', $this->bootstrap('cachemanager')->getPluginResource('cachemanager')->getCacheManager()->getCache('default'));
    }

    /**
     * Ustawia loggery w Zend_Registry
     */
    protected function _initLogs()
    {
        $resource = $this->bootstrap('log')->getPluginResource('log');
        Zend_Registry::set('log', $resource->getLog());
    }

}
