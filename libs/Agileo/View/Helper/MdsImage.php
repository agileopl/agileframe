<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_View_Helper_MdsImage extends Agileo_View_Helper_Mds
{

    protected $_imageOptions = array(
        'crop' => null, // {L}eft|{R}ight|{M}iddle {T}op|{M}iddle|{B}ottom}
        'resize' => null, // string exml: 100x100
        'keep' => null, // keep proportion with borders {L}eft|{R}ight|{M}iddle {T}op|{M}iddle|{B}ottom}
        'keepBg' => null // color in hexa with out #
    );

    public function mdsImage($image, array $options = array())
    {
        
        $options = array_merge($this->_imageOptions, $options);
        $this->_prepareOptions($options);
        
        $path = '';
        if ($image instanceof Attachment && $image->isImage()) {
            $path = $image->url;
        } else {
            $path = $image;
        }

        if(substr($path,0,4) == 'http') {
            
            $src = $path;
            
        } else {
            
            $modOptions = 'o';
            if(!empty($this->_options['keep'])) {
                $modOptions = 'k'.(!empty($this->_options['resize']) ? ','.join(',',explode('x',$this->_options['resize'])) : '') .','. strtolower($this->_options['keep']);
                if(!empty($this->_options['keepBg'])) {
                    $modOptions .= ','.$this->_options['keepBg'];
                }
            } elseif(!empty($this->_options['crop'])) {
                $modOptions = 'c'.(!empty($this->_options['resize']) ? ','.join(',',explode('x',$this->_options['resize'])) : '') .','. strtolower($this->_options['crop']);
            } elseif(!empty($this->_options['resize'])) {
                $modOptions = 'r'.','.join(',',explode('x',$this->_options['resize']));
            }
            
            $src = Zend_Registry::get('config')->globals->host->mds . '/p/'.$modOptions.'/f' . $path;
            
        }
        

        if (!empty($this->_options['content'])) {
            $ret = '<img src="'.$src.'"';
            $ret .= $this->_prepareAttribs();
            $ret .= ' alt="'.$this->view->escape($this->_options['content']).'" />';
        } else {
            $ret = $src;
        }

        return $ret;
    }

}
