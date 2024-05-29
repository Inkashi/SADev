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

		$new_permission = Permission::create([
			'name' => $request->input('name'),
			'description' => $request->input('description'),
			'code' => $request->input('code'),
			'created_by' => $user->id,
		]);

		return response()->json($new_permission);
	}

	public function updatePermission(ChangePermissionRequest $request)
	{

		$user = $request->user();

		$permission = Permission::where('id', $request->id)->first();

		$permission->update([
			'name' => $request->input('name'),
			'description' => $request->input('description'),
			'code' => $request->input('code'),
		]);

		return response()->json($permission);
	}

	public function hardDeletePermission(ChangePermissionRequest $request)
	{

		$permission_id = $request->id;

		$permission = Permission::withTrashed()->find($permission_id);

		$permission->forcedelete();

		return response()->json(['status' => '200']);
	}

	public function softDeletePermission(ChangePermissionRequest $request)
	{

		$permission_id = $request->id;
		$user = $request->user();

		$permission = Permission::where('id', $permission_id)->first();

		$permission->deleted_by = $user->id;
		$permission->delete();
		$permission->save();

		return response()->json(['status' => '200']);
	}

	public function restoreDeletedPermission(ChangePermissionRequest $request)
	{

		$permission_id = $request->id;

		$permission = Permission::withTrashed()->find($permission_id);

		$permission->restore();
		$permission->deleted_by = null;
		$permission->save();

		return response()->json(['status' => '200']);
	}
}
