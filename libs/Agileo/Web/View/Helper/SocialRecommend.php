<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Web_View_Helper_SocialRecommend extends Zend_View_Helper_Abstract
{
    
    protected static $_jsSemafor = false;

    public function socialRecommend(Agileo_Object $object)
    {
        if(!empty(Zend_Registry::get('config')->offlineMode)) {
            return false;
        }
        
        $html = '';
        if(!self::$_jsSemafor) {
            self::$_jsSemafor = true;
            
            $this->view->headScript()->appendFile('//connect.facebook.net/pl_PL/all.js');
            
            
            $this->view->inlineScript()->appendScript("
            
            FB.init({
                appId  : '".Zend_Registry::get('config')->globals->social->facebook->app_id."',
                status : true, // check login status
                cookie : true, // enable cookies to allow the server to access the session
                xfbml  : true, // parse XFBML
                channelUrl : '".Zend_Registry::get('config')->globals->host->web."/channel.html', // channel.html file
                oauth  : true // enable OAuth 2.0
            });
            
            FB.Event.subscribe('edge.create', function(href, widget) {
                $.get('/stat/social/refresh.fb.like', {href: href});
            });
            
            window.___gcfg = {lang: 'pl'};
              (function() {
                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                po.src = 'https://apis.google.com/js/plusone.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
            })();
            function plusOneClick(response) {
                $.get('/stat/social/refresh.g.plus', {href: response.href});
            }
            
            !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src='//platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','twitter-wjs');
            /* TODO callback*/
            
            ");
            
            $html .= '<div id="fb-root"></div>';
            
        }
        
        $objectsKeys = array_flip(StatMapper::getSupportedObjects());
        $hash = '';
        if(!empty($objectsKeys[get_class($object)])) {
            $hash = '#'.$objectsKeys[get_class($object)].'_'.$object->id;
        }
        
        $url = Zend_Registry::get('config')->globals->host->web . $object->getPublicUrl().$hash; 
        
        $html .= '
        <div class="socialForObject">
            
            <div class="social-t" data-url="'.$url.'">
                <a href="https://twitter.com/share" class="twitter-share-button" data-url="'.$url.'">Tweet</a>
            </div>
            
            <div class="social-g">
                <!-- Place this tag where you want the +1 button to render. -->
                <div class="g-plusone" data-size="medium" data-href="'.$url.'" data-callback="plusOneClick"></div>
            </div>

            <div class="social-fb">
                <div class="fb-like" data-href="'.$url.'" data-send="false" data-layout="button_count" data-show-faces="false"></div>
            </div>
            
        </div>    
        '; 
        
        return $html;
    }
    

}