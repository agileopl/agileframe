<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class TestController extends Zend_Controller_Action
{

    public function init()
    {

    }

    public function indexAction()
    {
        $this->_redirect('/test/examples');
    }
    
    public function examplesAction()
    {
        // action body

        $test = new Test(array('tst_id' => 1, 'tst_title' => 'Hello world ', 'tst_dd' => 'DD'));

        $this->view->test = $test;

        $this->view->collection = TestMapper::getMasterInstance()->getList();

        $this->view->validData = $test->toArray();

        $this->view->searchForm = new Form_TestSearch();
        $this->view->form = new Form_TestItem();

    }
    
    public function iconsAction()
    {
        
    }
  
    public function searchIndexAction()
    {
        $this->view->lastSearchItems = SearchMapper::getMasterInstance()->getPaginatorWithSubObject(array('refresh_date_from' => date('Y-m-d').' 00:00:00', 'refresh_date_to' => date('Y-m-d').' 23:59:59'), 1, 1000);
    }
}
