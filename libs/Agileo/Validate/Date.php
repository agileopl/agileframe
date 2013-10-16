<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Validate_Date extends Zend_Validate_Abstract
{

    protected $_messageTemplates = array(
        'notMatch' => "'%value%' nie jest datą w formacie YYYY-MM-DD"
    );

    public function isValid($value)
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value);
    }

}
