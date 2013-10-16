<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Form_UserSearch extends Agileo_Form_Twitter_Search
{

    public function init()
    {
        

        $this->addElement('text', 'search', array(
            'label' => '',
            'placeholder' => $this->getView()->translate('search'),
            'required' => false,
            'filters' => array('StringTrim')
        ));
        
        $element = new Zend_Form_Element_Select('usr_status', array('multiOptions' => array(
                '' => 'user_status_all',
                UserMapper::STATUS_NEW => 'user_status_new',
                UserMapper::STATUS_ACTIVE => 'user_status_active',
                UserMapper::STATUS_BLOCKED => 'user_status_blocked'
            )));
        $element->setLabel('usr_status')
            ->setRequired(false)
            ->setAttrib('class', 'input-medium')
            ->setValidators(array(new Zend_Validate_InArray( array(
                '',
                UserMapper::STATUS_NEW,
                UserMapper::STATUS_ACTIVE,
                UserMapper::STATUS_BLOCKED
            ))));
        $this->addElement($element);
        

        $this->_addFieldElementDate('usr_public_date', $this->getView()->translate('user_create_date'));

        $this->addElement('hidden', 'page', array('value' => '1'));

        $this->addElement('select', 'limit', array(
            'label' => $this->getView()->translate('search_limit_items'),
            'value' => 10,
            'class' => 'limit input-mini',
            'multiOptions' => array(
                10 => 10,
                20 => 20,
                50 => 50,
                1000 => 1000
            ),
        ));

        parent::init();
    }

    protected function _addFieldElementDate($fieldName, $label)
    {

        $element = new Zend_Form_Element_Text($fieldName . '_from');
        $element->setLabel('');
        $element->setAttrib('placeholder', $label . ' ' . $this->getView()->translate('search_from'));
        $element->setAttrib('class', $element->getAttrib('class').' datetime');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text($fieldName . '_to');
        $element->setAttrib('placeholder', $this->getView()->translate('search_to'));
        $element->setAttrib('class', $element->getAttrib('class').' datetime');
        $this->addElement($element);

    }


}
