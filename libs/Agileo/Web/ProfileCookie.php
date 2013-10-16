<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Agileo_Web_ProfileCookie
{

    public static function refresh(User $user)
    {
        $info = array(
            'id' => $user->id,
            'nick' => $user->nick,
            'name' => $user->name,
            'surname' => $user->surname,
            'avatar' => !empty($user->avatar) ? Zend_Layout::getMvcInstance()->getView()->mdsImage($user->avatar, array('resize' => '50x50')) : '',
            'login_by' => !empty($user->login_by) ? $user->login_by : 'standard'
        );

        setcookie(Zend_Registry::get('config')->globals->cookie->profileName, Zend_Json::encode($info), time() + 30758400, "/", Zend_Registry::get('config')->globals->cookie->domain);

    }
    
    public static function remove()
    {
        setcookie(Zend_Registry::get('config')->globals->cookie->profileName, null, time(), "/", Zend_Registry::get('config')->globals->cookie->domain);
    }

}
