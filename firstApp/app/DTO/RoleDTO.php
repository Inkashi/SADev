<?php

namespace App\DTO;

class RoleDTO
{
    public $name;
    public $description;
    public $code;
    public $created_by;
    public $deleted_by;


    public function __construct(
        $name,
        $description,
        $code,
        $created_by,
        $deleted_by
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->code = $code;
        $this->created_by = $created_by;
        $this->deleted_by = $deleted_by;
    }
}
