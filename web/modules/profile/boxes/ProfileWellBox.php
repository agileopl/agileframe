<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Profile_ProfileWellBox extends Agileo_Box
{

    protected $_params = array();

    public function render()
    {
        
        if(!Zend_Registry::isRegistered('USER_PROFILE')) {
            return '';
        }
        
        $user = Zend_Registry::get('USER_PROFILE');
        
        return '<div class="well">WELL WELL WELL</div>';
        
    }
}
