<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class SearchMapper extends Agileo_Mapper_SearchLucene
{
    
    public static $prefix = 'ash';

    public static $fields = array(
        'ash_object_name', 'ash_object_id', 'ash_object_type', 'ash_owner_object_name', 'ash_owner_object_id', 'ash_owner_type',
        'ash_title', 'ash_lead', 'ash_fulltext', 'ash_tags', 'ash_public_date', 'ash_update_date',
        'ash_web_available', 'ash_cms_available'
    );
    
    const OWNER_TYPE_WEB = 'web';
    const OWNER_TYPE_CMS = 'cms';
    const OWNER_TYPE_CUMS = 'cums';
    
    public function getPaginatorWithSubObject($query, $page, $limit)
    {
        $paginator = $this->getPaginator($query, $page, $limit);
        
        if(count($paginator) > 0) {
            $byObjectsIds = array();
            foreach($paginator->getCurrentItems() as $item) {
                $byObjectsIds[$item->object_name][$item->object_id] = $item; 
            }
            
            $this->_joinSubObjects ($byObjectsIds);
            
        }

        return $paginator;
    }
    
    protected function _joinSubObjects ($byObjectsIds)
    {
        
        // User
        if(!empty($byObjectsIds['User']))
        {
            $ids = array_keys($byObjectsIds['User']);

            $collection = UserMapper::getInstance($this->context)->getByIds($ids, array(), UserMapper::$fieldsList);
            foreach($collection as $item) {
                if(!empty($byObjectsIds['User'][$item->id])) {
                    $byObjectsIds['User'][$item->id]->setSubObject($item);                    
                }
            }
        }

        
    }
    
    
    public function getPaginator(array $filter, $page, $limit)
    {
        
        $query = new Zend_Search_Lucene_Search_Query_Boolean();
        
        if(!empty($filter['query'])) {
            $q = T::normalize($filter['query']);
            if(strpos($q, '*') === false) {
                $q = $q.'*';
            }

            $q = Zend_Search_Lucene_Search_QueryParser::parse($q);
            $query->addSubquery($q, true /* required */);
        }

        if(!empty($filter['refresh_date_from']) && !empty($filter['refresh_date_to'])) {
            $subQuery = Zend_Search_Lucene_Search_QueryParser::parse('refresh_date:['.date('YmdHis', strtotime($filter['refresh_date_from'])).' TO '.date('YmdHis', strtotime($filter['refresh_date_to'])).']');
            $query->addSubquery($subQuery, true);
        }

        if(!empty($filter['public_date_from']) && !empty($filter['public_date_to'])) {
            $subQuery = Zend_Search_Lucene_Search_QueryParser::parse('public_date:['.date('YmdHis', strtotime($filter['public_date_from'])).' TO '.date('YmdHis', strtotime($filter['public_date_to'])).']');
            $query->addSubquery($subQuery, true);
        }
        
        if(!empty($filter['tag'])) {
            $tags = $this->_encodeTag ($filter['tag']);
            $query->addSubquery(Zend_Search_Lucene_Search_QueryParser::parse('tags:'.$tags), true);
        }
        

        if(!empty($filter['object_name'])) {
            $query->addSubquery(Zend_Search_Lucene_Search_QueryParser::parse('object_name:'.$filter['object_name']), true);
        }
        
        if(!empty($filter['object_id'])) {
            $query->addSubquery(Zend_Search_Lucene_Search_QueryParser::parse('object_id:'.$filter['object_id']), true);
        }

        if(!empty($filter['object_type'])) {
            $query->addSubquery(Zend_Search_Lucene_Search_QueryParser::parse('object_type:'.$filter['object_type']), true);
        }

        if(!empty($filter['owner_object_name'])) {
            $query->addSubquery(Zend_Search_Lucene_Search_QueryParser::parse('owner_object_name:'.$filter['owner_object_name']), true);
        }

        if(!empty($filter['owner_object_id'])) {
            $query->addSubquery(Zend_Search_Lucene_Search_QueryParser::parse('owner_object_id:'.$filter['owner_object_id']), true);
        }

        if(!empty($filter['owner_type'])) {
            $query->addSubquery(Zend_Search_Lucene_Search_QueryParser::parse('owner_type:'.$filter['owner_type']), true);
        }

        if(!empty($filter['web_available'])) {
            $query->addSubquery(Zend_Search_Lucene_Search_QueryParser::parse('web_available:1'), true);
        }
        if(!empty($filter['cms_available'])) {
            $query->addSubquery(Zend_Search_Lucene_Search_QueryParser::parse('cms_available:1'), true);
        }


        $paginator = SearchPaginator::factory($this->_index->find($query), 'Search');
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);
        // Send to view
        return $paginator;
    }
    
    public function refreshDocument(Agileo_Object $object)
    {

        if($object instanceof SearchInterface) {
            
            $this->deleteFromIndex($object);
            
            if($object->isAvailable()) {
                
                $doc = new Zend_Search_Lucene_Document();
                
                $searchData = $object->getSearchData();
                
                $doc->addField($this->_createLuceneField ('id', $this->_generateId ($object)));
                $doc->addField($this->_createLuceneField ('object_name', get_class($object)));
                $doc->addField($this->_createLuceneField ('object_id', $object->id));
                $doc->addField($this->_createLuceneField ('refresh_date', date('YmdHis')));
                
                if(!empty($searchData['object_type'])) {
                    $doc->addField($this->_createLuceneField ('object_type', $searchData['object_type']));
                }
                if(!empty($searchData['owner_object_name'])) {
                    $doc->addField($this->_createLuceneField ('owner_object_name', $searchData['owner_object_name']));
                }
                if(!empty($searchData['owner_object_id'])) {
                    $doc->addField($this->_createLuceneField ('owner_object_id', $searchData['owner_object_id']));
                }
                if(!empty($searchData['owner_type'])) {
                    $doc->addField($this->_createLuceneField ('owner_type', $searchData['owner_type']));
                }
                if(!empty($searchData['title'])) {
                    $doc->addField($this->_createLuceneField ('title', $searchData['title']));
                }
                if(!empty($searchData['lead'])) {
                    $doc->addField($this->_createLuceneField ('lead', $searchData['lead']));
                }
                if(!empty($searchData['lead'])) {
                    $doc->addField($this->_createLuceneField ('lead', $searchData['lead']));
                }
                if(!empty($searchData['tags'])) {
                    $doc->addField($this->_createLuceneField ('tags', $this->_encodeTag($searchData['tags'])));
                }
                if(!empty($searchData['public_date'])) {
                    $doc->addField($this->_createLuceneField ('public_date', date('YmdHis', strtotime($searchData['public_date']))));
                }

                $doc->addField($this->_createLuceneField ('web_available', !empty($searchData['web_available']) ? 1 : 0));
                $doc->addField($this->_createLuceneField ('cuweb_available', !empty($searchData['cuweb_available']) ? 1 : 0));
                $doc->addField($this->_createLuceneField ('cms_available', !empty($searchData['cms_available']) ? 1 : 0));
                $doc->addField($this->_createLuceneField ('cums_available', !empty($searchData['cums_available']) ? 1 : 0));
                
                if(isset($searchData['fulltext'])) {
                    $doc->addField($this->_createLuceneField ('fulltext', !empty($searchData['fulltext']) ? $searchData['fulltext'] : ''));
                } else {
                    // dociąg dane full text
                    $mapperName = get_class($object) . 'Mapper';
                    $mapper = $mapperName::getMasterInstance();
                    $fulltext = $mapper->getSearchFulltextForObject($object);
                    $doc->addField($this->_createLuceneField ('fulltext', $fulltext));
                }
                
                $this->_index->addDocument($doc);
            }

            
        } else {
            throw new Agileo_Exception(get_class($object). ' is not instance of SearchInterface' );
        }

    }

    protected function _encodeTag ($tags) {
        $tags = preg_replace('/\s+/', '_', $tags);
        $tags = str_replace(',',' ', $tags);
        $tags = T::normalize($tags);
        return $tags;
    }
    
}
