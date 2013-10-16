<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Form_Twitter_Search extends Agileo_Form_Twitter
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->setAttrib("inline", true);

    }

    protected function _getElementDecorators()
    {
        return array(
            "ViewHelper",
            array(
                "Errors",
                array("placement" => "append")
            ),
            array(
                "Description",
                array(
                    "tag" => "p",
                    "class" => "help-block"
                )
            )
        );
    }

    protected function _setRadioCheckboxDecorators (Zend_Form_Element $element) {
        $element->setDecorators(array(
                "ViewHelper",
                array("Errors", array("placement" => "append")),
                array("Description", array("tag" => "span", "class" => "help-block")),
                array("Label", array("class" => "control-label"))
            ));
    }

    protected function _setWithLabelDecorators (Zend_Form_Element $element) {
        $element->setDecorators(array(
                "ViewHelper",
                array("Errors", array("placement" => "append")),
                array("Description", array("tag" => "span", "class" => "help-block")),
                array("Label", array("class" => "control-label"))
            ));
    }

    public function init()
    {

        $this->setMethod(self::METHOD_GET);

        foreach($this->getElements() as $element) {

            if($element instanceof Zend_Form_Element_MultiCheckbox || $element instanceof Zend_Form_Element_Checkbox || $element instanceof Zend_Form_Element_Radio) {
                $this->_setRadioCheckboxDecorators ($element);
            }

            if($element instanceof Zend_Form_Element_Select) {
                $this->_setWithLabelDecorators ($element);
            }

        }

        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => 'search',
            'icon' => 'ok',
            'whiteIcon' => true,
            'iconPosition' => Twitter_Form_Element_Button::ICON_POSITION_RIGHT,
            'buttonType' => Twitter_Form_Element_Submit::BUTTON_INFO
        ));

    }

}
