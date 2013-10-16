<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Agileo_Form_Twitter extends Twitter_Form
{

    public function __construct($options = null)
    {

        $this->addPrefixPath("Agileo_Form_Twitter_Decorator", "Agileo/Form/Twitter/Decorator/", "decorator");
        $this->addPrefixPath('Agileo_Form_Twitter_Element', 'Agileo/Form/Twitter/Element', 'element');

        if (null !== $this->getView())
        {
            $this->getView()->addHelperPath(
                    'Agileo/Form/Twitter/View/Helper',
                    'Agileo_Form_Twitter_View_Helper'
            );
        }

        parent::__construct($options);
    }

}
