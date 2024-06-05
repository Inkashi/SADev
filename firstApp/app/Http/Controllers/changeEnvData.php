<?php

namespace App\Http\Controllers;

use App\Http\Requests\changeEnvRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class changeEnvData extends Controller
{
    public function updateEnv(changeEnvRequest $request)
    {
        $this->changeData('MAX_ACTIVE_TOKENS', $request->input('MAX_ACTIVE_TOKENS'));
        $this->changeData('TOKEN_EXPIRATION_DAYS', $request->input('TOKEN_EXPIRATION_DAYS'));
        $this->changeData('MAX_CODE_COUNT', $request->input('MAX_CODE_COUNT'));
        $this->changeData('REFRESH_CODE_LIMIT', $request->input('REFRESH_CODE_LIMIT'));

        Artisan::call('config:cache');

        return response()->json(['MAX_ACTIVE_TOKENS' => env('MAX_ACTIVE_TOKENS')]);
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
