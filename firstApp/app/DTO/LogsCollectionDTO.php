<?php

namespace App\DTO;

use Illuminate\Support\Collection;
use App\DTO\LogsDTO;
use App\Models\ChangeLogs;

class LogsCollectionDTO
{
    public function __construct($logs)
    {
        $this->logs = $logs->map(function ($log) {
            return new LogsDTO(
                $log->table_name,
                $log->row_id,
                $log->value_before,
                $log->value_after
            );
        });
    }
}
