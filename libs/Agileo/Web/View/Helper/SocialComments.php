<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Web_View_Helper_SocialComments extends Zend_View_Helper_Abstract
{
    
    protected static $_jsSemafor = false;

    public function socialComments(Agileo_Object $object)
    {
        $html = '';
        if(!self::$_jsSemafor) {
            self::$_jsSemafor = true;
        }

        $objectsKeys = array_flip(StatMapper::getSupportedObjects());
        $hash = '';
        if(!empty($objectsKeys[get_class($object)])) {
            $hash = $objectsKeys[get_class($object)].'_'.$object->id;
        }

        $url = 'http://'. $_SERVER['SERVER_NAME'] . $object->getPublicUrl().'#'.$hash; 
        $dqShortName = Zend_Registry::get('config')->globals->social->disqus->shortname;

        $this->view->inlineScript()->appendScript("

        function disqus_config() {
            this.callbacks.onNewComment = [function() { alert(1) }];
        }
        
        var disqus_shortname = '".$dqShortName."'; // Required - Replace example with your forum shortname
        var disqus_identifier = '".$hash."';
        var disqus_title = '".$object->title."';
        var disqus_url = '".$url."';
        var disqus_config = function () {
            
            var config = this;
            config.callbacks.onNewComment.push(function() {
                $.get('/stat/social/refresh.disqus.counter', {identifier: '".$hash."'});
            });
            
        };
                
        (function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();

        function disqus_config() {
            this.callbacks.afterRender = [function() {
                alert(1);
            }];
        }

                
        ");
        
        $html .= '
        <div id="disqus_thread"></div>
        <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
        <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
        '; 
        
        return $html;
    }
    

}