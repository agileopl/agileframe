<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Paginator extends Zend_Paginator
{

    protected $_objectName = null;

    public function __construct($adapter, $objectName)
    {
        $this->_objectName = $objectName;
        parent::__construct($adapter);
    }

    public function getItemsByPage($page)
    {
        $items = parent::getItemsByPage($page);
        return Agileo_Collection::create($this->_objectName, (array) $items);
    }

}