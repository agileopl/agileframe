<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Search extends Agileo_Object
{
    
    public function hasOriginalObject()
    {
        return $this->hasSubObject($this->object_name);
    }

    public function getOriginalObject()
    {
        return $this->getSubObject($this->object_name);
    }
    
}