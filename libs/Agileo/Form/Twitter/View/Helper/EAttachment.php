<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Form_Twitter_View_Helper_EAttachment extends Agileo_Form_Twitter_View_Helper_EAbstract
{
    protected $_eiOptions = array(
        'chooser' => false
    );
    
    public function eAttachment($name, $value = null, $attribs = null)
    {
        
        $this->_initJs();
        
        foreach($this->_eiOptions as $key => $val) {
            if(!empty($attribs[$key])) {
                $this->_eiOptions[$key] = $attribs[$key];
                unset($attribs[$key]);
            }
        }
        
        $html = '<input type="hidden" data-type="attachment" id="'.$name.'" name="'.$name.'" value="' . $this->view->escape($value) . '" />';
        
        $html .= '<span id="jContainer'.$name.'" '. $this->_htmlAttribs($attribs) .'>';

        if(!empty($value)) {
            $html .= '<a class="attach-element" href="'.$this->view->mdsAttachment($value).'"><i class="icon-file"></i> ' . $this->view->escape($value) . '"</a>';
        }

        if($this->_eiOptions['chooser']) {

            $html .= '<a href="#'.$name.'ModalUploadSingle" role="button" class="btn btn-primary" data-toggle="modal">'.$this->view->translate('form_element_attachment_upload').'</a> ';
            $html .= '<a href="#'.$name.'ModalChooseSingle" role="button" class="btn btn-primary" data-toggle="modal">'.$this->view->translate('form_element_attachment_choose').'</a> ';
                
            $this->view->inlineScript()->appendScript("
                $('#".$name."ModalUploadSingle').click(function () {
                    agiIframePopoverShow('Wgraj plik', '/attachment/index/upload.single/poplay/1/type//target/".$name."');
                    return false;
                });
                $('#".$name."ModalChooseSingle').click(function () {
                    agiIframePopoverShow('Wybierz plik', '/attachment/index/choose.single/poplay/1/type//target/".$name."');
                    return false;
                });
            ");
                    
        }
        $html .= '</span>';

        return  $html;
    }
}
