<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class User extends Agileo_Object implements ChangeLogInterface, SearchInterface
{

    protected $_subObjectsConfig = array(
    );
    
    public function getPublicUrl($addToken = false)
    {
        
        // dla cms bedzie brane z webRouter
        $router = Zend_Registry::isRegistered('webRouter') ? Zend_Registry::get('webRouter') : Zend_Registry::get('router');
        
        $params = array(
            'nick' => $this->nick,
            'usrId' => $this->id
        );

        $host = Zend_Registry::isRegistered('webRouter') ? Zend_Registry::get('config')->globals->host->web : '';

        return $host . $router->assemble($params, 'user') . ($addToken ? '?token=' . $this->getPreviewToken() : '');
    }
    

    public function getSendMailUrl()
    {
        
        // dla cms bedzie brane z webRouter
        $router = Zend_Registry::isRegistered('webRouter') ? Zend_Registry::get('webRouter') : Zend_Registry::get('router');
        
        $params = array(
            'nick' => $this->nick,
            'usrId' => $this->id
        );

        $host = Zend_Registry::isRegistered('webRouter') ? Zend_Registry::get('config')->globals->host->web : '';

        return $host . $router->assemble($params, 'sendMail2User');
    }
    
    public function getSearchData()
    {
        
        $data = array(
            'object_type' => $this->type,
            'owner_object_name' =>  'User',
            'owner_object_id' =>  $this->id,
            'owner_type' =>  SearchMapper::OWNER_TYPE_WEB,
            'title' =>  ''.$this,
            'lead' =>  '',
            'public_date' =>  $this->update_date,
            'web_available' => 1,
            'cuweb_available' => 0,
            'cms_available' => 1,
            'cums_available' => 0,
            'tags' =>  ''
        );
        // 'fulltext' =>  pobierany jest z mappera
        
        return $data;
        
    }
    
    public function areRequiredFields()
    {
        return !(
                !isset($this->status) 
                || !isset($this->name)
                || !isset($this->surname) 
                || !isset($this->nick) 
                || !isset($this->description)
                
               ); 
    }
    
    public function isAvailable()
    {
        return $this->isActive();
    }
    
    public function isActive()
    {
        return !empty($this->status) && $this->status == UserMapper::STATUS_ACTIVE;
    }

    public function isNew()
    {
        return !empty($this->status) && $this->status == UserMapper::STATUS_NEW;
    }
    
    public function __toString()
    {
        return $this->name.' '.$this->surname;
    }
    
    public function isTypeStandard()
    {
        return $this->type == UserMapper::TYPE_STANDARD || $this->type == UserMapper::TYPE_MIXED;
    }
    
    public function isTypeSocial()
    {
        return $this->type == UserMapper::TYPE_SOCIAL || $this->type == UserMapper::TYPE_MIXED;
    }
    
    public function isSocFacebook()
    {
        return !empty($this->soc_facebook);
    }
    
    public function isSocGoogleplus()
    {
        return !empty($this->soc_googleplus);
    }
    
    public function isSocTweeter()
    {
        return !empty($this->soc_tweeter);
    }
    
    public function isSocLinkedin()
    {
        return !empty($this->soc_linkedin);
    }

}
