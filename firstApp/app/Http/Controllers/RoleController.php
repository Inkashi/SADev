<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DTO\RoleCollectionDTO;
use App\Models\Role;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\ChangeRoleRequest;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
	public function getRoles(Request $request)
	{
		$roles = new RoleCollectionDTO(Role::all());
		return response()->json($roles->roles);
	}

	public function getTargetRole(Request $request)
	{
		return response()->json(Role::find($request->id));
	}

	public function createRole(CreateRoleRequest $request)
	{

		$user = $request->user();

		$new_role = Role::create([
			'name' => $request->input('name'),
			'description' => $request->input('description'),
			'code' => $request->input('code'),
			'created_by' => $user->id,
		]);

		return response()->json($new_role);
	}

	public function updateRole(ChangeRoleRequest $request)
	{

		$user = $request->user();

		$role = Role::where('id', $request->id)->first();

		$role->update([
			'name' => $request->input('name'),
			'description' => $request->input('description'),
			'code' => $request->input('code'),
		]);

		return response()->json($role);
	}

	public function validateRole($role)
	{
		if ($role == 1) {
			return response()->json(['error' => "You can't remove the admin"]);
		}
		return true;
	}

	public function hardDeleteRole(ChangeRoleRequest $request)
	{
		$role_id = $request->id;
		$validationResult = $this->validateRole($role_id);
		if ($validationResult !== true) {
			return $validationResult;
		}
		$role = Role::withTrashed()->find($role_id);

		$role->forcedelete();

		return response()->json(['status' => '200']);
	}

	public function softDeleteRole(ChangeRoleRequest $request)
	{
		$role_id = $request->id;
		$validationResult = $this->validateRole($role_id);
		if ($validationResult !== true) {
			return $validationResult;
		}
		$user = $request->user();

		$role = Role::where('id', $role_id)->first();
		if (!$role) {
			return response()->json(['error' => "The role with the given id does not exist"]);
		}
		$role->deleted_by = $user->id;
		$role->delete();
		$role->save();

		return response()->json(['status' => '200']);
	}

	public function restoreDeletedRole(ChangeRoleRequest $request)
	{

		$role_id = $request->id;

		$role = Role::withTrashed()->find($role_id);

		$role->restore();
		$role->deleted_by = null;
		$role->save();

		return response()->json(['status' => '200']);
	}
}
