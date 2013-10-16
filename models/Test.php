<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Test extends Agileo_Object
{

    public function getDataSchema() {
        return array(
            'tst_id' => array(
                'validators' => array('Int')
                ),
            'tst_title' => array(
                'validators' => array('allowEmpty' => true, new Zend_Validate_StringLength(0, 100)),
                'filters' => array('StringTrim')
            )
        );

    }
}
