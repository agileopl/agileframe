<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_View_Helper_ObjectAttachmentsActions extends Zend_View_Helper_Abstract
{
    
    public static $init = array();

    public function objectAttachmentsActions($attId, $parentObject, $parentGroup = AttachmentParentRelMapper::PARENT_GROUP_DEFAULT, $type = 'all', $addOptions = array())
    {
        
        $parent = get_class($parentObject);
        $parentId = $parentObject->id;
        
        $ident = $parent.$parentId.$parentGroup;

        $html = '
        <a class="jIEdit-'.$ident.'" data-id="'.$attId.'" href="#"><i class="icon-edit"></i></a>
        <a class="jIDelete-'.$ident.'" data-id="'.$attId.'" class="delete" href="#"><i class="icon-remove"></i></a>
        ';
        
        if(empty(self::$init[$ident])) {
            self::$init[$ident] = true;
            
            $script = "
            $(function () {    
                $('.jIDelete-".$ident."').click(function (){
                    if(confirm('" . $this->view->translate('list_navi_delete_confirm') . "')) {
                        var unsetAttId = $(this).data('id');
                        
                        $.post('/attachment/index/unset.from.parent/', { 'id': $(this).data('id'), 'parent': '".$parent."', 'parentId': '".$parentId."', 'parentGroup': '".$parentGroup."'},
                            function(data){}, 'json')
                                .success(function(){
                                    $('#iAtt'+unsetAttId).hide();
                                });
                    }
                    return false;
                });    

                $('.jIEdit-".$ident."').click(function (){
                    
                    agiIframePopoverShow('Edycja załącznika', '/attachment/index/edit/poplay/1/id/'+$(this).data('id'));
                    
                    return false;
                });    

            });
            function attachmentEditCallback() {
                location.reload();
            }
            ";
            $this->view->inlineScript()->appendScript($script);
        }
        
        return $html;
        
    }

    

}
        
