<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Web_Auth_Facebook implements Zend_Auth_Adapter_Interface
{

    protected $_fbObject = null;
    protected $_accountAutoCreate = false;

    public function __construct($fbObject, $accountAutoCreate = false)
    {
        $this->_fbObject = $fbObject;
        $this->_accountAutoCreate = $accountAutoCreate;
    }

    public function authenticate()
    {

        // find by fbid
        if (!$user = UserMapper::getMasterInstance()->getBySocFacebook($this->_fbObject->id)) {
            if (!empty($this->_fbObject->email) && ($eUser = UserMapper::getMasterInstance()->getByEmail($this->_fbObject->email))) {
                // ustaw brakujące dane...
                $uUser = new User();
                $uUser->id = $eUser->id;
                $uUser->soc_facebook = $this->_fbObject->id;
                $uUser->type = UserMapper::TYPE_MIXED;
                if (UserMapper::getMasterInstance()->save($uUser)) {
                    $eUser->soc_facebook = $uUser->soc_facebook;
                    $user = $eUser; 
                }
            } elseif($this->_accountAutoCreate) {
                
                $uUser = new User(array(
                    'usr_type' => UserMapper::TYPE_SOCIAL,
                    'usr_email' => $this->_fbObject->email,
                    'usr_nick' => $this->_fbObject->name,
                    'usr_name' => $this->_fbObject->first_name,
                    'usr_surname' => $this->_fbObject->last_name,
                    'usr_soc_facebook' => $this->_fbObject->id,
                    'usr_soc_update_date' => date('Y-m-d H:i:s')
                ));
                
                if (UserMapper::getMasterInstance()->save($uUser)) {
                    $user = UserMapper::getMasterInstance()->getById($uUser->id); 
                }
            }
        }
        
        // odswież dane z facebooka jeżeli konto tylko FB / odświeżamy raz na tydzień
        if($user->isSocFacebook() && strtotime($user->soc_update_date) < strtotime('-7 days')) {
            $uUser = new User();
            $uUser->id = $user->id;
            
            $uUser->nick = $this->_fbObject->name;
            $uUser->name = $this->_fbObject->first_name;
            $uUser->surname = $this->_fbObject->last_name;
            $uUser->soc_update_date = date('Y-m-d H:i:s');
            
            // avatar
            $uUser->avatar = Agileo_Mds_Uploader::moveFormFileForObject('https://graph.facebook.com/'.$this->_fbObject->id.'/picture?width=640&height=640', $user);
            
            if (UserMapper::getMasterInstance()->save($uUser)) {
                // refresh
                $user = UserMapper::getMasterInstance()->getById($uUser->id); 
            }
        }
        
        if (!empty($user) && is_object($user) && $user instanceof User) {
            $user->login_by = 'facebook';
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user);
        }
        return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, null);
    }

}
