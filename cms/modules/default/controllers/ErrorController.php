<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {

            $this->view->errorCode = 5001;

        } else {
            
            if($errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE
                || $errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER
                || $errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION
                || $errors->exception->getCode() == 404
                ) {
                    
                $this->view->errorCode = 404;
                $priority = Zend_Log::NOTICE;
                
            } else {
                
                $this->view->errorCode = 500;
                $priority = Zend_Log::ERR;
                
            }

            // Log exception, if logger available
            $log = $this->_getLog();
            if (is_a($log, 'Zend_Log')) {
                $log->log($this->view->message, $priority, $errors->exception);
                $log->log('Message : ' . $errors->exception->getMessage(), $priority);
                if(APPLICATION_ENV != 'production') {
                    // log extra info\
                    $log->log( 'Error Details : ' . PHP_EOL 
                             . 'Request Parameters : ' . var_export($errors->request->getParams(), true) . PHP_EOL
                             . 'Stack Trace : ' . PHP_EOL . $errors->exception->getTraceAsString()
                             , $priority);
    
                }
                
                $this->view->exception = $errors->exception;
                $this->view->request = $errors->request;
            }
            
        }

    }

    private function _getLog()
    {
        if (!Zend_Registry::isRegistered('log')) {
            return false;
        }
        $log = Zend_Registry::get('log');
        return $log;
    }

}
