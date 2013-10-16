<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class SearchPaginator extends Zend_Paginator
{

    protected $_objectName = null;

    public function __construct($adapter, $objectName)
    {
        $this->_objectName = $objectName;
        parent::__construct($adapter);
    }

    public function getItemsByPage($page)
    {
        $items = parent::getItemsByPage($page);
        $itemsData = array();
        foreach($items as $item) {
            $fieldNames = $item->getDocument()->getFieldNames();
            $data = array();
            foreach($fieldNames as $fieldName) {
                $data['ash_'.$fieldName] = $item->getDocument()->getFieldValue($fieldName);
            }
            $itemsData[] = $data;     
        }
        
        return Agileo_Collection::create($this->_objectName, $itemsData);
    }
    
    public static function factory($data, $objectName, $adapter = self::INTERNAL_ADAPTER,
                                   array $prefixPaths = null)
    {

        if ($data instanceof Zend_Paginator_AdapterAggregate) {
            return new self($data->getPaginatorAdapter());
        } else {
            if ($adapter == self::INTERNAL_ADAPTER) {
                if (is_array($data)) {
                    $adapter = 'Array';
                } else if ($data instanceof Zend_Db_Table_Select) {
                    $adapter = 'DbTableSelect';
                } else if ($data instanceof Zend_Db_Select) {
                    $adapter = 'DbSelect';
                } else if ($data instanceof Iterator) {
                    $adapter = 'Iterator';
                } else if (is_integer($data)) {
                    $adapter = 'Null';
                } else {
                    $type = (is_object($data)) ? get_class($data) : gettype($data);

                    /**
                     * @see Zend_Paginator_Exception
                     */
                    require_once 'Zend/Paginator/Exception.php';

                    throw new Zend_Paginator_Exception('No adapter for type ' . $type);
                }
            }

            $pluginLoader = parent::getAdapterLoader();

            if (null !== $prefixPaths) {
                foreach ($prefixPaths as $prefix => $path) {
                    $pluginLoader->addPrefixPath($prefix, $path);
                }
            }

            $adapterClassName = $pluginLoader->load($adapter);
            
            return new self(new $adapterClassName($data), $objectName);
        }
    }


}