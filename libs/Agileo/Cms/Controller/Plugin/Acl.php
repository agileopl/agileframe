<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Agileo_Cms_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {

        $cache = Zend_Registry::get('cache');
        $cacheIdentity = md5('cmsAcl');
        if (!($acl = $cache->load($cacheIdentity))) {

            $config = Zend_Registry::get('config');

            $aclConfig = new Zend_Config_Yaml(realpath($config->acl->configFile), 'acl');

            // set up acl
            $acl = new Zend_Acl();

            // add the roles
            foreach ($aclConfig->rules as $rule) {
                $acl->addRole(new Zend_Acl_Role($rule->id), (!empty($rule->parent) ? $rule->parent : NULL));
            }

            // add the resources
            foreach ($aclConfig->resources as $resource) {
                $acl->add(new Zend_Acl_Resource($resource));
            }

            // add accesses
            foreach ($aclConfig->accesses as $role => $accesses) {
                foreach ($accesses as $access) {
                    $rrole = $role == 'all' ? null : $role;
                    $acl->allow($rrole, $access->resource, !empty($access->privilages) ? $access->privilages->toArray() : NULL);
                }
            }

            // administrators can do anything
            $acl->allow('admin', null);

            if ($config->acl->cache->lifeTime > 0) {
                $cache->save($acl, $cacheIdentity, array(), $config->acl->cache->lifeTime);
            }

        }
        Zend_Registry::set('Zend_Acl', $acl);

        // fetch the current user
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $role = strtolower($identity->role);
        }

        if (empty($role)) {
            $role = 'guest';
        }

        $module = $request->module;
        $controller = $request->controller;
        $action = $request->action;

        $resource = 'mc-' . $module . '-' . $controller;

        
        if(!$acl->has($resource)) {
            
            $request->setModuleName('default');
            $request->setControllerName('error');
            $request->setActionName('error');
            
            $exception        = new Exception('ACL: Brak zdefiniowanego resource: '.$resource, 404);
            $error            = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
            $exceptions       = $exception;
            $exceptionType    = get_class($exception);
            $error->exception = $exception;
            $error->type = Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION;
            $error->request = clone $request;
            
            $request->setParam('error_handler', $error);
            
        } elseif (!$acl->isAllowed($role, $resource, $action)) {

            if ($role == 'guest') {
                $request->setModuleName('default');
                $request->setControllerName('auth');
                $request->setActionName('login');
            } else {
                $request->setModuleName('default');
                $request->setControllerName('error');
                $request->setActionName('noauth');
            }
        }

    }

}
