<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Form_Login extends Agileo_Form_Twitter_Vertical
{

    public function init()
    {
        parent::init();

        $this->setMethod('post');

        $adminForm = new UserForm();
        $this->addElement($adminForm->getElement('usr_email'));
        $this->addElement($adminForm->getElement('usr_pass'));

        $this->addElement($this->createElement('hidden', 'referer'));

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => 'Zaloguj się',
            'buttonType' => Twitter_Form_Element_Submit::BUTTON_SUCCESS,
            'icon'       => 'ok',
            'whiteIcon'  => true,
            'iconPosition' => Twitter_Form_Element_Button::ICON_POSITION_RIGHT
        ));

        $this->addElement('button', 'forgotpass', array(
            'ignore' => true,
            'label' => 'profile_button_forgotpass',
            'buttonType' => Twitter_Form_Element_Submit::BUTTON_LINK
        ));
        
        $this->_addActionsDisplayGroupElement($this->getElement('submit'));
        $this->_addActionsDisplayGroupElement($this->getElement('forgotpass'));

    }

}