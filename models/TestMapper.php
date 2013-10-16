<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class TestMapper extends Agileo_Mapper_Db
{
    static public $prefix = 'tst';
    static public $tableName = 'test';
    static public $tableFields = array('tst_id', 'tst_title');

    public function getList()
    {

        $data = array(
            array('tst_id'=>1, 'tst_title'=>'Test 1'),
            array('tst_id'=>2, 'tst_title'=>'Test 2'),
            array('tst_id'=>3, 'tst_title'=>'Test 3'),
            array('tst_id'=>4, 'tst_title'=>'Test 4'),
            array('tst_id'=>5, 'tst_title'=>'Test 5'),
            array('tst_id'=>6, 'tst_title'=>'Test 6'),
        );
        return Agileo_Collection::create($this->getObjectName(), $data);
    }

}
