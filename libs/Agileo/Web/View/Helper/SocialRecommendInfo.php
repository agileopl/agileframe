<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Web_View_Helper_SocialRecommendInfo extends Zend_View_Helper_Abstract
{

    public function socialRecommendInfo(Agileo_Object $object)
    {
        
        $html = '<div class="recommendInfo">';
        $info = array();
        
        $info[] = '<i class="icon-facebook-sign"></i> '.$object->stat_fb_like.'';
        $info[] = '<i class="icon-twitter-sign"></i> '.$object->stat_tweet.'';
        $info[] = '<i class="icon-comment"></i> '.$object->stat_comments.'';
        
        $html .= join('<span class="sep">|</span>',$info);
        
        $html .= '</div>';
        
        return $html;
    }
    

}