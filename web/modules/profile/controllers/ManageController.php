<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Profile_ManageController extends Agileo_Web_ProfileController
{
    public function init()
    {
        parent::init();
        $this->_naviActivate();
    }
    
    public function indexAction()
    {
        
    }
    
    public function changepassAction()
    {
        $passwordForm = new Form_ChangePass();
        $passwordForm->setAction('/profile/manage/changepass');

        $userModel = UserMapper::getMasterInstance();

        $currentUser = null;

        if ($this->_request->isPost()) {
            if ($passwordForm->isValid($_POST)) {
                $userId = Zend_Auth::getInstance()->getIdentity()->id;
                
                $oldPass = $passwordForm->getValue('old_pass');
                if($userModel->checkPass($userId, $oldPass)) {
                    $user = new User($passwordForm->getValues());
                    $user->id = $userId;
                    UserMapper::getMasterInstance()->save($user);
                    $this->_helper->sessMessenger->notice('auth_password_changed');
                    return $this->_redirect('/');
                } else {
                    $this->_helper->sessMessenger->notice('auth_password_wrong_old_pass');
                    return $this->_redirect('/profile/auth/changepass');
                }
            }
        }

        $this->view->form = $passwordForm;
        
        $this->_addToNavigation ('Zmień hasło');
        
    }

    public function dataAction()
    {

        $id = Zend_Auth::getInstance()->getIdentity()->id;

        $userForm = new Form_PersonalData();
        $userForm->setAttrib('id', 'jUserForm');
        $userForm->setAction('/profile/manage/data');

        $userModel = UserMapper::getMasterInstance();

        $currentUser = null;

        if ($this->_request->isPost()) {
            if ($userForm->isValidPartial($_POST)) {
                
                $user = new User($userForm->getValues());
                $user->id = $id;

                if(isset($userForm->uploader_usr_avatar) && $userForm->uploader_usr_avatar->isUploaded()) {
                    $user->avatar = Agileo_Mds_Uploader::moveFormFileForObject($userForm->uploader_usr_avatar, $user);
                }

                UserMapper::getMasterInstance()->save($user);


                $identity = Zend_Auth::getInstance()->getIdentity();
                foreach($user->toArray() as $key => $value) {
                    $identity->{$key} = $value;
                }
                Agileo_Web_ProfileCookie::refresh($identity);

                $this->_helper->sessMessenger->notice('action_status_save_correct');
                
                return $this->_redirect('/profile/manage/data');
            } else {
                throw new Agileo_Exception('Błąd walidacji:'.print_r($userForm->getErrors(),true));
            }
        }

        $this->view->user = $currentUser = $userModel->getById($id);
        $userForm->populate($currentUser->toArray());
        
        $this->view->form = $userForm;

        // dodaj do breadcrubs
        $this->_addToNavigation ('Moje dane');

    }

    protected function _addToNavigation ($label, $params = array(), $naviParentId = 'profile_settings') {

        $zendNavi = Zend_Registry::get('Zend_Navigation');
        $n = $zendNavi->findById($naviParentId);
        $page = Zend_Navigation_Page::factory(array(
            'label' => $label,
            'module' => $this->_request->getModuleName(),
            'controller' => $this->_request->getControllerName(),
            'action' => $this->_request->getActionName(),
            'params' => $params
        ));

        $n->addPage($page);

    }
}
