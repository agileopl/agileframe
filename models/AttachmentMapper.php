<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class AttachmentMapper extends Agileo_Mapper_Db
{
    // @formatter:off
    public static $prefix = 'att';
    public static $tableName = 'ag_attachments';
    public static $tableFields = array('att_id', 'att_copy_att_id', 'att_adm_id', 'att_usr_id', 'att_cfr_id', 'att_cem_id', 
        'att_gallery_count', 'att_url', 'att_url_crop', 'att_thumb', 'att_name', 'att_description', 'att_create_date', 'att_type', 
        'att_source', 'att_source_id', 'att_filesize', 'att_mime_type', 'att_file_width', 'att_file_height', 'att_status');

    public static $fieldsList = array('att_id', 'att_copy_att_id', 'att_adm_id', 'att_usr_id', 'att_cfr_id', 'att_cem_id', 
        'att_gallery_count', 'att_url', 'att_url_crop', 'att_thumb', 'att_name', 'att_description', 'att_create_date', 'att_type', 
        'att_source', 'att_source_id', 'att_filesize', 'att_mime_type', 'att_file_width', 'att_file_height', 'att_status');
        
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_NEW = 'NEW';
    const STATUS_BLOCKED = 'BLOCKED';
    const STATUS_TRANSFORMING = 'TRANSFORMING';
    const STATUS_ERROR = 'ERROR';
    const STATUS_DELETED = 'DELETED';
    
    const TYPE_IMAGE = 'IMAGE';
    const TYPE_FLASH = 'FLASH';
    const TYPE_AUDIO = 'AUDIO';
    const TYPE_VIDEO = 'VIDEO';
    const TYPE_FILE = 'FILE';
    
    public static $types = array(self::TYPE_AUDIO, self::TYPE_FILE, self::TYPE_FLASH, self::TYPE_IMAGE, self::TYPE_VIDEO);
    
    const SOURCE_LOCAL = 'LOCAL';
    const SOURCE_YOUTUBE = 'YOUTUBE';
    const SOURCE_VENEO = 'VENEO';
    
    // @formatter:on

    public function getPagerForParent($parent, $parentId, $parentGroup = AttachmentParentRelMapper::PARENT_GROUP_DEFAULT, $filter = array(), $page = 1, $limit = 25)
    {
        $select = $this->_getSelectForParent($parent, $parentId, $parentGroup, $filter);

        $cSelect = clone $select;
        $cSelect->reset(Zend_Db_Select::COLUMNS)->reset(Zend_Db_Select::ORDER)->columns(new Zend_Db_Expr('COUNT(*) AS ' . Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN));

        $adapter = new Zend_Paginator_Adapter_DbSelect($select);
        $adapter->setRowCount($cSelect);

        $paginator = new Agileo_Paginator($adapter, 'Attachment');
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($limit);

        return $paginator;
    }

    public function getListForParent($parent, $parentId, $parentGroup = AttachmentParentRelMapper::PARENT_GROUP_DEFAULT, $filter = array(), $limit = 100)
    {
        $select = $this->_getSelectForParent($parent, $parentId, $parentGroup, $filter);
        $select->limit($limit);
        
        return Agileo_Collection::create('Attachment', $this->_db->query($select)->fetchAll());
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


    public function getListPager($filter = array(), $page = 1, $limit = 25)
    {
        $select = $this->_db->select()->from(self::$tableName, self::$fieldsList); ;


        if (!empty($filter['att_description'])) {
            $select->where("att_description like ?", '%'.$filter['att_description'].'%');
        }

        if (!empty($filter['att_type'])) {
            $select->where("att_type = ?", $filter['att_type']);
        }

        if (!empty($filter['adm_id'])) {
            $select->where("att_adm_id = ?", $filter['adm_id']);
        }
        if (!empty($filter['usr_id'])) {
            $select->where("att_usr_id = ?", $filter['usr_id']);
        }
        if (!empty($filter['cfr_id'])) {
            $select->where("att_cfr_id = ?", $filter['cfr_id']);
        }
        if (!empty($filter['cem_id'])) {
            $select->where("att_usr_id = ?", $filter['cem_id']);
        }

        if(empty($filter['adm_id']) && empty($filter['usr_id']) && empty($filter['cfr_id']) && empty($filter['cem_id'])) {
            $select->where("att_adm_id > 0");
        }
        
        if (!empty($filter['att_status'])) {
            $select->where("att_status = ?", $filter['att_status']);
        } else {
            if (!empty($filter['onlyActive'])) {
                $select->where("att_status = ?", self::STATUS_ACTIVE);
            } else {
                $select->where("att_status <> ?", self::STATUS_DELETED);
            }
        }

        if (!empty($filter['order'])) {
            $select->order($filter['order']);
        } else {
            $select->order('att_create_date desc');
        }

        $cSelect = clone $select;
        $cSelect->reset(Zend_Db_Select::COLUMNS)->reset(Zend_Db_Select::ORDER)->columns(new Zend_Db_Expr('COUNT(*) AS ' . Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN));

        $adapter = new Zend_Paginator_Adapter_DbSelect($select);
        $adapter->setRowCount($cSelect);

        $paginator = new Agileo_Paginator($adapter, 'Attachment');
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($limit);

        return $paginator;
    }

    public function save(Attachment $attach, $additionalRestrict = array())
    {

        if (empty($attach->id)) {
            $attach->create_date = date('Y-m-d H:i:s');
        }

        return $this->_save($attach, $additionalRestrict);
    }

    public function delete($id)
    {
        $attach = new Attachment( array(
            'att_id' => $id,
            'att_status' => AttachmentMapper::STATUS_DELETED
        ));
        return $this->_save($attach);
    }


    public function createCopyForAdmin(Attachment $attach, $admId)
    {
        $copy = clone $attach;
        $copy->id = null;
        $copy->usr_id = null;
        $copy->cfr_id = null;
        $copy->cem_id = null;
        $copy->copy_att_id = $attach->id;
        $copy->adm_id = $admId;
        
        if($this->save($copy)) {
            return $copy;
        }
        
        throw new Agileo_Exception('Nie udało się utworzyć kopi');
        
    }

    static public function getTypeForMimeType($mimeType)
    {
        $mimeType = strtolower(trim($mimeType));
        $type = substr($mimeType,0,strpos($mimeType,'/'));
        if($type == 'image') {
            return self::TYPE_IMAGE;
        } elseif ($type == 'audio' || $type == 'x-music') {
            return self::TYPE_AUDIO;
        } elseif ($type == 'video') {
            return self::TYPE_VIDEO;
        } elseif (preg_match('/flash/i', $mimeType)) {
            return self::TYPE_FLASH;
        } else {
            return self::TYPE_FILE;
        }
    }

    public function getTransformedList($source, $limit = 20)
    {
        $select = $this->_db->select()->from(self::$tableName, self::$fieldsList); ;

        $select->where("att_type = ?", self::TYPE_VIDEO);
        $select->where("att_status = ?", self::STATUS_TRANSFORMING);
        $select->where("att_source = ?", $source);
        $select->limit($limit);
        
        return Agileo_Collection::create('Attachment', $this->_db->query($select)->fetchAll());
    }
    
    public function cms_youtubeSaver($youtubeUrl, $admId, $desc = '') {
        
        $data = array(
            'att_adm_id' => $admId,
            'att_description' => $desc
        );
        
        return $this->_youtubeSaver($youtubeUrl, $data);

    }

    public function cms_vvideoSaver($inputFile, $admId, $desc = '') {
        
        $data = array(
            'att_adm_id' => $admId,
            'att_description' => $desc
        );
        
        return $this->_vvideoSaver($inputFile, $data);

    }
    
    public function cums_youtubeSaver($youtubeUrl, $cfrId, $cemId, $desc = '') {
        
        $data = array(
            'att_cfr_id' => $cfrId,
            'att_cem_id' => $cemId,
            'att_description' => $desc
        );
        
        return $this->_youtubeSaver($youtubeUrl, $data);

    }

    public function cums_vvideoSaver($inputFile, $cfrId, $cemId, $desc = '') {
        
        $data = array(
            'att_cfr_id' => $cfrId,
            'att_cem_id' => $cemId,
            'att_description' => $desc
        );
        
        return $this->_vvideoSaver($inputFile, $data);
        
    }
    
    public function web_youtubeSaver($youtubeUrl, $usrId, $desc = '') {
        
        $data = array(
            'att_usr_id' => $usrId,
            'att_description' => $desc
        );
        
        return $this->_youtubeSaver($youtubeUrl, $data);

    }

    public function web_vvideoSaver($inputFile, $usrId, $desc = '') {
        
        $data = array(
            'att_usr_id' => $usrId,
            'att_description' => $desc
        );
        
        return $this->_vvideoSaver($inputFile, $data);
    }

    public function _youtubeSaver($youtubeUrl, $data = array()) {
        
        if(!empty($youtubeUrl)) {
            
            $ytId = T::parseYoutubeId($youtubeUrl);
            if($ytId) {
                
                $data['att_status'] = AttachmentMapper::STATUS_ACTIVE;
                $data['att_type'] = AttachmentMapper::TYPE_VIDEO;
                $data['att_source'] = AttachmentMapper::SOURCE_YOUTUBE;
                $data['att_source_id'] = $ytId;
                $data['att_url'] = $youtubeUrl;
                $data['att_thumb'] = 'http://img.youtube.com/vi/' . $ytId . '/0.jpg';
                $data['att_name'] = $ytId;
                
                $attach = new Attachment($data);
                $this->save($attach);
                
                return $attach;

            } else {
                return -1;
            }
            
        }
        
        return 0;
    }
    
    protected function _vvideoSaver($inputFile, $data = array()) {
        
        if(Veneo_Video_Uploader::checkUploadedFile($inputFile)) {

            $data['att_status'] = AttachmentMapper::STATUS_TRANSFORMING;
            $data['att_type'] = AttachmentMapper::TYPE_VIDEO;
            $data['att_source'] = AttachmentMapper::SOURCE_VENEO;
            $data['att_name'] = $inputFile['name'];
            
            $attach = new Attachment($data);
            $this->save($attach);
            
            if($attach->id) {

                $vId = Veneo_Video_Uploader::moveUploadedFileToFtpFolder($inputFile, $attach->id);

                $uAttach = new Attachment();
                $uAttach->id = $attach->id;
                if(empty($vId) || $vId < 0) {
                    $uAttach->status = AttachmentMapper::STATUS_ERROR;
                    $uAttach->filesize = 0;
                } else {
                    $uAttach->source_id = $vId;
                }
                $this->save($uAttach);
                
                return $this->getById($attach->id);
                
            } else {
                return -1;
            }
            
        }
        
        return 0;        
    }

}
