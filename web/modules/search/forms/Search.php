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
        // Set the method for the display form to POST
        $this->setMethod('get');

        $this->addElement('text', 'q', array(
            'label' => '',
            'placeholder' => 'Szukaj...',
            'validators' => array(new Zend_Validate_StringLength(1, 255)),
            'filters' => array('StringTrim')
        ));
        
        parent::init();
        
    }
    
}