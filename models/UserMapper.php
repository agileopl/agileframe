<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class UserMapper extends Agileo_Mapper_Db
{
    // @formatter:off
    public static $prefix = 'usr';
    public static $tableName = 'ag_users';
    public static $tableFields = array('usr_id', 'usr_email', 'usr_pass', 'usr_status', 'usr_type', 'usr_create_date', 'usr_update_date', 'usr_nick', 'usr_name', 'usr_surname', 'usr_avatar', 'usr_description', 'usr_auth_token'
        ,'usr_soc_update_date','usr_soc_facebook','usr_soc_googleplus','usr_soc_tweeter','usr_soc_linkedin', 'usr_contact', 'usr_bg'
        ,'usr_geo_address','usr_geo_lat','usr_geo_lng','usr_geo_zoom');

    public static $fieldsList = array('usr_id', 'usr_email', 'usr_pass', 'usr_status', 'usr_type', 'usr_create_date', 'usr_update_date', 'usr_nick', 'usr_name', 'usr_surname', 'usr_avatar',
        'usr_soc_update_date','usr_soc_facebook','usr_soc_googleplus','usr_soc_tweeter','usr_soc_linkedin',
        'usr_stat_blogit_count',
        'usr_geo_address','usr_geo_lat','usr_geo_lng','usr_geo_zoom');

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_NEW = 'NEW';
    const STATUS_BLOCKED = 'BLOCKED';
    const STATUS_DELETED = 'DELETED';

    const TYPE_STANDARD = 'STANDARD';
    const TYPE_SOCIAL = 'SOCIAL';
    const TYPE_MIXED = 'MIXED';
    
    const ROLE_STANDARD = 'STANDARD';
    const ROLE_POLITICIAN = 'POLITICIAN';
    
    // @formatter:on

    public function login($email, $pass)
    {
        
        if(!empty($email) && !empty($pass)) {
            $select = $this->_db->select()
                            ->from(self::$tableName)
                            ->where('usr_email = ?', $email)
                            ->where('usr_pass = ?', $this->_hashPassword($pass));
    
            if($data = $this->_db->query($select)->fetch()) {
                return new User($data);
            }
        }

        return null;
    }
    
    public function getByToken($token)
    {
        $select = $this->_db->select()
                        ->from(self::$tableName)
                        ->where('usr_auth_token = ?', $token);

        if($data = $this->_db->query($select)->fetch()) {
            return new User($data);
        }

        return null;
    }

    public function getByEmail($email)
    {
        $select = $this->_db->select()
                        ->from(self::$tableName)
                        ->where('usr_email = ?', $email);

        if($data = $this->_db->query($select)->fetch()) {
            return new User($data);
        }

        return null;
    }

    private function _hashPassword($pass)
    {
        return md5(Zend_Registry::get('config')->model->user->passwordSecretKey . $pass);
    }

    public function getListPager($filter, $page, $limit)
    {

        $select = $this->_db->select()
            ->from(self::$tableName, self::$fieldsList)
            ;
        ;

        $this->_listRestrict($select, $filter);

        $cSelect = clone $select;
        $cSelect->reset(Zend_Db_Select::COLUMNS)
            ->reset(Zend_Db_Select::ORDER)
            ->columns(new Zend_Db_Expr('COUNT(*) AS ' . Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN));

        $adapter = new Zend_Paginator_Adapter_DbSelect($select);
        $adapter->setRowCount($cSelect);

        $paginator = new Agileo_Paginator($adapter, 'User');
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($limit);

        return $paginator;
    }

    public function getList($filter, $limit = 10)
    {
        $select = $this->_db->select()
            ->from(self::$tableName, self::$fieldsList)
            ;
        ;
        
        $this->_listRestrict($select, $filter);
        
        $select->limit($limit);
        
        return Agileo_Collection::create('User', $this->_db->query($select)->fetchAll());
    }


    protected function _listRestrict($select, $filter)
    {
        if(!empty($filter['available'])) {
            $select->where("usr_status= ?", self::STATUS_ACTIVE);
        } else {
            if(!empty($filter['status'])) {
                $select->where("usr_status = ?", $filter['status']);
            } else {
                $select->where("usr_status <> ?", UserMapper::STATUS_DELETED);
            }
        }

        if(!empty($filter['nick'])) {
            $select->where("usr_nick like ?", '%'.$filter['nick'].'%');
        }

        if(!empty($filter['search'])) {
            
            $term = '%'.$filter['search'].'%';
            
            $select->where("usr_nick like '{$term}' or usr_name like '{$term}' or usr_surname like '{$term}' or usr_email like '{$term}'");
        }

        
        if (!empty($filter['order'])){
            
            switch($filter['order']) {
                case 'mostActive' : $select->order('usr_most_active desc');break; 
                case 'mostPopular' : $select->order('usr_most_popular desc');break; 
                case 'lastActivity' : $select->order('usr_last_activity desc');break; 
                case 'createDate' : $select->order('usr_create_date desc');break; 
                case 'alphabetically' : $select->order('usr_nick asc');break; 
                default : $select->order($filter['order']); 
            }
        } else {
            $select->order('usr_id desc');
        }

    }

    public function save(User $user) 
    {

        if(!empty($user->pass)) {
            $user->pass = $this->_hashPassword($user->pass);
        }
        if(empty($user->id)) {
            $user->create_date = date('Y-m-d H:i:s'); 
        }
        
        $user->update_date = date('Y-m-d H:i:s');
        
        return $this->_save($user);
    } 
    
    public function checkPass($id, $pass)
    {
        if(!empty($id) && !empty($pass)) {
            
            $select = $this->_db->select()
                            ->from(self::$tableName)
                            ->where('usr_id = ?', $id)
                            ->where('usr_pass = ?', $this->_hashPassword($pass));
    
            if($data = $this->_db->query($select)->fetch()) {
                return true;
            }
            
        }
        
        return false;
    }
    
    public function delete(User $user) 
    {
        $oldObject = clone $user;
        
        $user->status = self::STATUS_DELETED;
        // TOTO zmienić dane osobowe
        
        $startTransaction = $this->beginTransaction();
        try {
            
            if($this->_save($user)) {
                ChangeLogMapper::getMasterInstance()->saveLog($user, $oldObject);
            }
                
            $this->commit($startTransaction);
            
            return true;
            
        } catch (Exception $e) {
            $this->rollBack($startTransaction);
            throw $e;
        }
        
        
        return false;
    } 
    
    public function getBySocFacebook($fbId)
    {
        $select = $this->_db->select()
                        ->from(self::$tableName)
                        ->where('usr_soc_facebook = ?', $fbId);

        if($data = $this->_db->query($select)->fetch()) {
            return new User($data);
        }

        return null;
    }

    public function getSearchFulltextForObject(User $user)
    {
        $fulltext = ''.$user.' '.$user->nick.' '.$user->description;
        return trim(strip_tags($fulltext)); 
    }
    
}
