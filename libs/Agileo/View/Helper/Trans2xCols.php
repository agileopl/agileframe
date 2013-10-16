<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Agileo_View_Helper_Trans2xCols extends Zend_View_Helper_Abstract
{

    public function trans2xCols($collection, $maxCols = 3)
    {
        $ret = array();

        $row = 0;
        $col = 0;
        foreach($collection as $item) {
            $ret[$row][$col] = $item;
            $col++;
            if($col%$maxCols == 0) {
                $col = 0;
                $row++;
            }
        }

        return $ret;
    }

}
