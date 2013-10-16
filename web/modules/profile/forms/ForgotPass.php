<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Form_ForgotPass extends Agileo_Form_Twitter_Vertical
{

    public function init()
    {
        parent::init();

        $this->setMethod('post');

        $adminForm = new UserForm();
        $this->addElement($adminForm->getElement('usr_email'));

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => 'profile_submit_forgotpass',
            'buttonType' => Twitter_Form_Element_Submit::BUTTON_SUCCESS,
            'icon'       => 'ok',
            'whiteIcon'  => true,
            'iconPosition' => Twitter_Form_Element_Button::ICON_POSITION_RIGHT
        ));

        $this->_addActionsDisplayGroupElement($this->getElement('submit'));

    }

}