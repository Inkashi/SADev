<?php

namespace App\DTO;

use Illuminate\Support\Collection;
use App\DTO\PermissionDTO;
use App\Models\Permission;

class PermissionCollectionDTO
{
    public function __construct($permissions)
    {
        $this->permissions = $permissions->map(function ($permission) {
            return new PermissionDTO(
                $permission->name,
                $permission->description,
                $permission->code,
                $permission->created_by,
                $permission->deleted_by
            );
        });
    }
}
