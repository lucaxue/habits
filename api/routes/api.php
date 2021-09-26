<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HabitController;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', fn (Request $request) => $request->user());

    Route::get('habits', [HabitController::class, 'index']);
    Route::post('habits', [HabitController::class, 'start']);
    Route::get('habits/{id}', [HabitController::class, 'retrieve']);
    Route::put('habits/{id}/complete', [HabitController::class, 'complete']);
    Route::put('habits/{id}/incomplete', [HabitController::class, 'complete']);
});
