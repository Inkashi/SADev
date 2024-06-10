<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DTO\PermissionCollectionDTO;
use App\Models\Permission;
use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\ChangePermissionRequest;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
	public function getPermissions(Request $request)
	{
		$permissions = new PermissionCollectionDTO(Permission::all());
		return response()->json($permissions->permissions);
	}

	public function getTargetPermission(Request $request)
	{
		return response()->json(Permission::where('id', $request->id)->first());
	}

	public function createPermission(CreatePermissionRequest $request)
	{

		$user = $request->user();

		DB::beginTransaction();

		try {
			$new_permission = Permission::create([
				'name' => $request->input('name'),
				'description' => $request->input('description'),
				'code' => $request->input('code'),
				'created_by' => $user->id,
			]);

			$Log = new LogsController();
			$Log->createLogs('permissions', __FUNCTION__, $new_permission->id, 'null', $new_permission, $user->id);

			DB::commit();

			return response()->json($new_permission);
		} catch (\Exception $e) {
			DB::rollBack();

			throw $e;
		}
	}

	public function updatePermission(ChangePermissionRequest $request)
	{

		$user = $request->user();

		DB::beginTransaction();

		try {
			$permission = Permission::where('id', $request->id)->first();
			$permissionPrev = clone $permission;
			$Log = new LogsController();

			$permission->update([
				'name' => $request->input('name'),
				'description' => $request->input('description'),
				'code' => $request->input('code'),
			]);

			$Log->createLogs('permissions', __FUNCTION__, $permission->id, $permissionPrev, $permission, $user->id);

			DB::commit();

			return response()->json($permission);
		} catch (\Exception $e) {
			DB::rollBack();

			throw $e;
		}
	}

	public function hardDeletePermission(ChangePermissionRequest $request)
	{

		$permission_id = $request->id;

		DB::beginTransaction();

		try {
			$permission = Permission::withTrashed()->find($permission_id);

			$Log = new LogsController();
			$Log->createLogs('permissions', __FUNCTION__, $permission->id, $permission, 'null', $request->user()->id);

			$permission->forcedelete();

			DB::commit();

			return response()->json(['status' => '200']);
		} catch (\Exception $e) {
			DB::rollBack();

			throw $e;
		}
	}

	public function softDeletePermission(ChangePermissionRequest $request)
	{

		$permission_id = $request->id;
		$user = $request->user();

		DB::beginTransaction();

		try {
			$permission = Permission::where('id', $permission_id)->first();

			$permission->deleted_by = $user->id;

			$Log = new LogsController();
			$Log->createLogs('permissions', __FUNCTION__, $permission->id, $permission, 'null', $user->id);

			$permission->delete();
			$permission->save();

			DB::commit();

			return response()->json(['status' => '200']);
		} catch (\Exception $e) {
			DB::rollBack();

			throw $e;
		}
	}

	public function restoreDeletedPermission(ChangePermissionRequest $request)
	{

		$permission_id = $request->id;

		DB::beginTransaction();

		try {
			$permission = Permission::withTrashed()->find($permission_id);

			$Log = new LogsController();
			$Log->createLogs('permissions', __FUNCTION__, $permission->id, 'null', $permission, $request->user()->id);

			$permission->restore();
			$permission->deleted_by = null;
			$permission->save();

			DB::commit();

			return response()->json(['status' => '200']);
		} catch (\Exception $e) {
			DB::rollBack();

			throw $e;
		}
	}
}
