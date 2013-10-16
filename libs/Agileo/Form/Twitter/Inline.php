<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Form_Twitter_Inline extends Agileo_Form_Twitter {

    public function __construct($options = null)
    {

        $this->setAttrib("inline", true);

        parent::__construct($options);
    }

}
