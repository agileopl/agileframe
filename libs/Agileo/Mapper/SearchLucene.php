<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

abstract class Agileo_Mapper_SearchLucene extends Agileo_Mapper
{
    // @formatter:off

    public static $luceneMap = array(
        'id' => 'keyword',
        'object_name' => 'keyword',
        'object_id' => 'keyword',
        'object_type' => 'keyword', 
        'owner_object_name' => 'keyword',
        'owner_object_id' => 'keyword',
        'owner_type' => 'keyword',
        'public_date' => 'keyword',
        'refresh_date' => 'keyword',

        'web_available' => 'keyword',
        'cuweb_available' => 'keyword',
        'cms_available' => 'keyword',
        'cums_available' => 'keyword',

        'title' => 'unIndexed',
        'lead' => 'unIndexed',
        'fulltext' => 'unStored',
        'tags' => 'unStored'
    );
        
    // @formatter:on
    
    protected $_index = null;
    
    abstract public function refreshDocument(Agileo_Object $object);
    
    protected function __construct($context)
    {
        Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive ()); 
        Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');
        
        $storagePath = Zend_Registry::get('config')->search->lucene->{$context}->storage_path;
        try {
            if($context == 'test') {
                $index = Zend_Search_Lucene::create($storagePath);
            } else {
                $index = Zend_Search_Lucene::open($storagePath);
            }
        } catch (Exception $e) {
            $index = Zend_Search_Lucene::create($storagePath);
        }
        
        $this->_index = $index; 
    }
    
    public static function getTestInstance()
    {
        return self::getInstance('test');
    }
    
    protected function _getIndex()
    {
        return $this->_index;
    }
    
    protected function _createLuceneField ($fieldName, $value) 
    {
        $type = self::$luceneMap[$fieldName];
        if($type == 'unStored') {
            $value = T::normalize($value);
        }
        return Zend_Search_Lucene_Field::$type($fieldName, $value, 'utf-8');
    }
    
    
    protected function _generateId (Agileo_Object $object) 
    {
        return get_class($object).'_'.$object->id;
    }
    
    public function checkObjectInIndex($query)
    {
        Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8'); 
        $query = Zend_Search_Lucene_Search_QueryParser::parse($query);
        return $this->_index->find($query);
    }
    
    public function find ($query)
    {
        Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');
        $query = T::normalize($query);
        $query = Zend_Search_Lucene_Search_QueryParser::parse($query);
        return $this->_index->find($query);
    }
    
    public function getPaginator($query, $page, $limit)
    {
        Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');       
        $query = T::normalize($query);
        $query = Zend_Search_Lucene_Search_QueryParser::parse($query);
        $paginator = Zend_Paginator::factory($this->_index->find($query));
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);
        // Send to view
        return $paginator;
    }
    
    public function deleteFromIndex(Agileo_Object $object)
    {
        $hits = $this->_index->find('id:' . $this->_generateId ($object));
        foreach ($hits as $hit) {
            $this->_index->delete($hit->id);
        }
    }
    
}
