<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_View_Helper_JForm extends Zend_View_Helper_Abstract
{

    public function jForm($formId, $addOptions = array())
    {
        
        $options = '';
        foreach($addOptions as $option => $val) {
            $options .= ',' . $option . ': ' . $val; 
        }
        
        $script = '
            function ' . $formId . 'Init() {
                formValidator = $("#' . $formId . '").validate({
                    errorClass: "help-inline",
                    errorElement: "span",
                    errorPlacement: function(error, element) {
                        if(!$(element).hasClass("ignore")) {
                            error.insertAfter(element);
                            element.parent().parent().addClass("error");
                        }
                    },
                    success: function(label) {
						label.parent().parent().addClass("success");
                    }
                    ' . ($options ? $options : '') . '
                });
                
                if(jQuery().datepicker) {

                    $("#' . $formId . ' input.date").datepicker({
                            changeMonth: true,
                            changeYear: true,
                            dateFormat: "yy-mm-dd",
                            constrainInput: false,
                            yearRange: "-80:+5",
                            
                            beforeShow: function() {
                                setTimeout(function(){
                                    $(".ui-datepicker").css("z-index", 99999999999999);
                                }, 0);
                            }
                                                    
                        });
                    
                    if(jQuery().dateplustimepicker) {
                        time = { hours: ' . date("G") . ', minutes: ' . (int) date("i") . '};
                        $("#' . $formId . ' input.datetime").dateplustimepicker({
                            changeMonth: true,
                            changeYear: true,
                            dateFormat: "yy-mm-dd",
                            constrainInput: false,
                            yearRange: "-80:+5",
                            timeFormat: "hh:mm:ss",
                            defaultTime: time,
                            
                            beforeShow: function() {
                                setTimeout(function(){
                                    $(".ui-datepicker").css("z-index", 99999999999999);
                                }, 0);
                            }
                        });
                    }
                }
                
                if(jQuery().tagsInput) { 
                    $("#' . $formId . ' input.tags").tagsInput({
                        autocomplete_url:"/service/tags",
                        defaultText: "'.$this->view->translate('form_add_tag').'",
                        height:"40px",
                        width:"95%"
                    });
                }
                
                $("#' . $formId . ' .control-group > label").each(function () {
                    label = $(this).html();
                    if(label && label.substring(label.length-1) != ":") {
                        $(this).html(label+":"); 
                    }
                });
                $("#' . $formId . ' .control-group > label.required").each(function () {
                    label = $(this).html();
                    if(label) {
                        $(this).html(label.substring(0, label.length-1) + "<sup class=\"required\">*</sup>:");
                    }

                });
                
                return formValidator;
            }

            function ' . $formId . 'Refresh() {
                form' . $formId . ' = ' . $formId . 'Init();
            }
            
            var form' . $formId . ' = ' . $formId . 'Init();
            ';
        
        $this->view->inlineScript()->appendScript($script);
        
        return '<!-- init jForm('.$formId.') //-->';
        
    }

}