<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {

    Route::get('login', function () {
        return view('/api/auth/login');
    })->name('/login');

    Route::post('login', [MainController::class, "login"])->name('login');


    Route::middleware('check')->group(function () {
        Route::get('register', function () {
            return view('/api/auth/register');
        });
        Route::post('register', [MainController::class, "register"]);
    });

    Route::middleware('auth:api')->group(function () {
        Route::get('me', [MainController::class, "check"])->name('me');
        Route::post('out', [MainController::class, "out"]);
        Route::get('tokens', [MainController::class, "getTokens"]);
        Route::post('out_all', [MainController::class, "outAll"]);
    });
});
