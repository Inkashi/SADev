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
    public function handle(Request $request, Closure $next, ...$permission): Response
    {
        //проверка наличия нужного permission
        if (!Auth::user()) {
            return response()->json(['error' => "You need login"], 403);
        }
        $roles = User::find(Auth::id())->roles();
        $userPermissions = $roles->pluck('id')->map(function ($id) {
            return Role::find($id)->permissions()->pluck('name');
        });
        $userPermissions =  $userPermissions->flatten()->toArray();
        if (array_intersect($permission, $userPermissions)) {
            return $next($request);
        } else {
            return response()->json(['error' => "You need one of these permissions -> " . implode(', ', $permission)], 403);
        }
    }
}
