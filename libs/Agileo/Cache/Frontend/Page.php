<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Agileo_Cache_Frontend_Page extends Zend_Cache_Frontend_Page {
    
    public function __construct(array $options = array())
    {
        if(!empty($options['pages_regexps'])) {
            if(empty($options['regexps'])) {
                $options['regexps'] = array();
            }
            foreach($options['pages_regexps'] as $i => $regexp) {
                $options['regexps'][$regexp] = array('cache' => true);
            }
        }
        parent::__construct($options);
    }
    
    /**
     * dodalem info że dane są z cache
     */
    public function start($id = false, $doNotDie = false)
    {
            
        $this->_cancel = false;
        $lastMatchingRegexp = null;
        if (isset($_SERVER['REQUEST_URI'])) {
            foreach ($this->_specificOptions['regexps'] as $regexp => $conf) {
                if (preg_match("`$regexp`", $_SERVER['REQUEST_URI'])) {
                    $lastMatchingRegexp = $regexp;
                }
            }
        }
        $this->_activeOptions = $this->_specificOptions['default_options'];
        if ($lastMatchingRegexp !== null) {
            $conf = $this->_specificOptions['regexps'][$lastMatchingRegexp];
            foreach ($conf as $key=>$value) {
                $this->_activeOptions[$key] = $value;
            }
        }
        if (!($this->_activeOptions['cache'])) {
            return false;
        }
        if (!$id) {
            $id = $this->_makeId();
            if (!$id) {
                return false;
            }
        }
        $array = $this->load($id);
        if ($array !== false) {
            $data = $array['data'];
            $headers = $array['headers'];
            if (!headers_sent()) {
                foreach ($headers as $key=>$headerCouple) {
                    $name = $headerCouple[0];
                    $value = $headerCouple[1];
                    header("$name: $value");
                }
            }
            if ($this->_specificOptions['debug_header']) {
                echo 'DEBUG HEADER : This is a cached page !';
            }
            echo $data . (Zend_Registry::get('config')->runMode == 'dev' ? ' -- from-cache ' : '<!-- from cache -->');
            if ($doNotDie) {
                return true;
            }
            die();
        }
        ob_start(array($this, '_flush'));
        ob_implicit_flush(false);
        return false;
    }
    
    protected function _makeId()
    {
        return md5($_SERVER['REQUEST_URI']);
    }
    
}
