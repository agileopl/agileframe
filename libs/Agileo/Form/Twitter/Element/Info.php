<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Form_Twitter_Element_Info extends Zend_Form_Element_Text
{

    public function __construct($spec, $options = null)
    {
        parent::__construct($spec, $options);

        $this->setAttrib('readonly', 'readonly');

        $this->setAttrib('class', $this->getAttrib('class') . ' info');

    }

}
