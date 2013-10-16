<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Controller_Plugin_SessManager extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // if session started torn on SessMessenger
        if(Zend_Session::isStarted()) {
            Zend_Controller_Action_HelperBroker::addHelper(new Agileo_Controller_Action_Helper_SessMessenger());
        }
    }

}
