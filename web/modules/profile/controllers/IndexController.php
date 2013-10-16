<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Profile_IndexController extends Zend_Controller_Action
{

    public function listAction()
    {
        $userMapper = UserMapper::getSlaveInstance();
        
        $filter = array('available' => 1);
        
        $mapSort = array(
            'najaktywniejsi' => 'mostActive',
            'najpopularniejsi' => 'mostPopular', 
            'aktywnosc' => 'lastActivity',
            'przystapienie' => 'createDate',
            'alfabetycznie' => 'alphabetically'
        );
        
        $this->view->order = $sort = $this->_getParam('sort', 'najaktywniejsi');
        
        $filter['order'] = !empty($mapSort[$sort]) ? $mapSort[$sort] : 'mostActive';
        $this->view->nick = $filter['nick'] = $this->_getParam('nick', '');
        
        $this->view->pager = $userMapper->getListPager($filter, $this->_getParam('page',1), 20);
        
        $zendNavi = Zend_Registry::get('Zend_Navigation');
        $n = $zendNavi->findById('users');
        $n->setActive(true);
        
    }

}
