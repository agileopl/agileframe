<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
require_once 'Agileo/Bootstrap/Core.php';
abstract class Agileo_Bootstrap_WebApp extends Agileo_Bootstrap_Core
{
    protected function _initAutoloader()
    {
        $loader = Zend_Loader_Autoloader::getInstance();
        $loader->setFallbackAutoloader(true);

        $loader->pushAutoloader(function($name)
        {
            $arrName = explode("_", $name);
            array_shift($arrName);
            require_once Zend_Controller_Front::getInstance()->getModuleDirectory() . '/forms/' . join('/', $arrName) . '.php';

        }, 'Form');

        $loader->pushAutoloader(function($name)
        {
            $arrName = explode("_", $name);       
            array_shift($arrName);
            if(!empty($arrName)) {
                require_once Zend_Controller_Front::getInstance()->getModuleDirectory() . '/forms/' . join('/', $arrName) . '.php';
            } else {
                require_once $name . '.php';
            }

        }, 'Box');

    }
    
    protected function _initCache()
    {
        if ($this->hasPluginResource('cachemanager')) {
            $cache = $this->getPluginResource('cachemanager')->getCacheManager()->getCache('app');
            // Save to registry
            Zend_Registry::set('cache', $cache);

            if (!empty($_REQUEST['clearCache'])) {
                $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
            }

        }
    }
    
    protected function _initMultiDatabase()
    {
        $options = $this->getOptions();
        if (isset($options['resources']['multidb'])) {
            Zend_Registry::set('multidb', $this->bootstrap('multidb')->getPluginResource('multidb'));
        }
    }
    
    protected function _initTranslate()
    {
        $cache = Zend_Registry::get('cache');
        $config = Zend_Registry::get('config');

        // TODO LG
        $currentLg = $config->translate->default_language;

        $cacheIdentity = md5(APPLICATION_NAME.'translatecache_' . $currentLg);
        if (!($translate = $cache->load($cacheIdentity))) {
            $translate = new Zend_Translate( array(
                'adapter' => Zend_Translate::AN_INI,
                'content' => realpath(APPLICATION_PATH . '/translation/'),
                'scan' => Zend_Translate::LOCALE_DIRECTORY,
                'disableNotices' => true
            ));
            $translate->setLocale($currentLg);

            if (!empty($config->translate->cache->lifeTime)) {
                $cache->save($translate, $cacheIdentity, array(), $config->translate->cache->lifeTime);
            }
        }

        if (!empty($config->translate->untranslatedLogTableName)) {
            $writer = new Agileo_Log_Writer_Translate(Zend_Registry::get('multidb')->getDb('master'), $config->translate->untranslatedLogTableName);
            $log = new Zend_Log($writer);
            $translate->setOptions(array(
                'log' => $log,
                'logUntranslated' => true
            ));
        }
        Zend_Registry::set('Zend_Translate', $translate);
    }

    protected function _initSession()
    {
        $config = Zend_Registry::get('config');
        if (!empty($config->session)) {
            Zend_Session::setOptions($config->session->toArray());
            if (isset($config->sessionHandler)) {
                // todo
                // Zend_Session::setSaveHandler(new Agileo_Session_Cache($cache,
                // $config->session->gc_maxlifetime));
                // Zend_Session::setSaveHandler(new
                // Agileo_Session_Db($config->session->gc_maxlifetime));
            }
        }
    }

    protected function _initPaginationControl()
    {

        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->addScriptPath(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'scripts');

        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
    }
    
    protected function _initBreadcrumbs()
    {
        $this->bootstrap('view');
        // make sure we have a view
        $view = $this->getResource('view');
        // get the view resource
        $view->navigation()->breadcrumbs()->setPartial('breadcrumbs.phtml')->setRenderInvisible(TRUE);
    }
    
}