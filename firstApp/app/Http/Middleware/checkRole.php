<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpFoundation\Response;

class checkRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $actions = [
            'getUserRoles' => 'read-user',
            'giveUserRoles' => 'update-user',
            'hardDeleteRole' => 'update-user',
            'softDeleteRole' => 'update-user',
            'restoreDeletedRole' => 'update-user',

            'getRoles' => 'get-list-role',
            'getTargetRole' => 'read-role',
            'createRole' => 'create-role',
            'updateRole' => 'update-rol',
            'hardDeleteRole' => 'delete-rol',
            'softDeleteRole' => 'delete-rol',
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

            'deleteUser' => 'delete-user',
            'changeUserData' => 'update-user'
        ];

        $route = $request->route();
        $action = explode('@', $route->getActionName())[1];
        $user = $request->user();
        if ($user) {
            $userRoles = $request->user()->roles();
            $roles = [];
            foreach ($userRoles as $role) {
                array_push($roles, $role->id);
            }
            $admin = in_array(1, $roles);
            $user = in_array(2, $roles);
            $guest = in_array(3, $roles);
            if ($admin and ($action == 'hardDeleteRole' or $action == 'softDeleteRole')) {
                if ($request->user()->id == $request->id and $request->role_id == 1) {
                    return response()->json(['message' => 'Админа не трогать'], 403);
                }
            }

            if ($user and !$admin) {
                if ($action != 'getUsers' and $action != 'getUserRoles') {
                    return response()->json(['message' => $actions[$action]], 403);
                } elseif ($action == 'getUserRoles') {
                    if ($request->user()->id != $request->id) {
                        return response()->json(['message' => $actions[$action]], 403);
                    }
                }
            } elseif ($guest and !$admin) {
                if ($action != 'getUsers') {
                    return response()->json(['message' => $actions[$action]], 403);
                }
            }
        } else {
            if ($action != 'getUsers') {
                return response()->json(['message' => $actions[$action]], 403);
            }
        }
        return $next($request);
    }
}
