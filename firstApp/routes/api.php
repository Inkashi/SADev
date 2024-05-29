<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleAndPermissionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('checkRole')->group(function () {
	Route::prefix('ref')->group(function () {
		Route::prefix('user')->group(function () {
			Route::get('/', [UserController::class, 'getUsers']);
			Route::delete('{id}/hard', [UserController::class, 'hardDeleteUser']);
			Route::delete('{id}/soft', [UserController::class, 'softDeleteUser']);
			Route::post('{id}/restore', [UserController::class, 'restoreDeletedUser']);
			Route::post('{id}/changeUserData', [UserController::class, 'changeUserData']);
			Route::get('{id}/role', [UserController::class, 'getUserRoles']);
			Route::post('{id}/role', [UserController::class, 'giveUserRoles']);
			Route::delete('{id}/role/{role_id}/hard', [UserController::class, 'hardDeleteRole']);
			Route::delete('{id}/role/{role_id}/soft', [UserController::class, 'softDeleteRole']);
			Route::post('{id}/role/{role_id}/restore', [UserController::class, 'restoreDeletedRole']);
		});

		Route::prefix('policy')->group(function () {
			Route::get('role', [RoleController::class, 'getRoles']);
			Route::get('role/{id}', [RoleController::class, 'getTargetRole']);
			Route::post('role', [RoleController::class, 'createRole']);
			Route::put('role/{id}', [RoleController::class, 'updateRole']);
			Route::delete('role/{id}/hard', [RoleController::class, 'hardDeleteRole']);
			Route::delete('role/{id}/soft', [RoleController::class, 'softDeleteRole']);
			Route::post('role/{id}/restore', [RoleController::class, 'restoreDeletedRole']);



			Route::get('permission', [PermissionController::class, 'getPermissions']);
			Route::get('permission/{id}', [PermissionController::class, 'getTargetPermission']);
			Route::post('permission', [PermissionController::class, 'createPermission']);
			Route::put('permission/{id}', [PermissionController::class, 'updatePermission']);
			Route::delete('permission/{id}/hard', [PermissionController::class, 'hardDeletePermission']);
			Route::delete('permission/{id}/soft', [PermissionController::class, 'softDeletePermission']);
			Route::post('permission/{id}/restore', [PermissionController::class, 'restoreDeletedPermission']);



			Route::get('role/{id}/permission', [RoleAndPermissionController::class, 'getRolePermission']);
			Route::get('role/{id}/permission/{permission_id}', [RoleAndPermissionController::class, 'addRolePermission']);
			Route::delete('role/{id}/permission/{permission_id}/hard', [RoleAndPermissionController::class, 'hardDeleteRolePermission']);
			Route::delete('role/{id}/permission/{permission_id}/soft', [RoleAndPermissionController::class, 'softDeleteRolePermission']);
			Route::post('role/{id}/permission/{permission_id}/restore', [RoleAndPermissionController::class, 'restoreDeletedRolePermission']);
		});
	});
});



Route::prefix('auth')->group(function () {

	Route::get('login', function () {
		return view('/api/auth/login');
	})->name('/login');

	Route::post('login', [MainController::class, "login"])->name('login');


	Route::middleware('check')->group(function () {
		Route::get('register', function () {
			return view('/api/auth/register');
		});

		Route::post('register', [MainController::class, "register"]);
	});

	Route::middleware('auth:api')->group(function () {

		Route::get('me', [MainController::class, "me"])->name('me');

		Route::post('out', [MainController::class, "out"]);

		Route::post('out_all', [MainController::class, "outAll"]);

		Route::get('tokens', [MainController::class, "getTokens"]);
	});
});
