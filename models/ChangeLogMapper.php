<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class ChangeLogMapper extends Agileo_Mapper_Db
{
    // @formatter:off
    public static $prefix = 'acg';
    
    public static $tableName = 'ag_changes_log';
    public static $tableFields = array('acg_id', 'acg_object_name', 'acg_object_id', 'acg_available', 'acg_old_data', 'acg_app_name', 'acg_owner_name', 'acg_owner_id');

    public static $fieldsList = array('acg_id', 'acg_object_name', 'acg_object_id', 'acg_available', 'acg_app_name', 'acg_owner_name', 'acg_owner_id');

    // @formatter:on

    public function saveLog(Agileo_Object $object, Agileo_Object $oldObject = null)
    {
        $isAvailable = $object->isAvailable();
        $log = array(
            'acg_object_name' => get_class($object),
            'acg_object_id' => $object->id,
            'acg_old_data' => Zend_Json::encode(!empty($oldObject) ? $oldObject->toArray() : array()),
            'acg_available' => is_null($isAvailable) ? ($oldObject && $oldObject->isAvailable() ? 2 : 0) : ($isAvailable ? 1 : 0),
            'acg_app_name' => defined('APPLICATION_NAME') ? APPLICATION_NAME : 'unknown'
        );
        
        if(Zend_Session::isStarted()) {
            if(Zend_Auth::getInstance()->hasIdentity()) {
                $owner = Zend_Auth::getInstance()->getIdentity();
                $log['acg_owner_name'] = get_class($owner);
                $log['acg_owner_id'] = $owner->id;                                
            }
        }
        
        $changeLog = new ChangeLog($log);
        
        return $this->save($changeLog);

    }
    
    public function save(ChangeLog $changeLog) 
    {
        return $this->_save($changeLog);
    } 

}
