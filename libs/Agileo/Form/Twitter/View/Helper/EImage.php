<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Form_Twitter_View_Helper_EImage extends Agileo_Form_Twitter_View_Helper_EAbstract
{

    protected $_eiOptions = array(
        'chooser' => false,
        'crop' => false,
        'resize' => '200x200'
    );

    public function eImage($name, $value = null, $attribs = null)
    {
        $this->_initJs();

        foreach($this->_eiOptions as $key => $val) {
            if(!empty($attribs[$key])) {
                $this->_eiOptions[$key] = $attribs[$key];
                unset($attribs[$key]);
            }
        }
        
        $html = '<input type="hidden" data-type="image" id="'.$name.'" name="'.$name.'" value="' . $this->view->escape($value) . '" />';

        $html .= '<span id="jContainer'.$name.'" '. $this->_htmlAttribs($attribs) .'>';
        
        $html .= '<div class="jThumb pull-left">';
        if(!empty($value)) {
            if(substr($value,0,7) == 'http://') {
                list($width,$height) = explode('x',$this->_eiOptions['resize']);
                $html .= '<img style="height:'.$height.'px" class="ico-element" src="' . $value . '" />';
            } else {
                $html .= '<img class="ico-element" src="' . $this->view->mdsImage($value, array('resize'=>$this->_eiOptions['resize'])) . '" />';
            }
        }
        $html .= '</div>';
        
        if(!empty($this->_eiOptions['crop'])) {
            $html .= '
            <div class="pull-left jCropRatios'.(empty($value) ? ' hide' : '').'">
                &nbsp;
                <a href="#chooseThumb" data-rel="'.$name.'" data-ratio="cancel" class="jCropRatio btn btn-danger" title="wyłącz"><i class=" icon-ban-circle"></i></a>
                <a href="#chooseThumb" data-rel="'.$name.'" data-ratio="1.0" class="jCropRatio btn btn-info"><i class="icon-check-empty"></i></a>
                <a href="#chooseThumb" data-rel="'.$name.'" data-ratio="1.3333" class="jCropRatio btn btn-info">4/3</a>
                <a href="#chooseThumb" data-rel="'.$name.'" data-ratio="1.5" class="jCropRatio btn btn-info">3/2</a>
                <a href="#chooseThumb" data-rel="'.$name.'" data-ratio="1.6" class="jCropRatio btn btn-info">16/10</a>
                <a href="#chooseThumb" data-rel="'.$name.'" data-ratio="1.7777" class="jCropRatio btn btn-info">16/9</a>
                <a href="#chooseThumb" data-rel="'.$name.'" data-ratio="2.6666" class="jCropRatio btn btn-info">24/9</a>
                <a href="#chooseThumb" data-rel="'.$name.'" data-ratio="3.5555" class="jCropRatio btn btn-info">32/9</a>
            </div>

            <div class="clearfix"></div>
            ';
        }
        if($this->_eiOptions['chooser']) {

            $html .= '<div class="jChooserButtons" style="margin-top: 4px;">';
            $html .= '<a href="#'.$name.'ModalUploadSingle" id="'.$name.'ModalUploadSingle" class="btn btn-primary">'.$this->view->translate('form_element_image_upload').'</a> ';
            $html .= '<a href="#'.$name.'ModalChooseSingle" id="'.$name.'ModalChooseSingle" class="btn btn-primary">'.$this->view->translate('form_element_image_choose').'</a> ';
            $html .= '<a href="#'.$name.'UnsetSingle" id="'.$name.'UnsetSingle" class="btn btn-link'.(empty($value) ? ' hide': '').'">'.$this->view->translate('form_element_image_unlink').'</a> ';
            $html .= '</div>';
            
            $this->view->inlineScript()->appendScript("
                
                $('#".$name."ModalUploadSingle').click(function () {
                    agiIframePopoverShow('Wgraj obrazek', '/attachment/index/upload.single/poplay/1/type/image/target/".$name."');
                    return false;
                });
                $('#".$name."ModalChooseSingle').click(function () {
                    agiIframePopoverShow('Wybierz obrazek', '/attachment/index/choose.single/poplay/1/type/image/target/".$name."');
                    return false;
                });
                $('#".$name."UnsetSingle').click(function () {
                    $('#".$name."').val('');
                    $('#jContainer".$name." .jThumb').html('');
                    $(this).addClass('hide');
                    return false;
                });
            ");
                        
        }
        $html .= '</span>';
        
        if(!empty($this->_eiOptions['crop'])) {
            $this->view->inlineScript()->appendScript("$(document).ready(function() {
                initCrop('".$name."');
            });
            ");
        }
        
        return  $html;
    }
}
