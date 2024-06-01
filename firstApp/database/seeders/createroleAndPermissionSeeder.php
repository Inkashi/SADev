<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RolesAndPermissions;

class createroleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleAndPermissions = [
            ["role_id" => "1", "permission_id" => "1", "created_by" => "1"],
            ["role_id" => "1", "permission_id" => "2", "created_by" => "1"],
            ["role_id" => "1", "permission_id" => "3", "created_by" => "1"],
            ["role_id" => "1", "permission_id" => "4", "created_by" => "1"],
            ["role_id" => "1", "permission_id" => "5", "created_by" => "1"],
            ["role_id" => "1", "permission_id" => "6", "created_by" => "1"],
            ["role_id" => "1", "permission_id" => "7", "created_by" => "1"],
            ["role_id" => "1", "permission_id" => "8", "created_by" => "1"],
            ["role_id" => "1", "permission_id" => "9", "created_by" => "1"],
            ["role_id" => "1", "permission_id" => "10", "created_by" => "1"],
            ["role_id" => "1", "permission_id" => "11", "created_by" => "1"],
            ["role_id" => "1", "permission_id" => "12", "created_by" => "1"],
            ["role_id" => "1", "permission_id" => "13", "created_by" => "1"],
            ["role_id" => "1", "permission_id" => "14", "created_by" => "1"],
            ["role_id" => "1", "permission_id" => "15", "created_by" => "1"],
            ["role_id" => "1", "permission_id" => "16", "created_by" => "1"],
            ["role_id" => "1", "permission_id" => "17", "created_by" => "1"],
            ["role_id" => "1", "permission_id" => "18", "created_by" => "1"],

            ["role_id" => "2", "permission_id" => "1", "created_by" => "1"],
            ["role_id" => "2", "permission_id" => "2", "created_by" => "1"],
            ["role_id" => "2", "permission_id" => "7", "created_by" => "1"],
            ["role_id" => "2", "permission_id" => "13", "created_by" => "1"],

            ["role_id" => "3", "permission_id" => "1", "created_by" => "1"],
            ["role_id" => "3", "permission_id" => "2", "created_by" => "1"],
        ];

        foreach ($roleAndPermissions as $roleAndPermission) {
            RolesAndPermissions::create($roleAndPermission);
        }
    }
}
