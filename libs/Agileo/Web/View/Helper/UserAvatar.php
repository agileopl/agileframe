<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Web_View_Helper_UserAvatar extends Zend_View_Helper_Abstract
{

    public function userAvatar(User $user, $options = array())
    {
        
        $resize = !empty($options['resize']) ? $options['resize'] : '100x100';
        $crop = !empty($options['crop']) ? $options['crop'] : 'MT';
        
        $thumb = '';
        if(!empty($user->avatar)) {
            $thumb .= $this->view->mdsImage($user->avatar, array('resize' => $resize, 'crop'=> $crop, 'content' => $user->name.' '.$user->surname));
        } else {
            $thumb .= $this->view->mdsImage('/user-avatar.png', array('resize' => $resize, 'crop'=> $crop, 'content' => $user->name.' '.$user->surname));
        }

        if(!empty($options['onlyThumb'])) {
            return $thumb;
        }

        $html = '<div class="thumbnail"><a class="userAvatar" href="'.$user->getPublicUrl().'" title="'.$user->nick.'">';
            
            $html .= $thumb; 
            
            $html .= '<em>'.$user->name.' '.$user->surname.'</em>';
            
            if($user->isSocFacebook()) {
                $html .= '<i class="icon-facebook-sign icon-i"></i>';
            }
        
        $html .= '</a></div>';
        
        return $html; 
    }
    

}