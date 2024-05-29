<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DTO\UserCollectionDTO;
use App\DTO\UserAndRoleCollectionDTO;
use App\Models\UsersAndRoles;
use App\Models\User;
use App\Models\Role;
use App\DTO\RoleCollectionDTO;
use App\Http\Requests\ChangeUserAndRoleRequest;
use App\Http\Requests\CreateUserAndRoleRequest;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
	public function getUsers(Request $request)
	{
		$users = new UserCollectionDTO();
		return response()->json($users->users);
	}

	public function getUserRoles(UserRequest $request)
	{
		$user_id = $request->id;

		$roles_id = UsersAndRoles::select('role_id')->where('user_id', $user_id)->get();

		$roles = $roles_id->map(function ($id) {
			return Role::find($id->role_id);
		});

		return response()->json($roles);
	}

	public function giveUserRoles(CreateUserAndRoleRequest $request)
	{

		$user_id = $request->id;

		$role_id = $request->input('role_id');

		$count = UsersAndRoles::where('user_id', $user_id)->where('role_id', $role_id)->count();

		if ($count) {
			return response()->json(['status' => '501']);
		}

		UsersAndRoles::create([
			'user_id' => $user_id,
			'role_id' => $role_id,
			'created_by' => $request->user()->id,
		]);
		return response()->json(['status' => '200']);
	}

	public function hardDeleteRole(ChangeUserAndRoleRequest $request)
	{
		$user_id = $request->id;
		$role_id = $request->role_id;

		$userAndRoles = UsersAndRoles::withTrashed()->where('user_id', $user_id)->where('role_id', $role_id);

		$userAndRoles->forcedelete();

		return response()->json(['status' => '200']);
	}

	public function softDeleteRole(ChangeUserAndRoleRequest $request)
	{

		$user_id = $request->id;
		$role_id = $request->role_id;

		$userAndRoles = UsersAndRoles::where('user_id', $user_id)->where('role_id', $role_id)->first();

		$userAndRoles->deleted_by = $request->user()->id;
		$userAndRoles->delete();
		$userAndRoles->save();

		return response()->json(['status' => '200']);
	}

	public function restoreDeletedRole(ChangeUserAndRoleRequest $request)
	{
		$user_id = $request->id;
		$role_id = $request->role_id;

		$userAndRoles = UsersAndRoles::withTrashed()->where('user_id', $user_id)->where('role_id', $role_id)->first();

		$userAndRoles->restore();
		$userAndRoles->deleted_by = null;
		$userAndRoles->save();

		return response()->json(['status' => '200']);
	}

	public function hardDeleteUser(Request $request)
	{
		$user_id = $request->id;
		UsersAndRoles::where('user_id', $user_id)->forceDelete();

		$user = User::find($user_id);
		if ($user) {
			$user->forceDelete();
		} else {
			return response()->json(['status' => '404', 'message' => 'User not found'], 404);
		}
		return response()->json(['status' => '200']);
	}

	public function softDeleteUser(Request $request)
	{
		$user_id = $request->id;
		UsersAndRoles::where('user_id', $user_id)->delete();
		User::find($user_id)->delete();
		return response()->json(['status' => '200']);
	}

	public function restoreDeletedUser(Request $request)
	{
		$user_id = $request->id;
		UsersAndRoles::withTrashed()->where('user_id', $user_id)->restore();
		User::withTrashed()->find($user_id)->restore();
		return response()->json(['status' => '200']);
	}

	public function changeUserData(Request $request)
	{
		$user_id = $request->id;

		$new_email = $request->email;
		$new_birthday = $request->birthday;

		$user = User::find($user_id);

		if (!$user) {
			return response()->json(['status' => '404', 'message' => 'User not found'], 404);
		}

		$validator = Validator::make($request->all(), [
			'email' => 'sometimes|string|email|unique:users,email,' . $user_id,
			'birthday' => 'sometimes|date',
		]);

		if ($validator->fails()) {
			return response()->json(['status' => '422', 'errors' => $validator->errors()], 422);
		}

		DB::beginTransaction();
		try {
			if (!empty($new_email)) {
				$user->email = $new_email;
			}

			if (!empty($new_birthday)) {
				$user->birthday = $new_birthday;
			}

			$user->save();

			DB::commit();

			return response()->json(['status' => '200', 'message' => 'User updated']);
		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json(['status' => '500', 'message' => 'Error'], 500);
		}

		$user->save();

		return response()->json(['status' => '200']);
	}
}
