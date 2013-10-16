<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Form_SetNewPass extends Agileo_Form_Twitter_Horizontal
{

    public function init()
    {

        // Make this form horizontal
        $this->setAttrib('enctype', 'multipart/form-data');
        
        parent::init();

        $this->setMethod('post');

        $userForm = new UserForm();

        $this->addElement($userForm->getElement('usr_email'));
        $this->getElement('usr_email')->setAttrib('readonly', true);
        
        $this->addElement($userForm->getElement('usr_pass'));
        
        $this->addElement('password', 're_pass', array(
            'label' => 'auth_re_pasword',
            'required' => TRUE,
            'attribs' => array('class' => 'required')
        ));

        $this->addElement('hidden','token');
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => 'submit_save',
            'buttonType' => Twitter_Form_Element_Submit::BUTTON_SUCCESS,
            'icon'       => 'ok',
            'whiteIcon'  => true,
            'iconPosition' => Twitter_Form_Element_Button::ICON_POSITION_RIGHT
        ));

        $this->_addActionsDisplayGroupElement($this->getElement('submit'));

    }

}