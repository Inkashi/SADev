<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UsersAndRoles;
use App\Models\Role;
use App\Models\RolesAndPermissions;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class checkRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $actions = [
            'getUsers' => 'get-list-user',
            'getUserRoles' => 'read-user',
            'giveUserRoles' => 'update-user',
            'hardDeleteUserRole' => 'update-user',
            'softDeleteUserRole' => 'update-user',
            'restoreDeletedUserRole' => 'update-user',
            'hardDeleteUser' => 'delete-user',
            'softDeleteUser' => 'delete-user',
            'restoreDeletedUser' => 'delete-user',
            'changeUserData' => 'update-user',
            'getRoles' => 'get-list-role',
            'getTargetRole' => 'read-role',
            'createRole' => 'create-role',
            'updateRole' => 'update-role',
            'hardDeleteRole' => 'delete-role',
            'softDeleteRole' => 'delete-role',
            'restoreDeletedRole' => 'restore-role',
            'getPermissions' => 'get-list-permission',
            'getTargetPermission' => 'read-permission',
            'createPermission' => 'create-permission',
            'updatePermission' => 'update-permission',
            'hardDeletePermission' => 'delete-permission',
            'softDeletePermission' => 'delete-permission',
            'restoreDeletedPermission' => 'restore-permission',
            'getRolePermission' => 'read-role',
            'addRolePermission' => 'update-role',
            'hardDeleteRolePermission' => 'update-role',
            'softDeleteRolePermission' => 'update-role',
            'restoreDeletedRolePermission' => 'update-role',
            'updateInformation' => 'read-user',
        ];

        //проверка наличия нужного permission
        $roles = User::find(Auth::id())->roles();
        $userPermissions = $roles->pluck('id')->map(function ($id) {
            return Role::find($id)->permissions()->pluck('name');
        });
        $userPermissions =  $userPermissions->flatten()->toArray();
        $commonElements = array_intersect($actions, $userPermissions);
        $route = $request->route();
        $action = explode('@', $route->getActionName())[1];
        if (array_key_exists($action, $commonElements)) {
            return $next($request);
        } else {
            return response()->json(['error' => "You need this permission -> " . $actions[$action]], 403);
        }
    }
}
