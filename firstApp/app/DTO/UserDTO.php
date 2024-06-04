<?php

namespace App\DTO;

use App\Models\UsersAndRoles;
use App\Models\Role;
use App\Models\RolesAndPermissions;
use App\Models\User;

class UserDTO
{
    public $id;
    public $username;
    public $email;
    public $birthday;
    public $created_at;
    public $roles;
    public $permissions;

    public function __construct($id, $username, $email, $birthday, $created_at)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->birthday = $birthday;
        $this->created_at = $created_at;
        $roles = User::find($id)->roles();
        $this->roles = $roles->pluck('name');
        $this->permissions = $roles->pluck('id')->map(function ($id) {
            return Role::find($id)->permissions()->pluck('name');
        });
    }
}
