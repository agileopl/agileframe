<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_View_Helper_Aurl extends Zend_View_Helper_Url
{

    private $_options = array(
        'controller' => null, //domyslny controller
        'module' => null, //domyslny modul
        'action' => null, //domyslna akcja
        'params' => array(), //parametry
        'router' => 'default', //domyslny router
        'content' => false, //Zawartosc <a>{content}</a>
        'attribs' => array(), //Atrybuty, np: klasa,id

        'aclMode' => 0, //0 nie sprawdza - 1 sprawdza i ukrywa - 2 sprawdza i dodaje klase z klodką
        'resource' => null,
        'privilege' => null,
        'role' => 'guest'

    );

    public function aurl(array $options)
    {
        $options = $this->_prepareOptions($options);

        $acl = true;
        if(!empty($options['aclMode'])) {
            $options['role'] = Zend_Auth::getInstance()->getIdentity()->getRole();
            $acl = $this->_checkAcl($options);

            if($options['aclMode'] == 2 && !$acl) {
                $options['attribs']['class'] = 'noaccess' . (!empty($options['attribs']['class']) ? ' '.$options['attribs']['class'] : '');
            }

        }

        $html = '';
        if($options['aclMode'] == 1 && !$acl) {
            // do nothing
        } else {
            $url = $this->url(array_merge(array('module' => $options['module'],
                    'controller' => $options['controller'],
                    'action' => $options['action']), $options['params']), $options['router']);

            if (!empty($options['content'])) {

                $attribs = '';
                if (count($options['attribs']) > 0) {
                    foreach ($options['attribs'] as $attrib => $value) {
                        $attribs .= $attrib . '="' . $value . '" ';
                    }
                }

                $html = '<a href="' . $url . '" ' . $attribs . '>' . $options['content'] . '</a>';
            } else {
                $html = $url;
            }

        }

        return $html;
    }

    protected function _prepareOptions($options)
    {
        if (!empty($options['path'])) {
            $path = substr($options['path'], 0, 1) == '/' ? substr($options['path'], 1) : $options['path'];
            $mca = explode("/", $path);
            if (count($mca) == 2) {
                $options['module'] = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
                $options['controller'] = $mca[0];
                $options['action'] = $mca[1];
            }
            if (count($mca) == 3) {
                $options['module'] = $mca[0];
                $options['controller'] = $mca[1];
                $options['action'] = $mca[2];
            }
        }

        if (empty($options['action'])) {
            $options['action'] = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
        }
        if (empty($options['module'])) {
            $options['module'] = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
        }
        if (empty($options['controller'])) {
            $options['controller'] = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        }
        return array_merge($this->_options, $options);
    }

    private function _checkAcl($options)
    {
        $acl = null;
        if (Zend_Registry::isRegistered('acl')) {
            $acl = Zend_Registry::get('acl');
        }

        // jeżeli nie ma zdefiniowanego acl'a to go nie sprawdzamy
        if(!$acl) {
            return true;
        }

        if ($options['resource'] == null AND $options['controller'] != null) {
            $resource = $options['resource-prefix'] . $options['module'] . ':' . $options['controller'];
            $privilege = $options['action'];
        } elseif ($options['resource'] != null AND $options['privilege'] != null) {
            $resource = $options['resource'];
            $privilege = $options['privilege'];
        } else {
            return false;
        }

        $role = $options['role'];
        if ($role == null) {
            return false;
        }

        if (!$acl->has($resource) || $acl->isAllowed($role, $resource, $privilege)) {
            return true;
        }
        return false;
    }

}
