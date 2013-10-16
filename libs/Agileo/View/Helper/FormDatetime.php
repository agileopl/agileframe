<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_View_Helper_FormDatetime extends Zend_View_Helper_Abstract
{

    public function formDatetime($date)
    {
        if(empty($date) || $date == '0000-00-00 00:00:00') {
            return '';
        }
        return date('Y-m-d H:i',strtotime($date));
    }

}