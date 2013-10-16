<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_View_Helper_MdsVideo extends Agileo_View_Helper_Mds
{

    protected $_videoOptions = array(
        'width' => 560,
        'height' => 315
    );
    
    public function mdsVideo(Attachment $video, array $options = array())
    {
        
        $options = array_merge($this->_videoOptions, $options);
        $this->_prepareOptions($options);
        
        $ret = '<!-- not suported -->';
        if($video->isVideo()) {
            if($video->isSourceYoutube()) {
                $ret = '<iframe width="' . $options['width'] . '" height="' . $options['height'] . '" src="http://www.youtube.com/embed/' . $video->name . '" frameborder="0" allowfullscreen></iframe>';
            } elseif($video->isSourceVeneo()) {
                if($video->isActive()) {
                    $thumb = !empty($video->thumb) ? $this->view->mdsImage($video->thumb, array('resize' => $options['width'] . 'x' . $options['height'])) : '';
                    $ret = '<video id="video'.$video->id.'" class="video-js vjs-default-skin" controls
                        preload="auto" width="' . $options['width'] . '" height="' . $options['height'] . '" poster="'.$thumb.'"
                        data-setup="{}">
                        <source src="'.Zend_Registry::get('config')->videoService->veneo->cdnHost.$video->url.'" type="video/mp4">
                    </video>';

                    if(!empty(Zend_Registry::get('config')->globals->videoStat)) {
                        
                        $supportedObjects = array_flip(StatMapper::getSupportedObjects());
                        
                        $objectName = get_class($video);
                        $id = $video->getId();
                
                        if (!empty($supportedObjects[$objectName])) {
                            
                            $jsParams = array();
                            $jsParams['params'][$supportedObjects[$objectName]]['id'] = $id;
                            $jsParams['params'][$supportedObjects[$objectName]]['hash'] = StatMapper::supportedHash($objectName, $id);
                            if (!empty($jsParams)) {
                                
                                $jsData = '
                                data = ' . Zend_Json::encode($jsParams) . ';
                                profile = $.cookies.get(\''.Zend_Registry::get('config')->globals->cookie->profileName.'\');
                                if(profile) {
                                    data["params"]["'.$supportedObjects[$objectName].'"]["usrId"] = profile.id; 
                                }
                                $.ajax({url: "/stat/play", cache: false, data: data});
                                '; 
                                
                                $this->view->inlineScript()->appendScript("
                                vjs('video".$video->id."').on('play', function () { ".$jsData." });
                                ");

                            }
                        }
                                    
                    
                    }
                               
                } elseif($video->isTransforming()) {
                    $ret = 'W trakcie przetwarzania...';
                } else {
                    $ret = 'W trakcie przetwarzania.';
                }
            } else {
                //
            }
        }
        
        return $ret;

    }

}
