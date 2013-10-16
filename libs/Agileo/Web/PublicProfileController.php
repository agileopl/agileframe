<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Web_PublicProfileController extends Zend_Controller_Action
{

    protected $_user = null;
    
    protected $_naviMinDepth;
    protected $_naviMaxDepth;
    
    public function init()
    {
        $this->_user = UserMapper::getSlaveInstance()->getById((int)$this->_getParam('usrId'));
        if(empty($this->_user) || !$this->_user->isActive()) {
            throw new Exception('No user defined', 404);
        }
        
        Zend_Registry::set('layoutContextDefault', 'public-profile');
                
        $this->view->profileUser = $this->_user;
        Zend_Registry::set('objectContext', $this->_user);
        Zend_Registry::set('USER_PROFILE', $this->_user);
                
    }
    
    protected function _naviGetActive()
    {
        $zendNavi = Zend_Registry::get('Zend_Navigation');
        $profileNavigation = $zendNavi->findById('profile_public');
        if($profileNavigation) {
            $activePage = $this->_naviFindActive($profileNavigation);
            if(!empty($activePage['page'])) {
                return $activePage['page'];
            }
        }
        return null;
    }
    
    protected function _naviActivate()
    {

        $zendNavi = Zend_Registry::get('Zend_Navigation');
        $profileNavigation = $zendNavi->findById('profile_public');
        if($profileNavigation) {
            
            $profileNavigation->setLabel($this->_user->name.' '.$this->_user->surname);
            
            $this->_naviSetParams($profileNavigation);
            
            $activePage = $this->_naviFindActive($profileNavigation);
            if(!empty($activePage['page'])) {
                $this->_naviSetParentActive($activePage['page']);
            }
            
            $this->view->profileNavigation = $profileNavigation;
        }
    }
    
    protected function _naviSetParams(Zend_Navigation_Page $page)
    {
        if($page instanceof Zend_Navigation_Page_Mvc) {
            $page->setParam('usrId', $this->_user->id);
            $page->setParam('nick', T::urlEscape($this->_user->nick));
        }
        if($page->hasPages()) {
            foreach($page->getPages() as $sPage) {
                $this->_naviSetParams($sPage);
            }
        }
    }

    protected function _naviSetParentActive(Zend_Navigation_Page $activePage)
    {
        $parent = $activePage->getParent();
        if(!empty($parent) && $parent instanceof Zend_Navigation_Page) {
            $parent->setActive(true);
            $this->_naviSetParentActive($parent);
        }
    }

    protected function _naviSetMinDepth($minDepth = null)
    {
        if (null === $minDepth || is_int($minDepth)) {
            $this->_naviMinDepth = $minDepth;
        } else {
            $this->_naviMinDepth = (int) $minDepth;
        }
        return $this;
    }

    protected function _naviGetMinDepth()
    {
        if (!is_int($this->_naviMinDepth) || $this->_naviMinDepth < 0) {
            return 0;
        }
        return $this->_naviMinDepth;
    }

    protected function _naviSetMaxDepth($maxDepth = null)
    {
        if (null === $maxDepth || is_int($maxDepth)) {
            $this->_naviMaxDepth = $maxDepth;
        } else {
            $this->_naviMaxDepth = (int) $maxDepth;
        }
        return $this;
    }

    protected function _naviGetMaxDepth()
    {
        return $this->_naviMaxDepth;
    }
    
    protected function _naviFindActive(Zend_Navigation_Container $container,
                               $minDepth = null,
                               $maxDepth = -1)
    {
        if (!is_int($minDepth)) {
            $minDepth = $this->_naviGetMinDepth();
        }
        if ((!is_int($maxDepth) || $maxDepth < 0) && null !== $maxDepth) {
            $maxDepth = $this->_naviGetMaxDepth();
        }

        $found  = null;
        $foundDepth = -1;
        $iterator = new RecursiveIteratorIterator($container,
                RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $page) {
            $currDepth = $iterator->getDepth();
            if ($currDepth < $minDepth) {
                // page is not accepted
                continue;
            }

            if ($page->isActive(false) && $currDepth > $foundDepth) {
                // found an active page at a deeper level than before
                $found = $page;
                $foundDepth = $currDepth;
            }
        }

        if (is_int($maxDepth) && $foundDepth > $maxDepth) {
            while ($foundDepth > $maxDepth) {
                if (--$foundDepth < $minDepth) {
                    $found = null;
                    break;
                }

                $found = $found->getParent();
                if (!$found instanceof Zend_Navigation_Page) {
                    $found = null;
                    break;
                }
            }
        }

        if ($found) {
            return array('page' => $found, 'depth' => $foundDepth);
        } else {
            return array();
        }
    }
    
}
