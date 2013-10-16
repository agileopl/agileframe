<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_View_Helper_ObjectAttachments extends Zend_View_Helper_Abstract
{
    
    public static $init = false;

    public function objectAttachments($parentObject, $parentGroup = AttachmentParentRelMapper::PARENT_GROUP_DEFAULT, $type = 'all', $addOptions = array())
    {
        
        $html = '<div class="form">
            <a href="#modalUploader" id="modalUploader" class="btn btn-primary">'.$this->view->translate('add_'.(!empty($type) && $type != 'all' ? strtolower($type) : 'attachment').'_for_parent').'</a>   
            <a href="#modalJoiner" id="modalJoiner" class="btn btn-primary">'.$this->view->translate('join_'.(!empty($type) && $type != 'all' ? strtolower($type) : 'attachment').'_for_parent').'</a>   
        </div>';
        
        
        $parent = get_class($parentObject);
        $parentId = $parentObject->id;
        
        $this->view->inlineScript()->appendScript("
            $('#modalUploader').click(function () {
                agiIframePopoverShow('Wgraj plik', '/attachment/index/uploader/poplay/1/type/".$type."/parent/".$parent."/parentId/".$parentId."/parentGroup/".$parentGroup."');
                return false;
            });
            $('#modalJoiner').click(function () {
                agiIframePopoverShow('Podłącz pliki', '/attachment/index/appand.items.to.parent/poplay/1/type/".$type."/parent/".$parent."/parentId/".$parentId."/parentGroup/".$parentGroup."');
                return false;
            });
        ");
        
        if(!self::$init) {
            self::$init = true;
            
            $script = "
            function objectAttachmentsUploaderCallback() {
                location.reload();
            }
            function objectAttachmentsJoinerCallback(responseData) {
                location.reload();
            }
            ";
            $this->view->inlineScript()->appendScript($script);
        }
        
        return $html;
        
    }


}
        
