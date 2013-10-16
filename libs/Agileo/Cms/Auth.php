<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Cms_Auth implements Zend_Auth_Adapter_Interface
{

    protected $_login = null;
    protected $_pass = null;

    public function __construct($login, $pass)
    {
        $this->_login = $login;
        $this->_pass = $pass;
    }

    public function authenticate()
    {

        $admin = AdminMapper::getMasterInstance()->login($this->_login, $this->_pass);

        if (!empty($admin) && is_object($admin) && $admin instanceof Admin) {
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $admin);
        }
        return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, null);
    }

}
