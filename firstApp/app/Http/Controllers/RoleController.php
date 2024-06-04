<?php

namespace App\Http\Controllers;

use App\Models\RolesAndPermissions;
use Illuminate\Http\Request;
use App\DTO\RoleCollectionDTO;
use App\Models\Role;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\ChangeRoleRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RoleAndPermissionController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\ChangeRoleAndPermissionRequest;

class RoleController extends Controller
{
	public function getRoles(Request $request)
	{
		$roles = new RoleCollectionDTO(Role::all());
		return response()->json($roles->roles);
	}

	public function getTargetRole(Request $request)
	{
		return response()->json(Role::where('id', $request->id)->first());
	}

	public function createRole(CreateRoleRequest $request)
	{

		$user = $request->user();

		DB::beginTransaction();

		try {
			$new_role = Role::create([
				'name' => $request->input('name'),
				'description' => $request->input('description'),
				'code' => $request->input('code'),
				'created_by' => $user->id,
			]);

			$Log = new LogsController();
			$Log->createLogs('roles', __FUNCTION__, $new_role->id, 'null', $new_role, Auth::id());

			DB::commit();

			return response()->json($new_role);
		} catch (\Exception $e) {
			DB::rollBack();

			throw $e;
		}
	}

	public function updateRole(ChangeRoleRequest $request)
	{

		$user = $request->user();

		DB::beginTransaction();

		try {
			$role = Role::where('id', $request->id)->first();
			$rolePrev = clone $role;
			$Log = new LogsController();
			$role->update([
				'name' => $request->input('name'),
				'description' => $request->input('description'),
				'code' => $request->input('code'),
			]);
			$role->save();
			$Log->createLogs('roles', __FUNCTION__, $role->id, $rolePrev, $role, Auth::id());

			DB::commit();

			return response()->json($role);
		} catch (\Exception $e) {
			DB::rollBack();

			throw $e;
		}
	}

	public function hardDeleteRole(ChangeRoleRequest $request)
	{

		$role_id = $request->id;
		$roleAndPermission = new RoleAndPermissionController();
		DB::beginTransaction();

		try {
			$role = Role::withTrashed()->find($role_id);
			$rolePermissions = $role->permissions();
			$rolePermissions->each(function ($rolePermissions) use ($role_id, $roleAndPermission, $request) {
				$newRequest = new ChangeRoleAndPermissionRequest();
				$newRequest->replace($request->all());
				$newRequest->replace([
					'id' => $role_id,
					'permission_id' => $rolePermissions->id,
					'need_logs' => 'true',
				]);
				$roleAndPermission->hardDeleteRolePermission($newRequest);
			});

			$Log = new LogsController();
			$Log->createLogs('roles', __FUNCTION__, $role->id, $role, 'null', Auth::id());

			$role->forcedelete();

			DB::commit();

			return response()->json(['status' => '200']);
		} catch (\Exception $e) {
			DB::rollBack();

			throw $e;
		}
	}

	public function softDeleteRole(ChangeRoleRequest $request)
	{

		$role_id = $request->id;
		$user = $request->user();
		$roleAndPermission = new RoleAndPermissionController();
		DB::beginTransaction();

		try {
			$role = Role::where('id', $role_id)->first();
			$rolePermissions = $role->permissions();
			$rolePermissions->each(function ($rolePermissions) use ($role_id, $roleAndPermission, $request) {
				$newRequest = new ChangeRoleAndPermissionRequest();
				$newRequest->replace($request->all());
				$newRequest->replace([
					'id' => $role_id,
					'permission_id' => $rolePermissions->id,
					'need_logs' => 'true',
				]);
				$roleAndPermission->softDeleteRolePermission($newRequest);
			});
			$Log = new LogsController();
			$Log->createLogs('roles', __FUNCTION__, $role->id, $role, 'null', $user->id);

			$role->deleted_by = $user->id;
			$role->delete();
			$role->save();

			DB::commit();

			return response()->json(['status' => '200']);
		} catch (\Exception $e) {
			DB::rollBack();

			throw $e;
		}
	}

	public function restoreDeletedRole(ChangeRoleRequest $request)
	{

		$role_id = $request->id;
		$roleAndPermission = new RoleAndPermissionController();
		DB::beginTransaction();

		try {
			$role = Role::withTrashed()->find($role_id);
			$role->restore();
			$role->deleted_by = null;
			$role->save();
			$rolePermissions = RolesAndPermissions::withTrashed()->where('role_id', $role_id)->get();
			$rolePermissions->each(function ($rolePermissions) use ($role_id, $roleAndPermission, $request) {
				$newRequest = new ChangeRoleAndPermissionRequest();
				$newRequest->replace($request->all());
				$newRequest->replace([
					'id' => $role_id,
					'permission_id' => $rolePermissions->permission_id,
					'need_logs' => 'no',
				]);
				$roleAndPermission->restoreDeletedRolePermission($newRequest);
			});
			$Log = new LogsController();
			$Log->createLogs('roles', __FUNCTION__, $role->id, 'null', $role, $request->user()->id);

			DB::commit();

			return response()->json(['status' => '200']);
		} catch (\Exception $e) {
			DB::rollBack();

			throw $e;
		}
	}
}
