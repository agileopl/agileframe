<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Admin extends Agileo_Object
{

    public function isActive()
    {
        return !empty($this->status) && $this->status == AdminMapper::STATUS_ACTIVE;
    }

    public function isAdmin()
    {
        return !empty($this->role) && $this->role == AdminMapper::ROLE_ADMIN;
    }
    public function isUser()
    {
        return !empty($this->role) && $this->role == AdminMapper::ROLE_USER;
    }

}
