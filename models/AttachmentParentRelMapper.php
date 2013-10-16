<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class AttachmentParentRelMapper extends Agileo_Mapper_Db
{
    // @formatter:off
    public static $prefix = 'atp';
    public static $tableName = 'ag_attachments_parents';
    public static $tableFields = array('atp_id', 'atp_att_id', 'atp_parent', 'atp_parent_id', 'atp_parent_group', 'atp_weight', 'atp_create_date', 'atp_description', 'atp_main');

    public static $fieldsList = array('atp_id', 'atp_att_id', 'atp_parent', 'atp_parent_id', 'atp_parent_group', 'atp_weight', 'atp_create_date', 'atp_description', 'atp_main');


    const PARENT_GROUP_DEFAULT = 'default';
    // @formatter:on

    public function setListOrder($parent, $parentId, $parentGroup, $orderList)
    {

        $startTransaction = $this->_db->beginTransaction();
        try {
            $inx = 0;
            foreach ($orderList as $inx => $attId) {
                $data = array();
                if (!$inx) {
                    $data['atp_main'] = 1;
                }
                $data['atp_weight'] = ++$inx;
                $where = array(
                    'atp_parent = ? ' => $parent,
                    'atp_parent_id = ? ' => $parentId,
                    'atp_parent_group = ? ' => $parentGroup,
                    'atp_att_id = ? ' => $attId
                );

                $this->_db->update(self::$tableName, $data, $where);
            }

            $this->_db->commit($startTransaction);
            return true;

        } catch (Exception $e) {
            $this->_db->rollBack($startTransaction);
            throw $e;
        }

    }

    public function setMain($parent, $parentId)
    {
        T::dump($parent);
        T::dump($orderList);
        exit ;
    }

    public function save(AttachemntParentRel $attachRel)
    {

        if (empty($attachRel->id)) {
            $attach->create_date = date('Y-m-d H:i:s');
        }

        return $this->_save($admin);
    }

    /**
     * @param Attachment|Int $attachment
     */
    public function addRelation($attachment, $parent, $parentId, $parentGroup = self::PARENT_GROUP_DEFAULT)
    {
        try {
            $rel = new AttachmentParentRel();

            $rel->att_id = $attachment instanceof Attachment ? $attachment->id : $attachment;
            $rel->parent = $parent;
            $rel->parent_id = $parentId;
            $rel->parent_group = $parentGroup;
            
            $rel->create_date = date('Y-m-d H:i:s');
    
            return $this->_save($rel);                
        } catch (Exception $e) {
            if(strstr($e->getMessage(), 'Duplicate')) {
                return 1;
            } else {
                throw $e;
            }
        }
    }

    public function clearRelation($parent, $parentId, $parentGroup = self::PARENT_GROUP_DEFAULT) {
        $where = array(
                    'atp_parent = ? ' => $parent,
                    'atp_parent_id = ? ' => $parentId,
                    'atp_parent_group = ? ' => $parentGroup,
                );
        return $this->_db->delete(self::$tableName, $where);
    }
    
    public function unsetRelation($attId, $parent, $parentId, $parentGroup = self::PARENT_GROUP_DEFAULT) {
        $where = array(
                    'atp_parent = ? ' => $parent,
                    'atp_parent_id = ? ' => $parentId,
                    'atp_parent_group = ? ' => $parentGroup,
                    'atp_att_id = ? ' => $attId
                );
        return $this->_db->delete(self::$tableName, $where);
    }
    
    public function getListForParentIds($parent, array $parentIds, $parentGroup = null, $onlyActive = true)
    {

        $select = $this->_db->select()->from(self::$tableName, self::$fieldsList);

        $select->join(AttachmentMapper::$tableName, 'att_id = atp_att_id', AttachmentMapper::$fieldsList);

        $select->where("atp_parent = ?", $parent);
        $select->where("atp_parent_id in (?)", $parentIds);
        
        if(!empty($parentGroup)) {
            $select->where("atp_parent_group = ?", $parentGroup);
        }
        
        $result = Agileo_Collection::create('AttachmentParentRel', $this->_db->query($select)->fetchAll());
        
        if(!empty($result) && $onlyActive) {
            foreach($result as $offset => $item) {
                if(!$item->getSubObject('Attachment')->isActive()) {
                    $result->offsetUnset($offset);
                }
            }
        }
                
        return $result;
        
    }

    protected function _getSelectForParent($parent, $parentId, $parentGroup = AttachmentParentRelMapper::PARENT_GROUP_DEFAULT, $filter = array()) 
    {
        $select = $this->_db->select()->from(self::$tableName, self::$fieldsList); ;

        $select->join(AttachmentParentRelMapper::$tableName, 'atp_att_id = att_id', array('atp_parent_group as att_group'));

        $select->where("atp_parent = ?", $parent);
        $select->where("atp_parent_id = ?", $parentId);
        if(!empty($parentGroup)) {
            $select->where("atp_parent_group = ?", $parentGroup);
        }
        
        if (!empty($filter['type'])) {
            $select->where("att_type = ?", $filter['type']);
        }

        if (!empty($filter['onlyActive'])) {
            $select->where("att_status = ?", self::STATUS_ACTIVE);
        } else {
            $select->where("att_status <> ?", self::STATUS_DELETED);
        }

        if (!empty($filter['order'])) {
            $select->order($filter['order']);
        } else {
            $select->order('atp_weight asc');
        }
        return $select;        
    }

}
