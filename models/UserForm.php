<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class UserForm extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');

        // create new element
        $id = $this->createElement('hidden', 'usr_id');
        $this->addElement($id);

        //create the form elements
        $usrinname = $this->createElement('text', 'usr_email');
        $usrinname->setLabel('usr_email');
        $usrinname->setRequired('true');
        $usrinname->addFilter('StripTags','StringTrim');
        $usrinname->setAttribs(array('class' => 'required email'));
        $this->addElement($usrinname);

        $password = $this->createElement('password', 'usr_pass');
        $password->setLabel('usr_pass');
        $password->setRequired('true');
        $password->setAttribs(array('class' => 'required'));
        $this->addElement($password);
        
        // usr_status  enum('ACTIVE','BLOCKED','DELETED','NEW')    utf8_general_ci     Nie NEW                             
        $status = $this->createElement('select', 'usr_status');
        $status->setLabel("usr_status");
        $status->addMultiOption(UserMapper::STATUS_NEW, 'user_status_new');
        $status->addMultiOption(UserMapper::STATUS_ACTIVE, 'user_status_active');
        $status->addMultiOption(UserMapper::STATUS_BLOCKED, 'user_status_blocked');
        $this->addElement($status);
        
        // usr_create_date datetime            Nie None      
        $this->addElement('text', 'usr_create_date', array(
            'label' => 'usr_create_date',
            'attribs' => array('class' => 'datetime required', 'readonly' =>'readonly')
        ));

        // usr_update_date datetime            Nie None      
        $this->addElement('hidden', 'usr_update_date', array(
            'label' => 'usr_update_date',
            'attribs' => array('class' => 'datetime required', 'readonly' =>'readonly')
        ));
                               
        // usr_nick    varchar(255)    utf8_general_ci     Nie None 
        //create the form elements
        $usrinname = $this->createElement('text', 'usr_nick');
        $usrinname->setLabel('usr_nick');
        $usrinname->setRequired('true');
        $usrinname->addFilter('StripTags','StringTrim');
        $usrinname->setAttribs(array('class' => 'required'));
        $this->addElement($usrinname);
        
        // usr_name    varchar(255)    utf8_general_ci     Tak NULL                                
        $this->addElement('text', 'usr_name', array(
            'label' => 'usr_name',
            'required' => TRUE,
            'attribs' => array('class' => 'required'),
            'validators' => array(new Zend_Validate_StringLength(1, 255)),
            'filters' => array('StringTrim')
        ));
        
        // usr_surname varchar(100)    utf8_general_ci     Tak NULL          
        $this->addElement('text', 'usr_surname', array(
            'label' => 'usr_surname',
            'required' => TRUE,
            'attribs' => array('class' => 'required'),
            'validators' => array(new Zend_Validate_StringLength(1, 255)),
            'filters' => array('StringTrim')
        ));
        
                                      
        // usr_avatar  varchar(255)    utf8_general_ci     Tak NULL      
        $element = new Agileo_Form_Twitter_Element_Image('usr_avatar', array('resize'=> '100x100'));
        $element->setLabel('usr_avatar');
        $element->addFilter('StringTrim');
        $this->addElement($element);
                                  
        // usr_description text    utf8_general_ci 
        $options = array(
            'theme_advanced_buttons1' => "bold,italic,underline,|,justifyleft,justifycenter,justifyright",
            'theme_advanced_buttons2' => "",
            'theme_advanced_buttons3' => "",
        );        $element = new Agileo_Form_Twitter_Element_RichEditor('usr_description', $options);
        $element
            ->setLabel('usr_description')
            ->setRequired(false)
            ->setFilters(array(new Agileo_Filter_RichEditor(), 'StringTrim'));
        $this->addElement($element);
        
        //usr_geo_address
        $this->addElement('text', 'usr_geo_address', array(
            'label' => 'usr_geo_address',
            'validators' => array(new Zend_Validate_StringLength(1, 255)),
            'required' => FALSE,
            'attribs' => array('class' => ''),
            'filters' => array(
                'StringTrim'
                
            )
        ));     
     
        // usr_geo_lat double          Nie None                                
        $id = $this->createElement('hidden', 'usr_geo_lat');
        $id->setValidators(array(new Agileo_Validate_Double()));
        $id->setFilters(array('StringTrim', new Agileo_Filter_Double()));
        $this->addElement($id);
        
        // usr_geo_lng double          Nie None
        $id = $this->createElement('hidden', 'usr_geo_lng');
        $id->setValidators(array(new Agileo_Validate_Double()));
        $id->setFilters(array('StringTrim', new Agileo_Filter_Double()));
        $this->addElement($id);

                                        
        // usr_geo_zoom    tinyint(3)      UNSIGNED    Nie None                                
        $id = $this->createElement('hidden', 'usr_geo_zoom');
        $id->setValidators(array('Int'));
        $id->setFilters(array('StringTrim', new Agileo_Filter_Int()));
        $this->addElement($id);
        
        
        // usr_update_date datetime            Nie None      
        $this->addElement('hidden', 'usr_soc_update_date', array(
            'label' => 'usr_soc_update_date',
            'attribs' => array('class' => 'datetime required', 'readonly' =>'readonly')
        ));

        $this->addElement('hidden', 'usr_soc_facebook', array(
            'required' => false,
            'validators' => array(new Zend_Validate_StringLength(0, 20)),
            'filters' => array('StringTrim')
        ));
        $this->addElement('hidden', 'usr_soc_googleplus', array(
            'required' => false,
            'validators' => array(new Zend_Validate_StringLength(0, 20)),
            'filters' => array('StringTrim')
        ));
        $this->addElement('hidden', 'usr_soc_tweeter', array(
            'required' => false,
            'validators' => array(new Zend_Validate_StringLength(0, 20)),
            'filters' => array('StringTrim')
        ));
        $this->addElement('hidden', 'usr_soc_linkedin', array(
            'required' => false,
            'validators' => array(new Zend_Validate_StringLength(0, 20)),
            'filters' => array('StringTrim')
        ));

        //usr_role    enum('STANDARD','POLITICIAN')   utf8_general_ci     Nie STANDARD
        $status = $this->createElement('radio', 'usr_role');
        
        $status->setLabel("usr_role");
        $status->addMultiOption(UserMapper::ROLE_STANDARD, 'usr_role_standard');
        $status->addMultiOption(UserMapper::ROLE_POLITICIAN, 'usr_role_politician');
        $status->setValidators(array(
                new Zend_Validate_InArray(array(
                    UserMapper::ROLE_STANDARD,
                    UserMapper::ROLE_POLITICIAN
                ))));
        $this->addElement($status);
                                                                 
        //usr_role_label  varchar(50)
        $this->addElement('text', 'usr_role_label', array(
            'label' => 'usr_role_label',
            'required' => false,
            'validators' => array(new Zend_Validate_StringLength(0, 50)),
            'filters' => array('StringTrim')
        ));
        
        //`usr_role_cfr_id` INT UNSIGNED NULL
        $id = new Zend_Form_Element_Hidden('usr_role_cfr_id');
        $id->setLabel('Urząd');
        $id->setValidators(array('Int'))
            ->setFilters(array(new Agileo_Filter_Int()));
        $this->addElement($id);
        
        
        
        //`usr_contact` TEXT NULL ,
        $options = array(
            'theme_advanced_buttons1' => "bold,italic,underline,|,justifyleft,justifycenter,justifyright",
            'theme_advanced_buttons2' => "",
            'theme_advanced_buttons3' => "",
        );
        $element = new Agileo_Form_Twitter_Element_RichEditor('usr_contact', $options);
        $element
            ->setLabel('usr_contact')
            ->setRequired(false)
            ->setFilters(array(new Agileo_Filter_RichEditor(), 'StringTrim'));
        $this->addElement($element);
        
        //`usr_bg` VARCHAR( 255 ) NULL;
        $element = new Agileo_Form_Twitter_Element_Image('usr_bg', array('resize'=> '500x100'));
        $element->setLabel('usr_bg');
        $element->addFilter('StringTrim');
        $this->addElement($element);
        
    }

}
?>
