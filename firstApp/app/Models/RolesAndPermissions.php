<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolesAndPermissions extends Model
{
    use HasFactory, SoftDeletes;

    public $table = 'RolesAndPermissions';

    protected $fillable = [
        'name',
        'role_id',
        'permission_id',
        'created_by',
        'deleted_by'
    ];
}
