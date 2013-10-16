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
        
        $this->getResponse()->clearBody();
        
        $errors = $this->_getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        $code = $errors->exception->getCode();
        if(!$code) {
            switch ($errors->type) {
                case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
                case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
                case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                    $code = 404;
                    break;
                default:
                    $code = 500;
                    break;
            }
            
        }     


        // 400  Bad Request
        // 401  Authorization Required
        // 402  Payment Required (not used yet)
        // 403  Forbidden
        // 404  Not Found
        // 405  Method Not Allowed
        // 406  Not Acceptable (encoding)
        // 407  Proxy Authentication Required  
        // 408  Request Timed Out
        // 409  Conflicting Request
        // 410  Gone
        // 411  Content Length Required
        // 412  Precondition Failed
        // 413  Request Entity Too Long
        // 414  Request URI Too Long
        // 415  Unsupported Media Type
        // Server Errors
        // 500  Internal Server Error
        // 501  Not Implemented
        // 502  Bad Gateway    
        // 503  Service Unavailable    
        // 504  Gateway Timeout    
        // 505  HTTP Version Not Supported
        $this->view->errorCode = $code;
        switch ($code) {
            case 401:
                $this->getResponse()->setHttpResponseCode(401);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Brak dostępu';
                break;

            case 404:
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Strona nie istnieje';
                break;
                
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = 'Błąd strony ' . ($code);
                break;
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
        }

        $this->view->request   = $errors->request;
    }

    public function noauthAction()
    {
        
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
