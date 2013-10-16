<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    All rights reserved
 */

class PublicationMapper extends Agileo_Mapper_Db
{
    // @formatter:off
    public static $prefix = 'pub';
    
    public static $tableName = 'ag_publication_log';
    public static $tableFields = array('pub_id', 'pub_object_name', 'pub_object_id','pub_owner_name', 'pub_owner_id','pub_available','pub_public_date','pub_public_end_date', 'pub_check_date', 'pub_done_date', 'pub_done_status');

    public static $fieldsList = array('pub_id', 'pub_object_name', 'pub_object_id','pub_owner_name', 'pub_owner_id','pub_available','pub_public_date','pub_public_end_date', 'pub_check_date');

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_DRAFT = 'DRAFT';
    
    const DONE_STATUS_DONE = 'DONE';
    const DONE_STATUS_CANCELED = 'CANCELED';
    const DONE_STATUS_LOG = 'LOG';
    
    // @formatter:on

    public function getObjectLog($objName, $objId)
    {
        $select = $this->_db->select()
                        ->from(self::$tableName)
                        ->where('pub_object_name = ?', $objName)
                        ->where('pub_object_id = ?', $objId);

        if($data = $this->_db->query($select)->fetch()) {
            return new Publication($data);
        }

        return null;
    }

    public function refreshRelatedObjects (Agileo_Object $object)
    {

        if($object->isRequiredRefreshRelated()) {
            $mapperName = get_class($object) . 'Mapper';
            $mapper = $mapperName::getMasterInstance();
            $object = $mapper->getById($object->id);
        }
        
        // if($object instanceof TagInterface) {
            // TagParentMapper::getMasterInstance()->refreshTags($object);
        // }
        
    }
    
    public function saveLog(Agileo_Object $object)
    {
        
        $startTransaction = $this->beginTransaction();
        try {
            
            $log = new Publication(array(
                'pub_object_name' => get_class($object),
                'pub_object_id' => $object->id
            ));
            
            if(!empty($object->usr_id)) {
                $log->owner_name = 'User';
                $log->owner_id = $object->usr_id;
            } elseif(!empty($object->adm_id)) {
                $log->owner_name = 'Admin';
                $log->owner_id = $object->adm_id;
            }

            $isAvailable = $object->isAvailable();      
            if(!is_null($isAvailable) && (
                    (!$isAvailable && !empty($object->public_date)) 
                    || ($isAvailable && !empty($object->public_end_date))
                )
            ) {
                // set cancel earlier logs
                $this->cancelObjectLog(get_class($object), $object->id);
            } else {
                // set log as LOG
                $log->done_status = self::DONE_STATUS_LOG;
                $log->done_date = date('Y-m-d H:i:s');
            }
            
            
            $log->available = $object->isAvailable() ? self::STATUS_ACTIVE : self::STATUS_DRAFT;
            
            if($object->hasPublicDate()) {
                $log->public_date = $object->getPublicDate();
            }
            if($object->hasPublicEndDate()) {
                $log->public_end_date = $object->getPublicEndDate();
            }
            
            if(strtotime($log->public_date) > time()) {
                $log->check_date = $log->public_date;
            } elseif(!empty($log->public_end_date) && strtotime($log->public_end_date) > time()) {
                $log->check_date = $log->public_end_date;
            } else {
                $log->check_date = date('Y-m-d H:i:s');
            }
            $ret = $this->save($log);

            // add to user activities
            if($ret && !empty($object->usr_id) && $object->isAvailable()) {

                UserActivityMapper::getMasterInstance()->addLog(new UserActivity(array(
                    'uac_usr_id' => $object->usr_id,
                    'uac_type' => UserActivityMapper::ACTIVITY_TYPE_CREATE, 
                    'uac_object_name' => get_class($object), 
                    'uac_object_id' => $object->id
                )));
                
                // refresh usr_last_activity
                $where = array('usr_id = ?' => $object->usr_id);
                $this->getDb()->update(UserMapper::$tableName, array('usr_last_activity' => date('Y-m-d H:i:s')), $where); 
                
            }
            
            $this->commit($startTransaction);
            
            return $ret;
            
        } catch (Exception $e) {
            $this->rollBack($startTransaction);
            throw $e;
        }
        
        return -1;
    }
    
    public function cancelObjectLog($objName, $objId) 
    {

        $where = array('pub_object_name = ?' => $objName, 'pub_object_id = ?' => $objId);
        $data = array(
            'pub_done_status' => self::DONE_STATUS_CANCELED,
            'pub_done_date' => date('Y-m-d H:i:s')
        );
        return $this->getDb()->update(self::$tableName, $data, $where);
    }
    
    public function save(Publication $log) 
    {
        return $this->_save($log);
    } 

}
