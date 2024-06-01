<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DTO\UserCollectionDTO;
use App\DTO\UserAndRoleCollectionDTO;
use App\Models\UsersAndRoles;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\DTO\RoleCollectionDTO;
use App\Http\Requests\ChangeUserAndRoleRequest;
use App\Http\Requests\CreateUserAndRoleRequest;
use App\Http\Requests\UpdateInfoRequest;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

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
			return response()->json(['error' => 'The user already has such a role']);
		}

		UsersAndRoles::create([
			'user_id' => $user_id,
			'role_id' => $role_id,
			'created_by' => $request->user()->id,
		]);
		return response()->json(['status' => '200']);
	}

	public function hardDeleteUserRole(ChangeUserAndRoleRequest $request)
	{
		$user_id = $request->id;
		$role_id = $request->role_id;

		$userAndRoles = UsersAndRoles::withTrashed()->where('user_id', $user_id)->where('role_id', $role_id);

		$userAndRoles->forcedelete();

		return response()->json(['status' => '200']);
	}

	public function softDeleteUserRole(ChangeUserAndRoleRequest $request)
	{

		$user_id = $request->id;
		$role_id = $request->role_id;

		$userAndRoles = UsersAndRoles::where('user_id', $user_id)->where('role_id', $role_id)->first();

		$userAndRoles->deleted_by = $request->user()->id;
		$userAndRoles->delete();
		$userAndRoles->save();

		return response()->json(['status' => '200']);
	}

	public function restoreDeletedUserRole(ChangeUserAndRoleRequest $request)
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
		$user = UsersAndRoles::where('user_id', $request->id)->first();
		$role = $request->role; //int значение

		$user->update([
			'role_id' => $role,
		]);
		return response()->json(['status' => '200']);
	}

	public function updateInformation(UpdateInfoRequest $request)
	{
		$new_pass = $request->new_pass;
		$new_email = $request->new_email;
		$new_birthday = $request->new_birthday;
		$new_username = $request->new_username;

		$user = User::find(Auth::id());
		if (!$user || !Hash::check($request->old_pass, $user->password)) {
			return response()->json(['message' => 'Old password is incorrect'], 401);
		}
		if ($new_pass != '') {
			$user->update([
				'password' => $new_pass,
			]);
			$user->token()->revoke();
		}
		if ($new_email != '') {
			$user->update([
				'email' => $new_email,
			]);
		}
		if ($new_birthday != '') {
			$user->update([
				'birthday' => $new_birthday,
			]);
		}
		if ($new_username != '') {
			$user->update([
				'username' => $new_username,
			]);
		}
		return response()->json(['status' => '200']);
	}
}
