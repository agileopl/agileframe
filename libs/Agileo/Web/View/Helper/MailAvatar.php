<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Web_View_Helper_MailAvatar extends Zend_View_Helper_Abstract
{

    public function mailAvatar(Mail $mail, $fromTo = 'to')
    {
        
        $avatarObject = null; 
        $fieldParentName = $fromTo.'_parent_name';
        if($mail->{$fieldParentName} == 'User'):
            $avatarObject = ($mail->hasSubObject('User') ? $mail->getSubObject('User') : NULL);
        elseif($mail->{$fieldParentName} == 'Admin') :
            $avatarObject = ($mail->hasSubObject('Admin') ? $mail->getSubObject('Admin') : NULL);
        endif; 
        
        if($avatarObject) {
            return $this->view->avatar($avatarObject);
        }
        return '';
         
    }
    

}