<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

include realpath(dirname(__FILE__)) . '/../../common.inc.php';

if (Semaphor::check()) {

    try {
        StatMapper::getMasterInstance()->updateStatTotal();
    } catch (Exception $e) {
        Zend_Registry::get('log')->err($e);
    }

    Semaphor::remove();
}
