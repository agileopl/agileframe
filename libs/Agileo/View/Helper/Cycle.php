<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Agileo_View_Helper_Cycle extends Zend_View_Helper_Abstract
{

    private static $_counter = array();

    public function cycle($id, array $cycles)
    {
        if (!isset(self::$_counter[$id])) {
            self::$_counter[$id] = 0;
        } else {
            self::$_counter[$id]++;
        }

        $cCycles = count($cycles);
        return $cycles[self::$_counter[$id] % $cCycles];
    }

}
