<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Agileo_Log_Writer_Translate extends Zend_Log_Writer_Abstract
{

    private $_db;

    private $_table;

    public function __construct($db, $table)
    {
        $this->_db = $db;
        $this->_table = $table;
    }

    static public function factory($config)
    {
        $config = self::_parseConfig($config);
        $config = array_merge(array(
            'db' => null,
            'table' => null
        ), $config);

        return new self($config['db'], $config['table']);
    }

    /**
     * Remove reference to database adapter
     *
     * @return void
     */
    public function shutdown()
    {
        $this->_db = null;
    }

    protected function _write($event)
    {

        if ($this->_db === null) {
            require_once 'Zend/Log/Exception.php';
            throw new Zend_Log_Exception('Database adapter is null');
        }

        $dataToInsert = array(
            'app' => APPLICATION_NAME,
            'key' => $this->_parseKey($event['message']),
            'timestamp' => $event['timestamp'],
            'request_uri' => $_SERVER['REQUEST_URI']
        );

        try {
            $this->_db->insert($this->_table, $dataToInsert);
        } catch (Zend_Db_Statement_Exception $e) {
            // on UNIQUE error, update
            if ($e->getCode() == 23000) {
            } else {
                throw $e;
            }
        }

    }

    protected function _parseKey($message)
    {
        return trim(substr($message, strrpos($message, ':')+1));
    }

}
