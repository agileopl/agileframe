<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Search_IndexController extends Zend_Controller_Action
{
    
    public function resultsAction() 
    {
        $searchForm = new Form_Search();
        $searchForm->setAction($this->view->url(array(),'search'));
        
        if ($searchForm->isValidPartial($this->_request->getParams())) {

            $limit = 20;
            $filter = $searchForm->getValues();
            
            if(!empty($filter['q'])) {
                $this->view->pager = SearchMapper::getMasterInstance()->getPaginatorWithSubObject(array('query' => $filter['q'], 'web_available' => 1), $this->_getParam('page', 1), $limit);
            }
            
        }
        $this->view->searchForm = $searchForm;
        
    }

}
