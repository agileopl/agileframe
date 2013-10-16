<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_View_Helper_LayoutColorer extends Zend_View_Helper_Abstract
{

    protected $_options = array(
        '_contentColorerCallback' => null,
        '_boxColorerCallback' => null,
        '_colColorerCallback' => null,
        '_rowColorerCallback' => null,
        '_rowsColorerCallback' => null,
    );

    public function layoutColorer($layoutConfig, array $options = array())
    {
        $this->_options = array_merge($this->_options, $options);

        return $this->_renderLayout($layoutConfig);
    }

    private function _renderLayout(Zend_Config $layout)
    {
            
        $result = '';
        if (isset($layout->rows)) {
            foreach($layout->rows as $row) {
                $result .= $this->_renderRow($row);
            }
        }
        
        return $this->_layoutColorer($layout, $result);
    }
    
    private function _renderRow(Zend_Config $row)
    {
        $result = '';
        if (isset($row->columns)) {
            foreach ($row->columns as $col) {
                $result .= $this->_renderCol($col);
            }
        }
        return $this->_rowColorer($row, $result);
    }
    
    private function _renderCol(Zend_Config $col)
    {
        $result = '';
        if (isset($col->boxes)) {
            foreach ($col->boxes as $segmentId => $box) {
                $boxHtml = $this->view->layout()->{$segmentId};
                if(!empty($boxHtml)) {
                    if (!empty($box->type) && $box->type == 'content') {
                        $result .= $this->_contentColorer($box, $boxHtml);
                    } elseif (!empty($box->type) && $box->type == 'box') {
                        $result .= $this->_boxColorer($box, $boxHtml);
                    }
                }
            }
        }
        return $this->_colColorer($col, $result);
    }

    protected function _contentColorer(Zend_Config $box, $content)
    {
        if (!empty($this->_options['_contentColorerCallback'])) {
            $content = call_user_func_array($this->_options['_contentColorerCallback'], array($box, $content, $this));
        }
        return $this->_containerColorer($box->container, $content);
    }

    protected function _boxColorer(Zend_Config $box, $content)
    {
        if (!empty($this->_options['_boxColorerCallback'])) {
            $content = call_user_func_array($this->_options['_boxColorerCallback'], array($box, $content, $this));
        }
        return $this->_containerColorer($box->container, $content);
    }

    protected function _colColorer(Zend_Config $col, $content)
    {
        if (!empty($this->_options['_colColorerCallback'])) {
            $content = call_user_func_array($this->_options['_colColorerCallback'], array($col, $content, $this));
        }
        return $this->_containerColorer($col->container, $content);
    }

    protected function _rowColorer(Zend_Config $row, $content)
    {
        if (!empty($this->_options['_rowColorerCallback'])) {
            $content = call_user_func_array($this->_options['_rowColorerCallback'], array($row, $content, $this));
        }
        return $this->_containerColorer($row->container, $content);
    }

    protected function _layoutColorer(Zend_Config $layout, $content)
    {
        if (!empty($this->_options['_rowsColorerCallback'])) {
            $content = call_user_func_array($this->_options['_rowsColorerCallback'], array($layout, $content, $this));
        }
        return $this->_containerColorer($layout->container, $content);
    }
    
    protected function _containerColorer($container, $content)
    {
        if (!empty($container) && $container instanceof Zend_Config) {
            
            $tagName = !empty($container->tagName) ? $container->tagName : 'div';
            
            $html = '<'.$tagName;
            // attribs
            if(!empty($container->attribs)) {
                foreach($container->attribs as $key => $value) {
                    $html .= ' '.$key.'="'.$value.'"';
                }
            }
            $html .= '>';
            $html .= $content;
            $html .= '</'.$tagName.'>';
            
            $content = $html; 
            
            if (!empty($container->container)) {
                $content = $this->_containerColorer($container->container, $content);
            }
        }
        
        return $content;
    }


}
