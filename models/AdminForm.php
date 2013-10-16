<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class AdminForm extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');

        // create new element
        $id = $this->createElement('hidden', 'adm_id');
        $this->addElement($id);

        //create the form elements
        $adminname = $this->createElement('text', 'adm_login');
        $adminname->setLabel('adm_login');
        $adminname->setRequired('true');
        $adminname->addFilter('StripTags','StringTrim');
        $adminname->setAttribs(array('class' => 'required'));
        $this->addElement($adminname);

        $password = $this->createElement('password', 'adm_pass');
        $password->setLabel('adm_pass');
        $password->setRequired('true');
        $password->setAttribs(array('class' => 'required'));
        $this->addElement($password);

        $this->addElement('text', 'adm_name', array(
            'label' => 'adm_name',
            'required' => TRUE,
            'attribs' => array('class' => 'required'),
            'validators' => array(new Zend_Validate_StringLength(1, 255)),
            'filters' => array('StringTrim')
        ));
        $this->addElement('text', 'adm_surname', array(
            'label' => 'adm_surname',
            'required' => TRUE,
            'attribs' => array('class' => 'required'),
            'validators' => array(new Zend_Validate_StringLength(1, 255)),
            'filters' => array('StringTrim')
        ));

        $this->addElement('text', 'adm_email', array(
            'label' => 'adm_email',
            'required' => TRUE,
            'attribs' => array('class' => 'required email'),
            'filters' => array('StringTrim')
        ));

        $this->addElement('text', 'adm_create_date', array(
            'label' => 'adm_create_date',
            'attribs' => array('class' => 'datetime required', 'readonly' =>'readonly')
        ));

        $role = $this->createElement('select', 'adm_status');
        $role->setLabel("adm_status");
        $role->addMultiOption(AdminMapper::STATUS_ACTIVE, 'adm_status_active');
        $role->addMultiOption(AdminMapper::STATUS_BLOCKED, 'adm_status_blocked');
        $this->addElement($role);

        $role = $this->createElement('select', 'adm_role');
        $role->setLabel("adm_role");
        $role->addMultiOption(AdminMapper::ROLE_ADMIN, 'adm_role_admin');
        $role->addMultiOption(AdminMapper::ROLE_USER, 'adm_role_user');
        $this->addElement($role);
        
        //adm_avatar  varchar(255)    utf8_general_ci     Tak NULL
        $element = new Agileo_Form_Twitter_Element_Image('adm_avatar', array('resize'=> '100x100'));
        $element->setLabel('adm_avatar');
        $element->addFilter('StringTrim');
        $this->addElement($element);
        
        //adm_cms_bg  varchar(255)    utf8_general_ci     Tak NULL
        $element = new Agileo_Form_Twitter_Element_Image('adm_cms_bg');
        $element->setLabel('adm_cms_bg');
        $element->addFilter('StringTrim');
        $this->addElement($element);

    }

}
?>
