<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_View_Helper_PopoverIframeInit extends Zend_View_Helper_Abstract
{
    
    protected static $_isInit = false;
    
    public function popoverIframeInit()
    {
        
        if(!self::$_isInit) {
            
            self::$_isInit = true;
            
            $html = '
            <div id="jIframePopover" class="modal hide fade iframe" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="jIframePopoverLabel">&nbsp;</h3>
              </div>
              <div class="modal-body">
                <div id="jIframePopoverLoader" class="ajax-loader-big hide"></div>
                <iframe id="jIframePopoverIframe" src=""></iframe>
              </div>
              <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">'.$this->view->translate('modal_close').'</button>
              </div>
            </div>
            ';
            
            $js = "
            function agiIframePopoverShow(title, src) {
                $('#jIframePopover h3').html(title);
                
                $('#jIframePopoverLoader').show();
                $('#jIframePopoverIframe').css('height', 0);
                
                $('#jIframePopoverIframe').attr('src', src);
                $('#jIframePopoverIframe').load(function() {
                    $('#jIframePopoverLoader').hide();
                    $('#jIframePopoverIframe').css('height', '100%');
                });
                
                $('#jIframePopover').modal('show');
            }
            function agiIframePopoverHide(title, src) {
                $('#jIframePopover').modal('hide');
            }
            ";
            
            $this->view->inlineScript()->appendScript($js);
            
            return $html;
            
        }
        
        return '';
        
    }

}