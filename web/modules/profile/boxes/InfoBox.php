<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Profile_InfoBox extends Agileo_Box
{

    protected $_params = array();

    public function render()
    {
        return $this->_view->render('infoBox.phtml');
    }
}
