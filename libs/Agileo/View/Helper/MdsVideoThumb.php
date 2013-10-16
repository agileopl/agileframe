<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_View_Helper_MdsVideoThumb extends Agileo_View_Helper_Mds
{

    protected $_imageOptions = array(
        'resize' => null
    );

    public function mdsVideoThumb(Attachment $video, array $options = array())
    {
        
        $options = array_merge($this->_imageOptions, $options);
        $this->_prepareOptions($options);
        
        
        $ret = '<!-- not suported -->';
        if($video->isVideo()) {
            
            if(!empty($video->thumb)) {
                if(substr($video->thumb, 0, 4) == 'http') {
                    
                    if(!empty($options['resize'])) {
                        list($width,$height) = explode('x', $options['resize']);
                    }
                    
                    $img = '<img src="'.$video->thumb.'" ';
                    
                    if(!empty($width)) {
                        $img .= ' width="'.$width.'"';
                    } elseif(!empty($height)) {
                        $img .= ' height="'.$height.'"';
                    }
                    
                    $img .= '/>';
                    
                    $ret = $img;
                } else {
                    $ret = $this->view->mdsImage($video->thumb, array('resize' => $options['resize']));
                }
            }

        }
        
        return $ret;

    }

}
