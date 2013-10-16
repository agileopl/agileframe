<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Web_Auth implements Zend_Auth_Adapter_Interface
{

    protected $_email = null;
    protected $_pass = null;

    public function __construct($email, $pass)
    {
        $this->_email = $email;
        $this->_pass = $pass;
    }

    public function authenticate()
    {

        $user = UserMapper::getMasterInstance()->login($this->_email, $this->_pass);

        if (!empty($user) && is_object($user) && $user instanceof User) {
            $user->login_by = 'standard';
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user);
        }
        return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, null);
    }

}
