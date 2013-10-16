<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

abstract class Agileo_Object
{

    protected $_data = array();
    protected $_dataPrefix = NULL;

    protected $_subObjects = array();
    protected $_subObjectsConfig = array(); // array ('{Nazwa_Obiektu}'=>'{klucz}', 'Marrow_Node' => 'nnd_id')
    protected $_subObjectsCollections = array();

    public function __construct($data = null)
    {

        if(empty($data)) {
            $data = array();
        } else {
            $data = (array)$data;
        }

        $this->setData($data);

        $this->populate($data);

        return $this;
    }

    public function getPrefix()
    {
        if(!isset($this->_dataPrefix)) {
            $mapperName = get_class($this) . 'Mapper';
            $this->_dataPrefix = $mapperName::$prefix;
        }
        return $this->_dataPrefix;
    }

    final public function setData(array $data)
    {
        foreach ($data as $key => $v) {
            if(strpos($key, $this->getPrefix().'_') === 0) {
                $this->_data[$key] = $v;
                unset($data[$key]); 
            }
        }

        // podłącz sub obiekty
        foreach($this->_subObjectsConfig as $subObjectName => $subObjectKey) {
            if(!empty($data[$subObjectKey])) {
                $subObject = new $subObjectName($data);
                $this->setSubObject($subObject);
            }
        }

    }

    // metoda umozliwiająca wypełnianie obiektu przy inicjacli - najczęściej rozszżana w konkretnym obiekcie 
    public function populate($data) 
    {

    }

    public function __set($name, $value)
    {
        $this->_data[$this->getPrefix().'_'.$name] = $value;
        return $this;
    }
    public function __isset($name)
    {
        return array_key_exists($this->getPrefix().'_'.$name, $this->_data);
    }
    public function __unset($name)
    {
        unset($this->_data[$this->getPrefix().'_'.$name]);
    }

    public function __get($name)
    {
        $name = $this->getPrefix().'_'.$name;
        return array_key_exists($name, $this->_data) ? $this->_data[$name] : NULL;  
    }

    public function __call($name, $arguments)
    {
        $command = substr($name, 0, 3);
        $propName = substr(strtolower(preg_replace('/([A-Z])/', '_$1', substr($name, 3))),1);
        switch($command) {
            case 'get' : return $this->$propName;
            case 'has' : return isset($this->$propName);
            case 'set' : $this->__set($propName, reset($arguments)); return $this;
            default : throw new Agileo_Exception('Unknow method ' . $name . ' at model ' . get_class($this));

        }
    }

    public function toArray() 
    {
        return $this->_data;
    }

    public function hasSubObject($objectName)
    {
        return !empty($this->_subObjects[$objectName]);
    }

    public function getSubObject($objectName)
    {
        if(!$this->hasSubObject($objectName)) {
            throw new Agileo_Exception('Brak zdefiniowanego subobiektu: '.$objectName);
        }
        return $this->_subObjects[$objectName];
    }

    public function setSubObject(Agileo_Object $subObject)
    {
        $this->_subObjects[get_class($subObject)] = $subObject;
        return $this;
    }

    public function addSubCollectionObject(Agileo_Object $subObject, $collectionName = NULL)
    {
        if(empty($collectionName)) {
            $collectionName = get_class($subObject);
        }
        if(!isset($this->_subObjectsCollections[$collectionName])) {
            $this->_subObjectsCollections[$collectionName] = Agileo_Collection::create(get_class($subObject), array());
        }  
        $this->_subObjectsCollections[$collectionName]->append($subObject);
        return $this;
    }
    
    public function setSubCollection(Agileo_Collection $collection, $collectionName = NULL)
    {
        if(empty($collectionName)) {
            $collectionName = $collection->getEntityClass();
        }
        $this->_subObjectsCollections[$collectionName] = $collection;
        return $this;
    }
    
    public function hasSubCollection($collectionName)
    {
        return !empty($this->_subObjectsCollections[$collectionName]);
    }

    public function getSubCollection($collectionName)
    {
        if(!empty($this->_subObjectsCollections[$collectionName])) {
            return $this->_subObjectsCollections[$collectionName];
        }
        return NULL;
    }

    public function checkPreviewToken($token) 
    {
        return !empty($token) && $this->getPreviewToken() == $token; 
    }

    public function getPreviewToken() 
    {
        return md5(Zend_Registry::get('config')->globals->previewChecker->salt.'_'.$this->id);
    }

}
