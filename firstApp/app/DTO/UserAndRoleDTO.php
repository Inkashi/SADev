<?php

namespace App\DTO;

class UserAndRoleDTO
{
    public $role_id;
    public $user_id;
    public $created_by;
    public $deleted_by;


    public function __construct($role_id,
     $user_id, $created_by, $deleted_by)
    {
        $this->role_id = $role_id;
        $this->user_id = $user_id;
        $this->created_by = $created_by;
        $this->deleted_by = $deleted_by;
    }
}