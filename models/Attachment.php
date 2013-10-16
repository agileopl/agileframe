<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Attachment extends Agileo_Object
{

    public function isAvailable()
    {
        return $this->isActive();
    }
    
    public function isActive()
    {
        return !empty($this->status) && $this->status == AttachmentMapper::STATUS_ACTIVE;
    }

    public function isNew()
    {
        return !empty($this->status) && $this->status == AttachmentMapper::STATUS_NEW;
    }
    
    public function isBlocked()
    {
        return !empty($this->status) && $this->status == AttachmentMapper::STATUS_BLOCKED;
    }
    
    public function isTransforming()
    {
        return !empty($this->status) && $this->status == AttachmentMapper::STATUS_TRANSFORMING;
    }
    
    public function isError()
    {
        return !empty($this->status) && $this->status == AttachmentMapper::STATUS_ERROR;
    }
    
    
    public function isImage()
    {
        return $this->type == AttachmentMapper::TYPE_IMAGE;
    }
    
    public function isVideo()
    {
        return $this->type == AttachmentMapper::TYPE_VIDEO;
    }
    
    public function isSourceYoutube()
    {
        return $this->source == AttachmentMapper::SOURCE_YOUTUBE;
    }
    
    public function isSourceVeneo()
    {
        return $this->source == AttachmentMapper::SOURCE_VENEO;
    }
    
    
    

}
