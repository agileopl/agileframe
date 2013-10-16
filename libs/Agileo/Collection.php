<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Collection implements Iterator, Countable, ArrayAccess
{

    protected $_entities = array();

    protected $_entityClass = null;

    protected $_count = 0;

    protected $_findCache = array();

    public function __construct($args = null) 
    {
        if(is_string($args)) {
            $this->_entityClass = $args;
        } elseif (null !== $args) {
            $this->setEntities($args);
        }
    }
    
    public function setEntities($entities) 
    {
        if (!is_array($entities) && (!$entities instanceof Iterator || !$entities instanceof Countable)) {
            // TODO: Use a better Exception class
            throw new Agileo_Exception('Collection entities must be an array or implement Iterator and Countable');
        }

        $this->_entities = $entities;
        $this->_count = count($entities);

        return $this;
    }

    public function append(Agileo_Object $entity) 
    { 
        $this->_entities[] = $entity;
        $this->_count = count($this->_entities);
        return $this;
    }
    
    public function prepend(Agileo_Object $entity) 
    {
        array_unshift($this->_entities, $entity);
        $this->_count = count($this->_entities);
        return $this;
    }

    public function appendFromArray(array $entity, $entityClass = null) 
    {

        if($entityClass) {
            $this->_entityClass = $entityClass; 
        }

        $this->_entities[] = new $this->_entityClass($entity);
        $this->_count = count($this->_entities);
        return $this;
    }

    public static function create($entityClass, array $entities) 
    {
        $collection = new self($entityClass);
        foreach($entities as $item) {
            if($item instanceof Agileo_Object) {
                $collection->append($item);
            } else {
                $collection->appendFromArray($item);
            }
        }
        return $collection;
    }

    public function clear()
    {
        $this->_entities = array();
    }

    public function current() 
    {
        return $this->_ensureEntity(current($this->_entities));
    }

    public function key() 
    {
        return key($this->_entities);
    }

    public function next() 
    {
        next($this->_entities);
    }

    public function rewind() 
    {
        reset($this->_entities);
    }

    public function valid() 
    {
        return (null !== $this->key());
    }

    public function offsetExists($offset) 
    {
        return isset($this->_entities[$offset]);
    }

    public function offsetGet($offset) 
    {
        return ($this->offsetExists($offset) ? $this->_ensureEntity($this->_entities[$offset]) : null);
    }

    public function offsetSet($offset, $value) 
    {
        // TODO: Should you even be able to update a collection?
        $value = $this->_ensureEntity($value);

        $this->_entities[$offset] = $value;
        $this->_count = count($this->_entities);
    }

    public function offsetUnset($offset) 
    {
        unset($this->_entities[$offset]);
        $this->_entities = array_values($this->_entities);
        $this->_count = count($this->_entities);
    }

    public function count() 
    {
        return count($this->_entities);
    }

    public function setEntityClass($className) 
    {
        $this->_entityClass = $className;
    }

    public function getEntityClass()
    {
        return $this->_getEntityClass();
    }


    protected function _getEntityClass() 
    {
        if (null == $this->_entityClass) {
            $namespace = self::getClassNamespace($this);
            $modelName = self::getClassType($this);
            $this->_entityClass = "{$namespace}_Model_{$modelName}";
        }

        return $this->_entityClass;
    }

    protected function _ensureEntity($entity) 
    {
        $className = $this->_getEntityClass();

        if (is_array($entity)) {
            $entity = new $className($entity);
        }

        if (!$entity instanceof $className) {
            throw new InvalidArgumentException(get_class($this) . " expects all entities to be of type '{$className}', instead received : " . get_class($entity));
        }

        return $entity;
    }

    public function toArray() 
    {
        $data = array();
        foreach ($this->_entities as $entity) {
            if (is_array($entity)) {
                $className = $this->_getEntityClass();
                $entity = new $className($entity);
            }

            $data[] = $this->_ensureEntity($entity) ? $entity->toArray() : null;
        }
        return $data;
    }

    public function getAllEntityIds() 
    {
        $ids = array();
        foreach ($this as $entity) {
            $ids[] = $entity->getId();
        }
        return $ids;
    }

    public function entityIdsAsKey() 
    {
        $entities = array();
        foreach ($this as $entity) {
            $entities[$entity->getId()] = $entity;
        }
        $this->setEntities($entities);
        return $this;
    }

    public function getFirst()
    {
        reset($this->_entities);
        return current($this->_entities);
    }

    public function findBy($prop, $value) 
    {
        if(!isset($this->_findCache[$prop]))
        {
            $this->_findCache[$prop] = array();
            foreach ($this as $entity) {
                $this->_findCache[$prop][$entity->{$prop}] = $entity;
            }
        }

        return !empty($this->_findCache[$prop][$value]) ? $this->_findCache[$prop][$value] : null;

    }
    

    public function groupBy($prop, $multi = false) 
    {
        if(!isset($this->_findCache[$prop]))
        {
            $collection = clone $this;
            $this->_findCache[$prop] = array();
            foreach ($collection as $entity) {
                if($multi) {
                    $this->_findCache[$prop][$entity->{$prop}][] = $entity;
                } else {
                    $this->_findCache[$prop][$entity->{$prop}] = $entity;
                }
                
            }
        }

        return !empty($this->_findCache[$prop]) ? $this->_findCache[$prop] : array();

    }
    
    
    public function sortBy($attribute, $order = 'asc')
    {
        $this->_sortKey = $attribute;
        if ($order == 'desc') {
            uasort($this->_data, array($this, '_reverseCompare'));
        } else {
            uasort($this->_data, array($this, '_compare'));
        }
    }

    protected function _compare($x, $y)
    {
        if (is_string($x->{$this->_sortKey})) {
            $valueFirst = $this->_comparePrepare($x->{$this->_sortKey});
            $valueSecond = $this->_comparePrepare($y->{$this->_sortKey});
            return strcmp($valueFirst, $valueSecond);
        } else {
            if ($x->{$this->_sortKey} == $y->{$this->_sortKey}) {
                return 0;
            } else if ($x->{$this->_sortKey} < $y->{$this->_sortKey}) {
                return -1;
            } else {
                return 1;
            }
        }
    }

    protected function _reverseCompare($x, $y)
    {
        if (is_string($x->{$this->_sortKey})) {
            $valueFirst = $this->_comparePrepare($x->{$this->_sortKey});
            $valueSecond = $this->_comparePrepare($y->{$this->_sortKey});
            return strcmp($valueSecond, $valueFirst);
        } else {
            if ($x->{$this->_sortKey} == $y->{$this->_sortKey}) {
                return 0;
            } else if ($x->{$this->_sortKey} > $y->{$this->_sortKey}) {
                return -1;
            } else {
                return 1;
            }
        }
    }


}