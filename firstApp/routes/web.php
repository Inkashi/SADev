<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InfoController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/info/server', [InfoController::class, 'serverInfo']);
Route::get('/info/client', [InfoController::class, 'clientInfo']);
Route::get('/info/database', [InfoController::class, 'databaseInfo']);

Route::get('/', function () {
    return view('welcome');
});
