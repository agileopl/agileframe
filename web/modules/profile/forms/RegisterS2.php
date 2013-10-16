<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Form_RegisterS2 extends Agileo_Form_Twitter_Horizontal
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

        $this->addElement($userForm->getElement('usr_nick'));
        
        $this->addElement($userForm->getElement('usr_name'));
        $this->addElement($userForm->getElement('usr_surname'));
        
        $this->addElement($userForm->getElement('usr_avatar'));
        $file = new Zend_Form_Element_File('uploader_usr_avatar');
        $file->setLabel('')
            ->setDestination(Agileo_Mds_Uploader::getTempDirectory())
            ->setRequired(false);
        $this->addElement($file);
        
        $this->addElement('hidden','token');
        
        $this->addElement($userForm->getElement('usr_geo_address'));
        $this->addElement($userForm->getElement('usr_geo_lat'));
        $this->addElement($userForm->getElement('usr_geo_lng'));
        $this->addElement($userForm->getElement('usr_geo_zoom'));
        
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