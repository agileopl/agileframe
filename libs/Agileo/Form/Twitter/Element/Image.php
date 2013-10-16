<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Form_Twitter_Element_Image extends Zend_Form_Element_Xhtml
{
    public $helper = 'eImage';
    
    public static $defOptions = array(
        'chooser' => false,
        'crop' => false
    );
    
    public function __construct($spec, $options = null)
    {
        
        $options = array_merge(self::$defOptions, (array) $options);
        
        parent::__construct($spec, $options);

    }
    
}
