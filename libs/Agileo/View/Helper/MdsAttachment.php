<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_View_Helper_MdsAttachment extends Agileo_View_Helper_Mds
{

    public function mdsAttachment($attachment, array $options = array())
    {
        $this->_prepareOptions($options);

        $path = '';
        if ($attachment instanceof Attachment) {
            $path = $attachment->url;
        } else {
            $path = $attachment;
        }

        $src = Zend_Registry::get('config')->globals->host->mds.'/f/o/f'.$path;

        if (!empty($this->_options['content'])) {
            $ret = '<a href="'.$src.'"';
            $ret .= $this->_prepareAttribs();
            $ret .= '>'.$options['content'].'</a>';                
        } else {
            $ret = $src;
        }

        return $ret;
    }

}
