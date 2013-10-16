<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */


class Agileo_Form_Twitter_Element_RichEditor extends Zend_Form_Element_Textarea
{
    
    private $_editorOptions = array(
    
        'script_url' => '/tiny_mce/tiny_mce.js',
    
        'theme' => 'advanced',
        'skin' => 'default',
        
        //imageschoose,imagesadd,attachchoose,attachadd,
        'plugins' => 'pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template',
        //,youtube
        'theme_advanced_buttons1' => "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,sub,sup,styleselect,bullist,numlist",
        'theme_advanced_buttons2' => 'tablecontrols',
        'theme_advanced_buttons3' => 'paste,pastetext,pasteword,search,replace,|,undo,redo,|,blockquote,link,unlink,anchor,|,hr,removeformat,pagebreak,|,fullscreen,code',
        
        //imageschoose,imagesadd,attachchoose,attachadd,
        
        // Style formats
        'style_formats' => "[
                {title : 'Tytuł 2 poziomu', block : 'h2'},
                {title : 'Tytuł 3 poziomu', block : 'h3'},
                {title : 'Tytuł 4 poziomu', block : 'h4'},
                {title : 'Akapit', block : 'p'},
                {title : 'Duży tekst', block : 'strong', classes : 'txtbig'},
                {title : 'Tekst red', inline : 'span', classes : 'txtred'},
                {title : 'Tekst blue', inline : 'span', classes : 'txtblue'},
                {title : 'Cytat w bloku', block : 'blockquote'},
                {title : 'Źródło/podpis', block : 'p', classes : 'txtsource'}
        ]",
        'theme_advanced_toolbar_location' => "top",
        'theme_advanced_toolbar_align' => "left",
        'theme_advanced_statusbar_location' => "bottom",
        'theme_advanced_resizing' => false,
        'theme_advanced_blockformats' => "h2,h3,h4,h5,h6,p,pre,blockquote,code",
        'width' => "100%",
        'height' => "400",
        'object_resizing' => false,
        'theme_advanced_resizing_max_width' => "800",
        'theme_advanced_resizing_min_width' => "200",
        'convert_urls' => false,
        'extended_valid_elements' => 'iframe[src|width|height|name|align|frameborder|allowfullscreen]',
        'language' => 'en',
        //nie chcemy zamiany space i polskich znaków na encje, dane są filtrowane
        'entities' => ''
    );

    public function __construct($spec, $options = array())
    {

        foreach($this->_editorOptions as $option => $val) {
            if(!preg_match('/^(prepend_|append_)(.+)/i', $option) && isset($options[$option])) {
                $this->_editorOptions[$option] = $options[$option]; 
            }
        }
        
        foreach($options as $option => $val) {
            if(preg_match('/^(prepend|append)_(.+)/i', $option, $match) && isset($options[$option])) {
                if(isset($this->_editorOptions[$match[2]])) {
                    if($match[1] == 'append') {
                        $arr = array($this->_editorOptions[$match[2]]);
                        array_push($arr, join(',',$val));
                        $this->_editorOptions[$match[2]] = join(',',$arr);
                    } elseif ($match[1] == 'prepend') {
                        array_push($val, $this->_editorOptions[$match[2]]);
                        $this->_editorOptions[$match[2]] = join(',',$val);
                    }
                }
            }
        }
        

        if(empty($this->_editorOptions['content_css'])) {
            $this->_editorOptions['content_css'] = STATIC_PATH . '/css/tinymce-content.css';
        }
        if(empty($this->_editorOptions['popup_css'])) {
            $this->_editorOptions['popup_css_add'] = STATIC_PATH . '/css/tinymce-popup.css';
        }

        parent::__construct($spec, $options);
    }

    public function render(Zend_View_Interface $view = null)
    {
        $content = parent::render($view);

        $script = '$(document).ready(function($) {
            $("#' . $this->getName() . '").tinymce({' . "\n";

        foreach ($this->_editorOptions as $key => $value) {
            $value = trim($value);
            if (substr($value, 0, 1) == '[' || substr($value, 0, 1) == '{') {
                $script .= $key . ' : ' . $value . ',' . "\n";
            } else {
                $script .= $key . ' : "' . $value . '",' . "\n";
            }
        }
        $script = substr($script, 0, -2) . "});\n";

        // odswiezenie edytorka przed włączeniem jvalidatora
        $script .= "
            $(\"input[type='submit']\",\"button\").click(function() {
                tinyMCE.triggerSave();
            });
        ";

        $script .=  "\n});\n";

        $this->getView()->inlineScript()->appendScript($script);

        return $content;

    }

    public function setEditorOption($optionName, $value) {
        $this->_editorOptions[$optionName] = $value;
    }

    public function getEditorOption($optionName) {
        return !empty($this->_editorOptions[$optionName]) ? $this->_editorOptions[$optionName] : '';
    }

}
