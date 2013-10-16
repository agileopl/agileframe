<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
abstract class Agileo_Mapper_Db extends Agileo_Mapper
{

    public static $tableName = null;
    public static $tableFields = null;

    protected $_db = null;

    private static $_startTransaction = false;

    protected function __construct($context)
    {
        $this->_db = Zend_Registry::get('multidb')->getDb($context);

        if(is_null(static::$tableName)) {
            throw new Agileo_Exception('Table name in mapper is not configured');
        }

        if(!isset(static::$tableFields) || is_null(static::$tableFields)) {
            throw new Agileo_Exception('Table fields in mapper is not configured');
        }

    }

    public function getDb()
    {
        return $this->_db;
    }

    public function getById($id, $additionalRestrict = array(), $columns = '*')
    {
        if (is_null($id)) {
            return array();
        }


        $select = $this->getDb()->select()->from(static::$tableName, $columns)->where(static::$prefix . '_id = ?', $id);
        
        foreach($additionalRestrict as $w => $v) {
            $select->where($w, $v);
        }
        
        $data = $this->getDb()->query($select)->fetch();

        $objectName = $this->getObjectName();
        
        if(!empty($data)) {
            return new $objectName($data);
        }
        return NULL;
    }

    public function getByIds($ids, $additionalRestrict = array(), $columns = '*')
    {
        if (empty($ids) || !is_array($ids)) {
            return Agileo_Collection::create($this->getObjectName(), array());
        }

        $select = $this->getDb()->select()->from(static::$tableName, $columns)->where(static::$prefix . '_id IN (?)', $ids);
        foreach($additionalRestrict as $w => $v) {
            $select->where($w, $v);
        }

        $data = $this->getDb()->query($select)->fetchAll();

        return Agileo_Collection::create($this->getObjectName(), $data);

    }

    protected function _save(Agileo_Object $object, $additionalRestrict = array())
    {

        $props = $object->toArray();
        $data = array();
        foreach (static::$tableFields as $field) {
            if (array_key_exists($field, $props)) {
                $data[$field] = $props[$field];
            }
        }

        $oldObject = null;

        $ret = null;
        if (!empty($object->id)) {
            
            if($object instanceof ChangeLogInterface) {
                $cols = array_merge(array_keys($data), isset(static::$fieldsChangeLog) ? static::$fieldsChangeLog : array());
                $oldObject = $this->getById($object->id, array(), $cols);
            }
            
            $where = array((!empty(static::$prefix) ? static::$prefix . '_' : '') . 'id' . ' = ? ' => $object->getId());
            if(!empty($additionalRestrict)) {
                $where = array_merge($where, $additionalRestrict);
            }
            $ret = $this->getDb()->update(static::$tableName, $data, $where);
        } else {
            $this->getDb()->insert(static::$tableName, $data);
            $id = $this->getDb()->lastInsertId(static::$tableName);
            $object->id = $id;
            $ret = $id > 0 ? $id : 0;
        }

        if($ret) {

            if($object instanceof ChangeLogInterface) {
                ChangeLogMapper::getMasterInstance()->saveLog($object, $oldObject);
            }

            // refresh relations
            if($object instanceof PublicationInterface) {
             
                if(!$object->areRequiredFields()) {
                    $mapperName = get_class($object) . 'Mapper';
                    $mapper = $mapperName::getMasterInstance();
                    $object = $mapper->getById($object->id);
                }

                // refresh is_public
                $data = array(
                    static::$prefix . '_is_public' => $object->isAvailable() ? 1 : 0 
                );
                $where = array((!empty(static::$prefix) ? static::$prefix . '_' : '') . 'id' . ' = ? ' => $object->getId());
                $this->getDb()->update(static::$tableName, $data, $where);
                
                PublicationMapper::getMasterInstance()->saveLog($object);

                if($object instanceof SearchInterface) {
                    SearchMapper::getMasterInstance()->refreshDocument($object);
                }
                
            }

        }

        
        return $ret; 

    }

    protected function _delete(Agileo_Object $object, $additionalRestrict = array())
    {
        if (!empty($object->id)) {
            $where = array((!empty(static::$prefix) ? static::$prefix . '_' : '') . 'id' . ' = ? ' => $object->getId());
            if(!empty($additionalRestrict)) {
                $where = array_merge($where, $additionalRestrict);
            }
            return $this->getDb()->delete(static::$tableName, $where);
        }
        return false;
    }

    public function beginTransaction() {
        if(!self::$_startTransaction) {
            self::$_startTransaction = true;
            $this->_db->beginTransaction();
            return true;
        }
        return false;
    }

    public function commit($isStarted) {
        if($isStarted) {
            $this->_db->commit();
            self::$_startTransaction = false;
        }
    }

    public function rollBack($isStarted){
        if($isStarted) {
            $this->_db->rollBack();
            self::$_startTransaction = false;
        }
    }

}
