<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Web_View_Helper_Avatar extends Zend_View_Helper_Abstract
{

    public function avatar(Agileo_Object $object, $options = array())
    {
        $ret = '<!-- avatar not defined //-->';
        
        if(!empty($object) && $object) {
            if(get_class($object) == 'User'):
                $ret = $this->view->userAvatar($object, $options);
            elseif(get_class($object) == 'Admin') :
                $ret = $this->view->adminAvatar($object, $options);
            endif; 
        }
        
        return $ret; 
    }
    

}