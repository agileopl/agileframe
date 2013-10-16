<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Profile_SettingsBoxMenuBox extends Agileo_Box
{

    protected $_params = array();

    public function render()
    {
        $this->_view->menuAction = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
        return $this->_view->render('settingsBoxMenuBox.phtml');
    }
}
