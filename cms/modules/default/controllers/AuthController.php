<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class AuthController extends Zend_Controller_Action
{

    public function loginAction()
    {
        $this->_helper->_layout->setLayout('login');
        
        $adminForm = new Form_Login();
        $adminForm->setAction('/auth/login');

        $adminForm->setAttrib('id', 'jLoginForm');

        if ($this->_request->isPost() && $adminForm->isValid($_POST)) {
            $data = $adminForm->getValues();
            $authAdapter = new Agileo_Cms_Auth($data['adm_login'], $data['adm_pass']);
            $result = $authAdapter->authenticate();
            if ($result->isValid()) {
                $auth = Zend_Auth::getInstance();
                $storage = $auth->getStorage();
                $storage->write($result->getIdentity());
                return $this->_redirect('/admin/index/index');
            } else {
                $this->_helper->sessMessenger->notice('admin_login_error');
                return $this->_redirect('/auth/login');
            }
        }
        $this->view->form = $adminForm;
    }

    public function logoutAction()
    {
        $authAdapter = Zend_Auth::getInstance();
        $authAdapter->clearIdentity();
        Zend_Session::destroy(true);
        return $this->_redirect('/auth/login');
    }

    public function changepassAction()
    {
        $passwordForm = new Form_ChangePass();
        $passwordForm->setAction('/auth/changepass');

        $adminModel = AdminMapper::getMasterInstance();

        $currentAdmin = null;

        if ($this->_request->isPost()) {
            if ($passwordForm->isValid($_POST)) {
                $adminId = Zend_Auth::getInstance()->getIdentity()->id;
                
                $oldPass = $passwordForm->getValue('old_pass');
                if($adminModel->checkPass($adminId, $oldPass)) {
                    $admin = new Admin($passwordForm->getValues());
                    $admin->id = $adminId;
                    AdminMapper::getMasterInstance()->save($admin);
                    $this->_helper->sessMessenger->notice('auth_password_changed');
                    return $this->_redirect('/');
                } else {
                    $this->_helper->sessMessenger->notice('auth_password_wrong_old_pass');
                    return $this->_redirect('/auth/changepass');
                }
            }
        }

        $this->view->form = $passwordForm;
        
        $this->_addToNavigation ($this->view->translate('admin_change_pass'));
        
    }

    public function updateAction()
    {

        $id = Zend_Auth::getInstance()->getIdentity()->id;

        $adminForm = new Form_PersonalData();
        $adminForm->setAttrib('id', 'jAdminForm');
        $adminForm->setAction('/auth/update');
        $adminForm->removeElement('adm_pass');

        $adminModel = AdminMapper::getMasterInstance();

        $currentAdmin = null;

        if ($this->_request->isPost()) {
            if ($adminForm->isValid($_POST)) {
                
                $admin = new Admin($adminForm->getValues());
                $admin->id = $id;

                if($adminForm->uploader_adm_avatar->isUploaded()) {
                    $admin->avatar = Agileo_Mds_Uploader::moveFormFileForObject($adminForm->uploader_adm_avatar, $admin);
                }
                if($adminForm->uploader_adm_cms_bg->isUploaded()) {
                    $admin->cms_bg = Agileo_Mds_Uploader::moveFormFileForObject($adminForm->uploader_adm_cms_bg, $admin);
                }
                
                AdminMapper::getMasterInstance()->save($admin);
                
                // aupdate auth
                if(isset($admin->avatar)) {
                    Zend_Auth::getInstance()->getIdentity()->avatar = $admin->avatar;
                }
                if(isset($admin->cms_bg)) {
                    Zend_Auth::getInstance()->getIdentity()->cms_bg = $admin->cms_bg;
                }
                Zend_Auth::getInstance()->getIdentity()->name = $admin->name;
                Zend_Auth::getInstance()->getIdentity()->surname = $admin->surname;
                Zend_Auth::getInstance()->getIdentity()->login= $admin->login;
                Zend_Auth::getInstance()->getIdentity()->cms_bg= $admin->cms_bg;
                
                
                $this->_helper->sessMessenger->notice('action_status_save_correct');
                
                return $this->_redirect('/');
            }
        }

        $this->view->admin = $currentAdmin = $adminModel->getById($id);
        $adminForm->populate($currentAdmin->toArray());
        $this->view->form = $adminForm;

        // dodaj do breadcrubs
        $this->_addToNavigation ($this->view->translate('admin_change_data'));

    }

    protected function _addToNavigation ($label, $params = array(), $naviParentId = 'home') {

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
