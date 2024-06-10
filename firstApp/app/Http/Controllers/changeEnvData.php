<?php

namespace App\Http\Controllers;

use App\Http\Requests\changeEnvRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class changeEnvData extends Controller
{
    public function updateEnv(changeEnvRequest $request)
    {
        $request->MAX_ACTIVE_TOKENS ? $this->changeData('MAX_ACTIVE_TOKENS', $request->MAX_ACTIVE_TOKENS) : null;
        $request->TOKEN_EXPIRATION_DAYS ? $this->changeData('TOKEN_EXPIRATION_DAYS', $request->TOKEN_EXPIRATION_DAYS) : null;
        $request->MAX_CODE_COUNT ? $this->changeData('MAX_CODE_COUNT', $request->MAX_CODE_COUNT) : null;
        $request->REFRESH_CODE_LIMIT ? $this->changeData('REFRESH_CODE_LIMIT', $request->REFRESH_CODE_LIMIT) : null;
        $request->MAX_CODE_TIME ? $this->changeData('MAX_CODE_TIME', $request->MAX_CODE_TIME) : null;

        Artisan::call('config:clear');

        return response()->json(['message' => 'Data updated'], 200);
    }

    private function changeData($key, $value)
    {
        if ($value == null) {
            return false;
        }
        $path = base_path('.env');

        if (file_exists($path)) {
            $envContent = file_get_contents($path);
            if (strpos($envContent, "$key=") !== false) {
                $newContent = preg_replace(
                    "/^$key=.*/m",
                    "$key=" . $value,
                    $envContent
                );
            } else {
                $newContent = $envContent . PHP_EOL . "$key=$value";
            }
        }
        if (file_put_contents($path, $newContent)) {
            return true;
        } else {
            return false;
        }
    }
}
