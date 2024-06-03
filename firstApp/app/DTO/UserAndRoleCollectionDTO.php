<?php

namespace App\DTO;

use Illuminate\Support\Collection;
use App\DTO\UserAndRoleDTO;

class UserAndRoleCollectionDTO
{
    public function __construct($usersAndRoles)
    {
        $this->usersAndRoles = $usersAndRoles->map(function ($uAr) {
            return new UserAndRoleDTO(
                $uAr->user_id,
                $uAr->role_id,
                $uAr->created_by,
                $uAr->deleted_by
            );
        });
    }
}
