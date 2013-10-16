<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Profile_ProfileController extends Agileo_Web_PublicProfileController
{

    public function init() 
    {
        parent::init();
        $this->_naviActivate();
    }
    
    public function indexAction()
    {
        $zendNavi = Zend_Registry::get('Zend_Navigation');
        $profileNavigation = $zendNavi->findById('pubProfileIndex');
        $profileNavigation->setActive(true);
    }

}
