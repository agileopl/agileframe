<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
abstract class Agileo_Box
{

    protected $_view = NULL;

    protected $_params = array();
    
    protected $_request = null;

    public function __construct($params)
    {
        $this->_params = $params;
        $this->_request = Zend_Controller_Front::getInstance()->getRequest();
        $this->_view = Zend_Layout::getMvcInstance()->getView();
    }

    abstract public function render();
    
    public static function getInstance($className, $module, $params = array())
    {
        $boxClassName = ucfirst($className) . 'Box';
        $boxesPath = APPLICATION_PATH . '/modules/' . $module . '/boxes';
        require_once $boxesPath . '/' . $boxClassName . '.php';
        Zend_Layout::getMvcInstance()->getView()->addScriptPath($boxesPath . '/scripts');

        // addprefix
        if (!empty($module) && $module != 'default') {
            $boxClassName = ucfirst($module) . '_' . $boxClassName;
        }

        return new $boxClassName($params);
    }

}
