<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos
 * (http://www.agileo.pl)
 * @license    MIT license
 */
require_once 'Agileo/Bootstrap/WebApp.php';
class Bootstrap extends Agileo_Bootstrap_WebApp
{

    protected function _initConfig()
    {
        parent::_initConfig();
    }

    protected function _initAutoloader()
    {
        parent::_initAutoloader();
    }

    protected function _initCache()
    {
        parent::_initCache();
    }

    protected function _initFrontCache()
    {
        if (Zend_Registry::get('config')->runMode != 'dev') {
            $cache = $this->bootstrap('cachemanager')->getPluginResource('cachemanager')->getCacheManager()->getCache('pagecache');
            if (!empty($_REQUEST['clearCache'])) {
                $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
            }
            $cache->start();
        }
    }

    protected function _initLog()
    {
        parent::_initLog();
    }

    protected function _initMultiDatabase()
    {
        parent::_initMultiDatabase();
    }

    protected function _initTranslate()
    {
        parent::_initTranslate();
    }

    protected function _initSession()
    {
        parent::_initSession();
    }

    protected function _initPaginationControl()
    {
        parent::_initPaginationControl();
    }

    protected function _initNaviagation()
    {
        $this->bootstrap('view');
        // make sure we have a view
        $view = $this->getResource('view');
        // get the view resource

        $cache = Zend_Registry::get('cache');
        $cacheIdentity = md5('cmsNavigation');
        if (empty($config->navigation->cache->lifeTime) || !($navigation = $cache->load($cacheIdentity))) {
            $config = Zend_Registry::get('config');
            $naviConfig = new Zend_Config_Yaml(realpath($config->navigation->configFile), 'navigation');
            $navigation = new Zend_Navigation($naviConfig);
            $home = $navigation->findById('home');

            /*
            Zend_Registry::set('subdomain', Marrow_NodeMapper::SUBDOMAIN_MAIN);
            $marrowMapper = Marrow_NodeMapper::getSlaveInstance();
            $nodesNavigation = $marrowMapper->transformToNavigation($marrowMapper->getNodes(array(), true));
            foreach ($nodesNavigation->getPages() as $page) {
                $home->addPage($page);
            }
             */

            if ($config->navigation->cache->lifeTime > 0) {
                $cache->save($navigation, $cacheIdentity, array(), $config->navigation->cache->lifeTime);
            }
        }
        Zend_Registry::set('Zend_Navigation', $navigation);

        $view->navigation()->menu()->setPartial('menu.phtml');
    }

    protected function _initBreadcrumbs()
    {
        parent::_initBreadcrumbs();
    }

    protected function _initRoutes()
    {
        $config = Zend_Registry::get('config');

        $cache = Zend_Registry::get('cache');
        $cache->setLifeTime($config->router->web->cache->lifeTime);

        $cacheIdentity = md5('webRouterConfig');
        if (!($routerConfig = $cache->load($cacheIdentity))) {
            $routerConfig = new Zend_Config_Yaml(realpath($config->router->web->configFile));
            if ($config->navigation->cache->lifeTime > 0) {
                $cache->save($routerConfig, $cacheIdentity);
            }
        }

        $front = Zend_Controller_Front::getInstance();
        $front->getRouter()->addConfig($routerConfig, 'routes');
        Zend_Registry::set('router', $front->getRouter());

    }

    protected function _initClickBufferCache()
    {
        Zend_Registry::set('clickbuffer', $this->bootstrap('cachemanager')->getPluginResource('cachemanager')->getCacheManager()->getCache('clickbuffer'));
    }

}
