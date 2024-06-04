<?php

namespace App\DTO;

class LogsDTO
{
    public $table_name;
    public $row_id;
    public $value_before;
    public $value_after;

    public function __construct($table_name, $row_id, $value_before, $value_after)
    {
        $this->table_name = $table_name;
        $this->row_id = $row_id;
        $this->value_before = $value_before;
        $this->value_after = $value_after;
    }
}
