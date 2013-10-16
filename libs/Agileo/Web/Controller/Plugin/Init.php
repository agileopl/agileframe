<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Agileo_Web_Controller_Plugin_Init extends Zend_Controller_Plugin_Abstract
{

    protected $_timedebuggerName = null;
    
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $f = new Agileo_Filter_Seo();
        $this->_timedebuggerName = 'REQ__'.$f->filter(substr($_SERVER['REQUEST_URI'],1));
        Agileo_Timedebuger::start($this->_timedebuggerName);
    }

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if ($request->isXmlHttpRequest()) {
            header('Content-Type: application/text; charset=UTF-8');
            Zend_Layout::getMvcInstance()->disableLayout();
        } else if ($request->has('poplay')) {   
            Zend_Layout::getMvcInstance()->setLayout('popup');
        }
    }

    public function dispatchLoopShutdown()
    {

        if(!Zend_Session::isStarted() && empty($_REQUEST['clearCache'])) {
            $cacheControl = Zend_Registry::get('config')->globals->headers->default->cache_control;
            if($cacheControl) {
                $this->getResponse()
                    ->setHeader('Cache-Control', $cacheControl)
                    ;
            }
        }        

        $time = Agileo_Timedebuger::stop($this->_timedebuggerName);
        $info = '';
        
        if(Zend_Registry::get('config')->runMode == 'dev') {
            
            $info = '<div style="background-color: silver;color: black;font-size: 10px;"> -- '.sprintf("%.2f",$time).'s -- '.date('H:i:s');
            
            $info .= ' | '.$this->_request->getModuleName().'/'.$this->_request->getControllerName().'/'.$this->_request->getActionName();
            
            $info .= ' | <a href="?clearCache=1">clearCache</a>';
            
            $info .= '</div>';
            
        } else {
            $info = '<!-- time: '.sprintf("%.2f",$time).', '.date('H:i:s').' //-->';
        }

        
        $this->getResponse()
             ->appendBody($info);
    }
    
}
