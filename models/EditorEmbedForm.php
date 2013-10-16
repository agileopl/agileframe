<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class EditorEmbedForm extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');


        // aee_id  int(10)     UNSIGNED    Nie None    auto_increment   
        $this->addElement('hidden', 'aee_id', array(
            'required' => false,
            'validators' => array(new Agileo_Filter_Int()),
            'filters' => array(new Agileo_Filter_Int())
        ));
                               
        // aee_parent  varchar(50) utf8_general_ci     Nie None 
        $this->addElement('text', 'aee_parent', array(
            'required' => TRUE,
            'attribs' => array('class' => 'required'),
            'validators' => array(new Zend_Validate_StringLength(1, 50)),
            'filters' => array('StringTrim')
        ));
        
        // aee_parent_id   int(10)     UNSIGNED    Nie None                                
        $this->addElement('hidden', 'aee_parent_id', array(
            'required' => true,
            'validators' => array(new Agileo_Filter_Int()),
            'filters' => array(new Agileo_Filter_Int())
        ));
        
        // aee_field_name  varchar(50) utf8_general_ci     Nie description
        $this->addElement('text', 'aee_field_name', array(
            'required' => TRUE,
            'attribs' => array('class' => 'required'),
            'validators' => array(new Zend_Validate_StringLength(1, 50)),
            'filters' => array('StringTrim')
        ));
                               
        // aee_position    int(11)         Nie None     
        $this->addElement('hidden', 'aee_position', array(
            'required' => true,
            'validators' => array(new Agileo_Filter_Int()),
            'filters' => array(new Agileo_Filter_Int())
        ));
                                   
        // aee_editor_embed  varchar(1000)   utf8_general_ci     Nie None                                
        $this->addElement('text', 'aee_editor_embed', array(
            'required' => TRUE,
            'attribs' => array('class' => 'required'),
            'validators' => array(new Zend_Validate_StringLength(1, 50)),
            'filters' => array('StringTrim')
        ));
        
        // aee_object_name varchar(50) utf8_general_ci     Nie None                                
        $this->addElement('text', 'aee_object_name', array(
            'required' => TRUE,
            'attribs' => array('class' => 'required'),
            'validators' => array(new Zend_Validate_StringLength(1, 50)),
            'filters' => array('StringTrim')
        ));

        // aee_object_id   int(11)         Nie None                                
        $this->addElement('hidden', 'aee_object_id', array(
            'required' => true,
            'validators' => array(new Agileo_Filter_Int()),
            'filters' => array(new Agileo_Filter_Int())
        ));
        
        // aee_type    varchar(50) utf8_general_ci     Nie None                                
        $this->addElement('text', 'aee_type', array(
            'required' => TRUE,
            'validators' => array(
                new Zend_Validate_InArray(array(
                    EditorEmbed::TYPE_USER_IMAGE,
                    EditorEmbed::TYPE_USER_VIDEO,
                    EditorEmbed::TYPE_USER_QUIZ
                ))),
            'filters' => array('StringTrim')
        ));
        
        // aee_class   varchar(50) utf8_general_ci     Nie None                                
        $this->addElement('text', 'aee_object_name', array(
            'required' => false,
            'validators' => array(new Zend_Validate_StringLength(0, 50)),
            'filters' => array('StringTrim')
        ));
        
        // aee_href    varchar(255)    utf8_general_ci     Nie None
        $this->addElement('text', 'aee_href', array(
            'required' => false,
            'validators' => array(new Zend_Validate_StringLength(0, 255)),
            'filters' => array('StringTrim')
        ));
                                                
        // aee_align   varchar(50) utf8_general_ci     Nie None        $this->addElement('text', 'aee_align', array(
            'required' => false,
            'validators' => array(new Zend_Validate_StringLength(0, 50)),
            'filters' => array('StringTrim')
        ));


    }

}
?>
