<?    

    $this->headLink()
        ->appendStylesheet(Zend_Registry::get('config')->globals->cdnFiles->css->jqueryui)
        ;

    $this->headScript()
        ->prependFile(Zend_Registry::get('config')->globals->cdnFiles->js->jqueryui)
        ->prependFile(Zend_Registry::get('config')->globals->cdnFiles->js->jquery)
        ;
    
    if(!empty(Zend_Registry::get('config')->globals->compressor_cms_hash)
        && (Zend_Registry::get('config')->runMode != 'dev' || isset($_REQUEST['compress']))
    ) {
        
        $this->headLink()->appendStylesheet(STATIC_PATH.'/cmprs/'.Zend_Registry::get('config')->globals->compressor_cms_hash.'.css');
        $this->headScript()->appendFile(STATIC_PATH.'/cmprs/'.Zend_Registry::get('config')->globals->compressor_cms_hash.'.js');
        
    } else {

        $this->headLink()->appendStylesheet(STATIC_PATH.'/css/bootstrap.2.3.0.css')
            ->appendStylesheet(STATIC_PATH.'/css/bootstrap-responsive.css')
            ->appendStylesheet(STATIC_PATH.'/css/font-awesome.min.css')
            ->appendStylesheet(STATIC_PATH.'/css/main.css')
            ->appendStylesheet(STATIC_PATH.'/css/jquery.treeview.css')
            ->appendStylesheet(STATIC_PATH.'/css/jquery.tagsinput.css')
            ->appendStylesheet(STATIC_PATH.'/css/jquery.Jcrop.css')
            ->appendStylesheet(STATIC_PATH.'/css/select2.css')
            ;

        $this->headScript()
            ->appendFile(STATIC_PATH.'/js/bootstrap.2.3.0.js')
            ->appendFile(STATIC_PATH.'/js/jquery.validate.js')
            ->appendFile(STATIC_PATH.'/js/jquery.validate.adds.js')
            //->appendFile(STATIC_PATH.'/js/validate/messages_pl.js')
            ->appendFile(STATIC_PATH.'/js/jquery.form.js')
            ->appendFile(STATIC_PATH.'/js/jquery.cookies.2.2.0.js')
            ->appendFile(STATIC_PATH.'/js/jquery-dateplustimepicker.js')
            ->appendFile(STATIC_PATH.'/js/jquery.treeview.js')
            ->appendFile(STATIC_PATH.'/js/jquery.tinymce.js')
            ->appendFile(STATIC_PATH.'/js/jquery.tagsinput.min.js')
            ->appendFile(STATIC_PATH.'/js/jquery.livequery.js')
            ->appendFile(STATIC_PATH.'/js/jquery.delay.keyup.js')
            ->appendFile(STATIC_PATH.'/js/awesomechart.js')
            ->appendFile(STATIC_PATH.'/js/jquery.Jcrop.js')
            ->appendFile(STATIC_PATH.'/js/select2.js')
            ->appendFile(STATIC_PATH.'/js/select2_locale_pl.js')
            ->appendFile(STATIC_PATH.'/js/cms.js')
            ;        
    }


        
