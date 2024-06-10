<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GitController extends Controller
{
    public function updateGit(Request $request)
    {
        $secret_key_user = $request->secret_key_user;
        $secret_key_env = env('SECRET_KEY');

        if ($secret_key_user !== $secret_key_env) {
            return response()->json(['error' => 'Incorrect secret key'], 401);
        }

        if (Cache::has('lock')) {
            return response()->json(['error' => 'Another request is in progress'], 409);
        }
        Cache::put('lock', true, 600);
        try {
            $logUser = ['date' => now(), 'user_ip' => $request->ip()];
            Log::info('Code update', $logUser);
            Log::info('Branch checkout:');
            $output = shell_exec('D:\Apps\Git\bin\git.exe checkout main');
            Log::info($output);
            Log::info('Fetch All:');
            $output = shell_exec('D:\Apps\Git\bin\git.exe fetch --all');
            Log::info($output);
            Log::info('Reset Hard:');
            $output = shell_exec('D:\Apps\Git\bin\git.exe reset --hard origin/main');
            Log::info($output);
            Log::info('Pull origin main:');
            $output = shell_exec('D:\Apps\Git\bin\git.exe pull origin main');
            Log::info($output);
            Cache::forget('lock');
            return response()->json(['message' => 'Code updated'], 200);
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            Cache::forget('lock');
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
