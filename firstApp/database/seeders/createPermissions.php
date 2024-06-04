<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class createPermissions extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'get-list-user', 'description' => 'can get list of users', 'code' => '1', 'created_by' => '1'],
            ['name' => 'read-user', 'description' => 'can read user', 'code' => '2', 'created_by' => '1'],
            ['name' => 'create-user', 'description' => 'can create user', 'code' => '3', 'created_by' => '1'],
            ['name' => 'update-user', 'description' => 'can update user', 'code' => '4', 'created_by' => '1'],
            ['name' => 'delete-user', 'description' => 'can delete user', 'code' => '5', 'created_by' => '1'],
            ['name' => 'restore-user', 'description' => 'can restore user', 'code' => '6', 'created_by' => '1'],

            ['name' => 'get-list-role', 'description' => 'can get list of roles', 'code' => '7', 'created_by' => '1'],
            ['name' => 'read-role', 'description' => 'can read role', 'code' => '8', 'created_by' => '1'],
            ['name' => 'create-role', 'description' => 'can create role', 'code' => '9', 'created_by' => '1'],
            ['name' => 'update-role', 'description' => 'can update role', 'code' => '10', 'created_by' => '1'],
            ['name' => 'delete-role', 'description' => 'can delete role', 'code' => '11', 'created_by' => '1'],
            ['name' => 'restore-role', 'description' => 'can restore deleted role', 'code' => '12', 'created_by' => '1'],

            ['name' => 'get-list-permission', 'description' => 'can get list of permissions', 'code' => '13', 'created_by' => '1'],
            ['name' => 'read-permission', 'description' => 'can read permission', 'code' => '14', 'created_by' => '1'],
            ['name' => 'create-permission', 'description' => 'can create permission', 'code' => '15', 'created_by' => '1'],
            ['name' => 'update-permission', 'description' => 'can update permission', 'code' => '16', 'created_by' => '1'],
            ['name' => 'delete-permission', 'description' => 'can delete permission', 'code' => '17', 'created_by' => '1'],
            ['name' => 'restore-permission', 'description' => 'can restore permission', 'code' => '18', 'created_by' => '1'],
            ['name' => 'get-logs-user', 'description' => 'can check logs of user', 'code' => '19', 'created_by' => '1'],
            ['name' => 'get-logs-role', 'description' => 'can check logs of role', 'code' => '20', 'created_by' => '1'],
            ['name' => 'get-logs-permission', 'description' => 'can check logs of permission', 'code' => '21', 'created_by' => '1'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
