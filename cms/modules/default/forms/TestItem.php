<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Form_TestItem extends Agileo_Form_Twitter_Horizontal
{

    public function init()
    {

        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAttrib('id', 'TestItem');

        $this->addElement('hidden', 'id', array(
            'validators' => array('Int')
        ));

        $this->addElement('text', 'title', array(
            'label' => 'test_title',
            'required' => TRUE,
            'attribs' => array('class' => 'required')
            // ,
            // 'validators' => Test::getValidatorSchema('title')
        ));

        $this->addElement('text', 'start_date', array(
            'label' => 'test_start_date',
            'required' => TRUE,
            // 'validators' => Test::getValidatorSchema('warranty_end_date'),
            'attribs' => array('class' => 'datetime required')
        ));

        $this->addElement('text', 'end_date', array(
            'label' => 'test_end_date',
            'required' => TRUE,
            // 'validators' => Test::getValidatorSchema('warranty_end_date'),
            'attribs' => array('class' => 'date required')
        ));

        $this->addElement('text', 'tags', array(
            'autocompleteUrl' => '/tag/index/autocomplete',
            'label' => 'test_tags',
            //'validators' => Paragono_ScanValidator::getValidatorSchema('tags'),
            'attribs' => array('class' => 'tags')
        ));

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => 'submit_save',
            'buttonType' => Twitter_Form_Element_Submit::BUTTON_SUCCESS,
            'icon'       => 'ok',
            'whiteIcon'  => true,
            'iconPosition' => Twitter_Form_Element_Button::ICON_POSITION_RIGHT
        ));

        $this->addElement('button', 'return', array(
            'ignore' => true,
            'label' => 'submit_return',
            'buttonType' => Twitter_Form_Element_Submit::BUTTON_LINK
        ));

        $this->_addActionsDisplayGroupElement($this->getElement('submit'));
        $this->_addActionsDisplayGroupElement($this->getElement('return'));

    }

}