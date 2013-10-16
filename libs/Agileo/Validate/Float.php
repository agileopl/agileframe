<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Validate_Float extends Zend_Validate_Abstract
{

    protected $_messageTemplates = array(
        'notMatch' => "'%value%' nie jest float'em"
    );

    public function isValid($value)
    {
        return is_float($value);
    }

}
