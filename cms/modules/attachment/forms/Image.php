<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Form_Image extends Agileo_Form_Twitter_Horizontal
{

    public function init()
    {

        $this->setAttrib('enctype', 'multipart/form-data');

        parent::init();

        $this->setMethod('post');

        $attachForm = new AttachmentForm();
        
        $urlEl = $attachForm->getElement('att_url');

        $element = new Agileo_Form_Twitter_Element_Image('att_url', array('crop' => true));
        $element->setValidators($urlEl->getValidators());
        $element->setFilters($urlEl->getFilters());
        $this->addElement($element);
        
        $this->addElement('hidden', 'att_url_crop', array(
            'required' => FALSE,
            'validators' => array(new Zend_Validate_StringLength(0, 20)),
            'filters' => array(
                'StringTrim',
                new Agileo_Filter_Truncate( array(
                    'length' => 20,
                    'etc' => ''
                ))
            )
        ));
        
        $file = new Zend_Form_Element_File('uploader_att_url');
        $file->setLabel('')
            ->setDestination(Agileo_Mds_Uploader::getTempDirectory())
            ->setRequired(false);
        $this->addElement($file);
        
        
        $this->addElement($attachForm->getElement('att_description'));
        
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