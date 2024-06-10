<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, SoftDeletes;

    public $table = 'permissions';

    protected $fillable = [
        'name',
        'description',
        'code',
        'created_by',
        'deleted_by'
    ];
}
