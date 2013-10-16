<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Form_TestSearch extends Agileo_Form_Twitter_Search
{

    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');

        $this->addElement('text', 'q', array(
            'label' => 'test_search'
            // ,
            // 'validators' => Paragono_ScanValidator::getValidatorSchema('title')
        ));

        $this->addElement('text', 'end_date_from', array(
            'label' => 'test_end_date_from',
            //'validators' => Paragono_ScanValidator::getValidatorSchema('warranty_end_date'),
            'attribs' => array('class' => 'datetime')
        ));

        $this->addElement('text', 'end_date_to', array(
            'label' => 'test_end_date_to',
            //'validators' => Paragono_ScanValidator::getValidatorSchema('warranty_end_date'),
            'attribs' => array('class' => 'datetime')
        ));

        parent::init();

    }

}
