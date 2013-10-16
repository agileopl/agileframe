<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class EditorEmbedMapper extends Agileo_Mapper_Db
{
    // @formatter:off
    public static $prefix = 'aee';
    public static $tableName = 'ag_editors_embeds';
    public static $tableFields = array('aee_id', 'aee_parent', 'aee_parent_id', 'aee_field_name', 'aee_position', 'aee_editor_embed', 
        'aee_object_name', 'aee_object_id', 'aee_type', 'aee_class', 'aee_href', 'aee_align');

    public static $fieldsList = array('aee_id', 'aee_parent', 'aee_parent_id', 'aee_field_name', 'aee_position', 'aee_editor_embed', 
        'aee_object_name', 'aee_object_id', 'aee_type', 'aee_class', 'aee_href', 'aee_align');

    const TYPE_USER_IMAGE = 'uatt-video';
    const TYPE_USER_VIDEO = 'uatt-image';
    const TYPE_USER_FILE = 'uatt-file';
    const TYPE_USER_QUIZ = 'uatt-quiz';
    const TYPE_USER_ARTICLE = 'uatt-article';
    const TYPE_CUSTOMER_ARTICLE = 'uatt-cu-article';
    
    public static $typesObjectMap = array(
        self::TYPE_USER_VIDEO => 'Attachment',
        self::TYPE_USER_IMAGE => 'Attachment',
        self::TYPE_USER_FILE => 'Attachment'
    );
                        
    // @formatter:on

    public function save(EditorEmbed $object)
    {
        return $this->_save($object);
    }
    
    public function clearObjectFieldEmbeds(Agileo_Object $object, $fieldName)
    {
        if (!empty($object->id)) {
            $where = array(
                'aee_parent = ?' => get_class($object),
                'aee_parent_id = ?' => $object->id,
                'aee_field_name = ?' => $fieldName
            );
            return $this->getDb()->delete(self::$tableName, $where);
        }
        return false;
    }
    
    public function getObjectFieldEmbeds(Agileo_Object $object, $fieldName)
    {
        
        $select = $this->_db->select()->from(self::$tableName, self::$fieldsList); ;

        $select->where('aee_parent = ?', get_class($object))
            ->where('aee_parent_id = ?', $object->id)
            ->where('aee_field_name = ?', $fieldName)
        ;
        $select->order('aee_position desc');
        
        return Agileo_Collection::create('EditorEmbed', $this->_db->query($select)->fetchAll());
        
    }

    public function getEmbedsInObject(Agileo_Object $object)
    {
        
        $select = $this->_db->select()->from(self::$tableName, self::$fieldsList); ;

        $select->where('aee_object_name = ?', get_class($object))
            ->where('aee_object_id = ?', $object->id)
        ;
        
        return Agileo_Collection::create('EditorEmbed', $this->_db->query($select)->fetchAll());
        
    }

    public function prepareEmbeds(Agileo_Object $object, $fieldName, $leadFieldName = null) 
    {
        
        if(isset($object->{$fieldName})) {
            $object->att_id = null;
            $object->att_thumb = null;
            $object->att_has_photos = null;
            $object->att_has_videos = null;
       
            $object->{$fieldName} = $this->saveEmbeds($object, $fieldName, $leadFieldName);
            
            // sprawdz czy nie ma zdjęcia w description i ustaw jako thumb
            if($object->hasSubCollection('EditorEmbed')) {
                foreach($object->getSubCollection('EditorEmbed') as $embed) {
                    if($embed->type == 'uatt-video') {
                        $object->att_has_videos = !empty($object->att_has_videos) ? $object->att_has_videos+1 : 1;
                    } elseif($embed->object_name == 'Attachment' && $embed->type == 'uatt-image') {
                        if(empty($object->att_id)) {
                            if($attach = AttachmentMapper::getMasterInstance()->getById($embed->object_id)) {
                                $object->att_id = $attach->id;
                                $object->att_thumb = $attach->url;
                            }
                        }
                        $object->att_has_photos = !empty($object->att_has_photos) ? $object->att_has_photos+1 : 1;
                    }
                }
            }
            
            if(!empty($leadFieldName)) {
                $filter = new Agileo_Filter_Truncate(array('length' => 300));
                $object->lead = $filter->filter(strip_tags($object->description));
            }
        }

    }
    
    public function saveEmbeds (Agileo_Object $object, $fieldName, $leadFieldName = null)
    {
        $description = null;
        if(isset($object->{$fieldName})) {
            
            $description = $object->{$fieldName};
            
            // video
            // <a class="att-youtube" href="http://www.youtube.com/watch?v=CLm2j7JUeyE" data-type="uatt-video" data-id="228"><img style="height:315px;" src="http://img.youtube.com/vi/CLm2j7JUeyE/0.jpg" alt=""></a>
            
            // galeria
            //<a class="att-gallery" href="http://mds.agifrem.local/p/r,800,600/f/a/h54/0be/32c/img-3839.jpg" data-type="uatt-image" data-id="211" data-align="gallery"><img src="http://mds.agifrem.local/p/r,480,360/f/a/h54/0be/32c/img-3839.jpg" alt=""></a>
    
            // zdjęcie
            //<a class="att-left" href="http://mds.agifrem.local/p/r,800,600/f/a/h54/0be/31a/img-4798.jpg" data-type="uatt-image" data-id="229" data-align="left"><img src="http://mds.agifrem.local/p/r,200,200/f/a/h54/0be/31a/img-4798.jpg" alt=""></a>
                 
            $parent = get_class($object);
            $parentId = $object->id;
            
            if(preg_match_all('@<a class="([^"]*)" href="([^"]*)" data-type="([^"]+)" data-id="([^"]+)"(?: data-align="([^"]+)")?>(.*)</a>@iU', $description, $matches, PREG_OFFSET_CAPTURE)) {

                $list = array();
                foreach($matches[0] as $inx => $match) {
                    
                    if(!empty(self::$typesObjectMap[$matches[3][$inx][0]])) {
                        $data = array(
                            'aee_parent' => $parent,                                
                            'aee_parent_id' => $parentId,                                
                            'aee_field_name' => $fieldName,
        
                            'aee_editor_embed' => $matches[0][$inx][0],
                            'aee_object_name' => self::$typesObjectMap[$matches[3][$inx][0]],
                            'aee_object_id' => $matches[4][$inx][0],
                            'aee_type' => $matches[3][$inx][0],
                            'aee_class' => $matches[1][$inx][0],
                            'aee_href' => $matches[2][$inx][0],
                            'aee_align' => $matches[5][$inx][0],
        
                            'aee_hpos' => $matches[0][$inx][1],
                            'aee_hlength' => mb_strlen($matches[0][$inx][0])
                        );
                        
                        $list[] = new EditorEmbed($data);
                        
                    }
                    
                }
                                
                $shiftPos = 0;
                            
                foreach($list as $inx => $item) {
                        
                    $startPos = $item->hpos - $shiftPos;
                    $endPos = $startPos + $item->hlength;
                    // check <p>
                    if(mb_substr($description, $startPos-3, 3) == '<p>' && mb_substr($description, $endPos, 4) == '</p>') {
                        $item->editor_embed = '<p>'.$item->editor_embed.'</p>';
                        $startPos -= 3;
                        $endPos += 4;
                    }
                    $item->position = $startPos;
                    
                    $shiftPos += ($endPos - $startPos);
                    
                    $description = mb_substr($description, 0, $startPos) . mb_substr($description, $endPos);
                    
                }
                
                //save
                $startTransaction = $this->beginTransaction();
                try {
                    
                    $this->clearObjectFieldEmbeds($object, $fieldName);
                    
                    foreach($list as $item) {
                        $this->save($item);
                        $object->addSubCollectionObject($item);
                    }
                    
                    // save to object ...
                    $this->commit($startTransaction);
    
                } catch (Exception $e) {
                    $this->rollBack($startTransaction);
                    throw $e;
                }
                
                
            }
        }
        return $description;
    }

    public function restoreEmbeds(Agileo_Object $object, $fieldName)
    {
        
        $embeds = $this->getObjectFieldEmbeds($object, $fieldName);

        $description = $object->{$fieldName};

        foreach($embeds as $item) {
                
            $description = mb_substr($description, 0, $item->position) . $item->editor_embed . mb_substr($description, $item->position);
            
        }
        
        $object->{$fieldName} = $description;
        
    }

    public function restoreEmbedsForCollection(Agileo_Collection $collection, $fieldName)
    {
        
        $select = $this->_db->select()->from(self::$tableName, self::$fieldsList); ;

        $select->where('aee_parent = ?', $collection->getEntityClass())
            ->where('aee_parent_id in (?)', $collection->getAllEntityIds())
            ->where('aee_field_name = ?', $fieldName)
        ;
        $select->order('aee_position desc');
        
        $embeds = Agileo_Collection::create('EditorEmbed', $this->_db->query($select)->fetchAll());
        $embedsByParentId = $embeds->groupBy('parent_id', true);
        
        foreach($collection as $object) {
            
            if(!empty($embedsByParentId[$object->id])) {
                $description = $object->{$fieldName};
                
                foreach($embedsByParentId[$object->id] as $item) {
                    $description = mb_substr($description, 0, $item->position) . $item->editor_embed . mb_substr($description, $item->position);
                }
                
                $object->{$fieldName} = $description;
                
            }
        }
        
    }

    public function setEmbedsForCollection(Agileo_Collection $collection, $fieldName)
    {
        
        $select = $this->_db->select()->from(self::$tableName, self::$fieldsList); ;

        $select->where('aee_parent = ?', $collection->getEntityClass())
            ->where('aee_parent_id in (?)', $collection->getAllEntityIds())
            ->where('aee_field_name = ?', $fieldName)
        ;
        $select->order('aee_position desc');
        
        $embeds = Agileo_Collection::create('EditorEmbed', $this->_db->query($select)->fetchAll());
        
        $embedsByParentId = $embeds->groupBy('parent_id', true);
        
        foreach($collection as $object) {
            if(!empty($embedsByParentId[$object->id])) {
                $object->setSubCollection(Agileo_Collection::create('EditorEmbed', $embedsByParentId[$object->id])); 
            }
        }
        
    }

    public function setEmbedsWithSubObjectForCollection(Agileo_Collection $collection, $fieldName)
    {
        $this->setEmbedsForCollection($collection, $fieldName);
        $embeds = array();
        foreach($collection as $object) {
            
            if($object->hasSubCollection('EditorEmbed')) {
                foreach($object->getSubCollection('EditorEmbed') as $embed) {
                    $embeds[] = $embed;
                }
            }
        }
        if(!empty($embeds)) {
            $this->setSubObjectForEmbeds($embeds);
        }
    }

    public function setEmbedsWithSubObjectForObject(Agileo_Object $object, $fieldName) 
    {
        if($embeds = EditorEmbedMapper::getInstance($this->context)->getObjectFieldEmbeds($object, $fieldName)) {
            $this->setSubObjectForEmbeds($embeds);
            $object->setSubCollection($embeds);
        }
        
    }
    
    public function setSubObjectForEmbeds($embeds)
    {
        $objectsByIds = array();
        foreach($embeds as $item) {
            $objectsByIds[$item->object_name][$item->object_id] = 1;
        }

        // dociąg dane dla obiektów
        
        // Attachments
        if(!empty($objectsByIds['Attachment'])) {
            $objectsByIds['Attachment'] = $this->_getAttachmentsByIds(array_keys($objectsByIds['Attachment']));
        }
        
        
        // reszta todo

        foreach($embeds as $item) {
            if(!empty($objectsByIds[$item->object_name][$item->object_id]) && $objectsByIds[$item->object_name][$item->object_id] instanceof Agileo_Object) {
                $item->setSubObject($objectsByIds[$item->object_name][$item->object_id]);
            }
        }
            
    }

    /**
     * return array Attachments by id
     */
    protected function _getAttachmentsByIds($attIds)
    {
        $relAttachments = AttachmentMapper::getSlaveInstance()->getByIds($attIds);
        $attachByIds = $relAttachments->groupBy('id');
        
        // dociąg glerie
        $getGalleryRel = array();
        foreach($attachByIds as $attach) {
            if($attach->isImage() && !empty($attach->gallery_count)) {
                $getGalleryRel[] = $attach->id;
            }
        }
        if(!empty($getGalleryRel)) {
            $galAtts = AttachmentParentRelMapper::getSlaveInstance()->getListForParentIds('Attachment', $getGalleryRel);
            foreach($galAtts as $rel) {
                if(!empty($attachByIds[$rel->parent_id])) {
                    $attachByIds[$rel->parent_id]->addSubCollectionObject($rel->getSubObject('Attachment'));
                }
            }
        }
        
        return $attachByIds;
    }
    
    
    
}
