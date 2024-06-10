<?php

namespace App\DTO;

use Illuminate\Support\Collection;
use App\DTO\UserDTO;
use App\Models\User;

class UserCollectionDTO
{
    public function __construct()
    {
        $users = User::all();
        $this->users = $users->map(function ($user) {
            return new UserDTO(
                $user->id,
                $user->username,
                $user->email,
                $user->birthday,
                $user->created_at
            );
        });
    }
}