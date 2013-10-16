<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos
 * (http://www.agileo.pl)
 * @license    MIT license
 */

class AdminMapper extends Agileo_Mapper_Db
{
    // @formatter:off
    public static $prefix = 'adm';
    public static $tableName = 'ag_admins';
    public static $tableFields = array('adm_id', 'adm_login', 'adm_pass', 'adm_status', 'adm_role', 'adm_adg_id', 'adm_name', 'adm_surname', 'adm_email', 'adm_create_date', 'adm_avatar', 'adm_cms_bg');

    public static $fieldsList = array('adm_id', 'adm_login', 'adm_status', 'adm_role', 'adm_adg_id', 'adm_name', 'adm_surname', 'adm_email', 'adm_create_date', 'adm_avatar', 'adm_cms_bg');

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_NEW = 'NEW';
    const STATUS_BLOCKED = 'BLOCKED';
    const STATUS_DELETED = 'DELETED';

    const ROLE_ADMIN = 'ADMIN';
    const ROLE_USER = 'USER';
    // @formatter:on

    public function login($login, $pass)
    {
        $select = $this->_db->select()->from(self::$tableName)->where('adm_login = ?', $login)->where('adm_pass = ?', $this->_hashPassword($pass));

        if ($data = $this->_db->query($select)->fetch()) {
            return new Admin($data);
        }

        return null;
    }

    private function _hashPassword($pass)
    {
        return md5(Zend_Registry::get('config')->model->admin->passwordSecretKey . $pass);
    }

    public function getListPager($filter, $page, $limit)
    {

        $select = $this->_db->select()->from(self::$tableName, self::$fieldsList); ;

        if (!empty($filter['status'])) {
            $select->where("adm_status = ?", $filter['status']);
        } else {
            $select->where("adm_status <> ?", AdminMapper::STATUS_DELETED);
        }

        if (!empty($filter['order'])) {
            $select->order($filter['order']);
        } else {
            $select->order('adm_id desc');
        }

        $cSelect = clone $select;
        $cSelect->reset(Zend_Db_Select::COLUMNS)->reset(Zend_Db_Select::ORDER)->columns(new Zend_Db_Expr('COUNT(*) AS ' . Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN));

        $adapter = new Zend_Paginator_Adapter_DbSelect($select);
        $adapter->setRowCount($cSelect);

        $paginator = new Agileo_Paginator($adapter, 'Admin');
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($limit);

        return $paginator;
    }

    public function save(Admin $admin)
    {

        if (!empty($admin->pass)) {
            $admin->pass = $this->_hashPassword($admin->pass);
        }
        if (empty($admin->id)) {
            $admin->create_date = date('Y-m-d H:i:s');
        }

        return $this->_save($admin);
    }

    public function delete($id)
    {
            
        if(!$admin = $this->getById($id)) {
            throw new Exception('Błędne dane wejściowe');
        }
        
        $admin->id = $id;
        $admin->login = 'D'.time().'_'.$admin->login;
        $admin->pass = md5($admin->pass);
        $admin->status = self::STATUS_DELETED;
        
        return $this->_save($admin);
    }

    public function checkPass($id, $pass)
    {
        $select = $this->_db->select()->from(self::$tableName)->where('adm_id = ?', $id)->where('adm_pass = ?', $this->_hashPassword($pass));

        if ($data = $this->_db->query($select)->fetch()) {
            return true;
        }

        return false;
    }

}
