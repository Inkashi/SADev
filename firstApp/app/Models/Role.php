<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    public $table = 'roles';

    protected $fillable = [
        'name',
        'description',
        'code',
        'created_by',
        'deleted_by'
    ];

    public function permissions()
    {
        $role_id = $this->id;

        $permissions_id = RolesAndPermissions::select('permission_id')->where('role_id', $role_id)->get();

        $permissions = $permissions_id->map(function ($id) {
            return Permission::where('id', $id->permission_id)->first();
        });

        return $permissions;
    }
}
