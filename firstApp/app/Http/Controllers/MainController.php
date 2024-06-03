<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Laravel\Passport\Token;
use Carbon\Carbon;
use Illuminate\Http\Requests;
use App\Http\Requests\RegisterRequest;
use App\DTO\RegisterDTO;
use App\Models\UsersAndRoles;
use Illuminate\Support\Facades\DB;
use App\DTO\UserDTO;

class MainController extends Controller
{
    public function login(LoginRequest $request)
    {
        $userdata = $request->createDTO();

        $user = User::where('username', $userdata->username)->first();

        if (!$user || !Hash::check($userdata->password, $user->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userTokenCount = $user->tokens()->count();
        while ($userTokenCount >= env('MAX_ACTIVE_TOKENS', 3)) {
            $oldestToken = $user->tokens()->get();
            $oldestToken = $oldestToken->filter(function ($token) {
                return $token->revoked == false;
            });
            $oldestToken->sortBy('created_at')->first()->revoke();
            $userTokenCount = $user->tokens()->where('revoked', false)->count();
        }
        if (env('MAX_ACTIVE_TOKENS') == 0) {
            return response()->json(['message' => 'change env MAX_ACTIVE_TOKENS'], 401);
        }
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addDays(env('TOKEN_EXPIRATION_DAYS', 15));
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
        ]);
    }


    public function out(Request $request)
    {
        $user = Auth::user();
        $user->token()->revoke();
        return response()->json(["Token is logout"], 200);
    }

    public function outAll(Request $request)
    {
        $user = Auth::user();

        $user->tokens->each(function ($token, $key) {
            $token->revoke();
        });
        return response()->json(["All tokens is logout"], 200);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json(["user" => $user]);
    }

    public function getTokens(Request $request)
    {
        $user = $request->user();
        $tokens = $user->tokens;

        return response()->json(['tokens' => $tokens]);
    }

    public function register(RegisterRequest $request)
    {
        $userData = $request->createDTO();

        DB::beginTransaction();

        try {
            $user = User::create([
                'username' => $userData->username,
                'email' => $userData->email,
                'password' => bcrypt($userData->password),
                'birthday' => $userData->birthday,
            ]);

            $role = UsersAndRoles::create([
                'user_id' => $user->id,
                'role_id' => 3,
                'created_by' => 1,
            ]);
            $Log = new LogsController();
            $Log->createLogs('User', "register", $user->id, 'null', $user, $user->id);
            $Log->createLogs('UsersAndRoles', 'register', $role->id, 'null', $role, $user->id);
            DB::commit();

            return response()->json($user, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
