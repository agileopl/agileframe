<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Admin_IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_redirect('/admin/index/list');
    }

    public function listAction()
    {
        $this->view->admins = AdminMapper::getMasterInstance()->getListPager(array(), $this->_getParam('page', 1), 10);
    }

    public function createAction()
    {
        $frmAdmin = new Form_Admin();
        $frmAdmin->setAttrib('id', 'jAdminForm');
        if ($this->_request->isPost()) {
            if ($frmAdmin->isValid($_POST)) {
                $admin = new Admin($frmAdmin->getValues());
                AdminMapper::getMasterInstance()->save($admin);
                $this->_helper->sessMessenger->notice('action_status_save_correct');
                return $this->_redirect('/admin/index/list');
            }
        }
        $frmAdmin->setAction('/admin/index/create');
        $this->view->form = $frmAdmin;

        // dodaj do breadcrubs
        $this->_addToNavigation ($this->view->translate('admin_h1_add'));

    }

    public function updateAction()
    {

        $id = (int) $this->_getParam('id');

        $adminForm = new Form_Admin();
        $adminForm->setAttrib('id', 'jAdminForm');
        $adminForm->setAction('/admin/index/update/id/'.$id);
        $adminForm->removeElement('adm_pass');

        $adminModel = AdminMapper::getMasterInstance();

        $currentAdmin = null;

        if ($this->_request->isPost()) {
            if ($adminForm->isValid($_POST)) {
                $admin = new Admin($adminForm->getValues());
                AdminMapper::getMasterInstance()->save($admin);
                $this->_helper->sessMessenger->notice('action_status_save_correct');
                return $this->_redirect('/admin/index/list');
            }
        }

        $this->view->admin = $currentAdmin = $adminModel->getById($id);
        $adminForm->populate($currentAdmin->toArray());
        $this->view->form = $adminForm;

        // dodaj do breadcrubs
        $label = $this->view->translate('admin_h1_edit', $currentAdmin->name.' '.$currentAdmin->surname);
        $this->_addToNavigation ($label, array('id' => $id));

    }

    public function deleteAction()
    {
        $id = $this->_request->getParam('id');
        $adminMapper = AdminMapper::getMasterInstance();
        $adminMapper->delete($id);
        return $this->_forward('list');
    }
    

    public function passwordAction()
    {
            
        $id = $this->_request->getParam('id');
            
        $passwordForm = new Form_ChangePass();
        $passwordForm->setAction('/admin/index/password/id/'.$id);

        $adminModel = AdminMapper::getMasterInstance();

        $currentAdmin = null;

        if ($this->_request->isPost()) {
            if ($passwordForm->isValid($_POST)) {
                
                $admin = new Admin();
                $admin->pass = $passwordForm->getValue('new_pass');
                $admin->id = $id;
                
                $adminModel->save($admin);
                
                $this->_helper->sessMessenger->notice('action_status_save_correct');
                
                return $this->_redirect('/admin/index/list');
            }
        }

        $this->view->form = $passwordForm;
        $this->view->admin = $currentAdmin = $adminModel->getById($id);
        
        $label = $this->view->translate('admin_h1_set_new_password', $currentAdmin->name.' '.$currentAdmin->surname);
        $this->_addToNavigation ($label, array('id' => $id));
        
    }
    
    
    protected function _addToNavigation ($label, $params = array(), $naviParentId = 'admin_list') {

        $zendNavi = Zend_Registry::get('Zend_Navigation');
        $n = $zendNavi->findById($naviParentId);
        $page = Zend_Navigation_Page::factory(array(
            'label' => $label,
            'module' => $this->_request->getModuleName(),
            'controller' => $this->_request->getControllerName(),
            'action' => $this->_request->getActionName(),
            'params' => $params,
            'active' => true
        ));
        $page->setVisible(true);
        $n->addPage($page);
        
        

    }


}
