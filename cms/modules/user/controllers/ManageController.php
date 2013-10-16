<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class User_ManageController extends Zend_Controller_Action
{

    public function usersListAction()
    {

        $userMapper = UserMapper::getMasterInstance();

        $searchForm = new Form_UserSearch();
        $searchForm->setAction($this->view->aurl(array()));
        
        $sfsess = new Zend_Session_Namespace($this->_request->getModuleName().'_'.$this->_request->getControllerName().'_'.$this->_request->getActionName());
        if(!$this->_request->has('page') && !empty($sfsess->params)) {
            $searchForm->populate($sfsess->params);
        }
        
        if ($searchForm->isValidPartial($this->_request->getParams())) {

            $sfsess->params = $searchForm->getValues();

            $limit = $searchForm->limit->getValue() ? $searchForm->limit->getValue() : 10;
            $filter = $searchForm->getValues();

            $this->view->pager = $userMapper->getListPager($filter, $this->_getParam('page', 1), $limit);
            
        }
        $this->view->searchForm = $searchForm;

    }

    public function userDeleteAction()
    {

        $userMapper = UserMapper::getMasterInstance();
        $itemId = $this->_getParam('id', 0);
        
        if ($itemId) {
            $user = $userMapper->getById($itemId);
        }
        if(empty($user)) {
            $this->_redirect('/user/manage/users.list');
        }
        
        if ($this->getRequest()->isPost()) {

            $user->adm_id = Zend_Auth::getInstance()->getIdentity()->id; 
            $status = $userMapper->delete($user);

            if ($status) {
                $this->_helper->sessMessenger->notice('action_status_save_correct');
            } elseif ($status == 0) {
                $this->_helper->sessMessenger->notice('action_status_save_notice');
            } else {
                $this->_helper->sessMessenger->error('action_status_save_error');
            }

            $this->_redirect('/user/manage/users.list');
            
        }
        $this->view->user = $user;
        
        $label = $this->view->translate('user_h1_delete', $user->name. ' ' . $user->surname);
        $params = array('id'=>$user->id);
        $this->_addToNavigation ($label, $params, 'users_list');
        
    }


    public function userItemAction()
    {

        $userMapper = UserMapper::getMasterInstance();

        $itemId = $this->_getParam('id', 0);

        $user = null;
        if ($itemId) {
            $user = $userMapper->getById($itemId);
        }
        
        if(empty($user)) {
            $this->_redirect('/user/manage/users.list');
        }
        
        $form = new Form_User();
        $form->setAction($this->_request->getPathInfo());
        
        if ($this->getRequest()->isPost()) {
            $inp = $this->getRequest()->getPost();

            if ($form->isValidPartial($inp)) {
                
                $userData = $form->getValues();
                $userData['usr_id'] = $user->id;

                $uUser = new User($userData);
                
                $status = $userMapper->save($uUser);

                if ($status) {
                    $this->_helper->sessMessenger->notice('action_status_save_correct');
                } elseif ($status == 0) {
                    $this->_helper->sessMessenger->notice('action_status_save_notice');
                } else {
                    $this->_helper->sessMessenger->error('action_status_save_error');
                }

                $this->_redirect('/user/manage/user.item/id/' . $user->id);

            } else {
                throw new Exception('No valid form ' . print_r($form->getErrorMessages(), true) . print_r($inp, true));
            }

        } else {
            $form->populate($user->toArray());
        }

        $this->view->userForm = $form;
        
        $this->view->user = $user;

        $this->_userItemAddToNavi ($user);
    }

    protected function _userItemAddToNavi (User $user)
    {
        $label = $this->view->translate('user_h1_user', $user->name. ' ' . $user->surname);
        $params = array('id'=>$user->id);
        $this->_addToNavigation ($label, $params);
        
    }

    protected function _addToNavigation ($label, $params = array(), $naviParentId = 'users_list') {

        $zendNavi = Zend_Registry::get('Zend_Navigation');
        if($n = $zendNavi->findById($naviParentId)) {
            $page = Zend_Navigation_Page::factory(array(
                'label' => $label,
                'module' => $this->_request->getModuleName(),
                'controller' => $this->_request->getControllerName(),
                'action' => $this->_request->getActionName(),
                'params' => $params,
                'active' => true
            ));
    
            $n->addPage($page);
        }

    }
    
}
