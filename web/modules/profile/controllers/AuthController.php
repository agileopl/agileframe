<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Profile_AuthController extends Zend_Controller_Action
{

    private $_lastReferer = '/';

    public function init()
    {
        $pasess = new Zend_Session_Namespace('profileauth');
        
        if(!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/profile/auth') === false
            && strpos($_SERVER['HTTP_REFERER'], substr(Zend_Registry::get('config')->globals->cookie->domain,1)) !== false
        ) {
            $pasess->lastReferer = $_SERVER['HTTP_REFERER'];
        }
        
        if(!empty($pasess->lastReferer)) {
            $this->_lastReferer = $pasess->lastReferer;
        }
        
    }
    

    public function fbloginAction()
    {
        $fbconfig = Zend_Registry::get('config')->globals->social->facebook;
        $fbsess = new Zend_Session_Namespace('fbconnect');
        $fbsess->state = md5(uniqid(rand(), TRUE));
        // CSRF protection
        $dialog_url = "https://www.facebook.com/dialog/oauth?client_id=" . $fbconfig->app_id . "&redirect_uri=" . urlencode($fbconfig->my_url) . "&state=" . $fbsess->state . "&scope=".$fbconfig->scope;
        header("Location: " . $dialog_url);
        exit;
    }

    public function fbconnectAction()
    {
        $fbconfig = Zend_Registry::get('config')->globals->social->facebook;
        $fbsess = new Zend_Session_Namespace('fbconnect');
        $code = $this->_getParam('code', null);
        if ($fbsess->state && ($fbsess->state === $_REQUEST['state'])) {

            $token_url = "https://graph.facebook.com/oauth/access_token?" . "client_id=" . $fbconfig->app_id . "&redirect_uri=" . urlencode($fbconfig->my_url) . "&client_secret=" . $fbconfig->app_secret . "&code=" . $code;

            $response = file_get_contents($token_url);
            $params = null;
            parse_str($response, $params);

            $fbsess->access_token = $params['access_token'];

            $graph_url = "https://graph.facebook.com/me?access_token=" . $params['access_token'];

            $fbuser = json_decode(file_get_contents($graph_url));
            
            $authAdapter = new Agileo_Web_Auth_Facebook($fbuser, true);

            $this->_login($authAdapter);

        } else {
            throw new Agileo_Exception("The state does not match. You may be a victim of CSRF.");
        }

        exit ;

    }

    public function loginAction()
    {

        $userForm = new Form_Login();
        $userForm->setAction('/profile/auth/login');

        $userForm->setAttrib('id', 'jLoginForm');

        if ($this->_request->isPost() && $userForm->isValid($_POST)) {
            $data = $userForm->getValues();
            $authAdapter = new Agileo_Web_Auth($data['usr_email'], $data['usr_pass']);
            $this->_login($authAdapter);
        }
        $this->view->form = $userForm;
    }

    public function logoutAction()
    {
        $authAdapter = Zend_Auth::getInstance();
        $authAdapter->clearIdentity();
        Zend_Session::destroy(true);
        Agileo_Web_ProfileCookie::remove();
        return $this->_redirect('/');
    }

    public function registerAction()
    {

        $userForm = new Form_Register();
        $userForm->setAttrib('id', 'jUserForm');
        $userForm->setAction('/profile/auth/register');

        $userModel = UserMapper::getMasterInstance();

        if ($this->_request->isPost()) {
            if ($userForm->isValid($_POST)) {

                $user = new User($userForm->getValues());
                $user->auth_token = md5(uniqid());
                $user->type = UserMapper::TYPE_STANDARD;
                
                
                try {
                    UserMapper::getMasterInstance()->save($user);
    
                    $link = Zend_Registry::get('config')->globals->host->web . '/profile/auth/registers2/token/' . $user->auth_token;
                    Agileo_Mail_Template::send(array(
                        'to' => $user->email,
                        'subject' => $this->view->translate('profile_register_mail_subject'),
                        'bodyHtml' => $this->view->translate('profile_register_mail_body', $user->name, $link)
                    ));
                    
                
                } catch (Zend_Db_Statement_Exception $e) {
                    if(strpos($e->getMessage(),'Duplicate')) {
                        $this->_redirect('/profile/auth/register/status/duplicate');
                    } else {
                        throw $e;
                    }
                } catch (Exception $e) {
                    throw $e;
                }

                $this->_redirect('/profile/auth/register/status/account_create');

            }
        }

        $this->view->status = $this->_getParam('status');

        $this->view->form = $userForm;

        // dodaj do breadcrubs
        $this->_addToNavigation($this->view->translate('profile_register'));

    }

    public function registers2Action()
    {

        $userForm = new Form_RegisterS2();
        $userForm->setAttrib('id', 'jUserForm');
        $userForm->setAction('/profile/auth/registers2');

        $userModel = UserMapper::getMasterInstance();

        if($token = $this->_getParam('token')) {
            if (!$user = $userModel->getByToken($token)) {
                throw new Agileo_Exception('Nie ma zarejestrowanego tokena');
            }
        } elseif(Zend_Auth::getInstance()->hasIdentity() && Zend_Auth::getInstance()->getIdentity()->isTypeSocial()) {

            $user = Zend_Auth::getInstance()->getIdentity();
            $userForm->removeElement('usr_pass');
            $userForm->removeElement('re_pass');
            $userForm->removeElement('usr_nick');
            $userForm->removeElement('usr_name');
            $userForm->removeElement('usr_surname');

        } else {
            throw new Agileo_Exception('Brak tokena lub sesji');
        }
        
        
        if ($this->_request->isPost()) {
            if ($userForm->isValidPartial($_POST)) {

                $user->setData($userForm->getValues());

                $user->status = UserMapper::STATUS_ACTIVE;
                $user->auth_token = NULL;

                UserMapper::getMasterInstance()->save($user);

                if($token) {
                    $authAdapter = new Agileo_Web_Auth($user->email, $userForm->usr_pass->getValue());
                } elseif($user->isSocFacebook()) {
                    $fbuser = new stdClass;
                    $fbuser->id = $user->soc_facebook;
                    $authAdapter = new Agileo_Web_Auth_Facebook($fbuser, false);
                } else {
                    throw new Agileo_Exception('Inne typy social nie są obsłygiwane');
                }
                $this->_login($authAdapter);
            } else {
                throw new Agileo_Exception('Błąd walidacji:' . print_r($userForm->getErrors(), true));
            }
        }

        $input = $user->toArray();
        $input['token'] = $token;
        $userForm->populate($input);
        $this->view->form = $userForm;
        $this->view->user = $user;

        // dodaj do breadcrubs
        $this->_addToNavigation($this->view->translate('profile_register'));

    }

    public function forgotpassAction()
    {

        $userForm = new Form_ForgotPass();
        $userForm->setAction('/profile/auth/forgotpass');
        if ($this->_request->isPost() && $userForm->isValid($_POST)) {
            $data = $userForm->getValues();
            if ($user = UserMapper::getMasterInstance()->getByEmail($data['usr_email'])) {

                // refresh token
                $uUser = new User();
                $uUser->id = $user->id;
                $uUser->auth_token = md5(uniqid());
                if (UserMapper::getMasterInstance()->save($uUser)) {
                    $link = Zend_Registry::get('config')->globals->host->web . '/profile/auth/setnewpass/token/' . $uUser->auth_token;
                    if (Agileo_Mail_Template::send(array(
                        'to' => $user->email,
                        'subject' => 'Ustawianie nowego hasła',
                        'bodyHtml' => 'Aby ustawić nowe hasło kliknij w link <a href="' . $link . '">' . $link . '</a>'
                    ))) {
                        $status = 'msent';
                    } else {
                        $status = 'err_msend';
                    }

                } else {
                    $status = 'err';
                }

            } else {
                $status = 'noemail';
            }

            $this->_redirect('/profile/auth/forgotpass/status/' . $status);
            exit ;

        }
        $this->view->status = $this->_getParam('status', '');
        $this->view->form = $userForm;
    }

    public function setnewpassAction()
    {
        $this->view->status = $status = $this->_getParam('status');
        if ($token = $this->_getParam('token')) {
            if (!$user = UserMapper::getMasterInstance()->getByToken($token)) {
                throw new Agileo_Exception('Nie ma zarejestrowanego tokena');
            }

            $userForm = new Form_SetNewPass();
            $userForm->populate(array(
                'usr_email' => $user->email,
                'token' => $token
            ));
            $userForm->setAction('/profile/auth/setnewpass');
            if ($this->_request->isPost() && $userForm->isValid($_POST)) {

                $uUser = new User();
                $uUser->id = $user->id;
                $uUser->pass = $userForm->getValue('usr_pass');
                $uUser->auth_token = NULL;
                if (UserMapper::getMasterInstance()->save($uUser)) {
                    $status = 'ok';
                } else {
                    $status = 'err';
                }

                $this->_redirect('/profile/auth/setnewpass/status/' . $status);
                exit ;

            }
            $this->view->form = $userForm;

        } elseif (!empty($status)) {
            // wyswietlamy komunikat
        } else {
            throw new Agileo_Exception('Niepoprawne dane wejściowe');
        }
    }

    protected function _login(Zend_Auth_Adapter_Interface $authAdapter)
    {
        $result = $authAdapter->authenticate();
        if ($result->isValid()) {
            
            $auth = Zend_Auth::getInstance();
            $storage = $auth->getStorage();
            $storage->write($result->getIdentity());
            
            Agileo_Web_ProfileCookie::refresh($result->getIdentity());
            
            if(!$result->getIdentity()->isActive()) {
                return $this->_redirect('/profile/auth/registers2');
            } else {
                return $this->_redirect($this->_lastReferer);
            }

        } else {
            return $this->_redirect('/profile/auth/login?error=1');
        }
    }

    protected function _addToNavigation($label, $params = array(), $naviParentId = 'home_hide')
    {

        $zendNavi = Zend_Registry::get('Zend_Navigation');
        $n = $zendNavi->findById($naviParentId);
        $page = Zend_Navigation_Page::factory(array(
            'label' => $label,
            'module' => $this->_request->getModuleName(),
            'controller' => $this->_request->getControllerName(),
            'action' => $this->_request->getActionName(),
            'params' => $params
        ));

        $n->addPage($page);

    }

}
