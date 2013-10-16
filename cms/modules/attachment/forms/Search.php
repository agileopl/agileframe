<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Form_Search extends Agileo_Form_Twitter_Search
{

    public function __construct($options = null)
    {

        parent::__construct($options);

    }

    public function init()
    {

        $this->addElement('hidden', 'page');

        $attachForm = new AttachmentForm();

        $this->addElement($attachForm->getElement('att_description'));
        $this->getElement('att_description')->setLabel('att_search_desc');
                
        $multiOptions = array(
                '' => 'att_status_all',
                AttachmentMapper::STATUS_NEW => 'att_status_new',
                AttachmentMapper::STATUS_ACTIVE => 'att_status_active',
                AttachmentMapper::STATUS_BLOCKED => 'att_status_blocked',
                AttachmentMapper::STATUS_ERROR => 'att_status_error'
            );
        $element = new Zend_Form_Element_Select('att_status', array('multiOptions' => $multiOptions));
            
        $element->setLabel('att_status')
            ->setRequired(false)
            ->setValidators(array(
                new Zend_Validate_InArray(array_keys($multiOptions))));
        $element->setValue(AttachmentMapper::STATUS_ACTIVE);
        $this->addElement($element);
                                     
        $multiOptions = array(
                '' => 'att_type_all',
                AttachmentMapper::TYPE_IMAGE => 'att_type_image',
                AttachmentMapper::TYPE_FLASH => 'att_type_flash',
                AttachmentMapper::TYPE_VIDEO => 'att_type_video',
                AttachmentMapper::TYPE_AUDIO => 'att_type_audio',
                AttachmentMapper::TYPE_FILE => 'att_type_file'
            );
        $element = new Zend_Form_Element_Select('att_type', array('multiOptions' => $multiOptions));
        $element->setLabel('att_type')
            ->setRequired(false)
            ->setValidators(array(
                new Zend_Validate_InArray(array_keys($multiOptions))));
        $this->addElement($element);

        parent::init();
    }


}
