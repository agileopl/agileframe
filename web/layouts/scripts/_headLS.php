<?    
    $this->headLink()
        ->appendStylesheet(Zend_Registry::get('config')->globals->cdnFiles->css->jqueryui)
        ;

    $this->headScript()
        ->prependFile(Zend_Registry::get('config')->globals->cdnFiles->js->jqueryui)
        ->prependFile(Zend_Registry::get('config')->globals->cdnFiles->js->jquery)
        ;
    
    if(!empty(Zend_Registry::get('config')->globals->compressor_web_hash)
        && (Zend_Registry::get('config')->runMode != 'dev' || isset($_REQUEST['compress']))
    ) {
        
        $this->headLink()->appendStylesheet(STATIC_PATH.'/cmprs/'.Zend_Registry::get('config')->globals->compressor_web_hash.'.css');
        $this->headScript()->appendFile(STATIC_PATH.'/cmprs/'.Zend_Registry::get('config')->globals->compressor_web_hash.'.js');
        
    } else {

        $this->headLink()
            ->appendStylesheet(STATIC_PATH.'/css/bootstrap.css')
            ->appendStylesheet(STATIC_PATH.'/css/bootstrap-responsive.css')
            ->appendStylesheet(STATIC_PATH.'/css/font-awesome.css')
            ->appendStylesheet(STATIC_PATH.'/css/jquery.tagsinput.css')
            ->appendStylesheet(STATIC_PATH.'/css/jquery.Jcrop.css')
            ->appendStylesheet(STATIC_PATH.'/css/colorbox.css')
            ->appendStylesheet(STATIC_PATH.'/css/calendario.css')
            ->appendStylesheet(STATIC_PATH.'/css/main.css')
            ;

        $this->headScript()
            ->appendFile(STATIC_PATH.'/js/bootstrap.js')
            ->appendFile(STATIC_PATH.'/js/jquery.validate.js')
            ->appendFile(STATIC_PATH.'/js/jquery.validate.adds.js')
            ->appendFile(STATIC_PATH.'/js/validate/messages_pl.js')
            ->appendFile(STATIC_PATH.'/js/jquery.cookies.2.2.0.js')
            ->appendFile(STATIC_PATH.'/js/jquery-dateplustimepicker.js')
            ->appendFile(STATIC_PATH.'/js/jquery.tagsinput.js')
            ->appendFile(STATIC_PATH.'/js/jquery.tinymce.js')
            ->appendFile(STATIC_PATH.'/js/jquery.form.js')
            ->appendFile(STATIC_PATH.'/js/jquery.livequery.js')
            ->appendFile(STATIC_PATH.'/js/jquery.delay.keyup.js')
            ->appendFile(STATIC_PATH.'/js/jquery.calendario.js')
            ->appendFile(STATIC_PATH.'/js/jquery.Jcrop.js')
            ->appendFile(STATIC_PATH.'/js/jquery.colorbox.js')
            ->appendFile(STATIC_PATH.'/js/bootstrap-typeahead.js')

            ->appendFile(STATIC_PATH.'/js/main.js')
            ;        
    }


    //$this->inlineScript()->appendScript("videojs.options.flash.swf = '".STATIC_PATH."/swf/video-js.swf';");
