<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Form_User extends Agileo_Form_Twitter_Horizontal
{ 

    public function __construct($options = null)
    {
        parent::__construct($options);
    }
    
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');

        $userForm = new UserForm();

        $this->addElement($userForm->getElement('usr_id'));
    
        $status = $this->createElement('radio', 'usr_status');
        $status->setLabel("usr_status_change");
        $status->addMultiOption(UserMapper::STATUS_ACTIVE, 'user_status_active');
        $status->addMultiOption(UserMapper::STATUS_BLOCKED, 'user_status_blocked');
        $status->setValidators(array(
                new Zend_Validate_InArray(array(
                    UserMapper::STATUS_ACTIVE,
                    UserMapper::STATUS_BLOCKED
                ))));
        $this->addElement($status);
    
        $this->addElement($userForm->getElement('usr_role'));
        $this->addElement($userForm->getElement('usr_role_label'));
        
        $this->addElement($userForm->getElement('usr_role_cfr_id'));
        
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