<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

abstract class Agileo_Form_Twitter_View_Helper_EAbstract extends Zend_View_Helper_FormElement
{
    
    public static $init = false;

    protected function _initJs() 
    {
        if(!self::$init) {
            self::$init = true;
            
            $js = '';
            $js .= "var MDS_ATTACHMENT_PATH = '".$this->view->mdsAttachment('')."';";
            $js .= "var MDS_IMAGE_PATH = '".$this->view->mdsImage('', array('resize' => '200x200'))."';";
            
            $js .= file_get_contents(realpath(dirname(__FILE__) . '/chooser.js'));
            
            
            $this->view->inlineScript()->appendScript($js);
        }
    }
    
}
