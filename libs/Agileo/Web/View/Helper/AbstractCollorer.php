<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

abstract class Agileo_Web_View_Helper_AbstractCollorer extends Zend_View_Helper_Abstract
{
    
    public static $addedScritpPath = false;
    
    protected function _addScriptPath () {
        if(!self::$addedScritpPath) {
            $this->view->addScriptPath(realpath(dirname(__FILE__)));
            self::$addedScritpPath = true;
        }
    }
    
}