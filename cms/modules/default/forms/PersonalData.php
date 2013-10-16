<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Form_PersonalData extends Agileo_Form_Twitter_Horizontal
{

    public function init()
    {

        // Make this form horizontal
        $this->setAttrib('enctype', 'multipart/form-data');
        
        parent::init();

        $this->setMethod('post');

        $adminForm = new AdminForm();

        $this->addElement($adminForm->getElement('adm_login'));

        $this->addElement($adminForm->getElement('adm_name'));
        $this->addElement($adminForm->getElement('adm_surname'));
        $this->addElement($adminForm->getElement('adm_email'));
        
        $this->addElement($adminForm->getElement('adm_avatar'));
        $file = new Zend_Form_Element_File('uploader_adm_avatar');
        $file->setLabel('')
            ->setDestination(Agileo_Mds_Uploader::getTempDirectory())
            ->setRequired(false);
        $this->addElement($file);
        
        $this->addElement($adminForm->getElement('adm_cms_bg'));
        $file = new Zend_Form_Element_File('uploader_adm_cms_bg');
        $file->setLabel('')
            ->setDestination(Agileo_Mds_Uploader::getTempDirectory())
            ->setRequired(false);
        $this->addElement($file);
        
        
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