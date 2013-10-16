<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Timedebuger
{

    static $logs = array();
    private $_proxyEntity;

    public function __construct($proxyEntity, $proxyContext = '')
    {
        $this->_proxyEntity = $proxyEntity;
        $this->_proxyContext = $proxyContext;
    }

    public function __call($name, $parameters)
    {
        $pinfo = self::logValue($parameters);

        $key = ($this->_proxyContext ? $this->_proxyContext . ' / ' : '') . get_class($this->_proxyEntity) . '::' . $name . "(" . $pinfo . ")";
        self::start($key);
        
        $ret = call_user_func_array(array($this->_proxyEntity, $name), $parameters);
        self::stop($key, '', true);
        
        return $ret;
    }

    public static function start($name)
    {
        self::$logs[$name]['start'] = self::getmicrotime();
    }

    public static function stop($name, $info = '')
    {
        self::$logs[$name]['time'] = self::getmicrotime() - self::$logs[$name]['start'];
        self::$logs[$name]['info'] = $info;
        self::logger($name);
        
        return self::$logs[$name]['time']; 
    }

    public static function logger($name)
    {
        $logDesc = 'TD: [' . number_format(self::$logs[$name]['time'], 5) . '] ' . $name . " : " . " (" . self::$logs[$name]['info'] . ")";
        $logger = Zend_Registry::get('log');
        $logger->log($logDesc, (self::$logs[$name]['time'] > 1 ? Zend_Log::EMERG : Zend_Log::DEBUG));
    }

    public static function getmicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }

    public static function logValue($value)
    {
        if (!is_string($value)) {
            $value = print_r($value, true);
        }
        return preg_replace('/\s+/', ' ', $value);
    }

}
