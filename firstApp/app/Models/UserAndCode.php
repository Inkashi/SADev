<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAndCode extends Model
{
    use HasFactory;

    public $table = 'usersAndCode';

    protected $fillable = [
        'user_id',
        'code',
        'time_to_expire',
        'refreshCode'
    ];
}
