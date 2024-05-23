<?php

namespace App\DTO;

class UserDTO
{
    public $id;
    public $username;
    public $email;
    public $birthday;
    public $created_at;

    public function __construct($id, $username, $email, $birthday, $created_at)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->birthday = $birthday;
        $this->created_at = $created_at;
    }
}
