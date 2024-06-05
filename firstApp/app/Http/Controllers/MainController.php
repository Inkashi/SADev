<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Http\Requests\RegisterRequest;
use App\Models\UsersAndRoles;
use Illuminate\Support\Facades\DB;
use App\Models\UserAndCode;
use Illuminate\Support\Facades\Mail;

class MainController extends Controller
{
    public function login(LoginRequest $request)
    {
        $userdata = $request->createDTO();

        $user = User::where('username', $userdata->username)->first();

        if (!$user || !Hash::check($userdata->password, $user->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userAndCode = UserAndCode::where('user_id', $user->id)->first();
        if ($userAndCode && $userAndCode->refreshCode >= env("MAX_CODE_COUNT", 3)) {
            $now = Carbon::now();
            $oldestCode = UserAndCode::where('user_id', $user->id)->oldest()->first();
            $userAndCode->refreshCode += 1;
            if ($now->diffInSeconds($oldestCode->updated_at) <= 30) {
                return response()->json(['message' => 'You need to wait ' . 30 - $now->diffInSeconds($oldestCode->updated_at) . ' seconds'], 401);
            }
        }

        $code = rand(100000, 999999);
        if ($userAndCode) {
            $userAndCode->refreshCode > 3 ? $userAndCode->refreshCode : $userAndCode->refreshCode += 1;
            $userAndCode->time_to_expire = Carbon::now()->addMinutes(10);
            $userAndCode->code = $code;
            $userAndCode->save();
        } else {
            UserAndCode::create([
                'user_id' => $user->id,
                'code' => $code,
                'time_to_expire' => Carbon::now()->addMinutes(env("MAX_CODE_TIME", 10)),
                'refreshCode' => 1
            ]);
        }

        Mail::raw("Используйте данный код чтобы войти: $code", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Ваш код доступа');
        });
        return response()->json(['message' => 'Code send'], 200);
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
            DB::commit();

            return response()->json($user, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function verifyCode(Request $request)
    {
        $username = $request->username;
        $code = $request->code;
        $user = User::where('username', $username)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }
        $userCode = $user->tokens()->where('revoked', false)->where('expires_at', '>', Carbon::now())->count();
        if ($code == $userCode->code && Carbon::now() <= $userCode->time_to_expire) {
            $userTokenCount = $user->tokens()->count();
            if (env('MAX_ACTIVE_TOKENS') <= 0) {
                return response()->json(['message' => 'change env MAX_ACTIVE_TOKENS'], 401);
            }
            while ($userTokenCount >= env('MAX_ACTIVE_TOKENS', 3)) {
                $oldestToken = $user->tokens()->get();
                $oldestToken->sortBy('created_at')->first()->revoke();
                $userTokenCount = $user->tokens()->where('revoked', false)->count();
            }
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addDays(env('TOKEN_EXPIRATION_DAYS', 15));
            $token->save();
            $userCode->delete();
            return response()->json([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            ]);
        } else {
            return response()->json(['message' => 'Wrong code or time explode'], 401);
        }
    }

    public function refreshCode(Request $request)
    {
        $username = $request->username;
        $user = User::where('username', $username)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }
        $userCode = UserAndCode::where('user_id', $user->id)->first();
        if ($userCode) {
            if ($userCode->refreshCode >= env("MAX_CODE_COUNT", 3)) {
                $now = Carbon::now();
                $oldestCode = UserAndCode::where('user_id', $user->id)->oldest()->first();
                $userCode->refreshCode += 1;
                if ($now->diffInSeconds($oldestCode->updated_at) <= env("REFRESH_CODE_LIMIT", 30)) {
                    return response()->json(['message' => 'You need to wait ' . 30 - $now->diffInSeconds($oldestCode->updated_at) . ' seconds'], 401);
                }
            }
            $userCode->refreshCode > 3 ? $userCode->refreshCode : $userCode->refreshCode += 1;
            $userCode->time_to_expire = Carbon::now()->addMinutes(10);
            $userCode->code = rand(100000, 999999);
            $userCode->save();
            Mail::raw("Используйте данный код чтобы войти: $userCode->code", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Ваш код доступа');
            });
            return response()->json(['message' => 'Code send'], 200);
        } else {
            return response()->json(['message' => 'You need to login and request first code'], 401);
        }
    }
}
