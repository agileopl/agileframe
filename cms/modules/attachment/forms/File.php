<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Form_File extends Agileo_Form_Twitter_Horizontal
{

    public function init()
    {

        $this->setAttrib('enctype', 'multipart/form-data');

        parent::init();

        $this->setMethod('post');

        $attachForm = new AttachmentForm();
        
        $this->addElement($attachForm->getElement('att_url'));
        
        $file = new Zend_Form_Element_File('uploader_att_url');
        $file->setLabel('')
            ->setDestination(Agileo_Mds_Uploader::getTempDirectory())
            ->setRequired(false);
        $this->addElement($file);
        
        
        $this->addElement($attachForm->getElement('att_description'));
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => 'attachment_button_add',
            'buttonType' => Twitter_Form_Element_Submit::BUTTON_SUCCESS,
            'icon'       => 'ok',
            'whiteIcon'  => true,
            'iconPosition' => Twitter_Form_Element_Button::ICON_POSITION_RIGHT
        ));

        $this->addElement('button', 'return', array(
            'ignore' => true,
            'label' => 'attachment_button_return',
            'buttonType' => Twitter_Form_Element_Submit::BUTTON_LINK
        ));

        $this->_addActionsDisplayGroupElement($this->getElement('submit'));
        $this->_addActionsDisplayGroupElement($this->getElement('return'));

    }

}