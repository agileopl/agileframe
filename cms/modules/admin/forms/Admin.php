<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Form_Admin extends Agileo_Form_Twitter_Horizontal
{

    public function init()
    {

        // Make this form horizontal
        $this->setAttrib("horizontal", true);

        parent::init();

        $this->setMethod('post');

        $adminForm = new AdminForm();

        $this->addElement($adminForm->getElement('adm_id'));
        $this->addElement($adminForm->getElement('adm_login'));
        $this->addElement($adminForm->getElement('adm_pass'));

        $this->addElement($adminForm->getElement('adm_name'));
        $this->addElement($adminForm->getElement('adm_surname'));
        $this->addElement($adminForm->getElement('adm_email'));

        $this->addElement($adminForm->getElement('adm_status'));
        $this->addElement($adminForm->getElement('adm_role'));

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