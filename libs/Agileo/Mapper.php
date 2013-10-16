<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
abstract class Agileo_Mapper
{

    const CONTEXT_MASTER = 'master';
    const CONTEXT_SLAVE = 'slave';

    public static $prefix = '';

    public $context = '';

    protected static $_instance = null;

    abstract protected function __construct($context);

    public static function getInstance($context)
    {
        $object = new static($context);
        $object->context = $context;
        
        $className = get_class($object);

        $contextToken = $className.'_'.$context;

        if (!isset(self::$_instance[$contextToken])) {
            self::$_instance[$contextToken] = $object;
        }

        $instance = self::$_instance[$contextToken];
        
        $config = Zend_Registry::get('config');
        if (!empty($config->globals->timedebuger)) {
            $instance = new Agileo_Timedebuger($instance, 'MAP__'.$contextToken);
        }

        return $instance;

    }
    
    public static function getMasterInstance()
    {
        return self::getInstance(self::CONTEXT_MASTER);
    }
    
    public static function getSlaveInstance()
    {
        return self::getInstance(self::CONTEXT_SLAVE);
    }

    public function getObjectName()
    {
        return substr(get_class($this), 0, -strlen('Mapper'));
    }

}
