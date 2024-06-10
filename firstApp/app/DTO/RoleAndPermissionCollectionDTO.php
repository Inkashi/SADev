<?php

namespace App\DTO;

use Illuminate\Support\Collection;
use App\DTO\RoleAndPermissionDTO;

class RoleAndPermissionCollectionDTO
{
    public $rolesAndPermissions;

    public function __construct(Collection $rolesAndPermissions)
    {
        $this->rolesAndPermissions = $rolesAndPermissions->map(function ($rAp) {
            return new RoleAndPermissionDTO(
                $rAp->role_id,
                $rAp->permission_id,
                $rAp->created_by,
                $rAp->deleted_by
            );
        });
    }
}
