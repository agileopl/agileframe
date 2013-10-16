<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Form_Twitter_View_Helper_EVideo extends Zend_View_Helper_FormElement
{

    public function eVideo($name, $value = null, $attribs = null)
    {

        $html = '<span '. $this->_htmlAttribs($attribs) .'>';

        if(!empty($value)) {
            $html .= 'Video: ' . $this->view->escape($value) . '';
        }

        $html .= '<button class="btn btn-primary" type="button" onclick="alert(\'TODO\')">Wybierz lub dodaj</button>
                  <input type="hidden" name="'.$name.'" value="' . $this->view->escape($value) . '" />';
        $html .= '</span>';

        return  $html;
    }
}
