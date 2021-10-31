<?php

use App\Http\Controllers\AuthenticationController;
use HabitTracking\Infrastructure\HabitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthenticationController::class, 'login']);
Route::post('register', [AuthenticationController::class, 'register']);
Route::delete('logout', [AuthenticationController::class, 'logout'])
     ->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', fn (Request $request) => $request->user());

    Route::prefix('habits')->group(function () {
        Route::get('today', [HabitController::class, 'todayIndex']);
        Route::get('', [HabitController::class, 'index']);
        Route::get('{id}', [HabitController::class, 'show']);
        Route::post('', [HabitController::class, 'start']);
        Route::put('{id}', [HabitController::class, 'update']);
        Route::put('{id}/complete', [HabitController::class, 'complete']);
        Route::put('{id}/incomplete', [HabitController::class, 'incomplete']);
        Route::delete('{id}', [HabitController::class, 'stop']);
    });
});
