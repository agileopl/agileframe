<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Controller_Plugin_Layout extends Zend_Controller_Plugin_Abstract
{

    public static $prepareLayoutBoxes = true;
    
    private $_layoutConfig;

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if ($request->isXmlHttpRequest()) {
            header('Content-Type: application/text; charset=UTF-8');
            Zend_Layout::getMvcInstance()->disableLayout();
            self::$prepareLayoutBoxes = false;
        }
        if($this->getResponse()->isException()) {
            self::$prepareLayoutBoxes = false;
        }
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {

        if($this->getResponse()->isException()) {
            self::$prepareLayoutBoxes = false;
        }

        if (!self::$prepareLayoutBoxes) {
            return;
        }
        self::$prepareLayoutBoxes = false;

        $this->_layoutConfig = $this->getLayoutConfig($request);

        $layout = Zend_Layout::getMvcInstance();
        $layout->layoutConfig = $this->_layoutConfig;

        // pobranie boxów z konfiguracji
        $boxesConfig = $this->_getLayoutBoxes($this->_layoutConfig->layout);
        if (!empty($this->_layoutConfig->segments)) {
            if ($this->_layoutConfig->segments instanceof Zend_Config && count($this->_layoutConfig->segments) > 0) {
                $segmentsBoxes = $this->_getSegmentsBoxes($this->_layoutConfig->segments);
                $boxesConfig = array_merge($segmentsBoxes, $boxesConfig);
            }
        }

        if (!count($boxesConfig)) {
            return;
        }

        $cache = Zend_Registry::get('cache');
        $config = Zend_Registry::get('config');

        foreach ($boxesConfig as $segmentId => $boxConfig) {
            
            if (!empty($boxConfig->type) && $boxConfig->type == 'box') {
                
                $cacheIdentity = 'boxesCache'.md5($segmentId);
                if (empty($boxConfig->cache) || !($boxContent = $cache->load($cacheIdentity))) {
                    
                    $params = !empty($boxConfig->params) ? $boxConfig->params->toArray() : array();
                    $params = array_merge($params, $request->getParams());
                    
                    $box = Agileo_Box::getInstance($boxConfig->className, $boxConfig->module, $params);

                    $boxContent = $box->render();

                    if (!empty($boxConfig->cache)) {
                        $cache->save($boxContent, $cacheIdentity, array(), $boxConfig->cache);
                    }

                }

                $this->getResponse()->appendBody($boxContent, $segmentId);

            }
        }

    }

    public function getLayoutConfig($request)
    {
        $config = Zend_Registry::get('config');

        $cache = Zend_Registry::get('cache');

        $contexts = array('Default', 'Module', 'Controller', 'Action');

        $rModule = $request->getParam('module');
        $rController = $request->getParam('controller');
        $rAction = $request->getParam('action');

        $cacheIdentity = '';        
        foreach($contexts as $context) {
            $rParam = 'r'.$context;
            if(isset(${$rParam})) {
                $cacheIdentity .= '_'.${$rParam}; 
            }
            if(Zend_Registry::isRegistered('layoutContext'.$context)) {
                $cacheIdentity .= '_context-' . Zend_Registry::get('layoutContext'.$context);
            }
        }
        $cacheIdentity = 'layoutConfig_'.md5($cacheIdentity);

        if (!($layoutConfig = $cache->load($cacheIdentity))) {

            // default
            $layoutConfig = new Zend_Config_Yaml(APPLICATION_PATH . '/layouts/config/default.yaml', null, true);

            $path = APPLICATION_PATH . '/layouts/config';

            foreach($contexts as $context) {
                $rParam = 'r'.$context;
                if(isset(${$rParam})) {
                    $path .= '/' . strtolower(${$rParam});
                    $this->_addLayoutSubConfig ($path . '.yaml', $layoutConfig);
                }
                if(Zend_Registry::isRegistered('layoutContext'.$context)) {
                    $this->_addLayoutSubConfig ($path . '/' . 'context-' . Zend_Registry::get('layoutContext'.$context) . '.yaml', $layoutConfig);
                }
            }

            if ($config->layout->cache->lifeTime > 0) {
                $cache->save($layoutConfig, $cacheIdentity, array(), $config->layout->cache->lifeTime);
            }
        }
        return $layoutConfig;

    }

    private function _addLayoutSubConfig ($configPath, $layoutConfig)
    {

        if (file_exists($configPath)) {
            
            $oConfig = new Zend_Config_Yaml($configPath, null, true);
            foreach($layoutConfig as $key => $c) {
                // layout overwrite
                if($key == 'layout') {
                    if(!empty($oConfig->layout)) {
                        $layoutConfig->layout = $oConfig->layout;
                    }
                } elseif(!empty($oConfig->{$key})) {
                    $layoutConfig->{$key}->merge($oConfig->{$key});
                }
            }
            
        }
    }

    private function _getSegmentsBoxes($segmentsConfig)
    {
        $boxes = array();
        if(!empty($segmentsConfig)) {
            foreach($segmentsConfig as $segmentId => $boxConfig) {
                $boxes[$segmentId] = $boxConfig;
            }
        }
        return $boxes;
    }

    private function _getLayoutBoxes(Zend_Config $layoutConfig)
    {
        $boxes = array();
        foreach ($layoutConfig as $item) {
            if (!empty($item->boxes)) {
                foreach ($item->boxes as $segmentId => $boxConfig) {
                    $boxes[$segmentId] = $boxConfig;
                }
            } elseif ($item instanceof Zend_Config) {
                $boxes = array_merge($boxes, $this->_getLayoutBoxes($item));
            }
        }
        return $boxes;
    }

}
