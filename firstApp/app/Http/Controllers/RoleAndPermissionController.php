<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeRoleAndPermissionRequest;
use Illuminate\Http\Request;
use App\Models\RolesAndPermissions;
use App\Models\Permission;
use App\Http\Requests\CreateRoleAndPermissionRequest;

class RoleAndPermissionController extends Controller
{
    public function getRolePermission(Request $request)
    {
        $role_id = $request->id;

        $permissions_id = RolesAndPermissions::select('permission_id')->where('role_id', $role_id)->get();

        $permissions = $permissions_id->map(function ($id) {
            return Permission::where('id', $id->permission_id)->first();
        });

        return response()->json($permissions);
    }

    public function addRolePermission(CreateRoleAndPermissionRequest $request)
    {
        $role_id = $request->id;
        $permission_id = $request->permission_id;
        $user_id = $request->user()->id;

        $count = RolesAndPermissions::where('role_id',$role_id)->where('permission_id',$permission_id)->count();
        if($count) {
            return response()->json(['status'=> 501]);
        }

        RolesAndPermissions::create([
            'role_id' => $role_id,
            'permission_id' => $permission_id,
            'created_by' => $user_id
        ]);

        return response()->json(['status'=> 200]);
    }

    public function hardDeleteRolePermission(ChangeRoleAndPermissionRequest $request) {
    	$role_id = $request->id;
    	$permission_id = $request->permission_id;

    	$RolesAndPermissions = RolesAndPermissions::withTrashed()->where('role_id', $role_id)->where('permission_id', $permission_id);

    	$RolesAndPermissions->forcedelete();

    	return response()->json(['status' => '200']);
    }

    public function softDeleteRolePermission(ChangeRoleAndPermissionRequest $request) {
    	$role_id = $request->id;
    	$permission_id = $request->permission_id;

    	$RolesAndPermissions = RolesAndPermissions::withTrashed()->where('role_id', $role_id)->where('permission_id', $permission_id)->first();

    	$RolesAndPermissions->deleted_by = $request->user()->id;
    	$RolesAndPermissions->delete();
    	$RolesAndPermissions->save();

    	return response()->json(['status' => '200']);
    }

    public function restoreDeletedRolePermission(ChangeRoleAndPermissionRequest $request) {
    	$role_id = $request->id;
    	$permission_id = $request->permission_id;

    	$RolesAndPermissions = RolesAndPermissions::withTrashed()->where('role_id', $role_id)->where('permission_id', $permission_id)->first();

    	$RolesAndPermissions->restore();
    	$RolesAndPermissions->deleted_by = null;
    	$RolesAndPermissions->save();
    	
    	return response()->json(['status' => '200']);
    }
}
