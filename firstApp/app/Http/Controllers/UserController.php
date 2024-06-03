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

		DB::beginTransaction();

		try {
			$userPrevRoles = UsersAndRoles::where('user_id', $user_id)->get();
			$usersAndRoles = UsersAndRoles::create([
				'user_id' => $user_id,
				'role_id' => $role_id,
				'created_by' => $request->user()->id,
			]);
			$userAfterRoles = UsersAndRoles::where('user_id', $user_id)->get();
			$Log = new LogsController();
			$Log->createLogs('usersAndRoles', 'giveUserRole', $usersAndRoles->id, 'null', $usersAndRoles, $request->user()->id);

			DB::commit();

			return response()->json(['status' => '200']);
		} catch (\Exception $e) {
			DB::rollBack();

			throw $e;
		}
	}

	public function hardDeleteUserRole(ChangeUserAndRoleRequest $request)
	{
		$user_id = $request->id;
		$role_id = $request->role_id;

		DB::beginTransaction();

		try {
			$userAndRoles = UsersAndRoles::withTrashed()->where('user_id', $user_id)->where('role_id', $role_id);

			$userAndRoles = $userAndRoles->first();
			$Log = new LogsController();
			$Log->createLogs('usersAndRoles', 'hardDeleteUserRole', $userAndRoles->id, $userAndRoles, 'null', Auth::id());

			$userAndRoles->forcedelete();

			DB::commit();

			return response()->json(['status' => '200']);
		} catch (\Exception $e) {
			DB::rollBack();

			throw $e;
		}
	}

	public function softDeleteUserRole(ChangeUserAndRoleRequest $request)
	{

		$user_id = $request->id;
		$role_id = $request->role_id;

		DB::beginTransaction();

		try {
			$userAndRoles = UsersAndRoles::where('user_id', $user_id)->where('role_id', $role_id)->first();
			if ($userAndRoles == null) {
				return response()->json(['error' => 'The user does not have such a role']);
			}

			$Log = new LogsController();
			$Log->createLogs('usersAndRoles', 'softDeleteUserRole', $userAndRoles->id, $userAndRoles, 'null', $request->user()->id);

			$userAndRoles->deleted_by = $request->user()->id;
			$userAndRoles->delete();
			$userAndRoles->save();

			DB::commit();

			return response()->json(['status' => '200']);
		} catch (\Exception $e) {
			DB::rollBack();

			throw $e;
		}
	}

	public function restoreDeletedUserRole(ChangeUserAndRoleRequest $request)
	{
		$user_id = $request->id;
		$role_id = $request->role_id;

		DB::beginTransaction();

		try {
			$userAndRoles = UsersAndRoles::withTrashed()->where('user_id', $user_id)->where('role_id', $role_id)->first();

			$userAndRoles->restore();

			$Log = new LogsController();
			$Log->createLogs('usersAndRoles', 'restoreDeletedUserRole', $userAndRoles->id, 'null', $userAndRoles, $request->user()->id);

			$userAndRoles->deleted_by = null;
			$userAndRoles->save();

			DB::commit();

			return response()->json(['status' => '200']);
		} catch (\Exception $e) {
			DB::rollBack();

			throw $e;
		}
	}

	public function hardDeleteUser(Request $request)
	{
		$user_id = $request->id;
		DB::beginTransaction();
		try {
			$user = User::find($user_id);
			if (!$user) {
				return response()->json(['status' => '404', 'message' => 'User not found'], 404);
			}
			$usersAndRoles = UsersAndRoles::where('user_id', $user_id)->get();
			$Log = new LogsController();
			$Log->createLogs('user', 'hardDeleteUser', $user->id, $user, 'null', Auth::id());
			$usersAndRoles->each(function ($usersAndRoles) {
				$Log = new LogsController();
				$Log->createLogs('usersAndRoles', 'hardDeleteUser', $usersAndRoles->id, 'null', $usersAndRoles, Auth::id());
				$usersAndRoles->forcedelete();
			});
			$user->forceDelete();
			DB::commit();

			return response()->json(['status' => '200']);
		} catch (\Exception $e) {
			DB::rollBack();

			throw $e;
		}
	}

	public function softDeleteUser(Request $request)
	{
		$user_id = $request->id;
		DB::beginTransaction();
		try {
			$user = User::find($user_id);
			if (!$user) {
				return response()->json(['status' => '404', 'message' => 'User not found'], 404);
			}
			$usersAndRoles = UsersAndRoles::where('user_id', $user_id)->get();
			$Log = new LogsController();
			$Log->createLogs('user', 'softDeleteUser', $user->id, $user, 'null', Auth::id());
			$usersAndRoles->each(function ($usersAndRoles) {
				$Log = new LogsController();
				$Log->createLogs('usersAndRoles', 'softDeleteUser', $usersAndRoles->id, 'null', $usersAndRoles, Auth::id());
				$usersAndRoles->deleted_by = Auth::id();
				$usersAndRoles->delete();
				$usersAndRoles->save();
			});
			$user->delete();
			$user->save();
			DB::commit();

			return response()->json(['status' => '200']);
		} catch (\Exception $e) {
			DB::rollBack();

			throw $e;
		}
	}

	public function restoreDeletedUser(Request $request)
	{
		$user_id = $request->id;

		DB::beginTransaction();

		try {
			$userAndRoles = UsersAndRoles::withTrashed()->where('user_id', $user_id)->get();

			$userAndRoles->each(function ($userAndRoles) {
				$userAndRoles->restore();
				$Log = new LogsController();
				$Log->createLogs('usersAndRoles', 'restoreDeletedUser', $userAndRoles->id, 'null', $userAndRoles, Auth::id());
				$userAndRoles->deleted_by = null;
				$userAndRoles->save();
			});

			$user = User::withTrashed()->find($user_id);
			$user->restore();
			$Log = new LogsController();
			$Log->createLogs('user', 'restoreDeletedUser', $user->id, 'null', $user, Auth::id());
			$user->save();

			DB::commit();

			return response()->json(['status' => '200']);
		} catch (\Exception $e) {
			DB::rollBack();

			throw $e;
		}
	}

	public function changeUserRole(Request $request)
	{
		$role = $request->role; //int значение
		DB::beginTransaction();

		try {
			$user = UsersAndRoles::where('user_id', $request->id)->first();
			$userPrev = clone $user;
			$user->update([
				'role_id' => $role,
			]);
			$user->save();
			$Log = new LogsController();
			$Log->createLogs('usersAndRoles', 'UpdateUserRole', $user->id, $userPrev, $user, Auth::id());
			DB::commit();
			return response()->json(['status' => '200']);
		} catch (\Exception $e) {
			DB::rollBack();
			throw $e;
		}
	}

	public function updateInformation(UpdateInfoRequest $request)
	{
		$new_pass = $request->new_pass;
		$new_email = $request->new_email;
		$new_birthday = $request->new_birthday;
		$new_username = $request->new_username;

		DB::beginTransaction();
		try {
			$user = User::find(Auth::id());
			$userPrev = clone $user;
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
			$user->save();
			$Log = new LogsController();
			$Log->createLogs('user', 'updateInformation', $user->id, $userPrev, $user, Auth::id());
			DB::commit();
			return response()->json(['status' => '200']);
		} catch (\Exception $e) {
			DB::rollBack();
			throw $e;
		}
	}
}
