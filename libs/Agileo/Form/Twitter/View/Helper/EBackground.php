<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Form_Twitter_View_Helper_EBackground extends Agileo_Form_Twitter_View_Helper_EAbstract
{

    protected $_eiOptions = array(
        'withImage' => false
    );

    public static $init = false;

    public function eBackground($name, $value = null, $attribs = null)
    {
        $this->_initJs();

        foreach($this->_eiOptions as $key => $val) {
            if(!empty($attribs[$key])) {
                $this->_eiOptions[$key] = $attribs[$key];
                unset($attribs[$key]);
            }
        }
        
        
        $html = '<input type="text" id="'.$name.'" name="'.$name.'" value="' . $this->view->escape($value) . '" /><button class="btn btn-link jClearBg" data-rel="'.$name.'">wyczyść</button><div id="'.$name.'-background-preview" class="background-preview"><div>&nbsp;</div></div>';
        
        if(!self::$init) {
            self::$init = true;
            
            $this->view->inlineScript()->appendScript("
            function setBackgroundPreview(id) {
                bgColor = $('#'+id).val();
                if(bgColor !== false) {
                    $('#'+id+'-background-preview > div').css('background-color', bgColor);
                }
                
                bgUrl = ".(!empty($this->_eiOptions['withImage']) ? "$('#'+id+'_url').val()" : "''").";
                
                if(bgUrl) {
                    $('#'+id+'-background-preview > div').css('background-image', 'url('+MDS_ATTACHMENT_PATH+bgUrl+')');
                    
                    if($('#'+id+'-rem-img-button').length == 0) {
                        $('#'+id+'-background-preview').after('<button data-target=\"'+id+'\" id=\"'+id+'-rem-img-button\" class=\"btn btn-info jRemoveBgImg\">Usuń obrazek z tła</button>');
                    }
                }
                
            }
            $(document).ready(function() {
                $('.jRemoveBgImg').live('click', function() {
                    id = $(this).data('target');
                    $('#'+id+'_url').val('');    
                    $('#'+id+'-background-preview > div').css('background-image', '');
                    $('#'+id+'-background-preview > div').css('background-image', '');
                    $(this).remove();
                });
                $('.jClearBg').live('click', function() {
                    id = $(this).data('rel');
                    $('#'+id).val('');
                    return false;
                });
            });
            
            ");
        
        }
  
        $this->view->inlineScript()->appendScript("$(document).ready(function() {
                
            setBackgroundPreview('".$name."');
            $('input#".$name."').colorpicker({format: 'rgba'}).on('changeColor', function(ev){
                rgb = ev.color.toRGB();
                $('#".$name."').val('rgba('+rgb.r+','+rgb.g+','+rgb.b+','+rgb.a+')');
                setBackgroundPreview('".$name."');
                /*console.log(ev.color.toRGB().r);*/
            });
        });
        ");
        
        return  $html;
    }
}
