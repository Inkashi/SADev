<?php

namespace App\DTO;

use Illuminate\Support\Collection;
use App\DTO\RoleDTO;
use App\Models\Role;

class RoleCollectionDTO
{
    public function __construct($roles)
    {
        $this->roles = $roles->map(function ($role) {
            return new RoleDTO(
                $role->name,
                $role->description,
                $role->code,
                $role->created_by,
                $role->deleted_by
            );
        });
    }
}
