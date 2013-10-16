<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class AttachmentForm extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');

        // create new element
        $id = $this->createElement('hidden', 'att_id');
        $this->addElement($id);

        $id = $this->createElement('hidden', 'att_adm_id');
        $this->addElement($id);

        $id = $this->createElement('hidden', 'att_usr_id');
        $this->addElement($id);

        $id = $this->createElement('hidden', 'att_cfr_id');
        $this->addElement($id);

        $id = $this->createElement('hidden', 'att_cem_id');
        $this->addElement($id);
        
        $id = $this->createElement('hidden', 'att_copy_att_id');
        $this->addElement($id);

        $id = $this->createElement('hidden', 'att_gallery_count');
        $this->addElement($id);
        
        // att_status  enum('NEW','ACTIVE','TRANSFORMING','BLOCKED','ERROR','DELETED') utf8_polish_ci      Nie NEW
        $element = new Zend_Form_Element_Radio('att_status', array('multiOptions' => array(
                AttachmentMapper::STATUS_NEW => 'att_status_new',
                AttachmentMapper::STATUS_ACTIVE => 'att_status_active',
                AttachmentMapper::STATUS_BLOCKED => 'att_status_blocked',
                AttachmentMapper::STATUS_ERROR => 'att_status_error'
            )));
        $element->setLabel('att_status')
            ->setRequired(true)
            ->setAttrib('class', 'required')
            ->setValidators(array(
                new Zend_Validate_InArray(array(
                    AttachmentMapper::STATUS_NEW,
                    AttachmentMapper::STATUS_ACTIVE,
                    AttachmentMapper::STATUS_BLOCKED,
                    AttachmentMapper::STATUS_ERROR,
                    AttachmentMapper::STATUS_DELETED
                ))));
        $element->setValue(AttachmentMapper::STATUS_NEW);
        $this->addElement($element);
                                     
        // att_type    enum('IMAGE','FLASH','AUDIO','VIDEO')   utf8_polish_ci      Nie None
        $element = new Zend_Form_Element_Radio('att_type', array('multiOptions' => array(
                AttachmentMapper::TYPE_IMAGE => 'att_type_image',
                AttachmentMapper::TYPE_FLASH => 'att_type_flash',
                AttachmentMapper::TYPE_VIDEO => 'att_type_video',
                AttachmentMapper::TYPE_AUDIO => 'att_type_audio',
                AttachmentMapper::TYPE_FILE => 'att_type_file'
            )));
        $element->setLabel('att_type')
            ->setRequired(true)
            ->setAttrib('class', 'required')
            ->setValidators(array(
                new Zend_Validate_InArray(array(
                AttachmentMapper::TYPE_IMAGE,
                AttachmentMapper::TYPE_FLASH,
                AttachmentMapper::TYPE_VIDEO,
                AttachmentMapper::TYPE_AUDIO,
                AttachmentMapper::TYPE_FILE
                ))));
        $element->setValue(AttachmentMapper::STATUS_NEW);
        $this->addElement($element);
        
        // att_source  enum('LOCAL','YOUTUBE') utf8_polish_ci      Nie LOCAL
        $element = new Zend_Form_Element_Radio('att_source', array('multiOptions' => array(
                AttachmentMapper::SOURCE_LOCAL => 'att_source_local',
                AttachmentMapper::SOURCE_YOUTUBE => 'att_source_youtube'
            )));
        $element->setLabel('att_source')
            ->setRequired(true)
            ->setAttrib('class', 'required')
            ->setValidators(array(
                new Zend_Validate_InArray(array(
                    AttachmentMapper::SOURCE_LOCAL,
                    AttachmentMapper::SOURCE_YOUTUBE
                ))));
        $element->setValue(AttachmentMapper::STATUS_NEW);
        $this->addElement($element);
        
        // att_url varchar(255)    utf8_polish_ci      Nie None 
        $element = new Agileo_Form_Twitter_Element_Attachment('att_url');
        $element->setValidators(array(new Zend_Validate_StringLength(1, 255)));
        $element->setFilters(array('StringTrim'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Hidden('att_url_crop');
        $element->setRequired(false);
        $element->setValidators(array(new Zend_Validate_StringLength(0, 20)));
        $element->setFilters(array('StringTrim'));
        $this->addElement($element);
        
        $element = new Agileo_Form_Twitter_Element_Attachment('att_thumb');
        $element->setValidators(array(new Zend_Validate_StringLength(1, 255)));
        $element->setFilters(array('StringTrim'));
        $this->addElement($element);
                                              
        // att_name    varchar(255)    utf8_polish_ci      Nie None
        $element = $this->createElement('hidden', 'att_name');
        $element->setValidators(array(new Zend_Validate_StringLength(1, 255)));
        $element->setFilters(array('StringTrim'));
        $this->addElement($element);
        
        // att_description varchar(255)    utf8_polish_ci      Nie None
        $this->addElement('text', 'att_description', array(
            'label' => 'att_description',
            'required' => false,
            'validators' => array(new Zend_Validate_StringLength(1, 255)),
            'filters' => array('StringTrim')
        ));
        
        
        // att_create_date datetime            Nie None  
        $this->addElement('hidden', 'att_create_date', array(
            'validators' => array(new Agileo_Validate_Datetime()),
            'filters' => array(
                'StringTrim',
                new Agileo_Filter_Datetime()
            )
        ));
        
        // att_filesize    mediumint(9)            Nie 0                               
        $this->addElement('hidden', 'att_filesize', array(
            'validators' => array('Int'),
            'filters' => array(
                'StringTrim',
                new Agileo_Filter_Int()
            )
        ));
        
        // att_mime_type   varchar(255)    utf8_polish_ci      Nie None
        $this->addElement('hidden', 'att_mime_type', array(
            'validators' => array(new Zend_Validate_StringLength(1, 255)),
            'filters' => array(
                'StringTrim',
                new Agileo_Filter_Truncate(array('length'=>255))
            )
        ));
        
        // att_file_width  int(5)          Tak NULL                                
        $this->addElement('hidden', 'att_file_width', array(
            'validators' => array('Int'),
            'filters' => array(
                'StringTrim',
                new Agileo_Filter_Int()
            )
        ));
        
        // att_file_height int(5)          Tak NULL        $this->addElement('hidden', 'att_file_height', array(
            'validators' => array('Int'),
            'filters' => array(
                'StringTrim',
                new Agileo_Filter_Int()
            )
        ));

    }

}
?>
